<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PhysicalExam;
use App\Services\PhysicalExamCdssService;
use Illuminate\Http\Request;

class PhysicalExamApiController extends Controller
{
    protected $cdssService;

    public function __construct(PhysicalExamCdssService $cdssService)
    {
        $this->cdssService = $cdssService;
    }

    /**
     * Maps CDSS service output keys → actual DB column names in physical_exams.
     * The CDSS returns e.g. "skin_condition_alert" but the DB column is "skin_alert".
     */
    private const ALERT_KEY_MAP = [
        'general_appearance_alert' => 'general_appearance_alert',
        'skin_condition_alert'     => 'skin_alert',
        'eye_condition_alert'      => 'eye_alert',
        'oral_condition_alert'     => 'oral_alert',
        'cardiovascular_alert'     => 'cardiovascular_alert',
        'abdomen_condition_alert'  => 'abdomen_alert',
        'extremities_alert'        => 'extremities_alert',
        'neurological_alert'       => 'neurological_alert',
    ];

    public function index(Request $request)
    {
        $patientId = $request->query('patient_id');
        if (!$patientId) {
            return response()->json(['message' => 'patient_id required'], 400);
        }
        return response()->json(PhysicalExam::where('patient_id', $patientId)->latest()->get());
    }

    public function show($id)
    {
        return response()->json(PhysicalExam::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'         => 'required|exists:patients,patient_id',
            'general_appearance' => 'nullable|string',
            'skin_condition'     => 'nullable|string',
            'eye_condition'      => 'nullable|string',
            'oral_condition'     => 'nullable|string',
            'cardiovascular'     => 'nullable|string',
            'abdomen_condition'  => 'nullable|string',
            'extremities'        => 'nullable|string',
            'neurological'       => 'nullable|string',
        ]);

        $alerts = $this->resolveAlerts($data);

        foreach ($data as $k => $v) {
            if ($v === '' || $v === null) $data[$k] = 'N/A';
        }

        $record = PhysicalExam::updateOrCreate(
            ['patient_id' => $data['patient_id'], 'created_at' => now()->toDateString()],
            array_merge($data, $alerts)
        );

        return response()->json([
            'message' => 'Physical exam saved',
            'data'    => $record,
            'alerts'  => $alerts,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $record = PhysicalExam::findOrFail($id);

        $data = $request->validate([
            'general_appearance' => 'nullable|string',
            'skin_condition'     => 'nullable|string',
            'eye_condition'      => 'nullable|string',
            'oral_condition'     => 'nullable|string',
            'cardiovascular'     => 'nullable|string',
            'abdomen_condition'  => 'nullable|string',
            'extremities'        => 'nullable|string',
            'neurological'       => 'nullable|string',
        ]);

        $alerts = $this->resolveAlerts($data);

        foreach ($data as $k => $v) {
            if ($v === '' || $v === null) $data[$k] = 'N/A';
        }

        $record->update(array_merge($data, $alerts));

        return response()->json([
            'message' => 'Physical exam updated',
            'data'    => $record,
            'alerts'  => $alerts,
        ]);
    }

    /**
     * Returns only the physical exam alert fields for a patient's latest record.
     * This is scoped to physical exam alerts only — no ADPIE / nursing diagnosis data.
     */
    public function alerts($patient_id)
    {
        $record = PhysicalExam::where('patient_id', $patient_id)->latest()->first();

        if (!$record) {
            return response()->json(['message' => 'No physical exam record found for this patient.'], 404);
        }

        return response()->json([
            'patient_id'  => $record->patient_id,
            'exam_id'     => $record->id,
            'recorded_at' => $record->updated_at,
            'alerts' => [
                'eye'                => $record->eye_alert                ?? 'No Findings',
                'skin'               => $record->skin_alert               ?? 'No Findings',
                'oral'               => $record->oral_alert               ?? 'No Findings',
                'abdomen'            => $record->abdomen_alert            ?? 'No Findings',
                'general_appearance' => $record->general_appearance_alert ?? 'No Findings',
                'cardiovascular'     => $record->cardiovascular_alert     ?? 'No Findings',
                'extremities'        => $record->extremities_alert        ?? 'No Findings',
                'neurological'       => $record->neurological_alert       ?? 'No Findings',
            ],
        ]);
    }

    /**
     * Runs CDSS analysis on finding fields and maps the output to the correct
     * DB column names. Only generates physical exam alerts — not ADPIE diagnoses.
     */
    private function resolveAlerts(array $data): array
    {
        $cdssOutput = $this->cdssService->analyzeFindings($data);

        $mapped = [];
        foreach (self::ALERT_KEY_MAP as $cdssKey => $dbColumn) {
            $mapped[$dbColumn] = $cdssOutput[$cdssKey] ?? 'No Findings';
        }

        return $mapped;
    }
}
