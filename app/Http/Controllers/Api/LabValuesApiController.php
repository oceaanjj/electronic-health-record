<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LabValues;
use App\Models\Patient;
use App\Services\LabValuesCdssService;
use Illuminate\Http\Request;

class LabValuesApiController extends Controller
{
    protected $cdssService;

    public function __construct(LabValuesCdssService $cdssService)
    {
        $this->cdssService = $cdssService;
    }

    public function index(Request $request)
    {
        $patientId = $request->query('patient_id');
        if (!$patientId) return response()->json(['message' => 'patient_id required'], 400);
        return response()->json(LabValues::where('patient_id', $patientId)->latest()->get());
    }

    public function show($id)
    {
        return response()->json(LabValues::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $patient = Patient::where('patient_id', $data['patient_id'])->first();
        $ageGroup = $this->cdssService->getAgeGroup($patient);
        
        // Run CDSS
        $cdssAlerts = $this->cdssService->runLabCdss((object)$data, $ageGroup);
        foreach ($cdssAlerts as $p => $a) {
            $data[str_replace('_alerts', '_alert', $p)] = $a[0]['text'] ?? 'No findings.';
        }

        // Prevent duplicates (assuming one lab record per patient for now, matching web logic)
        $record = LabValues::updateOrCreate(
            ['patient_id' => $data['patient_id']],
            $data
        );

        return response()->json(['message' => 'Lab Values saved', 'data' => $record], 201);
    }

    public function update(Request $request, $id)
    {
        $record = LabValues::findOrFail($id);
        $data = $request->all();
        
        $patient = Patient::where('patient_id', $record->patient_id)->first();
        $ageGroup = $this->cdssService->getAgeGroup($patient);
        
        $cdssAlerts = $this->cdssService->runLabCdss((object)$data, $ageGroup);
        foreach ($cdssAlerts as $p => $a) {
            $data[str_replace('_alerts', '_alert', $p)] = $a[0]['text'] ?? 'No findings.';
        }

        $record->update($data);
        return response()->json(['message' => 'Lab Values updated', 'data' => $record]);
    }
}
