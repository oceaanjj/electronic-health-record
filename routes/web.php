<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\LabValuesController;
use App\Http\Controllers\MedicalController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PhysicalExamController;
use App\Http\Controllers\ActOfDailyLivingController;



// Home Page and Authentication Routes
Route::get('/', [HomeController::class, 'handleHomeRedirect'])->name('home');

Route::get('/login', [LoginController::class, 'showRoleSelectionForm'])->name('login');

Route::prefix('login')->name('login.')->group(function () {
    Route::get('/nurse', [LoginController::class, 'showNurseLoginForm'])->name('nurse');
    Route::get('/doctor', [LoginController::class, 'showDoctorLoginForm'])->name('doctor');
    Route::get('/admin', [LoginController::class, 'showAdminLoginForm'])->name('admin');
    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


//-------------------------------------------------------------
// Protected Routes for Admin
//-------------------------------------------------------------
Route::middleware(['auth', 'can:is-admin'])->group(function () {
    Route::get('/admin', [HomeController::class, 'adminHome'])->name('admin-home');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');
});


//-------------------------------------------------------------
// Protected Routes for Doctor
//-------------------------------------------------------------
Route::get('/doctor', [HomeController::class, 'doctorHome'])
    ->name('doctor-home')
    ->middleware(['auth', 'can:is-doctor']);


//-------------------------------------------------------------
// Protected Routes for Nurse
//-------------------------------------------------------------
Route::middleware(['auth', 'can:is-nurse'])->group(function () {
    Route::get('/nurse', [HomeController::class, 'nurseHome'])->name('nurse-home');

    // Array of simple nurse-accessible views to avoid repetition.
    $nurseViews = [
        'medical-history',
        'physical-exam' => 'physical-exam.index',
        'vital-signs',
        'intake-and-output',
        'act-of-daily-living',
        'lab-values',
        'ivs-and-lines',
        'medication-administration',
        'medication-reconciliation',
        'discharge-planning',
    ];

    foreach ($nurseViews as $uri => $name) {
        // If the key is a string (e.g., 'physical-exam'), use it for the URI.
        // Otherwise, use the value (e.g., 'medical-history').
        $routeUri = is_string($uri) ? $uri : $name;
        Route::view("/{$routeUri}", $name)->name($name);
    }

    // Patient & Medical Record Routes
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/search', fn() => view('patients.search'))->name('search');
        Route::get('/search-results', [PatientController::class, 'search'])->name('search-results');
    });

    Route::resource('patients', PatientController::class);

    //



    // physical exam
    Route::prefix('physical-exam')->name('physical-exam.')->group(function () {
        Route::get('/', [PhysicalExamController::class, 'show'])->name('index');
        Route::post('/', [PhysicalExamController::class, 'store'])->name('store');
        Route::post('/cdss', [PhysicalExamController::class, 'runCdssAnalysis'])->name('runCdssAnalysis');
    });




    // Medical History Store Routes
    Route::get('/medical-history', [MedicalController::class, 'show'])->name('medical-history');
    Route::post('/medical/store', [MedicalController::class, 'store'])->name('medical.store');
    Route::post('/present-illness', [MedicalController::class, 'storePresentIllness'])->name('present.store');
    Route::post('/past-medical', [MedicalController::class, 'storePastMedicalSurgical'])->name('past.store');
    Route::post('/allergies', [MedicalController::class, 'storeAllergies'])->name('allergy.store');
    Route::post('/vaccination', [MedicalController::class, 'storeVaccination'])->name('vaccination.store');
    Route::post('/developmental', [MedicalController::class, 'storeDevelopmentalHistory'])->name('developmental.store');

    //Activities of Daily Living (ADL)
    Route::prefix('adl')->name('adl.')->group(function () {
        Route::get('/', [ActOfDailyLivingController::class, 'show'])->name('show');
        Route::post('/', [ActOfDailyLivingController::class, 'store'])->name('store');
        Route::post('/cdss', [ActOfDailyLivingController::class, 'runCdssAnalysis'])->name('runCdssAnalysis');
    });

    // Lab Values Routes
    Route::get('/lab-values', [LabValuesController::class, 'show'])->name('lab-values.index');
    Route::post('/lab-values', [LabValuesController::class, 'store'])->name('lab-values.store');
    Route::post('/lab-values/run-cdss', [LabValuesController::class, 'runCdssAnalysis'])->name('lab-values.cdss');



});
