<?php

use App\Http\Controllers\nurseController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');


Route::prefix('login')->name('login.')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('index');
    Route::get('/nurse', [LoginController::class, 'showNurseLoginForm'])->name('nurse');
    Route::get('/doctor', [LoginController::class, 'showDoctorLoginForm'])->name('doctor');
    Route::get('/admin', [LoginController::class, 'showAdminLoginForm'])->name('admin');
});

Route::resource('patients', PatientController::class);

Route::post('/nurse/login', [NurseController::class, 'login'])->name('nurse.login');
