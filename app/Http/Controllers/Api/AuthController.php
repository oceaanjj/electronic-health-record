<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Str;

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
            return response()->json(['error' => "We couldn't find an account with that email address."], 404);
        }

        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));

        // Store OTP in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now(),
            ]
        );

        AuditLogController::log('Password Reset Requested', "Password reset OTP requested via API for email: {$request->email}");

        // Send Notification
        $user->notify(new ResetPasswordNotification($otp, 'mobile'));

        return response()->json(['message' => 'Verification code sent to your email.']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetToken) {
            return response()->json(['error' => 'Invalid request.'], 400);
        }

        if (Carbon::parse($resetToken->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['error' => 'Verification code has expired.'], 400);
        }

        if (!Hash::check($request->code, $resetToken->token)) {
            return response()->json(['error' => 'Invalid verification code.'], 400);
        }

        return response()->json(['message' => 'Code verified successfully.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\x\W]).*$/'
            ],
        ], [
            'password.regex' => 'For better security, your password must include at least one uppercase letter, one number, and one special character.',
            'code.digits' => 'The verification code must be 6 digits.',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetToken) {
            return response()->json(['error' => 'Invalid reset request.'], 400);
        }

        if (Carbon::parse($resetToken->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['error' => 'Verification code has expired.'], 400);
        }

        if (!Hash::check($request->code, $resetToken->token)) {
            return response()->json(['error' => 'Invalid verification code.'], 400);
        }

        // Success - Reset password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Log success
        AuditLogController::log('Password Reset Successful', "User {$user->username} successfully reset their password via API.", ['user_id' => $user->id]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
