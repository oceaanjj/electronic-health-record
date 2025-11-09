<?php

namespace App\Http\Controllers\ADPIE\Components;

use App\Http\Controllers\ADPIE\AdpieComponentInterface;
use App\Models\NursingDiagnosis;
use App\Models\Patient;
use App\Services\NursingDiagnosisCdssService;
use Illuminate\Http\Request;

class VitalSignsComponent implements AdpieComponentInterface
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
        return [
            'temperature' => $request->input('temperature'),
            'hr' => $request->input('hr'),
            'rr' => $request->input('rr'),
            'bp' => $request->input('bp'),
            'spo2' => $request->input('spo2'),
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

        $vitals = []; // Your original logic to fetch vitals

        return view('adpie.vital-signs.diagnosis', [
            'patient' => $patient,
            'vitals' => $vitals,
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
            $component, // 'vital-signs'
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
        return view('adpie.vital-signs.planning', [
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
        return view('adpie.vital-signs.intervention', [
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
        return view('adpie.vital-signs.evaluation', [
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