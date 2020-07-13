<?php

namespace Tests\Feature\User;

use App\Review;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserAvatarTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpload()
    {
        $image = $this->fakeImageStream();

        $user = factory(User::class)
            ->create();

        $file = new UploadedFile($image, 'test.jpeg', null, null, true);

        $this->actingAs($user)
            ->post(route('users.avatar.store', $user), [
                'avatar' => $file
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.settings', $user))
            ->assertSessionHas('success', __('user.avatar_was_installed_successfully'));

        $user->refresh();

        $avatar = $user->avatar;

        $this->assertNotNull($avatar);
        $this->assertTrue($user->is($avatar->create_user));

        $avatarPreview = $user->avatarPreview;

        $this->assertNotNull($avatarPreview);
        $this->assertTrue($user->is($avatarPreview->create_user));
    }

    public function testReplace()
    {
        $user = factory(User::class)
            ->states('with_avatar')
            ->create();

        $avatar = $user->avatar;
        $avatarPreview = $user->avatarPreview;

        $this->assertNotNull($avatar);
        $this->assertNotNull($avatarPreview);

        $fakeImage = $this->fakeImageStream();

        $file = new UploadedFile($fakeImage, 'test.jpeg', null, null, true);

        $this->actingAs($user)
            ->post(route('users.avatar.store', $user), [
                'avatar' => $file
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.settings', $user))
            ->assertSessionHas('success', __('user.avatar_was_installed_successfully'));

        $user->refresh();

        $avatar2 = $user->avatar;
        $avatarPreview2 = $user->avatarPreview;

        $this->assertNotNull($avatar2);
        $this->assertNotNull($avatarPreview2);

        $avatar->refresh();
        $avatarPreview->refresh();

        $this->assertSoftDeleted($avatar);
        $this->assertSoftDeleted($avatarPreview);
    }

    public function testShowIfAvatarExists()
    {
        $user = factory(User::class)
            ->states('with_avatar')
            ->create();

        $this->get(route('users.avatar', $user))
            ->assertOk()
            ->assertViewHas('user', $user)
            ->assertViewHas('avatar', $user->avatar)
            ->assertViewHas('width', 600)
            ->assertViewHas('height', 600)
            ->assertViewHas('quality', 90);
    }

    public function testShowIfAvatarNotExists()
    {
        $user = factory(User::class)
            ->create();

        $this->get(route('users.avatar', $user))
            ->assertRedirect(route('users.show', $user));
    }
}
