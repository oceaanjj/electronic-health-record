<?php

namespace App\Http\Controllers\ADPIE\Components;

use App\Http\Controllers\ADPIE\AdpieComponentInterface;
use App\Models\NursingDiagnosis;
use App\Models\Patient;
use App\Services\NursingDiagnosisCdssService;
use App\Models\Vitals;
use Illuminate\Http\Request;

class VitalSignsComponent implements AdpieComponentInterface
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
            'temperature' => $request->input('temperature'),
            'hr' => $request->input('hr'),
            'rr' => $request->input('rr'),
            'bp' => $request->input('bp'),
            'spo2' => $request->input('spo2'),
        ];
    }

    public function startDiagnosis(string $component, $id)
    {
        $patient = Patient::findOrFail($id);

        $diagnosis = NursingDiagnosis::where('patient_id', $id)
            ->whereNull('physical_exam_id')
            ->latest()
            ->first();

        $findings = session('findings', []);

        return view('adpie.vital-signs.diagnosis', [
            'patient' => $patient,
            'findings' => $findings,
            'component' => $component,
            'diagnosis' => $diagnosis
        ]);
    }



    public function storeDiagnosis(Request $request, string $component, $id)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);

        $patient = Patient::findOrFail($id);

        $nurseInput = [
            'diagnosis' => $request->input('diagnosis'),
        ];

        $diagnosisText = $request->input('diagnosis');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $diagnosisText);


        $diagnosisAlert = null;
        if ($alertObject && property_exists($alertObject, 'message')) {
            $diagnosisAlert = strip_tags($alertObject->message);
        }


        $latestVitals = Vitals::where('patient_id', $patient->patient_id)->latest()->first();

        $nursingDiagnosis = NursingDiagnosis::updateOrCreate(
            [
                'vitals_id' => $latestVitals ? $latestVitals->id : null,
            ],
            [
                'patient_id' => $patient->patient_id,
                'diagnosis' => $nurseInput['diagnosis'],
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
        return view('adpie.vital-signs.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    public function storePlanning(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);

        $planningText = $request->input('planning');

        $alertObject = $this->nursingDiagnosisCdssService->analyzePlanning($component, $planningText);

        // Check if the object and property exist before stripping html tags
        $planningAlert = null;
        if ($alertObject && property_exists($alertObject, 'message')) {
            $planningAlert = strip_tags($alertObject->message);
        }

        $diagnosis->update([
            'planning' => $planningText,
            'planning_alert' => $planningAlert, // Now plain text or null
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
        return view('adpie.vital-signs.intervention', [
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

        $interventionText = $request->input('intervention');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeIntervention($component, $interventionText);

        // Check if the object and property exist before stripping html tags
        $interventionAlert = null;
        if ($alertObject && property_exists($alertObject, 'message')) {
            $interventionAlert = strip_tags($alertObject->message);
        }

        $diagnosis->update([
            'intervention' => $interventionText,
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
        return view('adpie.vital-signs.evaluation', [
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

        $evaluationText = $request->input('evaluation');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $evaluationText);

        // Check if the object and property exist before stripping html tags
        $evaluationAlert = null;
        if ($alertObject && property_exists($alertObject, 'message')) {
            $evaluationAlert = strip_tags($alertObject->message);
        }

        $diagnosis->update([
            'evaluation' => $evaluationText,
            'evaluation_alert' => $evaluationAlert, // Now plain text or null
        ]);

        return redirect()->route('vital-signs.show')
            ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }
}