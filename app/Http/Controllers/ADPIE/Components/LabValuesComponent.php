<?php

namespace App\Http\Controllers\ADPIE\Components;

use App\Http\Controllers\ADPIE\AdpieComponentInterface;
use App\Models\NursingDiagnosis;
use App\Models\Patient;
use App\Services\NursingDiagnosisCdssService;
use Illuminate\Http\Request;
use App\Models\LabValues;
use Illuminate\Support\Facades\Log;

class LabValuesComponent implements AdpieComponentInterface
{
    protected $nursingDiagnosisCdssService;

    public function __construct(
        NursingDiagnosisCdssService $nursingDiagnosisCdssService
    ) {
        $this->nursingDiagnosisCdssService = $nursingDiagnosisCdssService;
    }

    private function getComponentData(Request $request, Patient $patient)
    {
        return [
            'wbc' => $request->input('wbc'),
            'rbc' => $request->input('rbc'),
            'hgb' => $request->input('hgb'),
        ];
    }

    public function startDiagnosis(string $component, $id)
    {
        Log::info('LabValuesComponent@startDiagnosis: Starting diagnosis for id: ' . $id);
        $labValues = LabValues::with('patient')->find($id); 

        // Temporarily dump $labValues to inspect its content
        // dd($labValues); 

        if (!$labValues) {
            Log::error('LabValuesComponent@startDiagnosis: LabValues record not found for id: ' . $id);
            abort(404, 'LabValues record not found.');
        }
        Log::info('LabValuesComponent@startDiagnosis: Found lab values record.', ['lab_values_id' => $id]);

        $patient = $labValues->patient;

        if (!$patient) {
            Log::error('LabValuesComponent@startDiagnosis: Patient not found for lab_values_id: ' . $id);
            abort(404, 'Patient not found for the given lab values.');
        }
        Log::info('LabValuesComponent@startDiagnosis: Found patient.', ['patient_id' => $patient->patient_id]);

        $diagnosis = NursingDiagnosis::where('lab_values_id', $id)
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
        if ($alerts['diagnosis'] && property_exists($alerts['diagnosis'], 'message')) {
            $alerts['diagnosis']->message = strip_tags($alerts['diagnosis']->message);
        }
        if ($alerts['planning'] && property_exists($alerts['planning'], 'message')) {
            $alerts['planning']->message = strip_tags($alerts['planning']->message);
        }
        if ($alerts['intervention'] && property_exists($alerts['intervention'], 'message')) {
            $alerts['intervention']->message = strip_tags($alerts['intervention']->message);
        }
        if ($alerts['evaluation'] && property_exists($alerts['evaluation'], 'message')) {
            $alerts['evaluation']->message = strip_tags($alerts['evaluation']->message);
        }

        // Put them in the session to persist across all pages
        session()->put('lab-values-alerts', $alerts);

        return view('adpie.lab-values.diagnosis', [
            'patient' => $patient,
            'labValues' => $labValues,
            'component' => $component,
            'diagnosis' => $diagnosis,
            'findings' => $findings
        ]);
    }

    public function storeDiagnosis(Request $request, string $component, $id)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);
        
        $labValues = LabValues::findOrFail($id); // $id is lab_values_id
        $patient = $labValues->patient;
        $diagnosisText = $request->input('diagnosis');
        
        // Get alert by analyzing the diagnosis text
        $alertObject = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $diagnosisText);
        $diagnosisAlert = $alertObject->raw_message ?? null;

        $nursingDiagnosis = NursingDiagnosis::updateOrCreate(
            [
                'lab_values_id' => $id,
            ],
            [
                'patient_id' => $patient->patient_id,
                'diagnosis' => $diagnosisText,
                'diagnosis_alert' => $diagnosisAlert,
            ]
        );

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showPlanning', [
                'component' => $component,
                'nursingDiagnosisId' => $nursingDiagnosis->id
            ])->with('success', 'Diagnosis saved. Now, please enter the plan.');
        }

        return redirect()->back()->with('success', 'Diagnosis saved.');
    }

    public function showPlanning(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.lab-values.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    public function storePlanning(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        // Fetch the LabValues record for the patient
        $labValues = LabValues::where('patient_id', $patient->patient_id)->first();
        $componentData = $labValues ? $labValues->toArray() : [];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $request->input('planning'),
            'intervention' => $diagnosis->intervention,
            'evaluation' => $diagnosis->evaluation,
        ];

        $alertObject = $this->nursingDiagnosisCdssService->analyzePlanning($component, $request->input('planning'));
        $planningAlert = $alertObject->raw_message ?? null;

        $diagnosis->update([
            'planning' => $request->input('planning'),
            'planning_alert' => $planningAlert,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showIntervention', ['component' => $component, 'nursingDiagnosisId' => $nursingDiagnosisId])
                ->with('success', 'Plan saved. Now, please enter interventions.');
        }

        return redirect()->back()->with('success', 'Plan saved.');
    }

    public function showIntervention(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.lab-values.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    public function storeIntervention(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        // Fetch the LabValues record for the patient
        $labValues = LabValues::where('patient_id', $patient->patient_id)->first();
        $componentData = $labValues ? $labValues->toArray() : [];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $request->input('intervention'),
            'evaluation' => $diagnosis->evaluation,
        ];

        $alertObject = $this->nursingDiagnosisCdssService->analyzeIntervention($component, $request->input('intervention'));
        $interventionAlert = $alertObject->raw_message ?? null;

        $diagnosis->update([
            'intervention' => $request->input('intervention'),
            'intervention_alert' => $interventionAlert,
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showEvaluation', ['component' => $component, 'nursingDiagnosisId' => $nursingDiagnosisId])
                ->with('success', 'Intervention saved. Now, please enter evaluation.');
        }

        return redirect()->back()->with('success', 'Intervention saved.');
    }

    public function showEvaluation(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.lab-values.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    public function storeEvaluation(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        $patient = $diagnosis->patient;

        // Fetch the LabValues record for the patient
        $labValues = LabValues::where('patient_id', $patient->patient_id)->first();
        $componentData = $labValues ? $labValues->toArray() : [];

        $nurseInput = [
            'diagnosis' => $diagnosis->diagnosis,
            'planning' => $diagnosis->planning,
            'intervention' => $diagnosis->intervention,
            'evaluation' => $request->input('evaluation'),
        ];

        $alertObject = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $request->input('evaluation'));
        $evaluationAlert = $alertObject->raw_message ?? null;

        $diagnosis->update([
            'evaluation' => $request->input('evaluation'),
            'evaluation_alert' => $evaluationAlert,
        ]);

        if ($request->input('action') == 'save_and_finish') {
            return redirect()->route('lab-values.index')
                ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
        }

        return redirect()->back()->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }
}