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

    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');

        $request->session()->put('selected_patient_id', $patientId);

        return redirect()->route('physical-exam.index');
    }


    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $physicalExam = null;

        // Get the patient ID from the session instead of the query string
        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                // Find the physical exam data for the selected patient
                $physicalExam = PhysicalExam::where('patient_id', $patientId)->first();
            }
        }

        return view('physical-exam', compact('patients', 'selectedPatient', 'physicalExam'));
    }


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

        // Run the CDSS analysis after storing the data
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

    /**
     * Runs CDSS analysis on findings.
     */
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