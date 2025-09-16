<?php

namespace App\Http\Controllers;


use App\Models\PhysicalExam;
use App\Models\CdssPhysicalExam;
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
        $this->runCdssAnalysis($physicalExam);

        // where itll go after storing
        return redirect()->route('physical_exams.index')->with('success', 'Physical exam registered successfully');
    }

    private function runCdssAnalysis($physicalExam)
    {
        // Build the if/else logic para sa cdss analysis
        // [] If else sa bawat condition from general appearance to neurological (long ass shit)
        // [] store it sa db
        // [] return a view or redirect it



    }
}