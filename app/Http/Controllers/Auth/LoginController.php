<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\AuditLogController; // Import the AuditLogController

class LoginController extends Controller
{
    /**
     * Redirects authenticated users to their home dashboard.
     * This is called by the route and ensures a logged-in user never sees the login page.
     */
    protected function redirectToRoleBasedDashboard()
    {
        if (Auth::check()) {
            $user = Auth::user();

            switch ($user->role) {
                case 'Nurse':
                    return redirect()->route('nurse-home');
                case 'Doctor':
                    return redirect()->route('doctor-home');
                case 'Admin':
                    return redirect()->route('admin-home');
            }
        }
        return null;
    }

    public function showLoginForm()
    {
        if ($response = $this->redirectToRoleBasedDashboard()) {
            return $response;
        }

        // views/login/login.blade.php
        return view('login.login');
    }


    //OLD: WITH ROLE SELECTION FORM (HOMEPAGE VBEFORE)
    // public function showRoleSelectionForm()
    // {
    //     if ($response = $this->redirectToRoleBasedDashboard()) {
    //         return $response;
    //     }
    //     return view('home');
    // }

    // public function showNurseLoginForm()
    // {
    //     if ($response = $this->redirectToRoleBasedDashboard()) {
    //         return $response;
    //     }
    //     return view('login.nurse-login');
    // }

    // public function showDoctorLoginForm()
    // {
    //     if ($response = $this->redirectToRoleBasedDashboard()) {
    //         return $response;
    //     }
    //     return view('login.doctor-login');
    // }

    // public function showAdminLoginForm()
    // {
    //     if ($response = $this->redirectToRoleBasedDashboard()) {
    //         return $response;
    //     }
    //     return view('login.admin-login');
    // }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();

            AuditLogController::log('Login Successful', 'User logged in to the system.', [
                'user_role' => $user->role,
            ]);

            // Redirect based on actual role (Admins always go to admin-home)
            switch ($user->role) {
                case 'Nurse':
                    return redirect()->route('nurse-home')->with('sweetalert', [
                        'type' => 'success',
                        'title' => 'Welcome Nurse!',
                        'text' => 'Login successful.',
                        'timer' => 2000
                    ]);
                case 'Doctor':
                    return redirect()->route('doctor-home')->with('sweetalert', [
                        'type' => 'success',
                        'title' => 'Welcome Doctor!',
                        'text' => 'Login successful.',
                        'timer' => 2000
                    ]);
                case 'Admin':
                    return redirect()->route('admin-home')->with('sweetalert', [
                        'type' => 'success',
                        'title' => 'Welcome Admin!',
                        'text' => 'Login successful.',
                        'timer' => 2000
                    ]);
                default:
                    Auth::logout();
                    return back()->with('sweetalert', [
                        'type' => 'error',
                        'title' => 'Access Denied',
                        'text' => 'You do not have permission to access this account.',
                        'timer' => 2500
                    ])->onlyInput('username');
            }
        }

        return back()->with('sweetalert', [
            'type' => 'error',
            'title' => 'Login Failed',
            'text' => 'Invalid login details.',
            'timer' => 2500
        ])->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        // Log the logout action before logging out
        if (Auth::check()) {
            AuditLogController::log('Logout', 'User logged out of the system.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('sweetalert', [
            'type' => 'success',
            'title' => 'Logged Out',
            'text' => 'You have been logged out successfully.',
            'timer' => 2000
        ]);
    }
}
