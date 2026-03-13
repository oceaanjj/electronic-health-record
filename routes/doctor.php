<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Doctor\ReportController;

//-------------------------------------------------------------
// Protected Routes for Doctor
//-------------------------------------------------------------
Route::middleware(['auth', 'can:is-doctor'])->group(function () {
    Route::get('/doctor', [HomeController::class, 'doctorHome'])->name('doctor-home');
    Route::get('/doctor/patient-report', [ReportController::class, 'showPatientReportForm'])->name('doctor.patient-report');
    Route::post('/doctor/generate-report', [ReportController::class, 'generateReport'])->name('doctor.generate-report');
    Route::get('/doctor/patient-report/{patient_id}/pdf', [ReportController::class, 'downloadPDF'])->name('doctor.report.pdf');
    Route::get('/doctor/recent-forms', [ReportController::class, 'recentForms'])->name('doctor.recent-forms');
    Route::get('/doctor/form-detail/{type}/{patient_id}', [ReportController::class, 'showPatientForm'])->name('doctor.form-detail');
    Route::get('/doctor/stats/total-patients', [ReportController::class, 'totalPatients'])->name('doctor.stats.total-patients');
    Route::get('/doctor/stats/active-patients', [ReportController::class, 'activePatients'])->name('doctor.stats.active-patients');
    Route::get('/doctor/stats/today-updates', [ReportController::class, 'todayUpdates'])->name('doctor.stats.today-updates');
    Route::post('/doctor/mark-read', [HomeController::class, 'markFormRead'])->name('doctor.mark-read');
    Route::get('/doctor/patient/{patient_id}', [ReportController::class, 'patientDetails'])->name('doctor.patient-details');
});
