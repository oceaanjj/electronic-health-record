<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalReconciliation\CurrentMedication;
use App\Models\MedicalReconciliation\HomeMedication;
use App\Models\MedicalReconciliation\ChangesInMedication;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class MedicationReconciliationApiController extends Controller
{
    private function sanitize($data) {
        foreach ($data as $k => $v) { if ($v === '' || $v === null) $data[$k] = 'N/A'; }
        return $data;
    }

    /**
     * UNIFIED FETCH: Get all 3 categories for a patient
     */
    public function showByPatient($patient_id) {
        return response()->json([
            'current' => CurrentMedication::where('patient_id', $patient_id)->get(),
            'home' => HomeMedication::where('patient_id', $patient_id)->get(),
            'changes' => ChangesInMedication::where('patient_id', $patient_id)->get(),
        ]);
    }

    // --- CURRENT MEDICATION ---
    public function getCurrent($id) { return response()->json(CurrentMedication::findOrFail($id)); }
    
    public function storeCurrent(Request $request) {
        $record = CurrentMedication::updateOrCreate(
            ['patient_id' => $request->patient_id], 
            $this->sanitize($request->all())
        );
        AuditLogController::log('Current Meds Saved (Mobile)', 'User ' . Auth::user()->username . ' updated current meds.', ['patient_id' => $request->patient_id]);
        return response()->json($record);
    }

    // --- HOME MEDICATION ---
    public function getHome($id) { return response()->json(HomeMedication::findOrFail($id)); }
    
    public function storeHome(Request $request) {
        $record = HomeMedication::updateOrCreate(
            ['patient_id' => $request->patient_id], 
            $this->sanitize($request->all())
        );
        AuditLogController::log('Home Meds Saved (Mobile)', 'User ' . Auth::user()->username . ' updated home meds.', ['patient_id' => $request->patient_id]);
        return response()->json($record);
    }

    // --- CHANGES IN MEDICATION ---
    public function getChange($id) { return response()->json(ChangesInMedication::findOrFail($id)); }
    
    public function storeChange(Request $request) {
        $record = ChangesInMedication::updateOrCreate(
            ['patient_id' => $request->patient_id], 
            $this->sanitize($request->all())
        );
        AuditLogController::log('Med Changes Saved (Mobile)', 'User ' . Auth::user()->username . ' updated med changes.', ['patient_id' => $request->patient_id]);
        return response()->json($record);
    }
}
