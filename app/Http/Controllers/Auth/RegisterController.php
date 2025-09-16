<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        //BRYAN: Keith need din ito baguhin to the front-end routes for registration 
        return view('register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Admin,Doctor,Nurse',
        ]);
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // <-- important
            'role' => $data['role'],
        ]);

        //BRYAN: Keith yung laman ng 'login' need din palitan  since there is different blade file for each login based role but this shit is working
        //bali need sa 'login' is yung login page na ginawa ni Rex
        return redirect()->route('login.index')->with('success', 'User registered successfully!');
    }
}
