<?php

namespace App\Http\Controllers;

use App\Models\NursingDiagnosis;
use App\Models\PhysicalExam;
use App\Services\PhysicalNursingDiagnosisService; // Service for recommendations
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NursingDiagnosisController extends Controller
{
    protected $nursingDiagnosisService;

    /**
     * Inject the service via the constructor.
     */
    public function __construct(PhysicalNursingDiagnosisService $nursingDiagnosisService)
    {
        $this->nursingDiagnosisService = $nursingDiagnosisService;
    }

    /**
     * Step 1: Show the Diagnosis form & generate a recommendation.
     */
    public function createStep1(Request $request, $physicalExamId)
    {
        $physicalExam = PhysicalExam::findOrFail($physicalExamId);

        // Generate a new recommendation based on the physical exam.
        $recommendation = $this->nursingDiagnosisService->generateDPIE($physicalExam);

        // Store the full recommendation in the session to use in subsequent steps.
        if (!empty($recommendation)) {
            $request->session()->put('nursing_diagnosis_recommendation', $recommendation);
        } else {
            // Clear any old recommendation if no new one is found.
            $request->session()->forget('nursing_diagnosis_recommendation');
        }

        return view('nursing-diagnosis.diagnosis', [
            'physicalExam' => $physicalExam,
            'selectedPatient' => $physicalExam->patient,
            'recommendation' => $recommendation, // Pass to the view
        ]);
    }

    /**
     * Store Diagnosis data in session and move to the next step.
     */
    public function storeStep1(Request $request, $physicalExamId)
    {
        $validatedData = $request->validate(['diagnosis' => 'required|string']);
        $request->session()->put('nursing_diagnosis.diagnosis', $validatedData['diagnosis']);
        return redirect()->route('nursing-diagnosis.create-step-2', ['physicalExamId' => $physicalExamId]);
    }

    /**
     * Step 2: Show the Planning form.
     */
    public function createStep2($physicalExamId)
    {
        return view('nursing-diagnosis.planning', [
            'physicalExam' => PhysicalExam::findOrFail($physicalExamId),
            'selectedPatient' => PhysicalExam::findOrFail($physicalExamId)->patient,
            'recommendation' => session('nursing_diagnosis_recommendation', []), // Get recommendation from session
        ]);
    }

    /**
     * Store Planning data in session and move to the next step.
     */
    public function storeStep2(Request $request, $physicalExamId)
    {
        $validatedData = $request->validate(['planning' => 'required|string']);
        $request->session()->put('nursing_diagnosis.planning', $validatedData['planning']);
        return redirect()->route('nursing-diagnosis.create-step-3', ['physicalExamId' => $physicalExamId]);
    }

    /**
     * Step 3: Show the Intervention form.
     */
    public function createStep3($physicalExamId)
    {
        return view('nursing-diagnosis.intervention', [
            'physicalExam' => PhysicalExam::findOrFail($physicalExamId),
            'selectedPatient' => PhysicalExam::findOrFail($physicalExamId)->patient,
            'recommendation' => session('nursing_diagnosis_recommendation', []), // Get recommendation from session
        ]);
    }

    /**
     * Store Intervention data in session and move to the next step.
     */
    public function storeStep3(Request $request, $physicalExamId)
    {
        $validatedData = $request->validate(['intervention' => 'required|string']);
        $request->session()->put('nursing_diagnosis.intervention', $validatedData['intervention']);
        return redirect()->route('nursing-diagnosis.create-step-4', ['physicalExamId' => $physicalExamId]);
    }

    /**
     * Step 4: Show the Evaluation form.
     */
    public function createStep4($physicalExamId)
    {
        return view('nursing-diagnosis.evaluation', [
            'physicalExam' => PhysicalExam::findOrFail($physicalExamId),
            'selectedPatient' => PhysicalExam::findOrFail($physicalExamId)->patient,
            'recommendation' => session('nursing_diagnosis_recommendation', []), // Get recommendation from session
        ]);
    }

    /**
     * Final Step: Validate the last input and store all session data into the database.
     */
    public function store(Request $request, $physicalExamId)
    {
        // Validate the final step's data.
        $validatedData = $request->validate(['evaluation' => 'required|string']);

        // Retrieve all data from the session.
        $data = $request->session()->get('nursing_diagnosis', []);
        
        // Add the final piece of data.
        $data['evaluation'] = $validatedData['evaluation'];

        // Add the foreign key for the relationship.
        $data['physical_exam_id'] = $physicalExamId;

        // Create the record in the database.
        NursingDiagnosis::create($data);

        // Clear the session data for the next entry.
        $request->session()->forget(['nursing_diagnosis', 'nursing_diagnosis_recommendation']);

        // Redirect with a success message.
        return redirect()->route('physical-exam.index')->with('success', 'Nursing diagnosis saved successfully!');
    }
}