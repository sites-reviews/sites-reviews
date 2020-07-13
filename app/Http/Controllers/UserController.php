<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvatar;
use App\Http\Requests\UpdateUser;
use App\Image;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load('avatar');

        $reviews = $user->reviews()
            ->with('site', 'create_user.avatarPreview', 'authUserRatings')
            ->latest()
            ->simplePaginate();

        return view('user.show', [
            'user' => $user,
            'reviews' => $reviews
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User $user
     * @return \Illuminate\Http\Response
     * @throws AuthorizationException
     */
    public function update(UpdateUser $request, User $user)
    {
        $this->authorize('edit', $user);

        $user->fill($request->all());
        $user->save();

        return redirect()
            ->route('users.settings', $user)
            ->with(['success' => __('user.user_data_was_successfully_saved')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function notificationsDropdown(User $user)
    {
        $this->authorize('see_notifications', $user);

        $unreadNotifications = $user->notifications()->get();

        $user->unreadNotifications->markAsRead();

        return view('user.notification.dropdown', [
            'user' => $user,
            'unreadNotifications' => $unreadNotifications
        ]);
    }

    /**
     *
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function avatarShow(User $user)
    {
        $avatar = $user->avatar;

        if (empty($avatar))
            return redirect()
                ->route('users.show', $user);

        return view('user.avatar', [
            'user' => $user,
            'avatar' => $avatar,
            'width' => 600,
            'height' => 600,
            'quality' => 90
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function notifications(User $user)
    {
        $this->authorize('see_notifications', $user);

        $notifications = $user->notifications()
            ->simplePaginate();

        return view('user.notification.index', [
            'user' => $user,
            'notifications' => $notifications
        ]);
    }
}
