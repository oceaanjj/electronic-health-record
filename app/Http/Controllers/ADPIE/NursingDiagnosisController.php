<?php

namespace App\Http\Controllers\ADPIE;

// Use all the services that your components will need
use App\Http\Controllers\Controller;
use App\Services\NursingDiagnosisCdssService;
use App\Services\PhysicalExamCdssService;
use App\Services\LabValuesCdssService;
use App\Services\VitalCdssService;
use App\Services\IntakeAndOutputCdssService;
use App\Services\ActOfDailyLivingCdssService;
use Illuminate\Http\Request;
use App\Models\NursingDiagnosis;
use App\Models\PhysicalExam; // For showByPatient
use App\Models\IntakeAndOutput; // For showByPatient
use Illuminate\Support\Facades\Log; // For error logging

// --- Import your new component classes ---
use App\Http\Controllers\ADPIE\Components\PhysicalExamComponent;
use App\Http\Controllers\ADPIE\Components\LabValuesComponent;
use App\Http\Controllers\ADPIE\Components\VitalSignsComponent;
use App\Http\Controllers\ADPIE\Components\IntakeAndOutputComponent;
use App\Http\Controllers\ADPIE\Components\ActOfDailyLivingComponent;


class NursingDiagnosisController extends Controller
{
    // Store all the services so we can pass them to the components
    protected $nursingDiagnosisCdssService;
    protected $physicalExamCdssService;
    protected $labValuesCdssService;
    protected $vitalCdssService;
    protected $intakeAndOutputCdssService;
    protected $actOfDailyLivingCdssService;

    public function __construct(
        NursingDiagnosisCdssService $nursingDiagnosisCdssService,
        PhysicalExamCdssService $physicalExamCdssService,
        LabValuesCdssService $labValuesCdssService,
        VitalCdssService $vitalCdssService,
        IntakeAndOutputCdssService $intakeAndOutputCdssService,
        ActOfDailyLivingCdssService $actOfDailyLivingCdssService
    ) {
        $this->nursingDiagnosisCdssService = $nursingDiagnosisCdssService;
        $this->physicalExamCdssService = $physicalExamCdssService;
        $this->labValuesCdssService = $labValuesCdssService;
        $this->vitalCdssService = $vitalCdssService;
        $this->intakeAndOutputCdssService = $intakeAndOutputCdssService;
        $this->actOfDailyLivingCdssService = $actOfDailyLivingCdssService;
    }

    /**
     * Factory method to get the correct component service.
     */
    private function getComponentService(string $componentName): AdpieComponentInterface
    {
        switch ($componentName) {
            case 'physical-exam':
                return new PhysicalExamComponent(
                    $this->nursingDiagnosisCdssService
                );
            case 'lab-values':
                return new LabValuesComponent(
                    $this->nursingDiagnosisCdssService
                );
            case 'vital-signs':
                return new VitalSignsComponent(
                    $this->nursingDiagnosisCdssService
                );
            case 'intake-and-output':
                return new IntakeAndOutputComponent(
                    $this->nursingDiagnosisCdssService
                );
            case 'act-of-daily-living':
                return new ActOfDailyLivingComponent(
                    $this->nursingDiagnosisCdssService
                );
            default:
                Log::error("Unsupported ADPIE component requested: $componentName");
                abort(404, "Unsupported ADPIE component: $componentName");
        }
    }

    //==================================================================
    // WIZARD "SHOW" METHODS
    //==================================================================

    public function startDiagnosis(string $component, $id)
    {
        return $this->getComponentService($component)->startDiagnosis($component, $id);
    }

    public function showPlanning(string $component, $nursingDiagnosisId)
    {
        return $this->getComponentService($component)->showPlanning($component, $nursingDiagnosisId);
    }

    public function showIntervention(string $component, $nursingDiagnosisId)
    {
        return $this->getComponentService($component)->showIntervention($component, $nursingDiagnosisId);
    }

    public function showEvaluation(string $component, $nursingDiagnosisId)
    {
        return $this->getComponentService($component)->showEvaluation($component, $nursingDiagnosisId);
    }

    //==================================================================
    // WIZARD "STORE" METHODS
    //==================================================================

    public function storeDiagnosis(Request $request, string $component, $id)
    {
        return $this->getComponentService($component)->storeDiagnosis($request, $component, $id);
    }

    public function storePlanning(Request $request, string $component, $nursingDiagnosisId)
    {
        return $this->getComponentService($component)->storePlanning($request, $component, $nursingDiagnosisId);
    }

    public function storeIntervention(Request $request, string $component, $nursingDiagnosisId)
    {
        return $this->getComponentService($component)->storeIntervention($request, $component, $nursingDiagnosisId);
    }

    public function storeEvaluation(Request $request, string $component, $nursingDiagnosisId)
    {
        return $this->getComponentService($component)->storeEvaluation($request, $component, $nursingDiagnosisId);
    }

    //==================================================================
    // WIZARD "CDSS" METHOD
    //==================================================================

    /**
     * This method is generic and doesn't need to be in a component.
     */
    public function analyzeDiagnosisField(Request $request)
    {
        try {
            // --- UPDATED VALIDATION ---
            $data = $request->validate([
                'fieldName' => 'required|string',
                'finding' => 'nullable|string',
                'patient_id' => 'nullable|exists:patients,patient_id',
                'component' => 'required|string', // --- ADD THIS ---
            ]);

            $recommendation = null;
            $finding = $data['finding'] ?? '';
            $patientId = $data['patient_id'] ?? null;
            $component = $data['component']; // --- ADD THIS ---

            // Route the analysis based on the field name
            switch ($data['fieldName']) {
                case 'diagnosis':
                    // --- UPDATE CALL ---
                    $recommendation = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $finding);
                    break;
                case 'planning':
                    // --- UPDATE CALL ---
                    $recommendation = $this->nursingDiagnosisCdssService->analyzePlanning($component, $finding);
                    break;
                case 'intervention':
                    // --- UPDATE CALL ---
                    $recommendation = $this->nursingDiagnosisCdssService->analyzeIntervention($component, $finding);
                    break;
                case 'evaluation':
                    // --- UPDATE CALL ---
                    $recommendation = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $finding);
                    break;
            }

            if ($recommendation === null) {
                return response()->json([
                    'level' => 'INFO',
                    'message' => '<span class="opacity-70 text-white font-semibold">No Recommendations</span>'
                ]);
            }

            return response()->json($recommendation);

        } catch (\Exception $e) {
            Log::error("Error in analyzeDiagnosisField: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'level' => 'CRITICAL',
                'message' => 'An internal server error occurred during analysis. Please check logs.'
            ], 500);
        }
    }

    //==================================================================
    // OTHER METHODS
    //==================================================================

    /**
     * This is also generic, so it's fine to keep here.
     */
    public function showByPatient($patientId)
    {
        $physicalExams = PhysicalExam::where('patient_id', $patientId)
            ->with('nursingDiagnoses')
            ->get();

        $intakeOutputs = IntakeAndOutput::where('patient_id', $patientId)
            ->with('nursingDiagnoses')
            ->get();

        $allNursingDiagnoses = NursingDiagnosis::whereHas('physicalExam', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })->orWhereHas('intakeAndOutput', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })->with(['physicalExam', 'intakeAndOutput'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('nursing-diagnosis.patient-all', compact('physicalExams', 'intakeOutputs', 'allNursingDiagnoses', 'patientId'));
    }
}