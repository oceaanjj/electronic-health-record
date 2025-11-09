<?php

namespace App\Http\Controllers\ADPIE\Components;

use App\Http\Controllers\ADPIE\AdpieComponentInterface;
use App\Models\NursingDiagnosis;
use App\Models\Patient;
use App\Services\NursingDiagnosisCdssService;
use Illuminate\Http\Request;

class LabValuesComponent implements AdpieComponentInterface
{
    protected $nursingDiagnosisCdssService;

    public function __construct(
        NursingDiagnosisCdssService $nursingDiagnosisCdssService
    ) {
        $this->nursingDiagnosisCdssService = $nursingDiagnosisCdssService;
    }

    // Helper to get component data
    private function getComponentData(Request $request, Patient $patient)
    {
        // Placeholder: You must implement how to fetch the relevant lab values.
        return [
            'wbc' => $request->input('wbc'),
            'rbc' => $request->input('rbc'),
            'hgb' => $request->input('hgb'),
            // ... other lab values
        ];
    }

    /**
     * Step 1: Show the Diagnosis form.
     */
    public function startDiagnosis(string $component, $id)
    {
        $patient = Patient::findOrFail($id);

        // Find the latest "patient-level" diagnosis
        $diagnosis = NursingDiagnosis::where('patient_id', $id)
            ->whereNull('physical_exam_id')
            // ->whereNull('intake_and_output_id') // Add other foreign keys
            ->latest()
            ->first();

        $labValues = []; // Your original logic to fetch lab values

        return view('adpie.lab-values.diagnosis', [
            'patient' => $patient,
            'labValues' => $labValues,
            'component' => $component,
            'diagnosis' => $diagnosis // Pass the found diagnosis (or null)
        ]);
    }

    /**
     * Step 1: Store the Diagnosis.
     */
    public function storeDiagnosis(Request $request, string $component, $id)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);
        $patient = Patient::findOrFail($id);

        $componentData = $this->getComponentData($request, $patient);

        $nurseInput = [
            'diagnosis' => $request->input('diagnosis'),
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            $component, // 'lab-values'
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
            return redirect()->route('nursing-diagnosis.showPlanning', ['component' => $component, 'nursingDiagnosisId' => $newDiagnosis->id])
                ->with('success', 'Diagnosis saved. Now, please enter the plan.');
        }

        return redirect()->back()->with('success', 'Diagnosis saved.');
    }

    /**
     * Step 2: Show the Planning form.
     */
    public function showPlanning(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.lab-values.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    /**
     * Step 2: Store the Planning.
     */
    public function storePlanning(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        $componentData = $this->getComponentData($request, $patient);

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $request->input('planning'),
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            $component,
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
            return redirect()->route('nursing-diagnosis.showIntervention', ['component' => $component, 'nursingDiagnosisId' => $nursingDiagnosisId])
                ->with('success', 'Plan saved. Now, please enter interventions.');
        }

        return redirect()->back()->with('success', 'Plan saved.');
    }

    /**
     * Step 3: Show the Intervention form.
     */
    public function showIntervention(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.lab-values.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    /**
     * Step 3: Store the Intervention.
     */
    public function storeIntervention(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        $componentData = $this->getComponentData($request, $patient);

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $request->input('intervention'),
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            $component,
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
            return redirect()->route('nursing-diagnosis.showEvaluation', ['component' => $component, 'nursingDiagnosisId' => $nursingDiagnosisId])
                ->with('success', 'Intervention saved. Now, please enter evaluation.');
        }

        return redirect()->back()->with('success', 'Intervention saved.');
    }

    /**
     * Step 4: Show the Evaluation form.
     */
    public function showEvaluation(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.lab-values.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    /**
     * Step 4: Store the Evaluation.
     */
    public function storeEvaluation(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        $componentData = $this->getComponentData($request, $patient);

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $diagnosis->intervention,
            'evaluation' => $request->input('evaluation'),
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            $component,
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