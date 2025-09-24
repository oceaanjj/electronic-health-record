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
    public function show(Request $request)
    {
        $patients = Patient::all();

        $physicalExam = null;

        $patientId = $request->get('patient_id');

        if ($patientId) {
            // Find the physical exam data for the selected patient
            $physicalExam = PhysicalExam::where('patient_id', $patientId)->first();


            if ($physicalExam) {

                $request->session()->flash('old', $physicalExam->toArray());
            }
        }
        //$physicalExam = data from the table 
        return view('physical-exam', compact('patients', 'physicalExam'));
    }

    public function store(Request $request)
    {
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
            // If it exists, update the record
            $existingExam->update($data);
            $message = 'Physical exam data updated successfully!';
            // Add audit log for update
            AuditLogController::log(
                'Physical Exam Updated',
                'User ' . Auth::user()->username . ' updated an Physical Exam record.',
                ['patient_id' => $data['patient_id']]
            );

        } else {
            // Otherwise, create a new record
            PhysicalExam::create($data);
            $message = 'Physical exam data saved successfully!';
            // Add audit log for creation
            AuditLogController::log(
                'Physical Exam Created',
                'User ' . Auth::user()->username . ' created an new Physical Exam record.',
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

        // Redirect back with the input, alerts, and a success message
        return redirect()->route('physical-exam.index', ['patient_id' => $data['patient_id']])
            ->withInput()
            ->with('cdss', $formattedAlerts)
            ->with('success', $message);
    }


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

        // Call the CDSS service
        $cdssService = new PhysicalExamCdssService();
        $alerts = $cdssService->analyzeFindings($data);

        // Reformat the alert array to match the view's expected keys
        $formattedAlerts = [];
        foreach ($alerts as $key => $value) {
            $newKey = str_replace(['_alerts'], '', $key);
            $formattedAlerts[$newKey] = $value;
        }

        return redirect()->route('physical-exam.index')
            ->withInput($data)
            ->with('cdss', $formattedAlerts)
            ->with('success', 'CDSS analysis run successfully!');
    }
}
