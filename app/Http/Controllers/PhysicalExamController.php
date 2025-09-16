<?php

namespace App\Http\Controllers;

use App\Models\PhysicalExam;
use Illuminate\Http\Request;

class PhysicalExamController extends Controller
{

    // TODO:
    //store it
    // call the cdss method and pass the $physicalExam data
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
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

        // call the cdss method here and pass the $physicalExam data

        //
        return redirect()->route('physical_exams.index')->with('success', 'Physical exam registered successfully');
    }
}
