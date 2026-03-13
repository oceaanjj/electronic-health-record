<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;

// Home Page — redirect based on role
Route::get('/', [HomeController::class, 'handleHomeRedirect'])->name('home');

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Role-specific routes
require __DIR__.'/admin.php';
require __DIR__.'/doctor.php';
require __DIR__.'/nurse.php';