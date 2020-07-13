<?php

namespace Tests\Feature\User\Setting;

use App\Review;
use App\Site;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserSettingProfileTest extends TestCase
{
    public function testRouteIsOk()
    {
        $user = factory(User::class)
            ->create();

        $this->actingAs($user)
            ->get(route('users.settings', $user))
            ->assertOk()
            ->assertViewHas('user', $user)
            ->assertSeeText(__('user.avatar'));
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpdate()
    {
        $user = factory(User::class)
            ->create();

        $userNew = factory(User::class)
            ->make();

        $this->actingAs($user)
            ->patch(route('users.update', $user), [
                'name' => $userNew->name,
                'gender' => $userNew->gender
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.settings', $user))
            ->assertSessionHas('success', __('user.user_data_was_successfully_saved'));

        $user->refresh();

        $this->assertEquals($userNew->name, $user->name);
        $this->assertEquals($userNew->gender, $user->gender);
    }
}
