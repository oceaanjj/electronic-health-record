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
Route::post('/auth/login',  [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ── ROLE-SPECIFIC ─────────────────────────────────────────────────────────
require __DIR__.'/api/nurse.php';
require __DIR__.'/api/doctor.php';
require __DIR__.'/api/admin.php';