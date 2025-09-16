<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PhysicalExamController extends Controller
{
    // SHOW PHYSICAL EXAM FORM
    public function show()
    {
        $patients = Patient::all();
        return view('physical-exam', compact('patients'));
    }

    // STORE PHYSICAL EXAM DATA
    public function store(Request $request)
    {
        $patientInfo = $request->input('patient_info'); //Patient id and name

        $parts = explode('|', $patientInfo); //String -> Array

        $request->merge([
            'patient_id' => $parts[0] ?? null,
            'patient_name' => $parts[1] ?? null,
        ]);

        try {
            $validatedData = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'patient_name' => 'required|exists:patients,name',
                'general_appearance_findings' => 'required|string',
                'skin_findings' => 'required|string',
                'eyes_findings' => 'required|string',
                'oral_cavity_findings' => 'required|string',
                'cardiovascular_findings' => 'required|string',
                'abdomen_findings' => 'required|string',
                'extremities_findings' => 'required|string',
                'neurological_findings' => 'required|string',
            ]);

            $request->session()->put('physical_exam_data', $validatedData);

            return redirect()->route('physical-exam.show')->with('success', 'Physical exam data stored temporarily.')->with('physical_exam_data', $validatedData);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }
}