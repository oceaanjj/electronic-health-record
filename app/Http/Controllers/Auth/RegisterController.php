<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{

    public function checkUsername(Request $request)
    {
        $username = $request->get('username');
        $exists = User::where('username', $username)->exists();
        return response()->json(['available' => !$exists]);
    }

    public function checkEmail(Request $request)
    {
        $email = $request->get('email');
        $exists = User::where('email', $email)->exists();
        return response()->json(['available' => !$exists]);
    }


    public function showRegistrationForm()
    {
        return view('admin.register', ['roles' => ['doctor', 'nurse']]);
    }

    public function register(Request $request)
    {
        //  Validation Rules
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            // Regex explanation:
            // ^: start of string
            // (?=.*[a-z]): must contain at least one lowercase letter
            // (?=.*[A-Z]): must contain at least one uppercase letter
            // (?=.*\d): must contain at least one digit (number)
            // (?=.*[@$!%*?&]): must contain at least one special character from the list
            // [A-Za-z\d@$!%*?&]{8,}: must be at least 8 characters long (including all required types)
            // $: end of string
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\x\W]).*$/'
            ],
            'role' => 'required|in:doctor,nurse',
        ], [
            //  Custom error messages
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password and Confirm Password do not match.',
            'password.regex' => 'Password must contain at least one uppercase letter, one number, and one special character.',
            'role.required' => 'Please select a role.',
        ]);

        // ✅ Create user
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin-home')->with('success', 'User successfully registered!');

    }
}