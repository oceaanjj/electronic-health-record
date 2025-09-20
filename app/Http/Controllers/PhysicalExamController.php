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

        return redirect()->route('physical-exam.index')
            ->with('success', 'Physical exam registered successfully')
            ->withInput();
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
        $patients = Patient::all(); // For the dropdown

        // Flash input to session so old() helper works
        $request->flash();

        return view('physical-exam', [
            'patients' => $patients,
            'alerts' => $alerts,
        ]);
    }
}
