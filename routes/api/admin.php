<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AdminApiController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
| Base URL: /api/admin/
| Auth: Bearer token (Sanctum) + admin role
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {

    // ── DASHBOARD ─────────────────────────────────────────────────────────
    // GET /api/admin/stats
    Route::get('stats', [AdminApiController::class, 'stats']);

    // ── USERS ─────────────────────────────────────────────────────────────
    // GET  /api/admin/users?role=nurse&search=
    Route::get('users', [AdminApiController::class, 'users']);

    // GET  /api/admin/users/{id}
    Route::get('users/{id}', [AdminApiController::class, 'showUser']);

    // POST /api/admin/users  — register new user
    Route::post('users', [AdminApiController::class, 'createUser']);

    // PATCH /api/admin/users/{id}/role
    Route::patch('users/{id}/role', [AdminApiController::class, 'updateRole']);

    // ── AUDIT LOGS ───────────────────────────────────────────────────────
    // GET /api/admin/audit-logs?search=&sort=desc&page=1&per_page=20
    Route::get('audit-logs', [AdminApiController::class, 'auditLogs']);
});
