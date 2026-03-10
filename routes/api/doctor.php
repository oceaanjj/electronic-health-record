<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Doctor\DoctorApiController;

/*
|--------------------------------------------------------------------------
| Doctor API Routes
| Base URL: /api/doctor/
| Auth: Bearer token (Sanctum) + doctor role
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:doctor'])->prefix('doctor')->group(function () {

    // ── DASHBOARD ─────────────────────────────────────────────────────────
    // GET /api/doctor/stats
    Route::get('stats', [DoctorApiController::class, 'stats']);

    // ── FEEDS ────────────────────────────────────────────────────────────
    // GET /api/doctor/recent-forms?type=all&patient=&date=&page=1&per_page=20
    Route::get('recent-forms', [DoctorApiController::class, 'recentForms']);

    // GET /api/doctor/today-updates
    Route::get('today-updates', [DoctorApiController::class, 'todayUpdates']);

    // ── PATIENTS ─────────────────────────────────────────────────────────
    // GET /api/doctor/patients?search=
    Route::get('patients', [DoctorApiController::class, 'allPatients']);

    // GET /api/doctor/patients/active
    Route::get('patients/active', [DoctorApiController::class, 'activePatients']);

    // GET /api/doctor/patient/{id}
    Route::get('patient/{id}', [DoctorApiController::class, 'patientDetails']);

    // ── PATIENT FORM RECORDS ─────────────────────────────────────────────
    // GET /api/doctor/patient/{id}/forms/{type}
    // type: vital-signs | physical-exam | adl | intake-output | lab-values | medication | ivs-lines
    Route::get('patient/{patient_id}/forms/{type}', [DoctorApiController::class, 'patientForms']);
});
