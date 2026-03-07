<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhysicalExam;
use App\Services\PhysicalExamCdssService;
use Illuminate\Http\Request;

class PhysicalExamApiController extends Controller
{
    protected $cdssService;
    public function __construct(PhysicalExamCdssService $cdssService) { $this->cdssService = $cdssService; }

    public function index(Request $request)
    {
        $patientId = $request->query('patient_id');
        if (!$patientId) return response()->json(['message' => 'patient_id required'], 400);
        return response()->json(PhysicalExam::where('patient_id', $patientId)->latest()->get());
    }

    public function show($id)
    {
        return response()->json(PhysicalExam::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $cdssAlerts = $this->cdssService->analyzeFindings($data);
        foreach ($cdssAlerts as $f => $v) { $data[$f] = $v; }
        foreach ($data as $k => $v) { if ($v === '' || $v === null) $data[$k] = 'N/A'; }
        
        $record = PhysicalExam::updateOrCreate(
            ['patient_id' => $data['patient_id'], 'created_at' => now()->toDateString()],
            $data
        );
        return response()->json(['message' => 'Physical exam saved', 'data' => $record], 201);
    }

    public function update(Request $request, $id)
    {
        $record = PhysicalExam::findOrFail($id);
        $data = $request->all();
        
        $cdssAlerts = $this->cdssService->analyzeFindings($data);
        foreach ($cdssAlerts as $f => $v) { $data[$f] = $v; }
        foreach ($data as $k => $v) { if ($v === '' || $v === null) $data[$k] = 'N/A'; }

        $record->update($data);
        return response()->json(['message' => 'Physical exam updated', 'data' => $record]);
    }
}
