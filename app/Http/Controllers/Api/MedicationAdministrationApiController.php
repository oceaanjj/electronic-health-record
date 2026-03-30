<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicationAdministration;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MedicationAdministrationApiController extends Controller
{
    /**
     * Get history for a specific patient
     */
    public function index($patient_id)
    {
        return response()->json(MedicationAdministration::where('patient_id', $patient_id)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get());
    }

    /**
     * NEW: Load data for a specific patient and time
     * Useful for displaying existing data in the mobile form when a time slot is selected.
     */
    public function getByTime($patient_id, $time, Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        
        // Normalize time to HH:mm:ss (e.g., 08:00 -> 08:00:00)
        $formattedTime = strlen($time) == 5 ? $time . ':00' : $time;

        $record = MedicationAdministration::where('patient_id', $patient_id)
            ->where('date', $date)
            ->where('time', $formattedTime)
            ->first();

        if (!$record) {
            // Return 200 OK with null data so mobile code doesn't enter the "catch" block
            return response()->json([
                'message' => 'No record for this time slot',
                'exists' => false,
                'data' => null
            ], 200);
        }

        // Add exists flag for easier mobile logic
        $data = $record->toArray();
        $data['exists'] = true;

        return response()->json($data);
    }

    /**
     * SUBMIT: Create or Update (Duplicate-Proof)
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $patientId = $data['patient_id'] ?? $data['PATIENT_ID'];
        $time = $data['time'] ?? $data['TIME'] ?? '08:00';
        $date = $data['date'] ?? $data['DATE'] ?? now()->toDateString();

        // Normalize time for DB matching
        $dbTime = strlen($time) == 5 ? $time . ':00' : $time;

        // Auto-sanitize empty to N/A
        $updateData = [
            'medication' => $request->input('medication') ?? $request->input('MEDICATION') ?? 'N/A',
            'dose' => $request->input('dose') ?? $request->input('DOSE') ?? 'N/A',
            'route' => $request->input('route') ?? $request->input('ROUTE') ?? 'N/A',
            'frequency' => $request->input('frequency') ?? $request->input('FREQUENCY') ?? 'N/A',
            'comments' => $request->input('comments') ?? $request->input('COMMENTS') ?? 'N/A',
        ];

        // This ensures EDITING instead of DUPLICATING
        $record = MedicationAdministration::updateOrCreate(
            ['patient_id' => $patientId, 'date' => $date, 'time' => $dbTime],
            $updateData
        );

        AuditLogController::log(
            'MEDICATION ADMINISTRATION RECORDED',
            "Nurse " . Auth::user()->username . " recorded medication administration for patient ID: {$patientId} at {$time} on {$date}.",
            ['patient_id' => $patientId, 'record_id' => $record->id]
        );

        return response()->json([
            'message' => 'Medication administration saved!',
            'id' => $record->id,
            'data' => $record
        ], 201);
    }

    public function show($id) { return response()->json(MedicationAdministration::findOrFail($id)); }

    public function update(Request $request, $id)
    {
        $record = MedicationAdministration::findOrFail($id);
        $data = $request->all();

        // Simple sanitization for update
        foreach ($data as $k => $v) {
            if ($v === '' || $v === null) $data[$k] = 'N/A';
        }

        $record->update($data);

        AuditLogController::log(
            'MEDICATION ADMINISTRATION UPDATED',
            "Nurse " . Auth::user()->username . " updated medication administration record (ID: {$id}) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id, 'record_id' => $id]
        );

        return response()->json(['message' => 'Record updated', 'data' => $record]);
    }
}
