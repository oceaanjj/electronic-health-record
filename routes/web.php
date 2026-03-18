<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;

// Landing Page Route (Main page) — handle role-based redirection for authenticated users
Route::get('/', [HomeController::class, 'handleHomeRedirect'])->name('landing');

// Home Page — redirect based on role (already in your code)
Route::get('/home', [HomeController::class, 'handleHomeRedirect'])->name('home')->middleware('auth'); // Use 'auth' middleware to ensure only authenticated users access this route

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest'); // Only show login if the user is not authenticated
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Role-specific routes (admin, doctor, nurse, etc.)
require __DIR__.'/admin.php';
require __DIR__.'/doctor.php';
require __DIR__.'/nurse.php';