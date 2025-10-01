<?php

namespace App\Http\Controllers;

use App\Services\PhysicalExamCdssService;
use App\Models\PhysicalExam;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class PhysicalExamController extends Controller
{

    /**
     * The function `selectPatient` sets the selected patient ID in the session and redirects to the
     * physical exam index page.
     * 
     * Args:
     *   request (Request): The `Request` parameter in the `selectPatient` function is an instance of
     * the `Illuminate\Http\Request` class in Laravel. It represents an HTTP request and allows you to
     * access input data, files, cookies, and more from the request.
     * 
     * Returns:
     *   The code is returning a redirect response to the 'physical-exam.index' route after setting the
     * 'selected_patient_id' session variable with the value of 'patient_id' obtained from the request
     * input.
     */
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        //  (to show on selected patient as session on all components)
        $request->session()->put('selected_patient_id', $patientId);

        return redirect()->route('physical-exam.index');
    }


    /**
     * This show function retrieves patient and physical exam data based on a selected patient ID stored
     * in the session and passes it to a view for display.
     * 
     * Args:
     *   request (Request): The `show` function in the code snippet is a controller method that
     * retrieves information about patients and their physical exams to display on a view. Here's a
     * breakdown of the parameters used in the function:
     * 
     * Returns:
     *   The `show` function is returning a view called 'physical-exam' with the variables ``,
     * ``, and `` passed to the view using the `compact` function. The
     * `` variable contains all the Patient records, the `` variable contains
     * the Patient record based on the selected patient ID stored in the session, and the
     * `` variable
     */
    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $physicalExam = null;

        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                $physicalExam = PhysicalExam::where('patient_id', $patientId)->first();
            }
        }

        return view('physical-exam', compact('patients', 'selectedPatient', 'physicalExam'));
    }


    /**
     * The function `store` in this PHP code snippet handles storing and updating physical exam data for
     * patients, logging the actions, running a CDSS service for analysis, and redirecting back to the
     * physical exam index page.
     * 
     * Args:
     *   request (Request): The `store` function you provided is responsible for storing physical exam
     * data for a patient. Let's break down the key points of this function:
     * 
     * Returns:
     *   The `store` function is returning a redirect response to the `physical-exam.index` route. It
     * includes the following data:
     */
    public function store(Request $request)
    {

        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
        ], [
            'patient_id.required' => 'Please choose a patient first.',
            'patient_id.exists' => 'Please choose a patient first.',
        ]);


        $data = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'general_appearance' => 'nullable|string',
            'skin_condition' => 'nullable|string',
            'eye_condition' => 'nullable|string',
            'oral_condition' => 'nullable|string',
            'cardiovascular' => 'nullable|string',
            'abdomen_condition' => 'nullable|string',
            'extremities' => 'nullable|string',
            'neurological' => 'nullable|string',
        ]);

        $existingExam = PhysicalExam::where('patient_id', $data['patient_id'])->first();

        if ($existingExam) {
            $existingExam->update($data);
            $message = 'Physical exam data updated successfully!';
            AuditLogController::log(
                'Physical Exam Updated',
                'User ' . Auth::user()->username . ' Updated an existing Physical Exam record.',
                ['patient_id' => $data['patient_id']]
            );
        } else {
            PhysicalExam::create($data);
            $message = 'Physical exam data saved successfully!';
            AuditLogController::log(
                'Physical Exam Created',
                'User ' . Auth::user()->username . ' Created a new Physical Exam record.',
                ['patient_id' => $data['patient_id']]
            );
        }

        // Run the CDSS service after storing the data
        $cdssService = new PhysicalExamCdssService();
        $alerts = $cdssService->analyzeFindings($data);

        $formattedAlerts = [];
        foreach ($alerts as $key => $value) {
            if (is_array($value)) {
                $newKey = str_replace(['_alerts'], '', $key);
                $formattedAlerts[$newKey] = $value;
            }
        }

        // Redirect without the patient_id in the URL.
        return redirect()->route('physical-exam.index')
            ->withInput()
            ->with('cdss', $formattedAlerts)
            ->with('success', $message);
    }


    //RUN CDSS (IGNORE)
    public function runCdssAnalysis(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'nullable|exists:patients,patient_id',
            'general_appearance' => 'nullable|string',
            'skin_condition' => 'nullable|string',
            'eye_condition' => 'nullable|string',
            'oral_condition' => 'nullable|string',
            'cardiovascular' => 'nullable|string',
            'abdomen_condition' => 'nullable|string',
            'extremities' => 'nullable|string',
            'neurological' => 'nullable|string',
        ]);

        $cdssService = new PhysicalExamCdssService();
        $alerts = $cdssService->analyzeFindings($data);

        $formattedAlerts = [];
        foreach ($alerts as $key => $value) {
            $newKey = str_replace(['_alerts'], '', $key);
            $formattedAlerts[$newKey] = $value;
        }

        // Redirect without the patient_id in the URL.
        return redirect()->route('physical-exam.index')
            ->withInput($data)
            ->with('cdss', $formattedAlerts)
            ->with('success', 'CDSS analysis run successfully!');
    }
}