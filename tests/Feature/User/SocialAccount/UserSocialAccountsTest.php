<?php

namespace Tests\Feature\User\SocialAccount;

use App\Http\Controllers\UserSocialAccountController;
use App\User;
use App\UserSocialAccount;
use Carbon\Carbon;
use ErrorException;
use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class UserSocialAccountsTest extends TestCase
{
    public $imageUrl = 'http://dev.sites-reviews.com/android-chrome-192x192.png';

    /**
     * Mock the Socialite Factory, so we can hijack the OAuth Request.
     * @param string $email
     * @param string $token
     * @param int $id
     */
    public function mockSocialiteFacade($email = null, $token = 'foo', $provider_name = 'google', $provider_user_id = 1, $avatar_url = '/img/nocover4.jpeg')
    {
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('redirect')->andReturn('Redirected');
        $providerName = class_basename($provider);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->token = $token;

        if (is_null($email)) {
            $abstractUser->shouldReceive('getId')
                ->andReturn($provider_user_id)
                ->shouldReceive('getName')
                ->andReturn('Laztopaz')
                ->shouldReceive('getAvatar')
                ->andReturn($avatar_url);
        } else {
            $abstractUser->shouldReceive('getId')
                ->andReturn($provider_user_id)
                ->shouldReceive('getEmail')
                ->andReturn($email)
                ->shouldReceive('getName')
                ->andReturn('Laztopaz')
                ->shouldReceive('getAvatar')
                ->andReturn($avatar_url);
        }

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with($provider_name)->andReturn($provider);
    }

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testIfNotRegistered()
	{
		Event::fake(Registered::class);

		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;

        $user = factory(User::class)
            ->make();

        $token = $this->faker->linuxPlatformToken;

		$this->mockSocialiteFacade($user->email, $token, 'facebook', $provider_user_id, $this->imageUrl);

		$response = $this->get(route('social_accounts.callback', ['provider' => 'facebook']))
			->assertRedirect()
            ->assertSessionHasNoErrors();

		$user = User::whereEmail($user->email)->first();

		$this->assertNotNull($user);

		$social_account = $user->social_accounts()->first();

		$this->assertNotNull($user);
		$this->assertNotNull($social_account);
		$this->assertEquals($provider_user_id, $social_account->provider_user_id);
		$this->assertEquals($token, $social_account->access_token);
		$this->assertNotNull($user->avatar()->first());

        $response->assertRedirect(route('users.show', $user));

		$this->assertAuthenticatedAs($user);

		Event::assertDispatched(Registered::class, 1);
	}

	public function testIfEmailFromProviderEmpty()
	{
		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;

		$this->mockSocialiteFacade('', $token, 'facebook', $provider_user_id);

		$this->get(route('social_accounts.callback', ['provider' => 'facebook']))
			->assertRedirect()
            ->assertSessionHasErrors()
			->assertSessionHas('email_not_found', true);
	}

	public function testIfRegistered()
	{
        Event::fake(Registered::class);

		$user = factory(User::class)
			->create();

		$social_account = factory(UserSocialAccount::class)
			->create(['user_id' => $user->id]);

		$this->mockSocialiteFacade('',
			$social_account->access_token,
			$social_account->provider,
			$social_account->provider_user_id);

		$response = $this->get(route('social_accounts.callback', ['provider' => $social_account->provider]))
			->assertRedirect(route('users.show', $user->id));

		$this->assertAuthenticatedAs($user);

        Event::assertDispatched(Registered::class, 0);
	}

	public function testIfRegisteredTokenMismatch()
	{
        Event::fake(Registered::class);

		$user = factory(User::class)
			->create();

		$social_account = factory(UserSocialAccount::class)
			->create(['user_id' => $user->id]);

		$this->mockSocialiteFacade('',
			$this->faker->linuxPlatformToken,
			$social_account->provider,
			$social_account->provider_user_id);

		$response = $this->get(route('social_accounts.callback', ['provider' => $social_account->provider]))
			->assertRedirect(route('users.show', $user->id));

		$this->assertAuthenticatedAs($user);

        Event::assertDispatched(Registered::class, 0);
	}

	public function testIfAuthTryAttach()
	{
        Event::fake(Registered::class);

		$provider_user_id = $this->faker->uuid;

		$user = factory(User::class)
			->create();

		$this->mockSocialiteFacade('',
			$this->faker->linuxPlatformToken,
			'google',
			$provider_user_id,
			$this->imageUrl);

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('social_accounts.callback', ['provider' => 'google']))
			->assertOk()
			->assertSeeText(__('Social network account :provider linked successfully', ['provider' => 'google']));

		$this->assertTrue(url()->current() === route('users.social_accounts.index', $user->id));

		$this->assertAuthenticatedAs($user);

		$this->assertNotNull($user->avatar()->first());

        Event::assertDispatched(Registered::class, 0);
	}

	public function testIfAuthTryAttachIfAvatarNotFound()
	{
        Event::fake(Registered::class);

		$provider_user_id = $this->faker->uuid;

		$user = factory(User::class)
			->create();

		$this->mockSocialiteFacade('',
			$this->faker->linuxPlatformToken,
			'google',
			$provider_user_id,
			'http://test.test/test.test');

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('social_accounts.callback', ['provider' => 'google']))
			->assertOk()
			->assertSeeText(__('Social network account :provider linked successfully', ['provider' => 'google']));

		$this->assertTrue(url()->current() === route('users.social_accounts.index', $user->id));

		$this->assertAuthenticatedAs($user);

		$this->assertNull($user->avatar()->first());

        Event::assertDispatched(Registered::class, 0);
	}

	public function testIfNotRegisteredCantDownloadAvatar()
	{
        Event::fake(Registered::class);

        $provider_user_id = $this->faker->uuid;
        $token = $this->faker->linuxPlatformToken;

        $user = factory(User::class)
            ->make();

        $token = $this->faker->linuxPlatformToken;

		$this->mockSocialiteFacade($user->email, $token, 'facebook',
			$provider_user_id, 'http://test.test/test.test');

		$this->get(route('social_accounts.callback', ['provider' => 'facebook']))
			->assertRedirect();

        $user = User::whereEmail($user->email)->first();

        $this->assertNotNull($user);

        $social_account = $user->social_accounts()->first();

		$this->assertNotNull($user);
		$this->assertNotNull($social_account);
		$this->assertEquals($provider_user_id, $social_account->provider_user_id);
		$this->assertEquals($token, $social_account->access_token);
		$this->assertNull($user->avatar()->first());

		$this->assertAuthenticatedAs($user);

        Event::assertDispatched(Registered::class, 1);
	}

	public function testIfUserNotFound()
	{
        Event::fake(Registered::class);

        $user = factory(User::class)
			->create();

		$social_account = factory(UserSocialAccount::class)
			->create(['user_id' => $user->id]);

		$user->forceDelete();

		$this->mockSocialiteFacade('',
			$social_account->access_token,
			$social_account->provider,
			$social_account->provider_user_id);

		$response = $this->get(route('social_accounts.callback', ['provider' => $social_account->provider]))
			->assertRedirect()
            ->assertSessionHas('user_not_found', true)
            ->assertSessionHasErrorsIn('login');

        Event::assertDispatched(Registered::class, 0);
	}

	public function testGoogleUndefinedIndexEmails()
	{
		$user = factory(User::class)
			->create();

		$social_account = factory(UserSocialAccount::class)
			->create(['user_id' => $user->id]);

		$this->mockSocialiteFacade('',
			$social_account->access_token,
			$social_account->provider,
			$social_account->provider_user_id);

		$response = $this->get(route('social_accounts.callback', ['provider' => $social_account->provider]))
			->assertRedirect(route('users.show', $user->id));

		$this->assertAuthenticatedAs($user);
	}

	public function testDontChangeAvatarIfAlreadyExists()
	{
		$provider_user_id = $this->faker->uuid;

		$user = factory(User::class)
			->states('with_avatar')
			->create();

		$avatar = $user->avatar()->first();

		$this->assertNotNull($avatar);

		$this->mockSocialiteFacade('',
			$this->faker->linuxPlatformToken,
			'google',
			$provider_user_id,
            $this->imageUrl);

		$response = $this->followingRedirects()
			->actingAs($user)
			->get(route('social_accounts.callback', ['provider' => 'google']))
			->assertOk()
			->assertSeeText(__('Social network account :provider linked successfully', ['provider' => 'google']));

		$this->assertTrue(url()->current() === route('users.social_accounts.index', $user->id));

		$this->assertAuthenticatedAs($user);

		$this->assertEquals($avatar, $user->avatar()->first());
	}

	public function testFacebook400BadRequestError()
	{
		$json = <<<EOT
{
  "error": "invalid_request",
  "error_description": "Missing required parameter: code"
}
EOT;

		$request = new Request('POST', 'https://accounts.google.com/o/oauth2/token');
		$response = new Response(400, ['header' => 'value'], $json, '1.1', 'Bad Request');

		$exception = RequestException::create($request, $response);

		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('user')
			->andThrow($exception, 'message');

		Socialite::shouldReceive('driver')->with('facebook')->andReturn($provider);

        $this->get(route('social_accounts.callback', ['provider' => 'facebook']))
            ->assertRedirect()
            ->assertSessionHasErrors(['error' => __('Login error occurred')]);
	}

	public function testVkontakte401UnauthorizedError()
	{
		$json = <<<EOT
{"error":"invalid_grant","error_description":"Code is invalid or expired."}
EOT;

		$request = new Request('POST', 'https://oauth.vk.com/access_token');
		$response = new Response(401, ['header' => 'value'], $json, '1.1', 'Unauthorized');

		$exception = RequestException::create($request, $response);

		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

		$provider->shouldReceive('user')
			->andThrow($exception, 'message');

		Socialite::shouldReceive('driver')
            ->with('vkontakte')
            ->andReturn($provider);

        $this->get(route('social_accounts.callback', ['provider' => 'vkontakte']))
            ->assertRedirect()
            ->assertSessionHas('login_error', true)
            ->assertSessionHasErrors();
	}

	public function testGoogle400BadRequestError()
	{
		$json = <<<EOT
{
  "error": "invalid_request",
  "error_description": "Missing required parameter: code"
}
EOT;

		$request = new Request('POST', 'https://accounts.google.com/o/oauth2/token');
		$response = new Response(400, ['header' => 'value'], $json, '1.1', 'Bad Request');

		$exception = RequestException::create($request, $response);

		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('user')
			->andThrow($exception, 'message');

		Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('social_accounts.callback', ['provider' => 'google']))
            ->assertRedirect()
            ->assertSessionHas('login_error', true)
            ->assertSessionHasErrors();
	}

	public function testUndefinedIndexEmails()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('user')
			->andThrow(Exception::class, 'Undefined index: emails');

		Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);

		$this->get(route('social_accounts.callback', ['provider' => 'google']))
			->assertRedirect()
			->assertSessionHas('email_not_found', true);
	}

	public function testOtherException()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

		$provider->shouldReceive('user')
			->andThrow(Exception::class, 'test');

		Socialite::shouldReceive('driver')
			->with('google')
			->andReturn($provider);

		$this->get(route('social_accounts.callback', ['provider' => 'google']))
			->assertRedirect()
			->assertSessionHas('login_error', true);
	}

	public function testUndefinedIndexDisplayNameException()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

		$provider->shouldReceive('user')
			->andThrow(ErrorException::class, 'Undefined index: displayName');

		Socialite::shouldReceive('driver')
			->with('google')
			->andReturn($provider);

		$this->get(route('social_accounts.callback', ['provider' => 'google']))
			->assertRedirect()
			->assertSessionHas('google_did_not_send_username', true);
	}

	public function testInvalidJSONResponseFromVK()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

		$provider->shouldReceive('user')
			->andThrow(RuntimeException::class, 'Invalid JSON response from VK: {"error":{"error_code":5,"error_msg":"User authorization failed: user revoke access for this token.","request_params":[{"key":"fields","value":"id,email,first_name,last_name,screen_name,photo"},{"key":"language","value":"en"},{"key":"v","value":"5.78"},{"key":"method","value":"users.get"},{"key":"oauth","value":"1"}]}}');

		Socialite::shouldReceive('driver')
			->with('vkontakte')
			->andReturn($provider);

		$this->get(route('social_accounts.callback', ['provider' => 'vkontakte']))
            ->assertRedirect()
            ->assertSessionHasErrors(['error' => __('User authorization failed: user revoke access for this token.')]);
	}

	public function testInvalidJSONResponseFromVKWithoutError()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

		$provider->shouldReceive('user')
			->andThrow(RuntimeException::class, 'Invalid JSON response from VK: {"test":{"test": ""}}');

		Socialite::shouldReceive('driver')
			->with('vkontakte')
			->andReturn($provider);

        $this->get(route('social_accounts.callback', ['provider' => 'vkontakte']))
            ->assertRedirect()
            ->assertSessionHasErrors(['error' => __('Login error occurred')]);
	}

	public function testProviderNotFound()
	{
		$this->get(route('social_accounts.callback', ['provider' => uniqid(), 'locale' => 'en']))
			->assertNotFound();
	}

	public function testProviders()
	{
		$this->get(route('social_accounts.callback', ['provider' => 'google', 'locale' => 'en']))
			->assertRedirect();

		$this->get(route('social_accounts.callback', ['provider' => 'facebook', 'locale' => 'en']))
			->assertRedirect();

		$this->get(route('social_accounts.callback', ['provider' => 'vkontakte', 'locale' => 'en']))
			->assertRedirect();
	}

    public function testIfSameEmailExists()
    {
        Event::fake(Registered::class);

        $user = factory(User::class)
            ->create();

        $social_account = factory(UserSocialAccount::class)
            ->make(['user_id' => $user->id]);

        $this->mockSocialiteFacade($user->email,
            $social_account->access_token,
            $social_account->provider,
            $social_account->provider_user_id);

        $response = $this->get(route('social_accounts.callback', ['provider' => $social_account->provider]))
            ->assertRedirect(route('users.show', $user->id));

        $this->assertAuthenticatedAs($user);

        Event::assertDispatched(Registered::class, 0);
    }
}
