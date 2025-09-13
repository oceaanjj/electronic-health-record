<?php

use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/nurse-login', function () {
    return view('nurse-login');
})->name('nurse-login');

Route::get('/doctor-login', function () {
    return view('doctor-login');
})->name('doctor-login');

Route::get('/admin-login', function () {
    return view('admin-login');
})->name('admin-login');


Route::prefix('patients')->name('patients.')->group(function () {
    Route::get('/', function () {
        return view('patients.index');
    })->name('index');

    Route::get('/create', function () {
        return view('patients.create');
    })->name('create');

    Route::get('/{patient}', function () {
        return view('patients.show');
    })->name('show');

    Route::get('/{patient}/edit', function () {
        return view('patients.edit');
    })->name('edit');
});

Route::resource('patients', PatientController::class);

