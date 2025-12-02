<?php

namespace App\Http\Controllers\ADPIE\Components;

use App\Http\Controllers\ADPIE\AdpieComponentInterface;
use App\Models\IntakeAndOutput;
use App\Models\NursingDiagnosis;
use App\Models\Patient;
use App\Services\NursingDiagnosisCdssService;
use Illuminate\Http\Request;

class IntakeAndOutputComponent implements AdpieComponentInterface
{
    protected $nursingDiagnosisCdssService;

    public function __construct(
        NursingDiagnosisCdssService $nursingDiagnosisCdssService
    ) {
        $this->nursingDiagnosisCdssService = $nursingDiagnosisCdssService;
    }

    /**
     * Step 1: Show the Diagnosis form.
     */
    public function startDiagnosis(string $component, $id)
    {
        $intakeAndOutput = IntakeAndOutput::with('patient')->findOrFail($id);

        // Find the most recent diagnosis for this specific intake/output record
        $diagnosis = NursingDiagnosis::where('intake_and_output_id', $id)
            ->latest()
            ->first();

        $findings = session('findings', []);

        return view('adpie.intake-and-output.diagnosis', [
            'intakeAndOutputId' => $intakeAndOutput->id,
            'patient' => $intakeAndOutput->patient,
            'component' => $component,
            'diagnosis' => $diagnosis,
            'findings' => $findings
        ]);
    }

    /**
     * Step 1: Store the Diagnosis.
     */
    public function storeDiagnosis(Request $request, string $component, $id)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);
        $intakeAndOutputId = $id;

        $intakeAndOutput = IntakeAndOutput::with('patient')->findOrFail($intakeAndOutputId);
        $patient = $intakeAndOutput->patient;

        $componentData = [
            'oral_intake' => $intakeAndOutput->oral_intake,
            'iv_fluids_volume' => $intakeAndOutput->iv_fluids_volume,
            'urine_output' => $intakeAndOutput->urine_output,
        ];

        $nurseInput = [
            'diagnosis' => $request->input('diagnosis'),
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            $component, // 'intake-and-output'
            $componentData,
            $nurseInput,
            $patient
        );

        $diagnosisAlerts = collect($generatedRules['alerts'] ?? [])
            ->pluck('alert')
            ->filter()
            ->map(fn($alert) => '- ' . $alert)
            ->implode("\n");

        $diagnosisAlert = empty($diagnosisAlerts) ? null : $diagnosisAlerts;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $nursingDiagnosis = NursingDiagnosis::updateOrCreate(
            [
                'intake_and_output_id' => $intakeAndOutputId,
            ],
            [
                'patient_id' => $patient->patient_id,
                'diagnosis' => $nurseInput['diagnosis'],
                'diagnosis_alert' => $diagnosisAlert,
                'planning' => '',
                'intervention' => '',
                'evaluation' => '',
                'rule_file_path' => $ruleFilePath,
            ]
        );

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showPlanning', ['component' => $component, 'nursingDiagnosisId' => $nursingDiagnosis->id])
                ->with('success', 'Diagnosis saved. Now, please enter the plan.');
        }

        return redirect()->back()
            ->with('success', 'Diagnosis saved.');
    }

    /**
     * Step 2: Show the Planning form.
     */
    public function showPlanning(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('intakeAndOutput.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.intake-and-output.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->intakeAndOutput->patient,
            'component' => $component
        ]);
    }

    /**
     * Step 2: Store the Planning.
     */
    public function storePlanning(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('intakeAndOutput.patient')->findOrFail($nursingDiagnosisId);
        $intakeAndOutput = $diagnosis->intakeAndOutput;
        $patient = $intakeAndOutput->patient;

        $componentData = [
            'oral_intake' => $intakeAndOutput->oral_intake,
            'iv_fluids_volume' => $intakeAndOutput->iv_fluids_volume,
            'urine_output' => $intakeAndOutput->urine_output,
        ];

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

        $planningAlerts = collect($generatedRules['alerts'] ?? [])
            ->pluck('alert')
            ->filter()
            ->map(fn($alert) => '- ' . $alert)
            ->implode("\n");

        $planningAlert = empty($planningAlerts) ? null : $planningAlerts;
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

        return redirect()->back()
            ->with('success', 'Plan saved.');
    }

    /**
     * Step 3: Show the Intervention form.
     */
    public function showIntervention(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('intakeAndOutput.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.intake-and-output.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->intakeAndOutput->patient,
            'component' => $component
        ]);
    }

    /**
     * Step 3: Store the Intervention.
     */
    public function storeIntervention(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('intakeAndOutput.patient')->findOrFail($nursingDiagnosisId);
        $intakeAndOutput = $diagnosis->intakeAndOutput;
        $patient = $intakeAndOutput->patient;

        $componentData = [
            'oral_intake' => $intakeAndOutput->oral_intake,
            'iv_fluids_volume' => $intakeAndOutput->iv_fluids_volume,
            'urine_output' => $intakeAndOutput->urine_output,
        ];

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

        $interventionAlerts = collect($generatedRules['alerts'] ?? [])
            ->pluck('alert')
            ->filter()
            ->map(fn($alert) => '- ' . $alert)
            ->implode("\n");

        $interventionAlert = empty($interventionAlerts) ? null : $interventionAlerts;
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

        return redirect()->back()
            ->with('success', 'Intervention saved.');
    }

    /**
     * Step 4: Show the Evaluation form.
     */
    public function showEvaluation(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('intakeAndOutput.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.intake-and-output.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->intakeAndOutput->patient,
            'component' => $component
        ]);
    }

    /**
     * Step 4: Store the Evaluation.
     */
    public function storeEvaluation(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('intakeAndOutput.patient')->findOrFail($nursingDiagnosisId);
        $intakeAndOutput = $diagnosis->intakeAndOutput;
        $patient = $intakeAndOutput->patient;

        $componentData = [
            'oral_intake' => $intakeAndOutput->oral_intake,
            'iv_fluids_volume' => $intakeAndOutput->iv_fluids_volume,
            'urine_output' => $intakeAndOutput->urine_output,
        ];

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

        $evaluationAlerts = collect($generatedRules['alerts'] ?? [])
            ->pluck('alert')
            ->filter()
            ->map(fn($alert) => '- ' . $alert)
            ->implode("\n");

        $evaluationAlert = empty($evaluationAlerts) ? null : $evaluationAlerts;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $diagnosis->update([
            'evaluation' => $nurseInput['evaluation'],
            'evaluation_alert' => $evaluationAlert,
            'rule_file_path' => $ruleFilePath,
        ]);

        // Check which button was clicked
        if ($request->input('action') == 'save_and_finish') {
            // 'FINISH' button was clicked
            return redirect()->route('io.show')
                ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
        }

        // 'SUBMIT' button was clicked (or default 'save_and_exit')
        return redirect()->back()
            ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }
}