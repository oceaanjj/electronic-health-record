<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\RegisterController;

//-------------------------------------------------------------
// Protected Routes for Admin
//-------------------------------------------------------------
Route::middleware(['auth', 'can:is-admin'])->group(function () {
    Route::get('/admin', [HomeController::class, 'adminHome'])->name('admin-home');

    Route::get('/users', [UserController::class, 'index'])->name('users');

    // Register users (doctor/nurse only)
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');

    Route::get('/check-username', [RegisterController::class, 'checkUsername'])->name('check.username');
    Route::get('/check-email', [RegisterController::class, 'checkEmail'])->name('check.email');

    // View Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');

    // Change user roles
    Route::patch('/users/{id}/role', [UserController::class, 'updateRole'])->name('users.role.update');
});
