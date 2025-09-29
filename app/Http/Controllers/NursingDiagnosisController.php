<?php

namespace App\Http\Controllers;

use App\Models\NursingDiagnosis;
use App\Models\PhysicalExam;
use Illuminate\Http\Request;

class NursingDiagnosisController extends Controller
{
    public function store(Request $request, $physicalExamId)
    {
        $data = $request->validate([
            'diagnosis' => 'required|string',
            'planning' => 'required|string',
            'intervention' => 'required|string',
            'evaluation' => 'required|string',
        ]);

        $data['physical_exam_id'] = $physicalExamId;

        NursingDiagnosis::create($data);

        return redirect()->back()->with('success', 'Nursing diagnosis saved!');
    }

    // Show all nursing diagnoses for a physical exam
    // [] Add all function for each showExam example is showByIntakeandOutput
    public function showByPhysicalExam($physicalExamId)
    {
        $physicalExam = PhysicalExam::with('nursingDiagnoses')->findOrFail($physicalExamId);

        return view('nursing-diagnosis.show', compact('physicalExam'));
    }
}
