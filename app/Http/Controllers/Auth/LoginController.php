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
        return view('home');
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


    // app/Http/Controllers/Auth/LoginController.php

    // ... other methods

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

                switch ($user->role) {
                    case 'Nurse': // Correct case
                        return redirect()->route('nurse-home')->with('success', 'Nurse ' . $user->username . ' login successful!');
                    case 'Doctor': // Correct case
                        return redirect()->route('doctor-home')->with('success', 'Doctor ' . $user->username . ' login successful!');
                    case 'Admin': // Correct case
                        return redirect()->route('admin-home')->with('success', 'Admin ' . $user->username . ' login successful!');
                    default:
                        // Fallback for any other role
                        return redirect()->route('home')->with('success', $user->username . ' FAILED!');
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
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout successful!');
    }
}
