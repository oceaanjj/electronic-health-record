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

    public function showRoleSelectionForm()
    {
        if ($response = $this->redirectToRoleBasedDashboard()) {
            return $response;
        }
        return view('home');
    }

    public function showNurseLoginForm()
    {
        if ($response = $this->redirectToRoleBasedDashboard()) {
            return $response;
        }
        return view('login.nurse-login');
    }

    public function showDoctorLoginForm()
    {
        if ($response = $this->redirectToRoleBasedDashboard()) {
            return $response;
        }
        return view('login.doctor-login');
    }

    public function showAdminLoginForm()
    {
        if ($response = $this->redirectToRoleBasedDashboard()) {
            return $response;
        }
        return view('login.admin-login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);

        $expectedRole = strtolower($request->input('role'));

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (strtolower($user->role) === $expectedRole) {
                $request->session()->regenerate();
                // Log the successful login action with user details
                AuditLogController::log('Login Successful', 'User logged in to the system.', ['user_role' => $user->role]);

                switch ($user->role) {
                    case 'Nurse':
                        return redirect()->route('nurse-home');
                    case 'Doctor':
                        return redirect()->route('doctor-home');
                    case 'Admin':
                        return redirect()->route('admin-home');
                    default:
                        Auth::logout();
                        return redirect()->route('home')->withErrors(['username' => 'Access denied. Unrecognized role.']);
                }
            }

            Auth::logout();
            return back()->withErrors(['username' => 'Access denied. You are not a ' . ucfirst($expectedRole) . '.'])
                ->onlyInput('username');
        }

        return back()->withErrors(['username' => 'Invalid login details.'])->onlyInput('username');
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

        return redirect('/');
    }

}
