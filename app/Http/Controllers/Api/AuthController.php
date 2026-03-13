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
            'full_name' => $user->username,
            'email' => $user->email,
            'user_id' => $user->id,
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
}
