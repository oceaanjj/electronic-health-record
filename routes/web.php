<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/nurse-login', function () {
    return view('nurse-login');
});

Route::get('/doctor-login', function(){
    return view('doctor-login');
});

Route::get('/admin-login', function(){
    return view('admin-login');
});

Route::get("/patient-registration", function(){
    return view('patient-registration');
});


