<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
| Base URL: /api/
| Authentication: POST /api/auth/login  →  returns Bearer token
|--------------------------------------------------------------------------
*/

// ── PUBLIC ────────────────────────────────────────────────────────────────
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:forgot-password');
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:password-reset');
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ── ROLE-SPECIFIC ─────────────────────────────────────────────────────────
require __DIR__.'/api/nurse.php';
require __DIR__.'/api/doctor.php';
require __DIR__.'/api/admin.php';