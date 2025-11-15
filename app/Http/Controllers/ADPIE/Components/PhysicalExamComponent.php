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
        $physicalExam = PhysicalExam::with('patient')->findOrFail($id);
        $diagnosis = NursingDiagnosis::where('physical_exam_id', $id)
            ->latest()
            ->first();

        return view('adpie.physical-exam.diagnosis', [
            'physicalExamId' => $physicalExam->id,
            'patient' => $physicalExam->patient,
            'component' => $component,
            'diagnosis' => $diagnosis
        ]);
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

        // --- THIS IS THE FIX ---
        // Get alert by analyzing the DIAGNOSIS text
        $alertObject = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $diagnosisText);
        $diagnosisAlert = $alertObject->raw_message ?? null;
        // --- END OF FIX ---

        $nursingDiagnosis = NursingDiagnosis::updateOrCreate(
            [
                'physical_exam_id' => $physicalExamId,
            ],
            [
                'patient_id' => $patient->patient_id,
                'diagnosis' => $diagnosisText,
                'diagnosis_alert' => $diagnosisAlert,
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
        $planningAlert = $alertObject->raw_message ?? null;

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
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient,
            'component' => $component
        ]);
    }

    public function storeIntervention(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);

        $interventionText = $request->input('intervention');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeIntervention($component, $interventionText);
        $interventionAlert = $alertObject->raw_message ?? null;

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
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient,
            'component' => $component
        ]);
    }

    public function storeEvaluation(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);

        $evaluationText = $request->input('evaluation');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $evaluationText);
        $evaluationAlert = $alertObject->raw_message ?? null;

        $diagnosis->update([
            'evaluation' => $evaluationText,
            'evaluation_alert' => $evaluationAlert,
        ]);

        if ($request->input('action') == 'save_and_finish') {
            return redirect()->route('physical-exam.index')
                ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
        }

        return redirect()->back()
            ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }
}