<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewPassword;
use App\PasswordReset;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;

class ResetPasswordController extends Controller
{
    // use Illuminate\Foundation\Auth\ResetsPasswords;

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();

        if ($response = $this->checkPasswordReset($request, $passwordReset))
            return $response;

        return view('auth.passwords.reset', ['token' => $token]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(StoreNewPassword $request)
    {
        $passwordReset = PasswordReset::where('token', $request->token)->first();

        if ($response = $this->checkPasswordReset($request, $passwordReset))
            return $response;

        $user = $passwordReset->user;
        $user->password = $request->password;
        $user->save();

        $passwordReset->used_at = now();
        $passwordReset->save();

        Auth::login($user);

        event(new PasswordResetEvent($user));

        return redirect()
            ->route('users.show', $user)
            ->with(['success' => __('The password change is successful')]);
    }

    public function checkPasswordReset($request, $passwordReset)
    {
        if (empty($passwordReset))
            return redirect()
                ->route('home')
                ->withInput($request->all())
                ->withErrors(['error' => __('The password recovery link is incorrect')]);

        if ($passwordReset->isUsed())
            return redirect()
                ->route('home')
                ->withInput($request->all())
                ->withErrors(['error' => __('The link was already used for password recovery')]);

        if ($passwordReset->created_at->addMinutes(config('auth.passwords.users.expire'))->isPast())
            return redirect()
                ->route('password.request')
                ->withInput($request->all())
                ->withErrors(['error' => __('The link to restore is outdated. You can send a new link.')]);
    }
}
