<?php

namespace App\Http\Controllers;


use App\Models\PhysicalExam;
use App\Models\CdssPhysicalExam;
use App\Models\Patient;
use Illuminate\Http\Request;

class PhysicalExamController extends Controller
{
     public function show()
    {
        $patients = Patient::all();
        return view('physical-exam', compact('patients'));
    }

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
        $assessment = [];
        $recommendations = [];

        if ($physicalExam->general_appearance === 'pale') {
        $assessment['general_appearance'] = 'Possible anemia';
        $recommendations[] = 'Order CBC test';
        }
         // ✅ Skin
    if ($physicalExam->skin_condition === 'rashes') {
        $assessment['skin'] = 'Possible allergic reaction';
        $recommendations[] = 'Check allergy history, prescribe antihistamine';
        $severity = 'moderate';
    }

    // ✅ Eyes
    if ($physicalExam->eye_condition === 'redness') {
        $assessment['eyes'] = 'Possible infection';
        $recommendations[] = 'Recommend ophthalmology consult';
        $severity = 'moderate';
    }

    // ✅ Oral Cavity
    if ($physicalExam->oral_condition === 'lesion') {
        $assessment['oral_cavity'] = 'Possible oral lesion';
        $recommendations[] = 'Advise dental or ENT check-up';
        $severity = 'mild';
    }

    // ✅ Cardiovascular
    if ($physicalExam->cardiovascular === 'arrhythmia') {
        $assessment['cardiovascular'] = 'Possible heart rhythm problem';
        $recommendations[] = 'Order ECG, cardiology referral';
        $severity = 'severe';
    }

    // ✅ Abdomen
    if ($physicalExam->abdomen_condition === 'tenderness') {
        $assessment['abdomen'] = 'Possible inflammation';
        $recommendations[] = 'Order ultrasound or further labs';
        $severity = 'moderate';
    }

    // ✅ Extremities
    if ($physicalExam->extremities === 'swelling') {
        $assessment['extremities'] = 'Possible edema';
        $recommendations[] = 'Check for kidney or cardiac issues';
        $severity = 'moderate';
    }

    // ✅ Neurological
    if ($physicalExam->neurological === 'weakness') {
        $assessment['neurological'] = 'Possible neurological deficit';
        $recommendations[] = 'Order neurological exam / imaging';
        $severity = 'severe';
    }

    // Final recommendation text
    $recommendationText = !empty($recommendations)
        ? implode('; ', $recommendations)
        : 'No significant findings';

    // Save CDSS result sa DB
    CdssPhysicalExam::create([
        'physical_exam_id' => $physicalExam->id,
        'patient_id'       => $physicalExam->patient_id,
        'assessment'       => json_encode($assessment),
        'recommendation'   => $recommendationText,
        'severity_level'   => $severity,
    ]);


    }
}


