<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
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