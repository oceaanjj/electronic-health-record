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

        $physicalExam = PhysicalExam::create($data);

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

        return redirect()->route('physical-exam.index')
            ->withInput()
            ->with('cdss', $formattedAlerts)
            ->with('success', 'Physical exam data saved successfully!');
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
            ->withInput()
            ->with('cdss', $formattedAlerts)
            ->with('success', 'CDSS analysis run successfully!');
    }
}
