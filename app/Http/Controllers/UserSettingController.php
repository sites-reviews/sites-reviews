<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvatar;
use App\Http\Requests\UpdateUser;
use App\Http\Requests\UpdateUserNotificationSetting;
use App\Image;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSettingController extends Controller
{
    /**
     * User settings
     *
     * @param User $user
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function settings(User $user)
    {
        $this->authorize('edit', $user);

        $user->load('avatar');

        return view('user.setting.profile', ['user' => $user]);
    }

    /**
     * User settings
     *
     * @param StoreAvatar $request
     * @param User $user
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function storeAvatar(StoreAvatar $request, User $user)
    {
        $this->authorize('edit', $user);

        $user->replaceAvatar($request->file('avatar')->getRealPath());

        return redirect()
            ->route('users.settings', $user)
            ->with(['success' => __('user.avatar_was_installed_successfully')]);
    }

    /**
     * User notifications settings
     *
     * @param StoreAvatar $request
     * @param User $user
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function notifications(User $user)
    {
        $this->authorize('edit_notification_settings', $user);

        return view('user.setting.notification', ['user' => $user]);
    }

    /**
     * User notifications settings update
     *
     * @param StoreAvatar $request
     * @param User $user
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function notificationsUpdate(UpdateUserNotificationSetting $request, User $user)
    {
        $this->authorize('edit_notification_settings', $user);

        $user->notificationSetting->fill($request->all());
        $user->push();

        return redirect()
            ->route('users.settings.notifications', $user)
            ->with(['success' => __('Notification settings have been changed successfully')]);
    }
}
