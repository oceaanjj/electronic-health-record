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
        return view('role');
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

    public function authenticateNurse(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (strtolower($user->role) !== 'nurse') {
                Auth::logout();
                return back()->withErrors(['name' => 'Access denied. Only nurses can log in here.']);
            }

            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Nurse login successful!');
        }

        return back()->withErrors([
            'name' => 'Invalid login details.',
        ])->onlyInput('name');
    }

    public function authenticateDoctor(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (strtolower($user->role) !== 'doctor') {
                Auth::logout();
                return back()->withErrors(['name' => 'Access denied. Only doctors can log in here.']);
            }

            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Doctor login successful!');
        }

        return back()->withErrors([
            'name' => 'Invalid login details.',
        ])->onlyInput('name');
    }

    public function authenticateAdmin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (strtolower($user->role) !== 'admin') {
                Auth::logout();
                return back()->withErrors(['name' => 'Access denied. Only admins can log in here.']);
            }

            $request->session()->regenerate();
            return redirect()->route('home')->with('success', 'Admin login successful!');
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