<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DiagnosticsController;
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

// -- UPDATED LOGIN ROUTES ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');



// Old routes for separate login forms (as requested, kept as comments)

// Route::prefix('login')->name('login.')->group(callback: function () {
//     Route::get('/login', [LoginController::class, 'showRoleSelectionForm'])->name('login');
//     Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

//     Route::get('/nurse', [LoginController::class, 'showNurseLoginForm'])->name('nurse');
//     Route::get('/doctor', [LoginController::class, 'showDoctorLoginForm'])->name('doctor');
//     Route::get('/admin', [LoginController::class, 'showAdminLoginForm'])->name('admin');
// });

//Logout
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

use App\Http\Controllers\Doctor\ReportController;

//-------------------------------------------------------------
// Protected Routes for Doctor
//-------------------------------------------------------------
Route::middleware(['auth', 'can:is-doctor'])->group(function () {
    Route::get('/doctor', [HomeController::class, 'doctorHome'])->name('doctor-home');
    Route::get('/doctor/patient-report', [ReportController::class, 'showPatientReportForm'])->name('doctor.patient-report');
    Route::post('/doctor/generate-report', [ReportController::class, 'generateReport'])->name('doctor.generate-report');
    Route::get('/doctor/patient-report/{patient_id}/pdf', [ReportController::class, 'downloadPDF'])->name('doctor.report.pdf');
});


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
        //paki ayos to keith nag 404 not found hindi ko maayos css
        'developmental-history',
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
        Route::get('/live-search', [PatientController::class, 'liveSearch'])->name('live-search');
        Route::get('/search-results', [PatientController::class, 'search'])->name('search-results');
    });

    Route::resource('patients', PatientController::class);
    Route::post('patients/{patient}/recover', [PatientController::class, 'recover'])->name('patients.recover');

    // physical exam
    Route::prefix('physical-exam')->name('physical-exam.')->group(function () {
        Route::get('/', [PhysicalExamController::class, 'show'])->name('index');
        Route::post('/select', [PhysicalExamController::class, 'selectPatient'])->name('select');
        Route::post('/', [PhysicalExamController::class, 'store'])->name('store');
        Route::post('/cdss', [PhysicalExamController::class, 'runCdssAnalysis'])->name('runCdssAnalysis');
        // New route for real-time, single-field analysis
        Route::post('/analyze-field', [PhysicalExamController::class, 'runSingleCdssAnalysis'])->name('analyze-field');
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
    //Developmental History
    Route::get('/developmental-history', [MedicalController::class, 'showDevelopmentalHistory'])->name('developmental-history');
    Route::post('/developmental-history', [MedicalController::class, 'storeDevelopmentalHistory'])->name('developmental.store');

    // Lab Values Routes
    Route::get('/lab-values', [LabValuesController::class, 'show'])->name('lab-values.index');
    Route::post('/lab-values/select', [LabValuesController::class, 'selectPatient'])->name('lab-values.select');
    Route::post('/lab-values', [LabValuesController::class, 'store'])->name('lab-values.store');
    Route::post('/lab-values/run-cdss', [LabValuesController::class, 'runCdssAnalysis'])->name('lab-values.cdss');
    Route::post('/lab-values/analyze-field', [LabValuesController::class, 'runSingleCdssAnalysis'])->name('lab-values.run-cdss-field');

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
        // Route::post('/select', [ActOfDailyLivingController::class, 'selectPatientAndDate'])->name('select');

        Route::post('/select-patient', [ActOfDailyLivingController::class, 'selectPatient'])->name('select');
        Route::post('/select-date-day', [ActOfDailyLivingController::class, 'selectDateAndDay'])->name('select-date-day');
        Route::post('/analyze-field', [ActOfDailyLivingController::class, 'analyzeField'])->name('analyze-field');
    });

    Route::get('/diagnostics', [DiagnosticsController::class, 'index'])->name('diagnostics.index');
    Route::post('/diagnostics/select', [DiagnosticsController::class, 'selectPatient'])->name('diagnostics.select');
    Route::post('/diagnostics/submit', [DiagnosticsController::class, 'submit'])->name('diagnostics.submit');
    Route::delete('/diagnostics/{id}', [DiagnosticsController::class, 'destroy'])->name('diagnostics.destroy');
    Route::delete('/diagnostics/destroy-all/{type}/{patient_id}', [DiagnosticsController::class, 'destroyAll'])->name('diagnostics.destroy-all');

    //VITAL SIGNS:
    Route::prefix('vital-signs')->name('vital-signs.')->group(function () {
        Route::get('/', [VitalSignsController::class, 'show'])->name('show');
        Route::post('/', [VitalSignsController::class, 'store'])->name('store');
        Route::post('/cdss', [VitalSignsController::class, 'runCdssAnalysis'])->name('runCdssAnalysis');
        Route::post('/select', [VitalSignsController::class, 'selectPatient'])->name('select');
        Route::post('/cdss', [VitalSignsController::class, 'runCdssAnalysis'])->name('cdss');
        Route::post('/check', [VitalSignsController::class, 'checkVitals'])->name('check');
    });

    //Intake and Output
    Route::get('/intake-and-output', [IntakeAndOutputController::class, 'show'])->name('io.show');
    Route::post('/intake-and-output/select', [IntakeAndOutputController::class, 'selectPatientAndDate'])->name('io.select');
    Route::post('/intake-and-output/store', [IntakeAndOutputController::class, 'store'])->name('io.store');
    Route::post('/intake-and-output/check', [IntakeAndOutputController::class, 'checkIntakeOutput'])->name('io.check');

    // ---------------
    // ADPIE Routes
    // -----------------
    Route::prefix('adpie')->name('adpie.')->group(function () {
        $adpieCategories = [
            'adl',
            'intake-output',
            'lab-values',
            'physical-exam',
            'vital-signs'
        ];

        $adpiePages = [
            'diagnosis',
            'evaluation',
            'interventions',
            'planning'
        ];

        foreach ($adpieCategories as $category) {
            Route::prefix($category)->name("{$category}.")->group(function () use ($adpiePages, $category) {
                foreach ($adpiePages as $page) {
                    // /adpie/adl/diagnosis which shows view('adpie.adl.diagnosis')
                    // and has the name adpie.adl.diagnosis
                    Route::view("/{$page}", "adpie.{$category}.{$page}")->name($page);
                }
            });
        }
    });
});
