<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // <-- Includes the fix for the previous error
use App\Models\NursingDiagnosis;
use App\Models\PhysicalExam;
use App\Models\IntakeAndOutput;
use App\Models\Patient;
use App\Services\NursingDiagnosisCdssService; // <-- The "brain" for recommendations
use Illuminate\Http\Request;
use App\Services\PhysicalExamCdssService;
use App\Services\LabValuesCdssService;
use App\Services\VitalCdssService;
use App\Services\IntakeAndOutputCdssService;
use App\Services\ActOfDailyLivingCdssService;
use Illuminate\Support\Facades\Log; // Add this line

class NursingDiagnosisController extends Controller
{
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
    //==================================================================
    // ORIGINAL METHODS (FROM YOUR FIRST FILE)
    //==================================================================

    // Store DPIE for Physical Exam
    public function storeForPhysicalExam(Request $request, $physicalExamId)
    {
        $data = $request->validate([
            'diagnosis' => 'required|string',
            'planning' => 'required|string',
            'intervention' => 'required|string',
            'evaluation' => 'required|string',
        ]);

        $data['physical_exam_id'] = $physicalExamId;
        NursingDiagnosis::create($data);
        // return redirect()->back()->with('success', 'Nursing diagnosis for Physical Exam saved!');
    }

    // Store DPIE for Intake and Output
    public function storeForIntakeAndOutput(Request $request, $intakeAndOutputId)
    {
        $data = $request->validate([
            'diagnosis' => 'required|string',
            'planning' => 'required|string',
            'intervention' => 'required|string',
            'evaluation' => 'required|string',
        ]);

        $data['intake_and_output_id'] = $intakeAndOutputId;
        NursingDiagnosis::create($data);
        // return redirect()->back()->with('success', 'Nursing diagnosis for Intake/Output saved!');
    }

    // Show all DPIE for a physical exam
    public function showByPhysicalExam($physicalExamId)
    {
        $physicalExam = PhysicalExam::with('nursingDiagnoses')->findOrFail($physicalExamId);
        // return view('nursing-diagnosis.show', compact('physicalExam'));
    }

    // Show all  DPIE for an intake and output record
    public function showByIntakeAndOutput($intakeAndOutputId)
    {
        $intakeAndOutput = IntakeAndOutput::with('nursingDiagnoses')->findOrFail($intakeAndOutputId);
        // return view('nursing-diagnosis.show-intake-output', compact('intakeAndOutput'));
    }

    // Show all DPIE for a specific patient
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

        // return view('nursing-diagnosis.patient-all', compact('physicalExams', 'intakeOutputs', 'allNursingDiagnoses', 'patientId'));
    }


    //==================================================================
    // WIZARD "SHOW" METHODS
    //==================================================================

    /**
     * Step 1: Show the Diagnosis form.
     */
    public function startDiagnosis($physicalExamId)
    {
        $physicalExam = PhysicalExam::with('patient')->findOrFail($physicalExamId);
        return view('adpie.physical-exam.diagnosis', [
            'physicalExamId' => $physicalExam->id,
            'patient' => $physicalExam->patient
        ]);
    }

    /**
     * Step 2: Show the Planning form.
     */
    public function showPlanning($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient
        ]);
    }

    /**
     * Step 3: Show the Intervention form.
     */
    public function showIntervention($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient
        ]);
    }

    /**
     * Step 4: Show the Evaluation form.
     */
    public function showEvaluation($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient
        ]);
    }

    //==================================================================
    // WIZARD "STORE" METHODS (These save the data)
    //==================================================================

    /**
     * Step 1: Store the Diagnosis and CREATE the new record.
     */
    public function storeDiagnosis(Request $request, $physicalExamId)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);

        $physicalExam = PhysicalExam::with('patient')->findOrFail($physicalExamId);
        $patient = $physicalExam->patient;

        // Prepare component data (example: physical exam findings)
        // This would typically come from the PhysicalExam model or related data
        $componentData = [
            'general_appearance' => $physicalExam->general_appearance,
            'skin_condition' => $physicalExam->skin_condition,
            'eye_condition' => $physicalExam->eye_condition,
            'oral_condition' => $physicalExam->oral_condition,
            'cardiovascular' => $physicalExam->cardiovascular,
            'abdomen_condition' => $physicalExam->abdomen_condition,
            'extremities' => $physicalExam->extremities,
            'neurological' => $physicalExam->neurological,
        ];

        // Prepare nurse input for diagnosis step
        $nurseInput = [
            'diagnosis' => $request->input('diagnosis'),
            'planning' => '', // Will be filled in subsequent steps
            'intervention' => '',
            'evaluation' => '',
        ];

        // Generate comprehensive nursing diagnosis rules and alerts
        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'physical-exam',
            $componentData,
            $nurseInput,
            $patient
        );

        $diagnosisAlert = $generatedRules['alerts'][0]['alert'] ?? null; // Assuming the first alert is the primary diagnosis alert
        $ruleFilePath = $generatedRules['rule_file_path'];

        $newDiagnosis = NursingDiagnosis::create([
            'physical_exam_id' => $physicalExamId,
            'diagnosis' => $nurseInput['diagnosis'],
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
            'diagnosis_alert' => $diagnosisAlert, // Use the generated alert
            'rule_file_path' => $ruleFilePath, // Save the path to the generated rule file
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showPlanning', $newDiagnosis->id)
                ->with('success', 'Diagnosis saved. Now, please enter the plan.');
        }

        return redirect()->route('physical-exam.index')
            ->with('success', 'Diagnosis saved.');
    }

    /**
     * Step 2: Store the Planning by UPDATING the record.
     */
    public function storePlanning(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        $physicalExam = $diagnosis->physicalExam;
        $patient = $physicalExam->patient;

        // Prepare component data (example: physical exam findings)
        $componentData = [
            'general_appearance' => $physicalExam->general_appearance,
            'skin_condition' => $physicalExam->skin_condition,
            'eye_condition' => $physicalExam->eye_condition,
            'oral_condition' => $physicalExam->oral_condition,
            'cardiovascular' => $physicalExam->cardiovascular,
            'abdomen_condition' => $physicalExam->abdomen_condition,
            'extremities' => $physicalExam->extremities,
            'neurological' => $physicalExam->neurological,
        ];

        // Prepare nurse input for planning step (combine with existing diagnosis)
        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $request->input('planning'),
            'intervention' => '',
            'evaluation' => '',
        ];

        // Generate comprehensive nursing diagnosis rules and alerts
        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'physical-exam',
            $componentData,
            $nurseInput,
            $patient
        );

        $planningAlert = $generatedRules['alerts'][0]['alert'] ?? null; // Assuming the first alert is the primary planning alert
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'planning' => $nurseInput['planning'],
            'planning_alert' => $planningAlert, // Use the generated alert
            'rule_file_path' => $ruleFilePath, // Update the path to the generated rule file
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showIntervention', $nursingDiagnosisId)
                ->with('success', 'Plan saved. Now, please enter interventions.');
        }

        return redirect()->route('physical-exam.index')
            ->with('success', 'Plan saved.');
    }

    /**
     * Step 3: Store the Intervention by UPDATING the record.
     */
    public function storeIntervention(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        $physicalExam = $diagnosis->physicalExam;
        $patient = $physicalExam->patient;

        // Prepare component data (example: physical exam findings)
        $componentData = [
            'general_appearance' => $physicalExam->general_appearance,
            'skin_condition' => $physicalExam->skin_condition,
            'eye_condition' => $physicalExam->eye_condition,
            'oral_condition' => $physicalExam->oral_condition,
            'cardiovascular' => $physicalExam->cardiovascular,
            'abdomen_condition' => $physicalExam->abdomen_condition,
            'extremities' => $physicalExam->extremities,
            'neurological' => $physicalExam->neurological,
        ];

        // Prepare nurse input for intervention step (combine with existing diagnosis and planning)
        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $request->input('intervention'),
            'evaluation' => '',
        ];

        // Generate comprehensive nursing diagnosis rules and alerts
        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'physical-exam',
            $componentData,
            $nurseInput,
            $patient
        );

        $interventionAlert = $generatedRules['alerts'][0]['alert'] ?? null; // Assuming the first alert is the primary intervention alert
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'intervention' => $nurseInput['intervention'],
            'intervention_alert' => $interventionAlert, // Use the generated alert
            'rule_file_path' => $ruleFilePath, // Update the path to the generated rule file
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showEvaluation', $nursingDiagnosisId)
                ->with('success', 'Intervention saved. Now, please enter evaluation.');
        }

        return redirect()->route('physical-exam.index')
            ->with('success', 'Intervention saved.');
    }

    /**
     * Step 4: Store the Evaluation by UPDATING the record.
     */
    public function storeEvaluation(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        $physicalExam = $diagnosis->physicalExam;
        $patient = $physicalExam->patient;

        // Prepare component data (example: physical exam findings)
        $componentData = [
            'general_appearance' => $physicalExam->general_appearance,
            'skin_condition' => $physicalExam->skin_condition,
            'eye_condition' => $physicalExam->eye_condition,
            'oral_condition' => $physicalExam->oral_condition,
            'cardiovascular' => $physicalExam->cardiovascular,
            'abdomen_condition' => $physicalExam->abdomen_condition,
            'extremities' => $physicalExam->extremities,
            'neurological' => $physicalExam->neurological,
        ];

        // Prepare nurse input for evaluation step (combine with existing diagnosis, planning, and intervention)
        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $diagnosis->intervention,
            'evaluation' => $request->input('evaluation'),
        ];

        // Generate comprehensive nursing diagnosis rules and alerts
        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'physical-exam',
            $componentData,
            $nurseInput,
            $patient
        );

        $evaluationAlert = $generatedRules['alerts'][0]['alert'] ?? null; // Assuming the first alert is the primary evaluation alert
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'evaluation' => $nurseInput['evaluation'],
            'evaluation_alert' => $evaluationAlert, // Use the generated alert
            'rule_file_path' => $ruleFilePath, // Update the path to the generated rule file
        ]);

        return redirect()->route('physical-exam.index')
            ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }

    //==================================================================
    // WIZARD "CDSS" METHOD
    //==================================================================

    /**
     * Run CDSS analysis for any single field on the Nursing Diagnosis wizard.
     */
    public function analyzeDiagnosisField(Request $request)
    {
        // dd('analyzeDiagnosisField method reached!'); // You can uncomment this to test

        try {
            $data = $request->validate([
                'fieldName' => 'required|string',
                'finding' => 'nullable|string',
                'patient_id' => 'nullable|exists:patients,patient_id'
            ]);

            $recommendation = null;
            $finding = $data['finding'] ?? '';
            $patientId = $data['patient_id'] ?? null;

            // Route the analysis based on the field name
            switch ($data['fieldName']) {
                case 'diagnosis':
                    $recommendation = $this->nursingDiagnosisCdssService->analyzeDiagnosis($finding);
                    break;
                case 'planning':
                    $recommendation = $this->nursingDiagnosisCdssService->analyzePlanning($finding);
                    break;
                case 'intervention':
                    $recommendation = $this->nursingDiagnosisCdssService->analyzeIntervention($finding);
                    break;
                case 'evaluation':
                    $recommendation = $this->nursingDiagnosisCdssService->analyzeEvaluation($finding);
                    break;
            }

            // Return a default "no finding" object if no rule was triggered
            if ($recommendation === null) {
                return response()->json([
                    'level' => 'INFO', // Or any default
                    'message' => '<span class="opacity-70 text-white font-semibold">No Recommendations</span>'
                ]);
            }

            return response()->json($recommendation);

        } catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error("Error in analyzeDiagnosisField: " . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'level' => 'CRITICAL',
                'message' => 'An internal server error occurred during analysis. Please check logs.'
            ], 500);
        }
    }
    //==================================================================
    // LAB VALUES WIZARD "SHOW" METHODS
    //==================================================================

    /**
     * Step 1: Show the Diagnosis form for Lab Values.
     */
    public function startLabValuesDiagnosis(Patient $patient)
    {
        // You would typically fetch the latest lab values for this patient here
        // For now, we'll pass an empty array or a placeholder
        $labValues = []; // Replace with actual fetching logic
        return view('adpie.lab-values.diagnosis', [
            'patient' => $patient,
            'labValues' => $labValues,
        ]);
    }

    /**
     * Step 2: Show the Planning form for Lab Values.
     */
    public function showLabValuesPlanning($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId); // Assuming NursingDiagnosis can be directly linked to Patient
        return view('adpie.lab-values.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
        ]);
    }

    /**
     * Step 3: Show the Intervention form for Lab Values.
     */
    public function showLabValuesIntervention($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.lab-values.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
        ]);
    }

    /**
     * Step 4: Show the Evaluation form for Lab Values.
     */
    public function showLabValuesEvaluation($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.lab-values.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
        ]);
    }

    //==================================================================
    // LAB VALUES WIZARD "STORE" METHODS
    //==================================================================

    /**
     * Step 1: Store the Diagnosis for Lab Values and CREATE the new record.
     */
    public function storeLabValuesDiagnosis(Request $request, Patient $patient)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);

        // Fetch latest lab values for the patient
        // This is a placeholder. You'll need to implement how to get the relevant lab values.
        $componentData = [
            'wbc' => $request->input('wbc'), // Example: assuming these come from the form or a related model
            'rbc' => $request->input('rbc'),
            'hgb' => $request->input('hgb'),
            // ... other lab values
        ];

        $nurseInput = [
            'diagnosis' => $request->input('diagnosis'),
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'lab-values',
            $componentData,
            $nurseInput,
            $patient
        );

        $diagnosisAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $newDiagnosis = NursingDiagnosis::create([
            'patient_id' => $patient->patient_id, // Assuming patient_id is directly on NursingDiagnosis or can be linked
            'diagnosis' => $nurseInput['diagnosis'],
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
            'diagnosis_alert' => $diagnosisAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showLabValuesPlanning', $newDiagnosis->id)
                ->with('success', 'Diagnosis saved. Now, please enter the plan.');
        }

        return redirect()->back()->with('success', 'Diagnosis saved.');
    }

    /**
     * Step 2: Store the Planning for Lab Values by UPDATING the record.
     */
    public function storeLabValuesPlanning(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        // Fetch latest lab values for the patient
        $componentData = [
            'wbc' => $request->input('wbc'), // Example: assuming these come from the form or a related model
            'rbc' => $request->input('rbc'),
            'hgb' => $request->input('hgb'),
            // ... other lab values
        ];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $request->input('planning'),
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'lab-values',
            $componentData,
            $nurseInput,
            $patient
        );

        $planningAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'planning' => $nurseInput['planning'],
            'planning_alert' => $planningAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showLabValuesIntervention', $nursingDiagnosisId)
                ->with('success', 'Plan saved. Now, please enter interventions.');
        }

        return redirect()->back()->with('success', 'Plan saved.');
    }

    /**
     * Step 3: Store the Intervention for Lab Values by UPDATING the record.
     */
    public function storeLabValuesIntervention(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        // Fetch latest lab values for the patient
        $componentData = [
            'wbc' => $request->input('wbc'), // Example: assuming these come from the form or a related model
            'rbc' => $request->input('rbc'),
            'hgb' => $request->input('hgb'),
            // ... other lab values
        ];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $request->input('intervention'),
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'lab-values',
            $componentData,
            $nurseInput,
            $patient
        );

        $interventionAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'intervention' => $nurseInput['intervention'],
            'intervention_alert' => $interventionAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showLabValuesEvaluation', $nursingDiagnosisId)
                ->with('success', 'Intervention saved. Now, please enter evaluation.');
        }

        return redirect()->back()->with('success', 'Intervention saved.');
    }

    /**
     * Step 4: Store the Evaluation for Lab Values by UPDATING the record.
     */
    public function storeLabValuesEvaluation(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        // Fetch latest lab values for the patient
        $componentData = [
            'wbc' => $request->input('wbc'), // Example: assuming these come from the form or a related model
            'rbc' => $request->input('rbc'),
            'hgb' => $request->input('hgb'),
            // ... other lab values
        ];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $diagnosis->intervention,
            'evaluation' => $request->input('evaluation'),
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'lab-values',
            $componentData,
            $nurseInput,
            $patient
        );

        $evaluationAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'evaluation' => $nurseInput['evaluation'],
            'evaluation_alert' => $evaluationAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        return redirect()->back()->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }

    //==================================================================
    // VITAL SIGNS WIZARD "SHOW" METHODS
    //==================================================================

    /**
     * Step 1: Show the Diagnosis form for Vital Signs.
     */
    public function startVitalSignsDiagnosis(Patient $patient)
    {
        // You would typically fetch the latest vital signs for this patient here
        // For now, we'll pass an empty array or a placeholder
        $vitals = []; // Replace with actual fetching logic
        return view('adpie.vital-signs.diagnosis', [
            'patient' => $patient,
            'vitals' => $vitals,
        ]);
    }

    /**
     * Step 2: Show the Planning form for Vital Signs.
     */
    public function showVitalSignsPlanning($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.vital-signs.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
        ]);
    }

    /**
     * Step 3: Show the Intervention form for Vital Signs.
     */
    public function showVitalSignsIntervention($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.vital-signs.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
        ]);
    }

    /**
     * Step 4: Show the Evaluation form for Vital Signs.
     */
    public function showVitalSignsEvaluation($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.vital-signs.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
        ]);
    }

    //==================================================================
    // VITAL SIGNS WIZARD "STORE" METHODS
    //==================================================================

    /**
     * Step 1: Store the Diagnosis for Vital Signs and CREATE the new record.
     */
    public function storeVitalSignsDiagnosis(Request $request, Patient $patient)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);

        // Fetch latest vital signs for the patient
        $componentData = [
            'temperature' => $request->input('temperature'),
            'hr' => $request->input('hr'),
            'rr' => $request->input('rr'),
            'bp' => $request->input('bp'),
            'spo2' => $request->input('spo2'),
        ];

        $nurseInput = [
            'diagnosis' => $request->input('diagnosis'),
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'vital-signs',
            $componentData,
            $nurseInput,
            $patient
        );

        $diagnosisAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $newDiagnosis = NursingDiagnosis::create([
            'patient_id' => $patient->patient_id,
            'diagnosis' => $nurseInput['diagnosis'],
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
            'diagnosis_alert' => $diagnosisAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showVitalSignsPlanning', $newDiagnosis->id)
                ->with('success', 'Diagnosis saved. Now, please enter the plan.');
        }

        return redirect()->back()->with('success', 'Diagnosis saved.');
    }

    /**
     * Step 2: Store the Planning for Vital Signs by UPDATING the record.
     */
    public function storeVitalSignsPlanning(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        $componentData = [
            'temperature' => $request->input('temperature'),
            'hr' => $request->input('hr'),
            'rr' => $request->input('rr'),
            'bp' => $request->input('bp'),
            'spo2' => $request->input('spo2'),
        ];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $request->input('planning'),
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'vital-signs',
            $componentData,
            $nurseInput,
            $patient
        );

        $planningAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'planning' => $nurseInput['planning'],
            'planning_alert' => $planningAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showVitalSignsIntervention', $nursingDiagnosisId)
                ->with('success', 'Plan saved. Now, please enter interventions.');
        }

        return redirect()->back()->with('success', 'Plan saved.');
    }

    /**
     * Step 3: Store the Intervention for Vital Signs by UPDATING the record.
     */
    public function storeVitalSignsIntervention(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        $componentData = [
            'temperature' => $request->input('temperature'),
            'hr' => $request->input('hr'),
            'rr' => $request->input('rr'),
            'bp' => $request->input('bp'),
            'spo2' => $request->input('spo2'),
        ];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $request->input('intervention'),
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'vital-signs',
            $componentData,
            $nurseInput,
            $patient
        );

        $interventionAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'intervention' => $nurseInput['intervention'],
            'intervention_alert' => $interventionAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showVitalSignsEvaluation', $nursingDiagnosisId)
                ->with('success', 'Intervention saved. Now, please enter evaluation.');
        }

        return redirect()->back()->with('success', 'Intervention saved.');
    }

    /**
     * Step 4: Store the Evaluation for Vital Signs by UPDATING the record.
     */
    public function storeVitalSignsEvaluation(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        $componentData = [
            'temperature' => $request->input('temperature'),
            'hr' => $request->input('hr'),
            'rr' => $request->input('rr'),
            'bp' => $request->input('bp'),
            'spo2' => $request->input('spo2'),
        ];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $diagnosis->intervention,
            'evaluation' => $request->input('evaluation'),
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'vital-signs',
            $componentData,
            $nurseInput,
            $patient
        );

        $evaluationAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'evaluation' => $nurseInput['evaluation'],
            'evaluation_alert' => $evaluationAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        return redirect()->back()->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }


    //==================================================================
    // INTAKE AND OUTPUT WIZARD "SHOW" METHODS
    //==================================================================

    /**
     * Step 1: Show the Diagnosis form for Intake and Output.
     */
    public function startIntakeAndOutputDiagnosis(Patient $patient)
    {
        // You would typically fetch the latest intake and output data for this patient here
        $intakeOutputData = []; // Replace with actual fetching logic
        return view('adpie.intake-and-output.diagnosis', [
            'patient' => $patient,
            'intakeOutputData' => $intakeOutputData,
        ]);
    }

    /**
     * Step 2: Show the Planning form for Intake and Output.
     */
    public function showIntakeAndOutputPlanning($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.intake-and-output.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
        ]);
    }

    /**
     * Step 3: Show the Intervention form for Intake and Output.
     */
    public function showIntakeAndOutputIntervention($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.intake-and-output.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
        ]);
    }

    /**
     * Step 4: Show the Evaluation form for Intake and Output.
     */
    public function showIntakeAndOutputEvaluation($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.intake-and-output.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
        ]);
    }

    //==================================================================
    // INTAKE AND OUTPUT WIZARD "STORE" METHODS
    //==================================================================

    /**
     * Step 1: Store the Diagnosis for Intake and Output and CREATE the new record.
     */
    public function storeIntakeAndOutputDiagnosis(Request $request, Patient $patient)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);

        $componentData = [
            'oral_intake' => $request->input('oral_intake'),
            'iv_fluids_volume' => $request->input('iv_fluids_volume'),
            'urine_output' => $request->input('urine_output'),
        ];

        $nurseInput = [
            'diagnosis' => $request->input('diagnosis'),
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'intake-and-output',
            $componentData,
            $nurseInput,
            $patient
        );

        $diagnosisAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $newDiagnosis = NursingDiagnosis::create([
            'patient_id' => $patient->patient_id,
            'diagnosis' => $nurseInput['diagnosis'],
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
            'diagnosis_alert' => $diagnosisAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showIntakeAndOutputPlanning', $newDiagnosis->id)
                ->with('success', 'Diagnosis saved. Now, please enter the plan.');
        }

        return redirect()->back()->with('success', 'Diagnosis saved.');
    }

    /**
     * Step 2: Store the Planning for Intake and Output by UPDATING the record.
     */
    public function storeIntakeAndOutputPlanning(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        $componentData = [
            'oral_intake' => $request->input('oral_intake'),
            'iv_fluids_volume' => $request->input('iv_fluids_volume'),
            'urine_output' => $request->input('urine_output'),
        ];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $request->input('planning'),
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'intake-and-output',
            $componentData,
            $nurseInput,
            $patient
        );

        $planningAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'planning' => $nurseInput['planning'],
            'planning_alert' => $planningAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showIntakeAndOutputIntervention', $nursingDiagnosisId)
                ->with('success', 'Plan saved. Now, please enter interventions.');
        }

        return redirect()->back()->with('success', 'Plan saved.');
    }

    /**
     * Step 3: Store the Intervention for Intake and Output by UPDATING the record.
     */
    public function storeIntakeAndOutputIntervention(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        $componentData = [
            'oral_intake' => $request->input('oral_intake'),
            'iv_fluids_volume' => $request->input('iv_fluids_volume'),
            'urine_output' => $request->input('urine_output'),
        ];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $request->input('intervention'),
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'intake-and-output',
            $componentData,
            $nurseInput,
            $patient
        );

        $interventionAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'intervention' => $nurseInput['intervention'],
            'intervention_alert' => $interventionAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showIntakeAndOutputEvaluation', $nursingDiagnosisId)
                ->with('success', 'Intervention saved. Now, please enter evaluation.');
        }

        return redirect()->back()->with('success', 'Intervention saved.');
    }

    /**
     * Step 4: Store the Evaluation for Intake and Output by UPDATING the record.
     */
    public function storeIntakeAndOutputEvaluation(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        $componentData = [
            'oral_intake' => $request->input('oral_intake'),
            'iv_fluids_volume' => $request->input('iv_fluids_volume'),
            'urine_output' => $request->input('urine_output'),
        ];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $diagnosis->intervention,
            'evaluation' => $request->input('evaluation'),
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            'intake-and-output',
            $componentData,
            $nurseInput,
            $patient
        );

        $evaluationAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'evaluation' => $nurseInput['evaluation'],
            'evaluation_alert' => $evaluationAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        return redirect()->back()->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }
}