<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function handleHomeRedirect()
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
                default:
                    // If the user has an unrecognized role, log them out and redirect to home
                    Auth::logout();
                    return redirect()->route('home');
            }
        }

        // If not logged in, show the default home view
        return view('home');
    }

    public function nurseHome()
    {
        return view('nurse-home');
    }

    public function doctorHome()
    {
        return view('doctor-home');
    }

    public function adminHome()
    {
        return view('admin-home');
    }
}
