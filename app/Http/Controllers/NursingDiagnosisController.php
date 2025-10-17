<?php

namespace App\Http\Controllers;

use App\Models\NursingDiagnosis;
use App\Models\PhysicalExam;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NursingDiagnosisController extends Controller
{
    /**
     * Step 1: Show the Diagnosis form.
     */
    public function createStep1(Request $request, $physicalExamId)
    {
        $physicalExam = PhysicalExam::findOrFail($physicalExamId);
        $selectedPatient = $physicalExam->patient;
        $patients = Auth::user()->patients;

        // Forget old session data if starting fresh
        if (!$request->session()->has('nursing_diagnosis')) {
             $request->session()->forget('nursing_diagnosis');
        }

        return view('nursing-diagnosis.diagnosis', compact('physicalExam', 'selectedPatient', 'patients'));
    }

    /**
     * Step 1: Store Diagnosis data in session and move to next step.
     */
    public function storeStep1(Request $request, $physicalExamId)
    {
        $validatedData = $request->validate(['diagnosis' => 'nullable|string']);
        
        // Store in session
        $request->session()->put('nursing_diagnosis.diagnosis', $validatedData['diagnosis']);

        return redirect()->route('nursing-diagnosis.create-step-2', ['physicalExamId' => $physicalExamId]);
    }

    /**
     * Step 2: Show the Planning form.
     */
    public function createStep2($physicalExamId)
    {
        $physicalExam = PhysicalExam::findOrFail($physicalExamId);
        $selectedPatient = $physicalExam->patient;
        return view('nursing-diagnosis.planning', compact('physicalExam', 'selectedPatient'));
    }

    /**
     * Step 2: Store Planning data in session.
     */
    public function storeStep2(Request $request, $physicalExamId)
    {
        $validatedData = $request->validate(['planning' => 'nullable|string']);
        $request->session()->put('nursing_diagnosis.planning', $validatedData['planning']);
        return redirect()->route('nursing-diagnosis.create-step-3', ['physicalExamId' => $physicalExamId]);
    }

    /**
     * Step 3: Show the Intervention form.
     */
    public function createStep3($physicalExamId)
    {
        $physicalExam = PhysicalExam::findOrFail($physicalExamId);
        $selectedPatient = $physicalExam->patient;
        return view('nursing-diagnosis.intervention', compact('physicalExam', 'selectedPatient'));
    }

    /**
     * Step 3: Store Intervention data in session.
     */
    public function storeStep3(Request $request, $physicalExamId)
    {
        $validatedData = $request->validate(['intervention' => 'nullable|string']);
        $request->session()->put('nursing_diagnosis.intervention', $validatedData['intervention']);
        return redirect()->route('nursing-diagnosis.create-step-4', ['physicalExamId' => $physicalExamId]);
    }

    /**
     * Step 4: Show the Evaluation form.
     */
    public function createStep4($physicalExamId)
    {
        $physicalExam = PhysicalExam::findOrFail($physicalExamId);
        $selectedPatient = $physicalExam->patient;
        return view('nursing-diagnosis.evaluation', compact('physicalExam', 'selectedPatient'));
    }

    /**
     * Final Step: Store all session data into the database.
     */
    public function store(Request $request, $physicalExamId)
    {
        // Add the final step's data to the session
        $validatedData = $request->validate(['evaluation' => 'nullable|string']);
        $request->session()->put('nursing_diagnosis.evaluation', $validatedData['evaluation']);
        
        // Retrieve all data from session
        $data = $request->session()->get('nursing_diagnosis');

        // Add the physical_exam_id
        $data['physical_exam_id'] = $physicalExamId;

        // Create the record
        NursingDiagnosis::create([
            'physical_exam_id' => $data['physical_exam_id'],
            'diagnosis' => $data['diagnosis'] ?? '',
            'planning' => $data['planning'] ?? '',
            'intervention' => $data['intervention'] ?? '',
            'evaluation' => $data['evaluation'] ?? '',
        ]);

        // Clear the session data
        $request->session()->forget('nursing_diagnosis');

        // Redirect with success message
        return redirect()->route('physical-exam.index')->with('success', 'Nursing diagnosis saved successfully!');
    }
}
