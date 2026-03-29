<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\x\W]).*$/'
            ],
        ], [
            'password.regex' => 'For better security, your password must include at least one uppercase letter, one number, and one special character.',
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the reset page with a success status so we can show the "Proceed to Login" button.
        return $status === Password::PASSWORD_RESET
            ? back()->with('status', trans($status))->with('sweetalert', [
                'type' => 'success',
                'title' => 'Password Updated!',
                'text' => 'Your password has been successfully reset. You can now sign in with your new credentials.',
                'timer' => 3000
            ])
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => trans($status)])
                ->with('sweetalert', [
                    'type' => 'error',
                    'title' => 'Reset Failed',
                    'text' => 'Unable to reset password. The link may have expired or is no longer valid.',
                    'timer' => 3000
                ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \App\Models\User  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        AuditLogController::log('Password Reset Successful', "User {$user->username} successfully reset their password.", ['user_id' => $user->id]);

        // Removed: Auth::guard()->login($user);
    }
}
