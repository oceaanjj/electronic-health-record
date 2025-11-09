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

    /**
     * Logic from
     */
    public function startDiagnosis(string $component, $id)
    {
        $physicalExam = PhysicalExam::with('patient')->findOrFail($id);

        // Find the most recent diagnosis for this specific exam
        $diagnosis = NursingDiagnosis::where('physical_exam_id', $id)
            ->latest() // Get the newest one
            ->first(); // Get one or null

        return view('adpie.physical-exam.diagnosis', [
            'physicalExamId' => $physicalExam->id,
            'patient' => $physicalExam->patient,
            'component' => $component,
            'diagnosis' => $diagnosis  // --- ADD THIS LINE ---
        ]);
    }

    /**
     * Logic from
     */
    public function storeDiagnosis(Request $request, string $component, $id)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);
        $physicalExamId = $id;

        $physicalExam = PhysicalExam::with('patient')->findOrFail($physicalExamId);
        $patient = $physicalExam->patient;

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

        $nurseInput = [
            'diagnosis' => $request->input('diagnosis'),
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
        ];

        $generatedRules = $this->nursingDiagnosisCdssService->generateNursingDiagnosisRules(
            $component, // 'physical-exam'
            $componentData,
            $nurseInput,
            $patient
        );

        $diagnosisAlert = $generatedRules['alerts'][0]['alert'] ?? null;
        $ruleFilePath = $generatedRules['rule_file_path'];

        $newDiagnosis = NursingDiagnosis::create([
            'physical_exam_id' => $physicalExamId,
            'patient_id' => $patient->patient_id, // Store patient ID for all
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

        // --- CHANGED THIS LINE ---
        return redirect()->back()
            ->with('success', 'Diagnosis saved.');
    }

    /**
     * Logic from
     */
    public function showPlanning(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient,
            'component' => $component
        ]);
    }

    /**
     * Logic from
     */
    public function storePlanning(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        $physicalExam = $diagnosis->physicalExam;
        $patient = $physicalExam->patient;

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

        // --- CHANGED THIS LINE ---
        return redirect()->back()
            ->with('success', 'Plan saved.');
    }

    /**
     * Logic from
     */
    public function showIntervention(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient,
            'component' => $component
        ]);
    }

    /**
     * Logic from
     */
    public function storeIntervention(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        $physicalExam = $diagnosis->physicalExam;
        $patient = $physicalExam->patient;

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

        // --- CHANGED THIS LINE ---
        return redirect()->back()
            ->with('success', 'Intervention saved.');
    }

    /**
     * Logic from
     */
    public function showEvaluation(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient,
            'component' => $component
        ]);
    }

    /**
     * Logic from
     */
    public function storeEvaluation(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        $physicalExam = $diagnosis->physicalExam;
        $patient = $physicalExam->patient;

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

        // --- CHANGED THIS LINE ---
        return redirect()->back()
            ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }
}