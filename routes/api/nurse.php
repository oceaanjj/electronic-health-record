<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientApiController;
use App\Http\Controllers\Api\VitalSignsApiController;
use App\Http\Controllers\Api\PhysicalExamApiController;
use App\Http\Controllers\Api\AdlApiController;
use App\Http\Controllers\Api\IntakeOutputApiController;
use App\Http\Controllers\Api\LabValuesApiController;
use App\Http\Controllers\Api\MedicalHistoryApiController;
use App\Http\Controllers\Api\MedicationAdministrationApiController;
use App\Http\Controllers\Api\MedicationReconciliationApiController;
use App\Http\Controllers\Api\ClinicalRecordApiController;
use App\Http\Controllers\Api\DiagnosticApiController;
use App\Http\Controllers\Api\NursingDiagnosisApiController;
use App\Http\Controllers\Api\DataAlertApiController;
use App\Http\Controllers\ADPIE\AdpieApiController;

/*
|--------------------------------------------------------------------------
| Nurse API Routes
| Base URL: /api/
| Auth: Bearer token (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // ── 1. PATIENTS ──────────────────────────────────────────────────────
    Route::apiResource('patient', PatientApiController::class);
    Route::post('patient/{id}/toggle-status', [PatientApiController::class, 'toggleStatus']);

    // ── 2. ASSESSMENT FORMS (ADPIE components) ───────────────────────────
    $adpie = ['vital-signs', 'physical-exam', 'adl', 'intake-and-output', 'lab-values'];
    foreach ($adpie as $uri) {
        $controller = match($uri) {
            'vital-signs'     => VitalSignsApiController::class,
            'physical-exam'   => PhysicalExamApiController::class,
            'adl'             => AdlApiController::class,
            'intake-and-output' => IntakeOutputApiController::class,
            'lab-values'      => LabValuesApiController::class,
        };
        Route::match(['post', 'put'], "$uri", [$controller, 'store']);
        Route::match(['get', 'put'], "$uri/{id}/assessment", [$controller, 'show']);
        Route::put("$uri/{id}/assessment", [$controller, 'update']);
        Route::get("$uri/patient/{patient_id}", [$controller, 'index']);
    }

    // Physical exam alerts only (no ADPIE / nursing diagnosis data)
    Route::get('physical-exam/patient/{patient_id}/alerts', [PhysicalExamApiController::class, 'alerts']);

    // ── 2.1 DATA ALERTS ──────────────────────────────────────────────────
    Route::get('data-alert/patient/{patient_id}', [DataAlertApiController::class, 'show']);
    Route::get('{component}/data-alert/patient/{patient_id}', [DataAlertApiController::class, 'showByComponent'])
        ->where('component', 'vital-signs|physical-exam|adl|intake-and-output|lab-values');

    // ── 2.2 ADPIE / CDSS ─────────────────────────────────────────────────
    Route::prefix('adpie')->group(function () {
        Route::get('{component}/{id}', [AdpieApiController::class, 'initialize']);
        Route::post('analyze', [AdpieApiController::class, 'analyze']);
        Route::post('analyze-batch', [AdpieApiController::class, 'analyzeBatch']);
        Route::put('{id}/{step}', [AdpieApiController::class, 'updateStep'])
            ->where('step', 'diagnosis|planning|intervention|evaluation');
    });

    // ── 3. MEDICAL HISTORY ───────────────────────────────────────────────
    Route::get('medical-history/patient/{patient_id}', [MedicalHistoryApiController::class, 'show']);
    $historyTypes = ['present-illness', 'past-history', 'allergies', 'vaccination', 'developmental'];
    foreach ($historyTypes as $uri) {
        $methodSuffix = str_replace('-', '', ucwords($uri, '-'));
        Route::get("medical-history/$uri/{id}", [MedicalHistoryApiController::class, "get$methodSuffix"]);
        Route::match(['post', 'put'], "medical-history/$uri", [MedicalHistoryApiController::class, "store$methodSuffix"]);
        Route::match(['post', 'put'], "medical-history/$uri/{id}", [MedicalHistoryApiController::class, "update$methodSuffix"]);
    }

    // ── 4. MEDICATION RECONCILIATION ────────────────────────────────────
    $reconPrefixes = ['medical-reconciliation', 'medication-reconciliation', 'medicalreconcilation'];
    foreach ($reconPrefixes as $prefix) {
        Route::get("$prefix/patient/{patient_id}", [MedicationReconciliationApiController::class, 'showByPatient']);
        $reconTypes = ['current' => 'Current', 'home' => 'Home', 'changes' => 'Change'];
        foreach ($reconTypes as $uri => $suffix) {
            Route::get("$prefix/$uri/{id}", [MedicationReconciliationApiController::class, "get$suffix"]);
            Route::match(['post', 'put'], "$prefix/$uri", [MedicationReconciliationApiController::class, "store$suffix"]);
            Route::match(['post', 'put'], "$prefix/$uri/{id}", [MedicationReconciliationApiController::class, "update$suffix"]);
        }
    }

    // ── 5. MEDICATION ADMINISTRATION ────────────────────────────────────
    Route::match(['get', 'post', 'put'], 'medication-administration', [MedicationAdministrationApiController::class, 'store']);
    Route::get('medication-administration/patient/{patient_id}', [MedicationAdministrationApiController::class, 'index']);
    Route::get('medication-administration/patient/{patient_id}/time/{time}', [MedicationAdministrationApiController::class, 'getByTime']);
    Route::match(['get', 'put'], 'medication-administration/{id}', [MedicationAdministrationApiController::class, 'show']);
    Route::put('medication-administration/{id}', [MedicationAdministrationApiController::class, 'update']);

    // ── 6. CLINICAL RECORDS & IMAGING ───────────────────────────────────
    Route::get('ivs-and-lines/patient/{patient_id}', [ClinicalRecordApiController::class, 'getIvsAndLinesByPatient']);
    Route::match(['post', 'put'], 'ivs-and-lines', [ClinicalRecordApiController::class, 'storeIvsAndLines']);
    Route::match(['get', 'put'], 'ivs-and-lines/{id}', [ClinicalRecordApiController::class, 'getIvsAndLine']);

    Route::get('discharge-planning/patient/{patient_id}', [ClinicalRecordApiController::class, 'getDischargePlanning']);
    Route::match(['post', 'put'], 'discharge-planning', [ClinicalRecordApiController::class, 'storeDischargePlanning']);

    // ── 7. DIAGNOSTICS ───────────────────────────────────────────────────
    Route::get('diagnostics/patient/{patient_id}', [DiagnosticApiController::class, 'getDiagnostics']);
    Route::post('diagnostics', [DiagnosticApiController::class, 'storeDiagnostic']);
});
