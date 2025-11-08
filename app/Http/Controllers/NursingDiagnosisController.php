<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // <-- Includes the fix for the previous error
use App\Models\NursingDiagnosis;
use App\Models\PhysicalExam;
use App\Models\IntakeAndOutput;
use App\Models\Patient;
use App\Services\NursingDiagnosisCdssService; // <-- The "brain" for recommendations
use Illuminate\Http\Request;

class NursingDiagnosisController extends Controller
{
    //==================================================================
    // ORIGINAL METHODS (FROM YOUR FIRST FILE)
    //==================================================================

    // Store DPIE for Physical Exam
    public function storeForPhysicalExam(Request $request, $physicalExamId)
    {
        $data = $request->validate([
            'diagnosis' => 'required|string',
            'planning' => 'required|string',
            'intervention' => 'required|string',
            'evaluation' => 'required|string',
        ]);

        $data['physical_exam_id'] = $physicalExamId;
        NursingDiagnosis::create($data);
        // return redirect()->back()->with('success', 'Nursing diagnosis for Physical Exam saved!');
    }

    // Store DPIE for Intake and Output
    public function storeForIntakeAndOutput(Request $request, $intakeAndOutputId)
    {
        $data = $request->validate([
            'diagnosis' => 'required|string',
            'planning' => 'required|string',
            'intervention' => 'required|string',
            'evaluation' => 'required|string',
        ]);

        $data['intake_and_output_id'] = $intakeAndOutputId;
        NursingDiagnosis::create($data);
        // return redirect()->back()->with('success', 'Nursing diagnosis for Intake/Output saved!');
    }

    // Show all DPIE for a physical exam
    public function showByPhysicalExam($physicalExamId)
    {
        $physicalExam = PhysicalExam::with('nursingDiagnoses')->findOrFail($physicalExamId);
        // return view('nursing-diagnosis.show', compact('physicalExam'));
    }

    // Show all  DPIE for an intake and output record
    public function showByIntakeAndOutput($intakeAndOutputId)
    {
        $intakeAndOutput = IntakeAndOutput::with('nursingDiagnoses')->findOrFail($intakeAndOutputId);
        // return view('nursing-diagnosis.show-intake-output', compact('intakeAndOutput'));
    }

    // Show all DPIE for a specific patient
    public function showByPatient($patientId)
    {
        $physicalExams = PhysicalExam::where('patient_id', $patientId)
            ->with('nursingDiagnoses')
            ->get();
        
        $intakeOutputs = IntakeAndOutput::where('patient_id', $patientId)
            ->with('nursingDiagnoses')
            ->get();

        $allNursingDiagnoses = NursingDiagnosis::whereHas('physicalExam', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })->orWhereHas('intakeAndOutput', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })->with(['physicalExam', 'intakeAndOutput'])
            ->orderBy('created_at', 'desc')
            ->get();

        // return view('nursing-diagnosis.patient-all', compact('physicalExams', 'intakeOutputs', 'allNursingDiagnoses', 'patientId'));
    }


    //==================================================================
    // WIZARD "SHOW" METHODS
    //==================================================================

    /**
     * Step 1: Show the Diagnosis form.
     */
    public function startDiagnosis($physicalExamId)
    {
        $physicalExam = PhysicalExam::with('patient')->findOrFail($physicalExamId);
        return view('adpie.physical-exam.diagnosis', [
            'physicalExamId' => $physicalExam->id,
            'patient' => $physicalExam->patient
        ]);
    }

    /**
     * Step 2: Show the Planning form.
     */
    public function showPlanning($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.planning', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient
        ]);
    }

    /**
     * Step 3: Show the Intervention form.
     */
    public function showIntervention($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.intervention', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient
        ]);
    }

    /**
     * Step 4: Show the Evaluation form.
     */
    public function showEvaluation($nursingDiagnosisId)
    {
        $diagnosis = NursingDiagnosis::with('physicalExam.patient')->findOrFail($nursingDiagnosisId);
        return view('adpie.physical-exam.evaluation', [
            'diagnosis' => $diagnosis,
            'patient' => $diagnosis->physicalExam->patient
        ]);
    }

    //==================================================================
    // WIZARD "STORE" METHODS (These save the data)
    //==================================================================

    /**
     * Step 1: Store the Diagnosis and CREATE the new record.
     */
    public function storeDiagnosis(Request $request, $physicalExamId)
    {
        $request->validate(['diagnosis' => 'required|string|max:1000']);

        // Run analysis before creating
        $cdssService = new NursingDiagnosisCdssService();
        $diagnosisText = $request->input('diagnosis');
        $recommendationObj = $cdssService->analyzeDiagnosis($diagnosisText);
        $diagnosisAlert = $recommendationObj ? $recommendationObj->message : null; // Get the HTML message

        $newDiagnosis = NursingDiagnosis::create([
            'physical_exam_id' => $physicalExamId,
            'diagnosis' => $diagnosisText,
            'planning' => '',
            'intervention' => '',
            'evaluation' => '',
            'diagnosis_alert' => $diagnosisAlert, // <-- SAVE THE ALERT
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showPlanning', $newDiagnosis->id)
                             ->with('success', 'Diagnosis saved. Now, please enter the plan.');
        }

        return redirect()->route('physical-exam.index')
                         ->with('success', 'Diagnosis saved.');
    }

    /**
     * Step 2: Store the Planning by UPDATING the record.
     */
    public function storePlanning(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['planning' => 'required|string|max:1000']);

        // Run analysis before updating
        $cdssService = new NursingDiagnosisCdssService();
        $planningText = $request->input('planning');
        $recommendationObj = $cdssService->analyzePlanning($planningText);
        $planningAlert = $recommendationObj ? $recommendationObj->message : null; // Get the HTML message

        $diagnosis = NursingDiagnosis::findOrFail($nursingDiagnosisId);
        $diagnosis->update([
            'planning' => $planningText,
            'planning_alert' => $planningAlert, // <-- SAVE THE ALERT
        ]);

        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showIntervention', $nursingDiagnosisId)
                             ->with('success', 'Plan saved. Now, please enter interventions.');
        }

        return redirect()->route('physical-exam.index')
                         ->with('success', 'Plan saved.');
    }

    /**
     * Step 3: Store the Intervention by UPDATING the record.
     */
    public function storeIntervention(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['intervention' => 'required|string|max:1000']);

        // Run analysis before updating
        $cdssService = new NursingDiagnosisCdssService();
        $interventionText = $request->input('intervention');
        $recommendationObj = $cdssService->analyzeIntervention($interventionText);
        $interventionAlert = $recommendationObj ? $recommendationObj->message : null; // Get the HTML message
        
        $diagnosis = NursingDiagnosis::findOrFail($nursingDiagnosisId);
        $diagnosis->update([
            'intervention' => $interventionText,
            'intervention_alert' => $interventionAlert, // <-- SAVE THE ALERT
        ]);
        
        if ($request->input('action') == 'save_and_proceed') {
            return redirect()->route('nursing-diagnosis.showEvaluation', $nursingDiagnosisId)
                             ->with('success', 'Intervention saved. Now, please enter evaluation.');
        }
        
        return redirect()->route('physical-exam.index')
                         ->with('success', 'Intervention saved.');
    }

    /**
     * Step 4: Store the Evaluation by UPDATING the record.
     */
    public function storeEvaluation(Request $request, $nursingDiagnosisId)
    {
        $request->validate(['evaluation' => 'required|string|max:1000']);

        // Run analysis before updating
        $cdssService = new NursingDiagnosisCdssService();
        $evaluationText = $request->input('evaluation');
        $recommendationObj = $cdssService->analyzeEvaluation($evaluationText);
        $evaluationAlert = $recommendationObj ? $recommendationObj->message : null; // Get the HTML message

        $diagnosis = NursingDiagnosis::findOrFail($nursingDiagnosisId);
        $diagnosis->update([
            'evaluation' => $evaluationText,
            'evaluation_alert' => $evaluationAlert, // <-- SAVE THE ALERT
        ]);

        return redirect()->route('physical-exam.index')
                         ->with('success', 'Evaluation saved. Nursing Diagnosis complete!');
    }


    //==================================================================
    // WIZARD "CDSS" METHOD
    //==================================================================
    
    /**
     * Run CDSS analysis for any single field on the Nursing Diagnosis wizard.
     */
    public function analyzeDiagnosisField(Request $request)
    {
        $data = $request->validate([
            'fieldName' => 'required|string',
            'finding' => 'nullable|string',
            'patient_id' => 'nullable|exists:patients,patient_id'
        ]);

        $cdssService = new NursingDiagnosisCdssService();
        $recommendation = null;
        $finding = $data['finding'] ?? '';
        $patientId = $data['patient_id'] ?? null;

        // Route the analysis based on the field name
        switch ($data['fieldName']) {
            case 'diagnosis':
                $recommendation = $cdssService->analyzeDiagnosis($finding);
                break;
            case 'planning':
                $recommendation = $cdssService->analyzePlanning($finding);
                break;
            case 'intervention':
                $recommendation = $cdssService->analyzeIntervention($finding);
                break;
            case 'evaluation':
                $recommendation = $cdssService->analyzeEvaluation($finding);
                break;
        }

        return response()->json($recommendation);
    }
}