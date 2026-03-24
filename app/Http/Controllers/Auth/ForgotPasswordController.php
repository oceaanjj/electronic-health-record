<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\AuditLogController;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Log the password reset attempt
        AuditLogController::log('Password Reset Requested', "Password reset link requested for email: {$request->email}");

        // We will send the password reset link to this user. Once it has been sent
        // we will examine the response then see the message we need to show to the user.
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? redirect()->route('password.request')->with('status', trans($status))->with('sweetalert', [
                'type' => 'success',
                'title' => 'Link Sent!',
                'text' => trans($status),
                'timer' => 3000
            ])
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => trans($status)])
                ->with('sweetalert', [
                    'type' => 'error',
                    'title' => 'Failed',
                    'text' => trans($status),
                    'timer' => 3000
                ]);
    }
}
