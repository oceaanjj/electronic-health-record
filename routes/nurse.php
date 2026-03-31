<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\MedicationAdministrationController;
use App\Http\Controllers\ADPIE\NursingDiagnosisController;
use App\Http\Controllers\NurseAiChatController;

//-------------------------------------------------------------
// Protected Routes for Nurse
//-------------------------------------------------------------
Route::middleware(['auth', 'can:is-nurse'])->group(function () {
    Route::get('/nurse', [HomeController::class, 'nurseHome'])->name('nurse-home');
    Route::post('/nurse-ai-chat/ask', [NurseAiChatController::class, 'ask'])->name('nurse-ai-chat.ask');

    // Simple nurse-accessible views
    $nurseViews = [
        'about-page',
        'medical-history',
        'physical-exam' => 'physical-exam.index',
        'vital-signs',
        'act-of-daily-living',
        'lab-values',
        'ivs-and-lines',
        'medication-administration',
        'medication-reconciliation',
        'discharge-planning',
        'developmental-history',
    ];

    foreach ($nurseViews as $uri => $name) {
        $routeUri = is_string($uri) ? $uri : $name;
        Route::view("/{$routeUri}", $name)->name($name);
    }

    // Patient & Medical Record Routes
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/search', fn() => view('patients.search'))->name('search');
        Route::get('/live-search', [PatientController::class, 'liveSearch'])->name('live-search');
        Route::get('/search-results', [PatientController::class, 'search'])->name('search-results');
    });

    Route::resource('patients', PatientController::class)->except(['destroy']);

    // Patient Demographic: Active/Inactive
    Route::delete('patients/{id}/deactivate', [PatientController::class, 'deactivate'])->name('patients.deactivate');
    Route::post('patients/{id}/activate', [PatientController::class, 'activate'])->name('patients.activate');

    // PHYSICAL EXAM
    Route::prefix('physical-exam')->name('physical-exam.')->group(function () {
        Route::get('/', [PhysicalExamController::class, 'show'])->name('index');
        Route::post('/select', [PhysicalExamController::class, 'selectPatient'])->name('select');
        Route::post('/', [PhysicalExamController::class, 'store'])->name('store');
        Route::post('/cdss', [PhysicalExamController::class, 'runCdssAnalysis'])->name('runCdssAnalysis');
        Route::post('/analyze-field', [PhysicalExamController::class, 'runSingleCdssAnalysis'])->name('analyze-field');
        Route::post('/analyze-batch', [PhysicalExamController::class, 'runBatchCdssAnalysis'])->name('analyze-batch');
    });

    // MEDICAL HISTORY
    Route::get('/medical-history', [MedicalController::class, 'show'])->name('medical-history');
    Route::post('/medical/store', [MedicalController::class, 'store'])->name('medical.store');
    Route::post('/present-illness', [MedicalController::class, 'storePresentIllness'])->name('present.store');
    Route::post('/past-medical', [MedicalController::class, 'storePastMedicalSurgical'])->name('past.store');
    Route::post('/allergies', [MedicalController::class, 'storeAllergies'])->name('allergy.store');
    Route::post('/vaccination', [MedicalController::class, 'storeVaccination'])->name('vaccination.store');
    Route::post('/developmental', [MedicalController::class, 'storeDevelopmentalHistory'])->name('developmental.store');
    Route::post('/medical-history/select', [MedicalController::class, 'selectPatient'])->name('medical-history.select');
    Route::get('/developmental-history', [MedicalController::class, 'showDevelopmentalHistory'])->name('developmental-history');

    // LAB VALUES
    Route::get('/lab-values', [LabValuesController::class, 'show'])->name('lab-values.index');
    Route::post('/lab-values/select', [LabValuesController::class, 'selectPatient'])->name('lab-values.select');
    Route::post('/lab-values', [LabValuesController::class, 'store'])->name('lab-values.store');
    Route::post('/lab-values/analyze-field', [LabValuesController::class, 'runSingleCdssAnalysis'])->name('lab-values.run-cdss-field');
    Route::post('/lab-values/analyze-batch', [LabValuesController::class, 'runBatchCdssAnalysis'])->name('lab-values.analyze-batch');
    Route::post('/lab-values/analyze-correlations', [LabValuesController::class, 'runCorrelationAnalysis'])->name('lab-values.analyze-correlations');

    // IVS AND LINES
    Route::get('/ivs-and-lines', [IvsAndLineController::class, 'show'])->name('ivs-and-lines');
    Route::post('/ivs-and-lines/select', [IvsAndLineController::class, 'selectPatient'])->name('ivs-and-lines.select');
    Route::post('/ivs-and-lines', [IvsAndLineController::class, 'store'])->name('ivs-and-lines.store');

    // MEDICATION RECONCILIATION
    Route::get('/medication-reconciliation', [MedReconciliationController::class, 'show'])->name('medication-reconciliation');
    Route::post('/medication-reconciliation/select', [MedReconciliationController::class, 'selectPatient'])->name('medreconciliation.select');
    Route::post('/medication-reconciliation', [MedReconciliationController::class, 'store'])->name('medreconciliation.store');

    // DISCHARGE PLANNING
    Route::get('/discharge-planning', [DischargePlanningController::class, 'show'])->name('discharge-planning');
    Route::post('/discharge-planning', [DischargePlanningController::class, 'store'])->name('discharge-planning.store');
    Route::post('/discharge-planning/select', [DischargePlanningController::class, 'selectPatient'])->name('discharge-planning.select');

    // ACTIVITIES OF DAILY LIVING (ADL)
    Route::prefix('adl')->name('adl.')->group(function () {
        Route::get('/', [ActOfDailyLivingController::class, 'show'])->name('show');
        Route::post('/', [ActOfDailyLivingController::class, 'store'])->name('store');
        Route::post('/select-patient', [ActOfDailyLivingController::class, 'selectPatient'])->name('select');
        Route::post('/select-date-day', [ActOfDailyLivingController::class, 'selectDateAndDay'])->name('select-date-day');
        Route::post('/analyze-field', [ActOfDailyLivingController::class, 'analyzeField'])->name('analyze-field');
        Route::post('/analyze-batch', [ActOfDailyLivingController::class, 'runBatchCdssAnalysis'])->name('analyze-batch');
    });

    // DIAGNOSTICS
    Route::get('/diagnostics', [DiagnosticsController::class, 'index'])->name('diagnostics.index');
    Route::post('/diagnostics/select', [DiagnosticsController::class, 'selectPatient'])->name('diagnostics.select');
    Route::post('/diagnostics/submit', [DiagnosticsController::class, 'submit'])->name('diagnostics.submit');
    Route::delete('/diagnostics/{id}', [DiagnosticsController::class, 'destroy'])->name('diagnostics.destroy');
    Route::delete('/diagnostics/destroy-all/{type}/{patient_id}', [DiagnosticsController::class, 'destroyAll'])->name('diagnostics.destroy-all');

    // VITAL SIGNS
    Route::prefix('vital-signs')->name('vital-signs.')->group(function () {
        Route::get('/', [VitalSignsController::class, 'show'])->name('show');
        Route::post('/', [VitalSignsController::class, 'store'])->name('store');
        Route::post('/cdss', [VitalSignsController::class, 'runCdssAnalysis'])->name('runCdssAnalysis');
        Route::post('/cdss', [VitalSignsController::class, 'runCdssAnalysis'])->name('cdss');
        Route::post('/select', [VitalSignsController::class, 'selectPatient'])->name('select');
        Route::post('/check', [VitalSignsController::class, 'checkVitals'])->name('check');
        Route::post('/fetch-data', [VitalSignsController::class, 'fetchVitalSignsData'])->name('fetch-data');
        Route::post('/analyze-batch', [VitalSignsController::class, 'runBatchCdssAnalysis'])->name('analyze-batch');
    });

    // INTAKE AND OUTPUT
    Route::get('/intake-and-output', [IntakeAndOutputController::class, 'show'])->name('io.show');
    Route::post('/intake-and-output/select', [IntakeAndOutputController::class, 'selectPatientAndDate'])->name('io.select');
    Route::post('/intake-and-output/store', [IntakeAndOutputController::class, 'store'])->name('io.store');
    Route::post('/intake-and-output/check', [IntakeAndOutputController::class, 'checkIntakeOutput'])->name('io.check');
    Route::post('/intake-and-output/cdss', [IntakeAndOutputController::class, 'runCdssAnalysis'])->name('io.cdss');
    Route::post('/intake-and-output/analyze-batch', [IntakeAndOutputController::class, 'runBatchCdssAnalysis'])->name('io.analyze-batch');

    // MEDICATION ADMINISTRATION
    Route::get('/medication-administration', [MedicationAdministrationController::class, 'show'])->name('medication-administration');
    Route::post('/medication-administration/store', [MedicationAdministrationController::class, 'store'])->name('medication-administration.store');
    Route::post('/medication-administration/select-patient', [MedicationAdministrationController::class, 'selectPatient'])->name('medication-administration.select-patient');
    Route::get('/medication-administration/records', [MedicationAdministrationController::class, 'getRecords'])->name('medication-administration.get-records');

    // NURSING DIAGNOSIS (legacy route)
    Route::get('/lab-values/nursing-diagnosis/{id}', [NursingDiagnosisController::class, 'startDiagnosis'])->name('lab-values.nursing-diagnosis.start');

    // ADPIE / NURSING DIAGNOSIS
    Route::post('/adpie/vitals/analyze-diagnosis', [VitalSignsController::class, 'analyzeDiagnosisForNursing'])->name('adpie.vitals.analyzeDiagnosis');

    Route::prefix('adpie')->name('nursing-diagnosis.')->group(function () {
        Route::post('/analyze-step', [NursingDiagnosisController::class, 'analyzeDiagnosisField'])->name('analyze-field');
        Route::post('/analyze-batch-step', [NursingDiagnosisController::class, 'analyzeBatchDiagnosisField'])->name('analyze-batch-field');
        Route::get('/{component}/process/{id}', [NursingDiagnosisController::class, 'showProcess'])->name('process');
        Route::post('/{component}/process/{id}', [NursingDiagnosisController::class, 'storeFullProcess'])->name('storeFullProcess');
        Route::get('/{component}/diagnosis/{id}', [NursingDiagnosisController::class, 'startDiagnosis'])->name('start');
        Route::post('/{component}/diagnosis/{id}', [NursingDiagnosisController::class, 'storeDiagnosis'])->name('storeDiagnosis');
        Route::get('/{component}/planning/{nursingDiagnosisId}', [NursingDiagnosisController::class, 'showPlanning'])->name('showPlanning');
        Route::post('/{component}/planning/{nursingDiagnosisId}', [NursingDiagnosisController::class, 'storePlanning'])->name('storePlanning');
        Route::get('/{component}/intervention/{nursingDiagnosisId}', [NursingDiagnosisController::class, 'showIntervention'])->name('showIntervention');
        Route::post('/{component}/intervention/{nursingDiagnosisId}', [NursingDiagnosisController::class, 'storeIntervention'])->name('storeIntervention');
        Route::get('/{component}/evaluation/{nursingDiagnosisId}', [NursingDiagnosisController::class, 'showEvaluation'])->name('showEvaluation');
        Route::post('/{component}/evaluation/{nursingDiagnosisId}', [NursingDiagnosisController::class, 'storeEvaluation'])->name('storeEvaluation');
        Route::get('/patient/{patientId}', [NursingDiagnosisController::class, 'showByPatient'])->name('showByPatient');
    });
});
