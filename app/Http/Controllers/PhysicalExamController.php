<?php

namespace App\Http\Controllers;

use App\Models\PhysicalExam;
use App\Models\CdssPhysicalExam;
use App\Models\Patient;
use Illuminate\Http\Request;

class PhysicalExamController extends Controller
{
    // Show the form
    public function show()
    {
        $patients = Patient::all();
        return view('physical-exam', compact('patients'));
    }

    // Store physical exam and run CDSS
    public function store(Request $request)
    {
        // Validate incoming request
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

        // Check if patient already has a record
        $existingExam = PhysicalExam::where('patient_id', $data['patient_id'])->latest()->first();
        if ($existingExam) {
            $cdss = $existingExam->cdss;
            return redirect()->route('physical-exam.show')->with([
                'success' => 'Patient already has a physical exam record.',
                'physicalExamData' => $existingExam,
                'cdssData' => $cdss ? [
                    'alerts' => json_decode($cdss->alerts, true),
                    'risk_level' => $cdss->risk_level,
                    'abnormal_findings' => json_decode($cdss->abnormal_findings, true),
                    'triggered_rules' => json_decode($cdss->triggered_rules, true),
                ] : []
            ]);
        }

        // Save physical exam
        $physicalExam = PhysicalExam::create($data);

        // Run CDSS and save results
        $cdss = $this->runCdssAnalysis($physicalExam);

        return redirect()->route('physical-exam.show')->with([
            'success' => 'Physical exam registered successfully',
            'physicalExamData' => $physicalExam,
            'cdssData' => [
                'alerts' => json_decode($cdss->alerts, true),
                'risk_level' => $cdss->risk_level,
                'abnormal_findings' => json_decode($cdss->abnormal_findings, true),
                'triggered_rules' => json_decode($cdss->triggered_rules, true),
            ]
        ]);
    }

    // CDSS analysis
    private function runCdssAnalysis($physicalExam)
    {
        $assessment = [];
        $recommendations = [];
        $severity = 'low';

        // === General Appearance ===
        switch ($physicalExam->general_appearance ?? '') {
            case 'pale':
                $assessment['general_appearance'] = 'Possible anemia';
                $recommendations[] = 'Order CBC test';
                $severity = 'moderate';
                break;
            case 'ill-looking':
                $assessment['general_appearance'] = 'Possible infection or systemic illness';
                $recommendations[] = 'Full physical workup';
                $severity = 'moderate';
                break;
            case 'dehydrated':
                $assessment['general_appearance'] = 'Possible dehydration';
                $recommendations[] = 'Oral/IV rehydration';
                $severity = 'moderate';
                break;
            case 'lethargic':
                $assessment['general_appearance'] = 'Possible CNS involvement or severe infection';
                $recommendations[] = 'Neurological exam, rule out meningitis/encephalitis';
                $severity = 'high';
                break;
            case 'well':
                $assessment['general_appearance'] = 'Normal finding';
                $recommendations[] = 'No immediate action needed';
                $severity = 'low';
                break;
            default:
                $assessment['general_appearance'] = 'Unspecified/Unknown condition';
                $recommendations[] = 'Further evaluation required';
        }

        // === Skin ===
        switch ($physicalExam->skin_condition ?? '') {
            case 'rashes':
                $assessment['skin'] = 'Possible allergic reaction';
                $recommendations[] = 'Check allergy history, prescribe antihistamine';
                $severity = max($severity, 'moderate');
                break;
            case 'pallor':
                $assessment['skin'] = 'Possible anemia';
                $recommendations[] = 'Order CBC test';
                $severity = max($severity, 'moderate');
                break;
            case 'jaundice':
                $assessment['skin'] = 'Possible liver disease or hemolysis';
                $recommendations[] = 'Liver function test, bilirubin levels';
                $severity = max($severity, 'high');
                break;
            case 'cyanosis':
                $assessment['skin'] = 'Possible hypoxia';
                $recommendations[] = 'Check oxygen saturation, urgent referral';
                $severity = max($severity, 'high');
                break;
            case 'normal':
                $assessment['skin'] = 'Normal finding';
                break;
            default:
                $assessment['skin'] = 'Unspecified/Unknown condition';
        }

        // === Eyes ===
        switch ($physicalExam->eye_condition ?? '') {
            case 'redness':
                $assessment['eyes'] = 'Possible conjunctivitis';
                $recommendations[] = 'Ophthalmology consult';
                $severity = max($severity, 'moderate');
                break;
            case 'discharge':
                $assessment['eyes'] = 'Possible infection';
                $recommendations[] = 'Swab if purulent, hygiene advice';
                $severity = max($severity, 'moderate');
                break;
            case 'strabismus':
                $assessment['eyes'] = 'Possible eye muscle imbalance';
                $recommendations[] = 'Refer to ophthalmology';
                $severity = max($severity, 'moderate');
                break;
            case 'vision_loss':
                $assessment['eyes'] = 'Possible severe eye pathology';
                $recommendations[] = 'Urgent ophthalmology referral';
                $severity = max($severity, 'high');
                break;
            case 'normal':
                $assessment['eyes'] = 'Normal finding';
                break;
            default:
                $assessment['eyes'] = 'Unspecified/Unknown condition';
        }

        // === Other body systems ===
        // Add similar logic for oral, cardiovascular, abdomen, extremities, neurological
        // For brevity, you can reuse your existing if/else logic here

        // === Prepare abnormal findings & triggered rules ===
        $abnormalFindings = array_filter($assessment, fn($a) => $a !== 'Normal finding' && $a !== 'Unspecified/Unknown condition');
        $triggeredRules = array_keys($abnormalFindings);

        // Save CDSS to DB
        return CdssPhysicalExam::create([
            'physical_exam_id' => $physicalExam->id,
            'patient_id'       => $physicalExam->patient_id,
            'alerts'           => json_encode($assessment),
            'risk_level'       => $severity,
            'requires_immediate_attention' => in_array($severity, ['high', 'critical']),
            'abnormal_findings'=> json_encode($abnormalFindings),
            'triggered_rules'  => json_encode($triggeredRules),
        ]);
    }
}
