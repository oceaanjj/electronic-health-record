<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IntakeAndOutput;
use App\Services\IntakeAndOutputCdssService;
use Illuminate\Http\Request;

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

        // Run CDSS
        $cdss = $this->cdssService->analyzeIntakeOutput($data);
        $data['alert'] = $cdss['alert'];

        // Prevent duplicates for the same patient and day
        $record = IntakeAndOutput::updateOrCreate(
            ['patient_id' => $patientId, 'day_no' => $dayNo],
            $data
        );

        return response()->json(['message' => 'Intake and Output saved', 'data' => $record], 201);
    }

    public function update(Request $request, $id)
    {
        $record = IntakeAndOutput::findOrFail($id);
        $data = $request->all();
        
        $cdss = $this->cdssService->analyzeIntakeOutput($data);
        $data['alert'] = $cdss['alert'];

        $record->update($data);
        return response()->json(['message' => 'Intake and Output updated', 'data' => $record]);
    }
}
