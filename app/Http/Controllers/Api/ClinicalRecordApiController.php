<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IvsAndLine;
use App\Models\DischargePlan;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class ClinicalRecordApiController extends Controller
{
    private function sanitize($data) {
        foreach ($data as $k => $v) { if ($v === '' || $v === null) $data[$k] = 'N/A'; }
        return $data;
    }

    // --- IVs AND LINES ---
    public function getIvsAndLinesByPatient($patient_id) {
        return response()->json(IvsAndLine::where('patient_id', $patient_id)->get());
    }

    public function getIvsAndLine($id) {
        return response()->json(IvsAndLine::findOrFail($id));
    }

    public function storeIvsAndLines(Request $request) {
        $data = $this->sanitize($request->all());
        $record = IvsAndLine::create($data);
        
        AuditLogController::log(
            'IVS AND LINES RECORDED',
            "Nurse " . Auth::user()->username . " recorded new IV/Line details for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id, 'record_id' => $record->id]
        );
        
        return response()->json($record, 201);
    }

    public function updateIvsAndLine(Request $request, $id) {
        $record = IvsAndLine::findOrFail($id);
        $record->update($this->sanitize($request->all()));
        
        AuditLogController::log(
            'IVS AND LINES UPDATED',
            "Nurse " . Auth::user()->username . " updated IV/Line record (ID: {$id}) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id, 'record_id' => $id]
        );
        
        return response()->json($record);
    }

    // --- DISCHARGE PLANNING ---
    public function getDischargePlanning($patient_id) {
        return response()->json(DischargePlan::where('patient_id', $patient_id)->first());
    }

    public function storeDischargePlanning(Request $request) {
        $record = DischargePlan::updateOrCreate(['patient_id' => $request->patient_id], $this->sanitize($request->all()));

        AuditLogController::log(
            'DISCHARGE PLANNING UPDATED',
            "Nurse " . Auth::user()->username . " updated discharge planning for patient ID: " . $request->patient_id . ".",
            ['patient_id' => $request->patient_id, 'record_id' => $record->id]
        );

        return response()->json($record);
    }
}
