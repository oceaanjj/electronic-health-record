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
        // $patients = Patient::all();
        $patients = Auth::user()->patients;


        // pang debug lang to, --IGNORE NIYO LANG-- 
        // if ($patients->isEmpty()) {
        //     dd('No patients found in database');
        // } else {
        //     dd('Patients found:', $patients->toArray());
        // }
        $selectedPatient = null;
        $physicalExam = null;

        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                $physicalExam = PhysicalExam::where('patient_id', $patientId)->first();
            } else {
                //
                $request->session()->forget('selected_patient_id');
                return redirect()->route('physical-exam.index')
                    ->with('error', 'Selected patient is not associated with your account.');
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


        //****
        $user_id = Auth::id();
        $patient = Patient::where('patient_id', $request->patient_id)
            ->where('user_id', $user_id)
            ->first();
        if (!$patient) {
            return back()->with('error', 'Unauthorized patient access.');
        }

        if (!$request->has('patient_id')) {
            return back()->with('error', 'No patient selected.');
        }
        //****

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
                'User ' . Auth::user()->username . ' updated an existing Physical Exam record.',
                ['patient_id' => $data['patient_id']]
            );
        } else {
            // call cdss
            $cdssService = new PhysicalExamCdssService();
            $alerts = $cdssService->analyzeFindings($data);

            PhysicalExam::create([
                'patient_id' => $data['patient_id'],
                'general_appearance' => $data['general_appearance'],
                'skin_condition' => $data['skin_condition'],
                'eye_condition' => $data['eye_condition'],
                'oral_condition' => $data['oral_condition'],
                'cardiovascular' => $data['cardiovascular'],
                'abdomen_condition' => $data['abdomen_condition'],
                'extremities' => $data['extremities'],
                'neurological' => $data['neurological'],
                // Store alerts
                'general_appearance_alert' => $alerts['general_appearance_alerts'] ?? null,
                'skin_alert' => $alerts['skin_alerts'] ?? null,
                'eye_alert' => $alerts['eye_alerts'] ?? null,
                'oral_alert' => $alerts['oral_alerts'] ?? null,
                'cardiovascular_alert' => $alerts['cardiovascular_alerts'] ?? null,
                'abdomen_alert' => $alerts['abdomen_alerts'] ?? null,
                'extremities_alert' => $alerts['extremities_alerts'] ?? null,
                'neurological_alert' => $alerts['neurological_alerts'] ?? null,
            ]);

            $message = 'Physical exam data saved successfully!';
            AuditLogController::log(
                'Physical Exam Created',
                'User ' . Auth::user()->username . ' created a new Physical Exam record.',
                ['patient_id' => $data['patient_id']]
            );
        }

        // Run CDSS analysis after storing
        $cdssService = new PhysicalExamCdssService();
        $alerts = $cdssService->analyzeFindings($data);

        $formattedAlerts = [];
        foreach ($alerts as $key => $value) {
            if (is_array($value)) {
                $newKey = str_replace(['_alerts'], '', $key);
                $formattedAlerts[$newKey] = $value;
            }
        }

        return redirect()->route('physical-exam.index')
            ->withInput()
            ->with('cdss', $formattedAlerts)
            ->with('success', $message);
    }

    // public function showPatientExams($id)
    // {
    //     $patient = Patient::findOrFail($id);
    //     $physicalExams = $patient->physicalExams;
    //     return view('patient-physical-exams', compact('patient', 'physicalExams'));
    // }

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

        return redirect()->route('physical-exam.index')
            ->withInput($data)
            ->with('cdss', $formattedAlerts)
            ->with('success', 'CDSS analysis run successfully!');
    }
}
