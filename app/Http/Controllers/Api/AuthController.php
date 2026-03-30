<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuditLogController;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $loginInput = $request->input('email') ?? $request->input('username');
        $password = $request->input('password');

        if (!$loginInput || !$password) {
            return response()->json(['detail' => 'Email or username, plus password, are required in the request body.'], 400);
        }

        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['detail' => 'Invalid credentials.'], 401);
        }

        Auth::login($user); // Set current user for AuditLogController
        $token = $user->createToken('mobile-app')->plainTextToken;

        AuditLogController::log(
            'LOGIN SUCCESSFUL',
            "User {$user->username} successfully logged in via API.",
            ['ip_address' => $request->ip()]
        );

        return response()->json([
            'access_token' => $token,
            'role' => strtolower((string) $user->role),
            'user_id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $user->full_name,
            'birthdate' => $user->birthdate,
            'age' => $user->age,
            'sex' => $user->sex,
            'address' => $user->address,
            'birthplace' => $user->birthplace,
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();
            AuditLogController::log(
                'LOGOUT SUCCESSFUL',
                "User {$user->username} successfully logged out via API."
            );
            $user->currentAccessToken()->delete();
        }
        return response()->json(['detail' => 'Logged out successfully']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => "We couldn't find an account with that email address."], 400);
        }

        // Generate a 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code in password_reset_tokens table
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => \Hash::make($code),
                'created_at' => now(),
            ]
        );

        AuditLogController::log('Password Reset Requested', "6-digit reset code generated via API for email: {$request->email}");

        // Send the notification
        $user->notify(new \App\Notifications\ResetPasswordNotification($code, 'mobile'));

        return response()->json(['message' => '6-digit reset code sent to your email.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6', // Using 'token' as the field name for consistency with Laravel expectations
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

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => "User not found."], 400);
        }

        // Verify the 6-digit code
        $record = \DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record || !\Hash::check($request->token, $record->token)) {
            return response()->json(['error' => 'Invalid or expired reset code.'], 400);
        }

        // Check if code is older than 60 minutes
        if (now()->parse($record->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['error' => 'Reset code has expired.'], 400);
        }

        // Update the password
        $user->password = Hash::make($request->password);
        $user->setRememberToken(\Illuminate\Support\Str::random(60));
        $user->save();

        // Delete the token
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        AuditLogController::log('Password Reset Successful', "User {$user->username} reset their password via API.", ['user_id' => $user->id]);

        return response()->json(['message' => 'Password reset successful.']);
    }
}
