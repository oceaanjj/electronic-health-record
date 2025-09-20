<?php
use App\Http\Controllers\MedicalController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PhysicalExamController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


// Home Page and Authentication Routes
Route::view('/', 'home')->name('home');

// This is the dedicated route for the 'auth' middleware to redirect to.
Route::get('/login', [LoginController::class, 'showRoleSelectionForm'])->name('login');

Route::prefix('login')->name('login.')->group(function () {
    Route::get('/nurse', [LoginController::class, 'showNurseLoginForm'])->name('nurse');
    Route::get('/doctor', [LoginController::class, 'showDoctorLoginForm'])->name('doctor');
    Route::get('/admin', [LoginController::class, 'showAdminLoginForm'])->name('admin');
    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// The registration routes to put in ADMIN!
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');


// Doctor Home Page
// Now uses 'auth' middleware to handle unauthenticated redirects.
Route::get('/doctor', [HomeController::class, 'doctorHome'])
    ->name('doctor-home')
    ->middleware(['auth', 'can:access-doctor-page']);

// Admin Home Page
// Now uses 'auth' middleware to handle unauthenticated redirects.
Route::get('/admin', [HomeController::class, 'adminHome'])
    ->name('admin-home')
    ->middleware(['auth', 'can:access-admin-page']);


// -----------------------------------------------------------
// Protected Routes for Nurse
// -----------------------------------------------------------
// Each route explicitly defines its middleware to ensure the 'auth' check occurs first.
// If the user is not authenticated, they will be redirected to 'login'.
Route::get('/nurse', [HomeController::class, 'nurseHome'])
    ->name('nurse-home')
    ->middleware(['auth', 'can:is-nurse']);

Route::view('/medical-history', 'medical-history')->name('medical-history')
    ->middleware(['auth', 'can:is-nurse']);
Route::view('/physical-exam', 'physical-exam')->name('physical-exam.index')
    ->middleware(['auth', 'can:is-nurse']);
Route::view('/vital-signs', 'vital-signs')->name('vital-signs')
    ->middleware(['auth', 'can:is-nurse']);
Route::view('/intake-and-output', 'intake-and-output')->name('intake-and-output')
    ->middleware(['auth', 'can:is-nurse']);
Route::view('/act-of-daily-living', 'act-of-daily-living')->name('act-of-daily-living')
    ->middleware(['auth', 'can:is-nurse']);
Route::view('/lab-values', 'lab-values')->name('lab-values')
    ->middleware(['auth', 'can:is-nurse']);
// Route::view('/diagnostics', 'diagnostics')->name('diagnostics');
Route::view('/ivs-and-lines', 'ivs-and-lines')->name('ivs-and-lines')
    ->middleware(['auth', 'can:is-nurse']);
Route::view('/medication-administration', 'medication-administration')->name('medication-administration')
    ->middleware(['auth', 'can:is-nurse']);
Route::view('/medication-reconciliation', 'medication-reconciliation')->name('medication-reconciliation')
    ->middleware(['auth', 'can:is-nurse']);
Route::view('/discharge-planning', 'discharge-planning')->name('discharge-planning')
    ->middleware(['auth', 'can:is-nurse']);

// Patient & Medical Record Routes
Route::prefix('patients')->name('patients.')->group(function () {
    Route::get('/search', fn() => view('patients.search'))->name('search')
        ->middleware(['auth', 'can:is-nurse']);
    Route::get('/search-results', [PatientController::class, 'search'])->name('search-results')
        ->middleware(['auth', 'can:is-nurse']);
});

Route::resource('patients', PatientController::class)
    ->middleware(['auth', 'can:is-nurse']);

Route::prefix('physical-exam')->name('physical-exam.')->group(function () {
    Route::get('/', [PhysicalExamController::class, 'show'])->name('index')
        ->middleware(['auth', 'can:is-nurse']);
    Route::post('/', [PhysicalExamController::class, 'store'])->name('store')
        ->middleware(['auth', 'can:is-nurse']);
    Route::post('/cdss', [PhysicalExamController::class, 'runCdssAnalysis'])->name('runCdssAnalysis')
        ->middleware(['auth', 'can:is-nurse']);
});

Route::get('/medical-history', [MedicalController::class, 'show'])->name('medical-history')
    ->middleware(['auth', 'can:is-nurse']);
Route::post('/medical/store', [MedicalController::class, 'store'])->name('medical.store')
    ->middleware(['auth', 'can:is-nurse']);
Route::post('/present-illness', [MedicalController::class, 'storePresentIllness'])->name('present.store')
    ->middleware(['auth', 'can:is-nurse']);
Route::post('/past-medical', [MedicalController::class, 'storePastMedicalSurgical'])->name('past.store')
    ->middleware(['auth', 'can:is-nurse']);
Route::post('/allergies', [MedicalController::class, 'storeAllergies'])->name('allergy.store')
    ->middleware(['auth', 'can:is-nurse']);
Route::post('/vaccination', [MedicalController::class, 'storeVaccination'])->name('vaccination.store')
    ->middleware(['auth', 'can:is-nurse']);
Route::post('/developmental', [MedicalController::class, 'storeDevelopmentalHistory'])->name('developmental.store')
    ->middleware(['auth', 'can:is-nurse']);
