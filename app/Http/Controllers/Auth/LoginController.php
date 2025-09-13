<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showRoleSelectionForm()
    {
        return view('login.index');
    }

    public function showNurseLoginForm()
    {
        return view('login.nurse-login');
    }

    public function showDoctorLoginForm()
    {
        return view('login.doctor-login');
    }

    public function showAdminLoginForm()
    {
        return view('login.admin-login');
    }


    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            switch (strtolower($user->role)) {
                case 'admin':
                    return redirect()->route('home')->with('success', 'Admin login successful!');
                case 'doctor':
                    return redirect()->route('home')->with('success', 'Doctor login successful!');
                case 'nurse':
                    return redirect()->route('home')->with('success', 'Nurse login successful!');
                default:
                    Auth::logout();
                    return redirect()->route('login.index')->withErrors(['name' => 'Unauthorized role.']);
            }
        }

        return back()->withErrors([
            'name' => 'Invalid login details.',
        ])->onlyInput('name');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.index');
    }
}