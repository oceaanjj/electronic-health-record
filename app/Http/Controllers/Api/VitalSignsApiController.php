<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vitals;
use App\Services\VitalCdssService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class VitalSignsApiController extends Controller
{
    protected $cdssService;

    public function __construct(VitalCdssService $cdssService)
    {
        $this->cdssService = $cdssService;
    }

    /**
     * Display patient's vitals history.
     */
    public function index(Request $request)
    {
        $patientId = $request->query('patient_id');
        if (!$patientId) return response()->json(['message' => 'patient_id required'], 400);

        $vitals = Vitals::where('patient_id', $patientId)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        return response()->json($vitals);
    }

    public function show($id)
    {
        return response()->json(Vitals::findOrFail($id));
    }

    /**
     * Submit Vitals
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $patientId = $data['patient_id'];
        $date = $data['date'] ?? now()->toDateString();
        $time = $data['time'] ?? '08:00';

        foreach ($data as $k => $v) { if ($v === '' || $v === null) $data[$k] = 'N/A'; }

        // Run CDSS
        $cdss = $this->cdssService->analyzeVitalsForAlerts($data);
        $data['alerts'] = $cdss['alert'];

        $record = Vitals::updateOrCreate(
            ['patient_id' => $patientId, 'date' => $date, 'time' => $time],
            $data
        );

        AuditLogController::log(
            'VITAL SIGNS RECORDED',
            "Nurse " . Auth::user()->username . " recorded new vital signs for patient ID: {$patientId} at {$time} on {$date}.",
            ['patient_id' => $patientId, 'record_id' => $record->id]
        );

        return response()->json(['message' => 'Vitals saved', 'data' => $record], 201);
    }

    public function update(Request $request, $id)
    {
        $record = Vitals::findOrFail($id);
        $data = $request->all();
        
        foreach ($data as $k => $v) { if ($v === '' || $v === null) $data[$k] = 'N/A'; }

        $cdss = $this->cdssService->analyzeVitalsForAlerts($data);
        $data['alerts'] = $cdss['alert'];

        $record->update($data);

        AuditLogController::log(
            'VITAL SIGNS UPDATED',
            "Nurse " . Auth::user()->username . " updated vital signs record (ID: {$id}) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id, 'record_id' => $id]
        );

        return response()->json(['message' => 'Vitals updated', 'data' => $record]);
    }
}
