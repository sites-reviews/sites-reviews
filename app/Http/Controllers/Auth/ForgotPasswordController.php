<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePasswordReset;
use App\PasswordReset;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    // use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

    public function showLinkRequestForm(Request $request)
    {
        return view('auth.passwords.email', [
            'email' => $request->email
        ]);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function sendResetLinkEmail(StorePasswordReset $request)
    {
        $user = User::where('email', $request->email)->first();

        if (empty($user))
            return redirect()
                ->route('password.request')
                ->withInput($request->all())
                ->withErrors(['error' => __('The user with this mailbox was not found.') . ' ' . __('Check whether you entered your mailbox correctly')]);

        $passwordReset = new PasswordReset();
        $passwordReset->fill($request->all());
        $user->passwordResets()->save($passwordReset);

        return redirect()
            ->route('password.request')
            ->with(['notification_send' => true]);
    }
}
