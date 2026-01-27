<?php

namespace App\Http\Controllers\ADPIE\Components;

use App\Http\Controllers\ADPIE\AdpieComponentInterface;
use App\Models\NursingDiagnosis;
use App\Models\Patient;
use App\Services\NursingDiagnosisCdssService;
use App\Models\ActOfDailyLiving;
use Illuminate\Http\Request;

class ActOfDailyLivingComponent implements AdpieComponentInterface
{
    protected $nursingDiagnosisCdssService;

    public function __construct(
        NursingDiagnosisCdssService $nursingDiagnosisCdssService
    ) {
        $this->nursingDiagnosisCdssService = $nursingDiagnosisCdssService;
    }

    /**
     * Prepare component data if needed (placeholder for future use).
     */
    private function getComponentData(Request $request, Patient $patient)
    {
        // You can extract ADL-specific context here if needed by the CDSS
        return [
            // e.g. admission_date => $patient->admission_date
        ];
    }

    /**
     * Show the Diagnosis form. $id is patient id for ADL component.
     */
   public function startDiagnosis(string $component, $adlId)
    {
        $adlData = ActOfDailyLiving::findOrFail($adlId); // Find ADL record by its ID
        $patient = Patient::findOrFail($adlData->patient_id); // Get patient from ADL record

        $diagnosis = NursingDiagnosis::where('adl_id', $adlId) // Query by adl_id
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
        session()->put('act-of-daily-living-alerts', $alerts);

        return view('adpie.adl.diagnosis', [
            'patient' => $patient,
            'adlData' => $adlData,
            'component' => $component,
            'diagnosis' => $diagnosis,
            'findings' => $findings
        ]);
    }

    public function storeDiagnosis(Request $request, string $component, $id)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);
        
        $adl = ActOfDailyLiving::findOrFail($id); // $id is adl_id
        $patient = $adl->patient; // Get patient from the relationship

        $diagnosisText = $request->input('diagnosis');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeDiagnosis($component, $diagnosisText);
        $diagnosisAlert = $alertObject->raw_message ?? null;

        $nursingDiagnosis = NursingDiagnosis::updateOrCreate(
            [
                'adl_id' => $id,
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

    /**
     * Show Planning (STEP 2)
     */
    public function showPlanning(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.adl.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    /**
     * Store Planning (STEP 2)
     */
    public function storePlanning(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);

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

        return redirect()->back()->with('success', 'Plan saved.');
    }

    /**
     * Show Intervention (STEP 3)
     */
    public function showIntervention(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.adl.interventions', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    /**
     * Store Intervention (STEP 3)
     */
    public function storeIntervention(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);

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

        return redirect()->back()->with('success', 'Intervention saved.');
    }

    /**
     * Show Evaluation (STEP 4)
     */
    public function showEvaluation(string $component, $nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.adl.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->patient,
            'component' => $component
        ]);
    }

    /**
     * Store Evaluation (STEP 4)
     */
    public function storeEvaluation(Request $request, string $component, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);
        $diagnosis = NursingDiagnosis::with('patient')->findOrFail($nursingDiagnosisId);

        $evaluationText = $request->input('evaluation');

        $alertObject = $this->nursingDiagnosisCdssService->analyzeEvaluation($component, $evaluationText);
        $evaluationAlert = $alertObject->raw_message ?? null;

        $diagnosis->update([
            'evaluation' => $evaluationText,
            'evaluation_alert' => $evaluationAlert,
        ]);

        if ($request->input('action') == 'save_and_finish') {
            return redirect()->route('adl.show')
                ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
        }

        return redirect()->back()->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }
}