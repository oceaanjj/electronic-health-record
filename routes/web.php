<?php
use App\Http\Controllers\MedicalController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PhysicalExamController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

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

//Login
Route::prefix('login')->name('login.')->group(function () {
    //for showing the login forms
    Route::get('/', [LoginController::class, 'showRoleSelectionForm'])->name('index');
    Route::get('/nurse', [LoginController::class, 'showNurseLoginForm'])->name('nurse');
    Route::get('/doctor', [LoginController::class, 'showDoctorLoginForm'])->name('doctor');
    Route::get('/admin', [LoginController::class, 'showAdminLoginForm'])->name('admin');

    //for handling authentication
    Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
});

//Register
Route::prefix('register')->group(function () {
    Route::get('/', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/', [RegisterController::class, 'register'])->name('register.attempt');
});

//Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


//Physical Exam:
Route::prefix('physical-exam')->group(function () {
    Route::get('/', [PhysicalExamController::class, 'show'])->name('physical-exam.show');
    Route::post('/', [PhysicalExamController::class, 'store'])->name('physical-exam.store');
});

//search:
Route::prefix('patients')->name('patients.')->group(function () {
    Route::get('/search', function () {
        return view('patients.search');
    })->name('search');

    Route::get('/search-results', [PatientController::class, 'search'])->name('search-results');
});

Route::resource('patients', PatientController::class);

Route::post('/medical/store', [MedicalController::class, 'store'])->name('medical.store');
Route::post('/present-illness', [MedicalController::class, 'storePresentIllness'])->name('present.store');
Route::post('/past-medical', [MedicalController::class, 'storePastMedicalSurgical'])->name('past.store');
Route::post('/allergies', [MedicalController::class, 'storeAllergies'])->name('allergy.store');
Route::post('/vaccination', [MedicalController::class, 'storeVaccination'])->name('vaccination.store');
Route::post('/developmental', [MedicalController::class, 'storeDevelopmentalHistory'])->name('developmental.store');


