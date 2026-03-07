<?php

namespace App\Http\Controllers\ADPIE;

use App\Http\Controllers\Controller;
use App\Models\NursingDiagnosis;
use App\Models\Patient;
use App\Models\Vitals;
use App\Models\PhysicalExam;
use App\Models\ActOfDailyLiving;
use App\Models\IntakeAndOutput;
use App\Models\LabValues;
use App\Services\NursingDiagnosisCdssService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdpieApiController extends Controller
{
    protected $nursingDiagnosisCdssService;

    public function __construct(NursingDiagnosisCdssService $nursingDiagnosisCdssService)
    {
        $this->nursingDiagnosisCdssService = $nursingDiagnosisCdssService;
    }

    /**
     * Get or Create a Nursing Diagnosis record for a specific assessment.
     * 
     * @param string $component (vital-signs, physical-exam, etc.)
     * @param int $id (The assessment record ID)
     */
    public function initialize(string $component, $id)
    {
        $idField = $this->getComponentIdField($component);
        $model = $this->getComponentModel($component);
        
        $assessment = $model::findOrFail($id);
        
        $nursingDiag = NursingDiagnosis::firstOrCreate(
            [$idField => $id],
            [
                'patient_id' => $assessment->patient_id,
                'diagnosis' => '',
                'planning' => '',
                'intervention' => '',
                'evaluation' => '',
            ]
        );

        return response()->json([
            'message' => 'ADPIE record initialized',
            'data' => $nursingDiag,
            'assessment' => $assessment
        ]);
    }

    /**
     * Analyze a single ADPIE field using CDSS.
     */
    public function analyze(Request $request)
    {
        try {
            $data = $request->validate([
                'fieldName' => 'required|string', // diagnosis, planning, intervention, evaluation
                'finding' => 'nullable|string',
                'component' => 'required|string', // vital-signs, physical-exam, etc.
            ]);

            $recommendation = null;
            $finding = $data['finding'] ?? '';
            $component = $data['component'];

            switch ($data['fieldName']) {
                case 'diagnosis':
                    $recommendation = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $finding);
                    break;
                case 'planning':
                    $recommendation = $this->nursingDiagnosisCdssService->analyzePlanning($component, $finding);
                    break;
                case 'intervention':
                    $recommendation = $this->nursingDiagnosisCdssService->analyzeIntervention($component, $finding);
                    break;
                case 'evaluation':
                    $recommendation = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $finding);
                    break;
            }

            if ($recommendation === null) {
                return response()->json([
                    'level' => 'INFO',
                    'message' => 'NO RECOMMENDATIONS',
                    'raw_message' => 'No findings.'
                ]);
            }

            return response()->json($recommendation);

        } catch (\Exception $e) {
            Log::error("[ADPIE API] Error in analyze: " . $e->getMessage());
            return response()->json(['error' => 'Analysis failed'], 500);
        }
    }

    /**
     * Analyze multiple fields at once.
     */
    public function analyzeBatch(Request $request)
    {
        try {
            $data = $request->validate([
                'batch' => 'required|array',
                'batch.*.fieldName' => 'required|string',
                'batch.*.finding' => 'nullable|string',
                'component' => 'required|string',
            ]);

            $results = [];
            foreach ($data['batch'] as $item) {
                $recommendation = null;
                $finding = $item['finding'] ?? '';
                $component = $data['component'];

                switch ($item['fieldName']) {
                    case 'diagnosis':
                        $recommendation = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $finding);
                        break;
                    case 'planning':
                        $recommendation = $this->nursingDiagnosisCdssService->analyzePlanning($component, $finding);
                        break;
                    case 'intervention':
                        $recommendation = $this->nursingDiagnosisCdssService->analyzeIntervention($component, $finding);
                        break;
                    case 'evaluation':
                        $recommendation = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $finding);
                        break;
                }

                $results[] = $recommendation ?? [
                    'level' => 'INFO',
                    'message' => 'NO RECOMMENDATIONS',
                    'raw_message' => 'No findings.'
                ];
            }

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error("[ADPIE API] Error in analyzeBatch: " . $e->getMessage());
            return response()->json(['error' => 'Batch analysis failed'], 500);
        }
    }

    /**
     * Update a specific ADPIE step.
     */
    public function updateStep(Request $request, $id, $step)
    {
        $nursingDiag = NursingDiagnosis::findOrFail($id);
        $finding = $request->input($step);
        $component = $request->input('component');

        if (!$component) {
            return response()->json(['error' => 'Component name is required'], 400);
        }

        $recommendation = null;
        switch ($step) {
            case 'diagnosis':
                $recommendation = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $finding);
                break;
            case 'planning':
                $recommendation = $this->nursingDiagnosisCdssService->analyzePlanning($component, $finding);
                break;
            case 'intervention':
                $recommendation = $this->nursingDiagnosisCdssService->analyzeIntervention($component, $finding);
                break;
            case 'evaluation':
                $recommendation = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $finding);
                break;
        }

        $alertMessage = $recommendation ? ($recommendation->raw_message ?? $recommendation->message) : 'No findings.';

        $nursingDiag->update([
            $step => $finding,
            $step . '_alert' => $alertMessage
        ]);

        return response()->json([
            'message' => ucfirst($step) . ' updated',
            'data' => $nursingDiag,
            'recommendation' => $recommendation
        ]);
    }

    private function getComponentIdField($component)
    {
        return [
            'physical-exam' => 'physical_exam_id',
            'intake-and-output' => 'intake_and_output_id',
            'lab-values' => 'lab_values_id',
            'adl' => 'adl_id',
            'vital-signs' => 'vital_signs_id',
        ][$component] ?? 'vital_signs_id';
    }

    private function getComponentModel($component)
    {
        return [
            'physical-exam' => PhysicalExam::class,
            'intake-and-output' => IntakeAndOutput::class,
            'lab-values' => LabValues::class,
            'adl' => ActOfDailyLiving::class,
            'vital-signs' => Vitals::class,
        ][$component] ?? Vitals::class;
    }
}
