<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

$views = [
    'role',
    'medical-history',
    'physical-exam',
    'vital-signs',
    'intake-and-output',
    'act-of-daily-living',
    'lab-values',
    'diagnostics',
    'ivs-and-lines',
];

foreach ($views as $view) {
    Route::view("/{$view}", $view)->name($view);
}

//login
Route::prefix('login')->name('login.')->group(function () {
    //for showing the login forms
    Route::get('/', [LoginController::class, 'showRoleSelectionForm'])->name('index');
    Route::get('/nurse', [LoginController::class, 'showNurseLoginForm'])->name('nurse');
    Route::get('/doctor', [LoginController::class, 'showDoctorLoginForm'])->name('doctor');
    Route::get('/admin', [LoginController::class, 'showAdminLoginForm'])->name('admin');

    //for handling authentication
    Route::post('/nurse', [LoginController::class, 'authenticateNurse'])->name('authenticate.nurse');
    Route::post('/doctor', [LoginController::class, 'authenticateDoctor'])->name('authenticate.doctor');
    Route::post('/admin', [LoginController::class, 'authenticateAdmin'])->name('authenticate.admin');
});







Route::resource('patients', PatientController::class);

