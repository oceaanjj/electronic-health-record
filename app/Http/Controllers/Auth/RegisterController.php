<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('admin.register', ['roles' => ['doctor', 'nurse']]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:doctor,nurse',
        ]);

        // Create user
        $user = User::create([
            'username'   => $request->username,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'role'  => $request->role,
        ]);

        // Create audit log
        AuditLog::create([
            'id' => Auth::id(),
            'action'  => 'Registered new user',
            'details' => "User: {$user->username} | Role: {$user->role_name}",
        ]);

        return redirect()->route('admin-home')->with('success', 'User registered successfully!');
    }
}
