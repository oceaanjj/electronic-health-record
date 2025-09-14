<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/role', function () {
    return view('role');
})->name('role');

Route::get('/medical-history', function () {
    return view('medical-history');
})->name('medical-history');

Route::get('/physical-exam', function () {
    return view('physical-exam');
})->name('physical-exam');

Route::get('/vital-signs', function () {
    return view('vital-signs');
})->name('vital-signs');


Route::get('/intake-and-output', function () {
    return view('intake-and-output');
})->name('intake-and-output');

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

