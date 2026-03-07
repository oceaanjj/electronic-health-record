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
        $loginInput = $request->query('email') ?? $request->input('email');
        $password = $request->query('password') ?? $request->input('password');

        if (!$loginInput || !$password) {
            return response()->json(['detail' => 'Login and password are required'], 400);
        }

        $user = User::where('email', $loginInput)->orWhere('username', $loginInput)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['detail' => 'Invalid credentials.'], 401);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'role' => $user->role,
            'full_name' => $user->username,
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
