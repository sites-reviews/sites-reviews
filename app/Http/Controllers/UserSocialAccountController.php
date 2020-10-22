<?php

namespace App\Http\Controllers;

use App\Notifications\UserHasRegisteredNotification;
use App\Notifications\WelcomeNotification;
use App\User;
use App\UserEmail;
use App\UserPhoto;
use App\UserSocialAccount;
use ErrorException;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ImagickException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use RuntimeException;

class UserSocialAccountController extends Controller
{
    /**
     *
     * Display a listing of the resource.
     *
     * @param User $user
     * @return View
     * @throws
     */
    public function index(User $user)
    {
        $this->authorize('edit', $user);

        $social_accounts = $user->social_accounts;

        $providers = [
            'google',
            //'facebook',
            'vkontakte'
        ];

        return view('user.setting.social_account', compact('user', 'social_accounts', 'providers'));
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        return DB::transaction(function () use ($provider) {
            try {
                try {
                    $providerUser = Socialite::driver($provider)->user();
                } catch (InvalidStateException $e) {
                    $providerUser = Socialite::driver($provider)->stateless()->user();
                }
            } catch (ClientException $clientException) {
                //dd(json_decode($clientException->getResponse()->getBody()->getContents()));
                return redirect()
                    ->route('login')
                    ->with('login_error', true)
                    ->withErrors(['error' => __('Login error occurred')]);

            } catch (RuntimeException $exception) {

            } catch (ErrorException | Exception $exception) {

            }

            if (isset($exception))
                return $this->exception($exception);

            $emails = [];

            if (!empty($providerUser->getEmail())) {
                $emails[] = $providerUser->getEmail();
            } else {
                if (!empty($providerUser->accessTokenResponseBody['email'])) {
                    $emails[] = $providerUser->accessTokenResponseBody['email'];
                }
            }

            // если пользователь вошел на сайт, то привязываем учетную запись
            if (auth()->check()) {

                $auth_user = auth()->user();

                UserSocialAccount::where('provider', $provider)
                    ->where('provider_user_id', $providerUser->getId())
                    ->delete();

                $userSocialAccount = new UserSocialAccount;
                $userSocialAccount->provider = $provider;
                $userSocialAccount->access_token = $providerUser->token;
                $userSocialAccount->provider_user_id = $providerUser->getId();
                $userSocialAccount->parameters = $providerUser;

                $auth_user->social_accounts()->save($userSocialAccount);

                $this->downloadUserPhotoIfNotExists($auth_user, $providerUser->getAvatar());

                return redirect()
                    ->route('users.social_accounts.index', auth()->user())
                    ->with(['success' => __('Social network account :provider linked successfully', ['provider' => $userSocialAccount->provider])]);
            } else {
                // если нет, то проверяем, существует ли у какого нибудь аккаунта привязка

                $userSocialAccount = UserSocialAccount::where('provider', $provider)
                    ->where('provider_user_id', $providerUser->getId())
                    ->first();

                if (!empty($userSocialAccount)) {
                    // если привязка есть, то залогиниваем пользователя

                    $user = $userSocialAccount->user;

                    if (empty($user))
                        return redirect()
                            ->route('login')
                            ->with('user_not_found', true)
                            ->withErrors(['error' => __('The user is not found')]);

                    auth()->login($userSocialAccount->user);

                    return redirect()
                        ->route('users.show', ['user' => $userSocialAccount->user]);
                } else {

                    if (empty($emails))
                        return redirect()
                            ->route('login')
                            ->with('email_not_found', true)
                            ->withErrors(['error' => __('The mailbox was not found or access to it was denied').
                            __('Please link your mailbox to the selected social network or allow access, or use a different login method')]);

                    $user = User::whereEmailsIn($emails)
                        ->first();

                    if (empty($user)) {
                        $user = User::whereEmailsIn($emails)
                            ->first();
                    }

                    if (empty($user)) {
                        $password = Str::random(8);

                        $user = new User;
                        $user->name = $providerUser->getName();
                        $user->email = $emails[0];
                        $user->email_verified_at = now();
                        $user->password = $password;
                        $user->save();

                        $is_new_user = true;
                    } else {
                        $is_new_user = false;
                    }

                    return $this->createSocialAccount($provider, $providerUser, $user, $is_new_user);
                }
            }
        });
    }

    private function exception($exception)
    {
        if (preg_match('/(Invalid\ JSON\ response\ from\ VK:)(.*)/iu', $exception->getMessage(), $matches)) {
            $json = json_decode($matches[2]);

            if (isset($json->error)) {
                return redirect()
                    ->route('login')
                    ->withErrors(['error' => __($json->error->error_msg)]);
            }

        } elseif ($exception->getMessage() == 'Undefined index: displayName') {
            return redirect()
                ->route('login')
                ->with('google_did_not_send_username', true)
                ->withErrors(['error' => __("Google didn't send the username")]);
        } elseif ($exception->getMessage() == 'Undefined index: emails') {
            return redirect()
                ->route('login')
                ->with('email_not_found', true)
                ->withErrors(['error' => __('Mailbox not found. Please allow us to use or link your mailbox to a social network')]);
        }

        Log::warning($exception);

        return redirect()
            ->route('login')
            ->with('login_error', true)
            ->withErrors(['error' => __('Login error occurred')]);
    }

    public function downloadUserPhotoIfNotExists(User $user, $url = null)
    {
        if (empty($user->avatar)) {
            if (!empty($url)) {
                try {
                    $user->replaceAvatar($url);
                } catch (ImagickException $exception) {

                }
            }
        }
    }

    public function createSocialAccount($provider, $providerUser, $user, $is_new_user = false)
    {
        // если нет, то извлекаем почтовый ящик

        $userSocialAccount = new UserSocialAccount([
            'provider_user_id' => $providerUser->getId(),
            'provider' => $provider,
            'access_token' => $providerUser->token,
            'parameters' => $providerUser
        ]);

        try {
            $user->social_accounts()
                ->save($userSocialAccount);
        } catch (QueryException $exception) {
            if ($exception->getCode() == 23505) {
                if (DB::transactionLevel() > 1)
                    DB::rollback();

                Log::warning($exception);

                return redirect()
                    ->route('login')
                    ->withErrors(['error' => __('An error occurred when creating an account. Please try again')]);
            } else {
                throw $exception;
            }
        }

        $this->downloadUserPhotoIfNotExists($user, $providerUser->getAvatar());

        auth()->login($user, true);

        if ($is_new_user)
            event(new Registered($user));

        if ($is_new_user)
            return redirect()->route('users.show', compact('user'));
        else
            return redirect()->route('users.show', compact('user'));
    }

    public function unbind(User $user, $id)
    {
        $account = $user->social_accounts()->findOrFail($id);

        $this->authorize('unbind', $account);

        $account->delete();

        return back()
            ->with(['success' => __('Social network account :provider successfully unlinked', ['provider' => $account->provider])]);
    }
}
