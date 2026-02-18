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
        $this->getProcessData($component, $id);
        return redirect()->route('nursing-diagnosis.process', ['component' => $component, 'id' => $id]);
    }

    public function getProcessData(string $component, $id)
    {
        $intakeAndOutput = IntakeAndOutput::with('patient')->findOrFail($id);

        // Find the most recent diagnosis for this specific intake/output record
        $diagnosis = NursingDiagnosis::where('intake_and_output_id', $id)
            ->latest()
            ->first();

        $findings = session('findings', []);

        // Pre-calculate all 4 alerts
        $alerts = [
            'diagnosis' => $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $diagnosis->diagnosis ?? ''),
            'planning' => $this->nursingDiagnosisCdssService->analyzePlanning($component, $diagnosis->planning ?? ''),
            'intervention' => $this->nursingDiagnosisCdssService->analyzeIntervention($component, $diagnosis->intervention ?? ''),
            'evaluation' => $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $diagnosis->evaluation ?? ''),
        ];

        // Check if the alert object AND its message exist before stripping html tags
        foreach ($alerts as $key => $alert) {
            if ($alert && property_exists($alert, 'message')) {
                $alert->message = strip_tags($alert->message);
            }
        }

        // Put them in the session to persist across all pages
        session()->put('intake-and-output-alerts', $alerts);

        return [
            'intakeAndOutputId' => $intakeAndOutput->id,
            'patient' => $intakeAndOutput->patient,
            'component' => $component,
            'diagnosis' => $diagnosis,
            'findings' => $findings
        ];
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
        $diagnosisText = $request->input('diagnosis');

        // Use the analyzeDiagnosis method directly for the diagnosis step
        $diagnosisAlertObject = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $diagnosisText);
        $diagnosisAlert = $diagnosisAlertObject->raw_message ?? null;

        $nursingDiagnosis = NursingDiagnosis::updateOrCreate(
            [
                'intake_and_output_id' => $intakeAndOutputId,
            ],
            [
                'patient_id' => $patient->patient_id,
                'diagnosis' => $diagnosisText,
                'diagnosis_alert' => $diagnosisAlert,
                'planning' => '',
                'intervention' => '',
                'evaluation' => '',
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
        $diagnosis = NursingDiagnosis::findOrFail($nursingDiagnosisId);
        return redirect()->route('nursing-diagnosis.process', ['component' => $component, 'id' => $diagnosis->intake_and_output_id])
            ->with('current_step', 2);
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
        $planningText = $request->input('planning');

        // Use the analyzePlanning method directly for the planning step
        $planningAlertObject = $this->nursingDiagnosisCdssService->analyzePlanning($component, $planningText);
        $planningAlert = $planningAlertObject->raw_message ?? null;

        $diagnosis->update([
            'planning' => $planningText,
            'planning_alert' => $planningAlert,
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
        $diagnosis = NursingDiagnosis::findOrFail($nursingDiagnosisId);
        return redirect()->route('nursing-diagnosis.process', ['component' => $component, 'id' => $diagnosis->intake_and_output_id])
            ->with('current_step', 3);
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
        $interventionText = $request->input('intervention');

        // Use the analyzeIntervention method directly for the intervention step
        $interventionAlertObject = $this->nursingDiagnosisCdssService->analyzeIntervention($component, $interventionText);
        $interventionAlert = $interventionAlertObject->raw_message ?? null;

        $diagnosis->update([
            'intervention' => $interventionText,
            'intervention_alert' => $interventionAlert,
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
        $diagnosis = NursingDiagnosis::findOrFail($nursingDiagnosisId);
        return redirect()->route('nursing-diagnosis.process', ['component' => $component, 'id' => $diagnosis->intake_and_output_id])
            ->with('current_step', 4);
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
        $evaluationText = $request->input('evaluation');

        // Use the analyzeEvaluation method directly for the evaluation step
        $evaluationAlertObject = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $evaluationText);
        $evaluationAlert = $evaluationAlertObject->raw_message ?? null;

        $diagnosis->update([
            'evaluation' => $evaluationText,
            'evaluation_alert' => $evaluationAlert,
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

    public function storeProcess(Request $request, string $component, $id)
    {
        $intakeAndOutput = IntakeAndOutput::with('patient')->findOrFail($id);
        $patient = $intakeAndOutput->patient;

        $data = $request->validate([
            'diagnosis' => 'nullable|string|max:2000',
            'planning' => 'nullable|string|max:2000',
            'intervention' => 'nullable|string|max:2000',
            'evaluation' => 'nullable|string|max:2000',
        ]);

        $updateData = [
            'patient_id' => $patient->patient_id,
            'intake_and_output_id' => $id,
        ];

        foreach (['diagnosis', 'planning', 'intervention', 'evaluation'] as $step) {
            if ($request->has($step)) {
                $text = $request->input($step);
                $updateData[$step] = $text;

                $method = 'analyze' . ucfirst($step);
                $alertObject = $this->nursingDiagnosisCdssService->$method($component, $text ?? '');
                
                if ($alertObject && property_exists($alertObject, 'message')) {
                    $message = str_replace(['<li>', '</li>'], ['— ', "\n"], $alertObject->message);
                    $updateData[$step . '_alert'] = strip_tags($message);
                }
            }
        }

        NursingDiagnosis::updateOrCreate(
            ['intake_and_output_id' => $id],
            $updateData
        );

        if ($request->input('action') === 'save_and_proceed') {
             return redirect()->route('io.show')
                ->with('success', 'ADPIE process completed and saved!');
        }

        return redirect()->back()
            ->with('success', 'ADPIE process progress saved.')
            ->with('current_step', $request->input('current_step', 1));
    }
}