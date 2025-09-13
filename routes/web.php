<?php


use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('home');
})->name('home');

Route::get('/nurse-login', function () {
    return view('nurse-login');
});

Route::get('/doctor-login', function(){
    return view('doctor-login');
});

Route::get('/admin-login', function(){
    return view('admin-login');
});

Route::get("/patient-registration", function(){
    return view('patient-registration');
});
//
Route::resource('patients', PatientController::class);
Route::post('/nurse/login', [NurseController::class, 'login'])->name('nurse.login');
Route::post('/doctor/login', [DoctorController::class, 'login'])->name('doctor.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');