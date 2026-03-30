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
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate')->middleware('throttle:login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::get('password/email', function() { return redirect()->route('password.request'); });
Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:forgot-password');
Route::post('password/verify', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyCode'])->name('password.verify');
Route::get('password/confirm/{token?}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update')->middleware('throttle:password-reset');

// Role-specific routes (admin, doctor, nurse, etc.)
require __DIR__.'/admin.php';
require __DIR__.'/doctor.php';
require __DIR__.'/nurse.php';