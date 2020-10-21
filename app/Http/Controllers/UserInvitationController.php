<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitation;
use App\Http\Requests\StoreUser;
use App\Notifications\InvitationNotification;
use App\User;
use App\UserInvitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class UserInvitationController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.invitation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvitation $request)
    {
        if ($response = $this->checkEmailExists($request, $request->email))
            return $response;

        $invitation = new UserInvitation();
        $invitation->fill($request->all());
        $invitation->save();

        Notification::route('mail', $invitation->email)
            ->notify(new InvitationNotification($invitation));

        return redirect()
            ->route('users.invitation.create')
            ->with([
                'invitation_was_sent' => true,
                'email' => $invitation->email
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function createUser(Request $request, string $token)
    {
        $invitation = UserInvitation::whereToken($token)->first();

        if ($response = $this->checkInvitation($request, $invitation))
            return $response;

        return view('user.invitation.user.create', ['invitation' => $invitation]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUser $request
     * @return \Illuminate\Http\Response
     */
    public function storeUser(StoreUser $request)
    {
        $invitation = UserInvitation::whereToken($request->token)->first();

        if ($response = $this->checkInvitation($request, $invitation))
            return $response;

        $user = new User();
        $user->email = $invitation->email;
        $user->email_verified_at = now();
        $user->fill($request->all());
        $user->save();

        $invitation->used();
        $invitation->user_id = $user->id;
        $invitation->save();

        Auth::login($user, true);

        event(new Registered($user));

        return redirect()
            ->route('users.show', $user)
            ->with(['success' => __('You have successfully registered')]);
    }

    public function checkInvitation($request, $invitation)
    {
        if (empty($invitation))
            return redirect()
                ->route('users.invitation.create')
                ->withInput($request->all())
                ->withErrors(['error' => __('Invitation not found')]);

        if ($invitation->isUsed())
            return redirect()
                ->route('users.invitation.create')
                ->withInput($request->all())
                ->withErrors(['error' => __('Registration has already taken place at this link')]);

        if ($response = $this->checkEmailExists($request, $invitation->email))
            return $response;
    }

    public function checkEmailExists($request, $email)
    {
        if (User::where('email', $email)->count() > 0)
            return redirect()
                ->route('login')
                ->withInput($request->all())
                ->withErrors(['error' => __('The user with this email address is already registered with the mailbox.').' '.
                    __('Please log in or restore your password')]);
    }
}
