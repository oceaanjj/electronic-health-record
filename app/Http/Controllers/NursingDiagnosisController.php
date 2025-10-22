<?php

namespace App\Http\Controllers;

use App\Models\NursingDiagnosis;
use App\Models\PhysicalExam;
use App\Models\IntakeAndOutput;
use Illuminate\Http\Request;

class NursingDiagnosisController extends Controller
{
    // Store DPIE for Physical Exam
    public function storeForPhysicalExam(Request $request, $physicalExamId)
    {
        $data = $request->validate([
            'diagnosis' => 'required|string',
            'planning' => 'required|string',
            'intervention' => 'required|string',
            'evaluation' => 'required|string',
        ]);

        $data['physical_exam_id'] = $physicalExamId;

        NursingDiagnosis::create($data);

        // return redirect()->back()->with('success', 'Nursing diagnosis for Physical Exam saved!');
    }

    // Store DPIE for Intake and Output
    public function storeForIntakeAndOutput(Request $request, $intakeAndOutputId)
    {
        $data = $request->validate([
            'diagnosis' => 'required|string',
            'planning' => 'required|string',
            'intervention' => 'required|string',
            'evaluation' => 'required|string',
        ]);

        $data['intake_and_output_id'] = $intakeAndOutputId;

        NursingDiagnosis::create($data);

        // return redirect()->back()->with('success', 'Nursing diagnosis for Intake/Output saved!');
    }

    // Show all DPIE for a physical exam
    public function showByPhysicalExam($physicalExamId)
    {
        $physicalExam = PhysicalExam::with('nursingDiagnoses')->findOrFail($physicalExamId);

        // return view('nursing-diagnosis.show', compact('physicalExam'));
    }

    // Show all  DPIE for an intake and output record
    public function showByIntakeAndOutput($intakeAndOutputId)
    {
        $intakeAndOutput = IntakeAndOutput::with('nursingDiagnoses')->findOrFail($intakeAndOutputId);

        // return view('nursing-diagnosis.show-intake-output', compact('intakeAndOutput'));
    }

    // Show all DPIE for a specific patient (from all modules: physical exams, tas intake/output)
    public function showByPatient($patientId)
    {
        // Get all physical exams for specific patient with dpie
        $physicalExams = PhysicalExam::where('patient_id', $patientId)
            ->with('nursingDiagnoses')
            ->get();

        // Get all intake/output records for specific patient with dpie 
        $intakeOutputs = IntakeAndOutput::where('patient_id', $patientId)
            ->with('nursingDiagnoses')
            ->get();

        // alt: Get all dpie directly
        $allNursingDiagnoses = NursingDiagnosis::whereHas('physicalExam', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })->orWhereHas('intakeAndOutput', function ($query) use ($patientId) {
            $query->where('patient_id', operator: $patientId);
        })->with(['physicalExam', 'intakeAndOutput'])
            ->orderBy('created_at', 'desc')
            ->get();

        // return view('nursing-diagnosis.patient-all', compact('physicalExams', 'intakeOutputs', 'allNursingDiagnoses', 'patientId'));
    }
}
