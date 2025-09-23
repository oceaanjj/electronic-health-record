<?php

namespace App\Http\Controllers;

use App\Services\PhysicalExamCdssService;
use App\Models\PhysicalExam;
use App\Models\Patient;
use Illuminate\Http\Request;


class PhysicalExamController extends Controller
{
    public function show()
    {
        $patients = Patient::all();

        // pang debug lang to, --IGNORE NIYO LANG-- 
        // if ($patients->isEmpty()) {
        //     dd('No patients found in database');
        // } else {
        //     dd('Patients found:', $patients->toArray());
        // }

        return view('physical-exam', compact('patients'));
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

        return redirect()->route('physical-exam.index')
            ->with('success', 'Physical exam registered successfully')
            ->withInput();
    }

    public function showPatientExams($id)
    {
        $patient = Patient::findOrFail($id);
        $physicalExams = $patient->physicalExams;
        return view('patient-physical-exams', compact('patient', 'physicalExams'));
    }


    public function generatedCdssAlerts(Request $request)
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
        $patients = Patient::all();
        $request->flash();
        return view('physical-exam', [
            'patients' => $patients,
            'alerts' => $alerts,
        ]);
    }
}
