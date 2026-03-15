<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActOfDailyLiving;
use App\Services\ActOfDailyLivingCdssService;
use Illuminate\Http\Request;

use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class AdlApiController extends Controller
{
    protected $cdssService;
    public function __construct(ActOfDailyLivingCdssService $cdssService) { $this->cdssService = $cdssService; }

    public function index(Request $request)
    {
        $patientId = $request->query('patient_id');
        if (!$patientId) return response()->json(['message' => 'patient_id required'], 400);
        return response()->json(ActOfDailyLiving::where('patient_id', $patientId)->latest()->get());
    }

    public function show($id)
    {
        return response()->json(ActOfDailyLiving::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $cdssAlerts = $this->cdssService->analyzeFindings($data);
        foreach ($cdssAlerts as $f => $r) { $data[str_replace('_assessment', '_alert', $f)] = $r['alert']; }
        foreach ($data as $k => $v) { if ($v === '' || $v === null) $data[$k] = 'N/A'; }

        $record = ActOfDailyLiving::updateOrCreate(
            ['patient_id' => $data['patient_id'], 'date' => $data['date'] ?? now()->toDateString()],
            $data
        );

        AuditLogController::log(
            'ADL RECORD CREATED',
            "Nurse " . Auth::user()->username . " created a new Activities of Daily Living (ADL) record for patient ID: " . $data['patient_id'] . ".",
            ['patient_id' => $data['patient_id'], 'record_id' => $record->id]
        );

        return response()->json(['message' => 'ADL record saved', 'data' => $record], 201);
    }

    public function update(Request $request, $id)
    {
        $record = ActOfDailyLiving::findOrFail($id);
        $data = $request->all();
        
        $cdssAlerts = $this->cdssService->analyzeFindings($data);
        foreach ($cdssAlerts as $f => $r) { $data[str_replace('_assessment', '_alert', $f)] = $r['alert']; }
        foreach ($data as $k => $v) { if ($v === '' || $v === null) $data[$k] = 'N/A'; }

        $record->update($data);

        AuditLogController::log(
            'ADL RECORD UPDATED',
            "Nurse " . Auth::user()->username . " updated the ADL record (ID: {$id}) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id, 'record_id' => $id]
        );

        return response()->json(['message' => 'ADL updated', 'data' => $record]);
    }
}
