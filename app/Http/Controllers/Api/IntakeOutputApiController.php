<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IntakeAndOutput;
use App\Services\IntakeAndOutputCdssService;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class IntakeOutputApiController extends Controller
{
    protected $cdssService;

    public function __construct(IntakeAndOutputCdssService $cdssService)
    {
        $this->cdssService = $cdssService;
    }

    public function index(Request $request)
    {
        $patientId = $request->query('patient_id');
        if (!$patientId) return response()->json(['message' => 'patient_id required'], 400);
        return response()->json(IntakeAndOutput::where('patient_id', $patientId)->latest()->get());
    }

    public function show($id)
    {
        return response()->json(IntakeAndOutput::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $patientId = $data['patient_id'];
        $dayNo = $data['day_no'] ?? 1;

        // Fetch existing record to merge data for accurate CDSS
        $existingRecord = IntakeAndOutput::where('patient_id', $patientId)
            ->where('day_no', $dayNo)
            ->first();

        $mergedData = $existingRecord ? array_merge($existingRecord->toArray(), $data) : $data;

        // Run CDSS on merged data
        $cdss = $this->cdssService->analyzeIntakeOutput($mergedData);
        $data['alert'] = $cdss['alert'];

        // Prevent duplicates for the same patient and day
        $record = IntakeAndOutput::updateOrCreate(
            ['patient_id' => $patientId, 'day_no' => $dayNo],
            $data
        );

        AuditLogController::log(
            'INTAKE & OUTPUT RECORDED',
            "Nurse " . Auth::user()->username . " recorded intake and output for patient ID: {$patientId} (Day No: {$dayNo}).",
            ['patient_id' => $patientId, 'record_id' => $record->id]
        );

        return response()->json(['message' => 'Intake and Output saved', 'data' => $record], 201);
    }

    public function update(Request $request, $id)
    {
        $record = IntakeAndOutput::findOrFail($id);
        $data = $request->all();
        
        // Merge with existing record data for accurate CDSS
        $mergedData = array_merge($record->toArray(), $data);

        $cdss = $this->cdssService->analyzeIntakeOutput($mergedData);
        $data['alert'] = $cdss['alert'];

        $record->update($data);

        AuditLogController::log(
            'INTAKE & OUTPUT UPDATED',
            "Nurse " . Auth::user()->username . " updated intake and output record (ID: {$id}) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id, 'record_id' => $id]
        );

        return response()->json(['message' => 'Intake and Output updated', 'data' => $record]);
    }
}
