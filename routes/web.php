<?php

use App\Http\Controllers\MedicalController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PhysicalExamController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// Home Page
Route::view('/', 'home')->name('home');

// Static Views
$views = [
    'sidebar',
    'role',
    'medical-history',
    'physical-exam',
    'vital-signs',
    'intake-and-output',
    'act-of-daily-living',
    'lab-values',
    'diagnostics',
    'ivs-and-lines',
    'medication-administration',
    'medication-reconciliation',
    'discharge-planning',
];

foreach ($views as $view) {
    Route::view("/{$view}", $view)->name($view);
}

// Authentication Routes
Route::prefix('login')->name('login.')->group(function () {
    Route::get('/', [LoginController::class, 'showRoleSelectionForm'])->name('index');
    Route::get('/nurse', [LoginController::class, 'showNurseLoginForm'])->name('nurse');
    Route::get('/doctor', [LoginController::class, 'showDoctorLoginForm'])->name('doctor');
    Route::get('/admin', [LoginController::class, 'showAdminLoginForm'])->name('admin');
    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
});

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Patients & Physical Exam

Route::prefix('patients')->name('patients.')->group(function () {
    Route::get('/search', function () {
        return view('patients.search');
    })->name('search');
    Route::get('/search-results', [PatientController::class, 'search'])->name('search-results');
});

Route::prefix('physical-exam')->name('physical-exam.')->group(function () {
    Route::get('/', [PhysicalExamController::class, 'show'])->name('index');
    Route::post('/', [PhysicalExamController::class, 'store'])->name('store');
});

Route::resource('patients', PatientController::class);


// More routes:
Route::post('/medical/store', [MedicalController::class, 'store'])->name('medical.store');
Route::post('/present-illness', [MedicalController::class, 'storePresentIllness'])->name('present.store');
Route::post('/past-medical', [MedicalController::class, 'storePastMedicalSurgical'])->name('past.store');
Route::post('/allergies', [MedicalController::class, 'storeAllergies'])->name('allergy.store');
Route::post('/vaccination', [MedicalController::class, 'storeVaccination'])->name('vaccination.store');
Route::post('/developmental', [MedicalController::class, 'storeDevelopmentalHistory'])->name('developmental.store');