<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        $token = $user->createToken('mobile-app')->plainTextToken;

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
            $request->user()->currentAccessToken()->delete();
        }
        return response()->json(['detail' => 'Logged out successfully']);
    }
}
