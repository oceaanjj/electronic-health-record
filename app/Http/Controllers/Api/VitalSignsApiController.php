<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vitals;
use App\Services\VitalCdssService;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        return response()->json(['message' => 'Vitals updated', 'data' => $record]);
    }
}
