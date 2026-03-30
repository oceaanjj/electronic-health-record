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

        // Check if user exists
        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => "We couldn't find an account with that email address."])
                ->with('sweetalert', [
                    'type' => 'error',
                    'title' => 'Email Failed',
                    'text' => "We couldn't find an account with that email address. Please verify and try again.",
                    'timer' => 3000
                ]);
        }

        // Generate a 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code in password_reset_tokens table
        // We use the same table but we'll store the code as the token
        // Laravel's default broker hashes the token, so we should too if we want to use the default broker for reset.
        // However, we can also just store it plainly if we want easier verification, but for security, let's hash it.
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => \Hash::make($code),
                'created_at' => now(),
            ]
        );

        // Log the password reset attempt
        AuditLogController::log('Password Reset Requested', "6-digit reset code generated for email: {$request->email}");

        // Reset the attempts counter in session
        session(['reset_attempts_count' => 0]);

        // Send the notification
        $source = $request->input('source', 'web');
        $user->notify(new \App\Notifications\ResetPasswordNotification($code, $source));

        return back()->withInput()->with('status', 'We have emailed your password reset code!')->with('sweetalert', [
            'type' => 'success',
            'title' => 'Code Sent!',
            'text' => "Check your inbox! We've sent a 6-digit reset code to your email.",
            'timer' => 3000
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'User not found.'], 404);
        }

        // Track attempts in session
        $attempts = session('reset_attempts_count', 0) + 1;
        session(['reset_attempts_count' => $attempts]);

        if ($attempts > 4) {
            return response()->json(['valid' => false, 'exceeded' => true, 'message' => 'Maximum attempts reached.']);
        }

        $isValid = \Password::broker()->tokenExists($user, $request->code);

        if ($isValid) {
            // Reset attempts on success
            session()->forget('reset_attempts_count');
        }

        return response()->json(['valid' => $isValid, 'attempts' => $attempts]);
    }
}
