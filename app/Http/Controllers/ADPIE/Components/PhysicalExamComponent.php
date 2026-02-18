<?php

namespace App\Http\Controllers\ADPIE\Components;

use App\Http\Controllers\ADPIE\AdpieComponentInterface;
use App\Models\NursingDiagnosis;
use App\Models\PhysicalExam;
use App\Models\Patient;
use App\Services\NursingDiagnosisCdssService;
use Illuminate\Http\Request;

class PhysicalExamComponent implements AdpieComponentInterface
{
    protected $nursingDiagnosisCdssService;

    public function __construct(
        NursingDiagnosisCdssService $nursingDiagnosisCdssService
    ) {
        $this->nursingDiagnosisCdssService = $nursingDiagnosisCdssService;
    }

    public function startDiagnosis(string $component, $id)
    {
        $this->getProcessData($component, $id);
        return redirect()->route('nursing-diagnosis.process', ['component' => $component, 'id' => $id]);
    }

    public function getProcessData(string $component, $id)
    {
        $physicalExam = PhysicalExam::with('patient')->findOrFail($id);
        $diagnosis = NursingDiagnosis::where('physical_exam_id', $id)
            ->latest()
            ->first();

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
        session()->put('physical-exam-alerts', $alerts);

        return [
            'physicalExamId' => $physicalExam->id,
            'patient' => $physicalExam->patient,
            'component' => $component,
            'diagnosis' => $diagnosis
        ];
    }

    /**
     * Step 1: Store the Diagnosis.
     * $id MUST be the physical_exam_id
     */
    public function storeDiagnosis(Request $request, string $component, $id)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);
        $physicalExamId = $id;
        $physicalExam = PhysicalExam::with('patient')->findOrFail($physicalExamId);
        $patient = $physicalExam->patient;

        $diagnosisText = $request->input('diagnosis');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $diagnosisText);

        // Check if the object and property exist before stripping html tags
        $diagnosisAlert = null;
        if ($alertObject && property_exists($alertObject, 'message')) {
            $message = str_replace(['<li>', '</li>'], ['— ', "\n"], $alertObject->message);
            $diagnosisAlert = strip_tags($message);
        }

        $nursingDiagnosis = NursingDiagnosis::updateOrCreate(
            [
                'physical_exam_id' => $physicalExamId,
            ],
            [
                'patient_id' => $patient->patient_id,
                'diagnosis' => $diagnosisText,
                'diagnosis_alert' => $diagnosisAlert, // Now plain text or null
            ]
        );

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showPlanning', ['component' => $component, 'nursingDiagnosisId' => $nursingDiagnosis->id])
                ->with('success', 'Diagnosis saved. Now, please enter the plan.');
        }

        return redirect()->back()
            ->with('success', 'Diagnosis saved.');
    }

    public function showPlanning(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient,
            'component' => $component
        ]);
    }

    public function storePlanning(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);

        $planningText = $request->input('planning');

        $alertObject = $this->nursingDiagnosisCdssService->analyzePlanning($component, $planningText);

        // Check if the object and property exist before stripping html tags
        $planningAlert = null;
        if ($alertObject && property_exists($alertObject, 'message')) {
            $message = str_replace(['<li>', '</li>'], ['— ', "\n"], $alertObject->message);
            $planningAlert = strip_tags($message);
        }

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

    public function showIntervention(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::findOrFail($nursingDiagnosisId);
        return redirect()->route('nursing-diagnosis.process', ['component' => $component, 'id' => $diagnosis->physical_exam_id])
            ->with('current_step', 3);
    }

    public function storeIntervention(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);

        $interventionText = $request->input('intervention');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeIntervention($component, $interventionText);

        // Check if the object and property exist before stripping html tags
        $interventionAlert = null;
        if ($alertObject && property_exists($alertObject, 'message')) {
            $message = str_replace(['<li>', '</li>'], ['— ', "\n"], $alertObject->message);
            $interventionAlert = strip_tags($message);
        }

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

    public function showEvaluation(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::findOrFail($nursingDiagnosisId);
        return redirect()->route('nursing-diagnosis.process', ['component' => $component, 'id' => $diagnosis->physical_exam_id])
            ->with('current_step', 4);
    }

    public function storeEvaluation(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);

        $evaluationText = $request->input('evaluation');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $evaluationText);

        // Check if the object and property exist before stripping html tags
        $evaluationAlert = null;
        if ($alertObject && property_exists($alertObject, 'message')) {
            $message = str_replace(['<li>', '</li>'], ['— ', "\n"], $alertObject->message);
            $evaluationAlert = strip_tags($message);
        }

        $diagnosis->update([
            'evaluation' => $evaluationText,
            'evaluation_alert' => $evaluationAlert, // Now plain text or null
        ]);

        if ($request->input('action') == 'save_and_finish') {
            session()->forget('adpie_alerts'); // Clear the session alerts
            return redirect()->route('physical-exam.index')
                ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
        }

        return redirect()->back()
            ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }

    public function storeProcess(Request $request, string $component, $id)
    {
        $physicalExam = PhysicalExam::with('patient')->findOrFail($id);
        $patient = $physicalExam->patient;

        $data = $request->validate([
            'diagnosis' => 'nullable|string|max:2000',
            'planning' => 'nullable|string|max:2000',
            'intervention' => 'nullable|string|max:2000',
            'evaluation' => 'nullable|string|max:2000',
        ]);

        $updateData = [
            'patient_id' => $patient->patient_id,
            'physical_exam_id' => $id,
        ];

        foreach (['diagnosis', 'planning', 'intervention', 'evaluation'] as $step) {
            if ($request->has($step)) {
                $text = $request->input($step);
                $updateData[$step] = $text;

                // Analyze for alert
                $method = 'analyze' . ucfirst($step);
                $alertObject = $this->nursingDiagnosisCdssService->$method($component, $text ?? '');
                
                if ($alertObject && property_exists($alertObject, 'message')) {
                    $message = str_replace(['<li>', '</li>'], ['— ', "\n"], $alertObject->message);
                    $updateData[$step . '_alert'] = strip_tags($message);
                }
            }
        }

        $nursingDiagnosis = NursingDiagnosis::updateOrCreate(
            ['physical_exam_id' => $id],
            $updateData
        );

        if ($request->input('action') === 'save_and_proceed') {
             return redirect()->route('physical-exam.index')
                ->with('success', 'ADPIE process completed and saved!');
        }

        return redirect()->back()
            ->with('success', 'ADPIE process progress saved.')
            ->with('current_step', $request->input('current_step', 1));
    }
}