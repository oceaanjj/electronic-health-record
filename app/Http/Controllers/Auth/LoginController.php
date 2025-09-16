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
        return view('login.role');
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
            'username' => ['required', 'string', 'max:255', 'exists:users,username'],
            'password' => ['required'],
        ]);

        $expectedRole = strtolower($request->input('role'));

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (strtolower($user->role) === $expectedRole) {
                $request->session()->regenerate();
                return redirect()->route('home')->with('success', ucfirst($expectedRole) . ' login successful!');
            }

            Auth::logout();
            return back()->withErrors(['username' => "Access denied. Only " . $expectedRole . "s can log in here."])
                ->onlyInput('username');
        }

        return back()->withErrors(['username' => 'Invalid login details.'])->onlyInput('username');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.index');
    }
}