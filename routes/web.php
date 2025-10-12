<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\LabValuesController;
use App\Http\Controllers\MedicalController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PhysicalExamController;
use App\Http\Controllers\ActOfDailyLivingController;
use App\Http\Controllers\IvsAndLineController;
use App\Http\Controllers\MedReconciliationController;
use App\Http\Controllers\DischargePlanningController;
use App\Http\Controllers\VitalSignsController;
use App\Http\Controllers\IntakeAndOutputController;






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
    // Admin Dashboard
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


    // No delete routes!
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
        // 'intake-and-output',
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

    // physical exam
    Route::prefix('physical-exam')->name('physical-exam.')->group(function () {
        Route::get('/', [PhysicalExamController::class, 'show'])->name('index');
        // New route to handle patient selection via POST
        Route::post('/select', [PhysicalExamController::class, 'selectPatient'])->name('select');
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
    Route::post('/medical-history/select', [MedicalController::class, 'selectPatient'])->name('medical-history.select');


    // Lab Values Routes
    Route::get('/lab-values', [LabValuesController::class, 'show'])->name('lab-values.index');
    Route::post('/lab-values/select', [LabValuesController::class, 'selectPatient'])->name('lab-values.select');
    Route::post('/lab-values', [LabValuesController::class, 'store'])->name('lab-values.store');
    Route::post('/lab-values/run-cdss', [LabValuesController::class, 'runCdssAnalysis'])->name('lab-values.cdss');

    // IVS AND LINES:
    Route::get('/ivs-and-lines', [IvsAndLineController::class, 'show'])->name('ivs-and-lines');
    Route::post('/ivs-and-lines/select', [IvsAndLineController::class, 'selectPatient'])->name('ivs-and-lines.select');
    Route::post('/ivs-and-lines', [IvsAndLineController::class, 'store'])->name('ivs-and-lines.store');


    // MEDICAL RECONCILIATION:
    Route::get('/medication-reconciliation', [MedReconciliationController::class, 'show'])->name('medication-reconciliation');
    Route::post('/medication-reconciliation/select', [MedReconciliationController::class, 'selectPatient'])->name('medreconciliation.select');
    Route::post('/medication-reconciliation', [MedReconciliationController::class, 'store'])->name('medreconciliation.store');


    // DISCHARGE PLANNING::
    Route::get('/discharge-planning', [DischargePlanningController::class, 'show'])->name('discharge-planning');
    Route::post('/discharge-planning', [DischargePlanningController::class, 'store'])->name('discharge-planning.store');
    Route::post('/discharge-planning/select', [DischargePlanningController::class, 'selectPatient'])->name('discharge-planning.select');


    //Activities of Daily Living (ADL)
    Route::prefix('adl')->name('adl.')->group(function () {
        Route::get('/', [ActOfDailyLivingController::class, 'show'])->name('show');
        Route::post('/', [ActOfDailyLivingController::class, 'store'])->name('store');
        Route::post('/cdss', [ActOfDailyLivingController::class, 'runCdssAnalysis'])->name('runCdssAnalysis');

        Route::post('/select', [ActOfDailyLivingController::class, 'selectPatientAndDate'])->name('select');

    });


    //VITAL SIGNS:
    Route::prefix('vital-signs')->name('vital-signs.')->group(function () {
        Route::get('/', [VitalSignsController::class, 'show'])->name('show');
        Route::post('/', [VitalSignsController::class, 'store'])->name('store');
        Route::post('/cdss', [VitalSignsController::class, 'runCdssAnalysis'])->name('runCdssAnalysis');
        Route::post('/select', [VitalSignsController::class, 'selectPatientAndDate'])->name('select');
        Route::post('/cdss', [VitalSignsController::class, 'runCdssAnalysis'])->name('cdss');

    });

    //Intake and Output
    Route::get('/intake-and-output', [IntakeAndOutputController::class, 'show'])->name('io.show');
    Route::post('/intake-and-output/select', [IntakeAndOutputController::class, 'selectPatientAndDate'])->name('io.select');
    Route::post('/intake-and-output/store', [IntakeAndOutputController::class, 'store'])->name('io.store');


    // Add more routes for role: NURSE here


});


