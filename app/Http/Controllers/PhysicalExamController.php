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

        // call the cdss method here and pass the $physicalExam data
        $this->runCdssAnalysis($physicalExam);

        // where itll go after storing
        return redirect()->route('physical-exam.index')->with('success', 'Physical exam registered successfully');
    }

    private function runCdssAnalysis($physicalExam)
    {
        // Build the if/else logic para sa cdss analysis
        // [] If else sa bawat condition from general appearance to neurological (long ass shit)
        // [] store it sa db
        // [] return a view or redirect it
        $assessment = [];
        $recommendations = [];

        // General Appearance Analysis
        if ($physicalExam->general_appearance === 'pale') {
            $assessment['general_appearance'] = 'Possible anemia';
            $recommendations[] = 'Order CBC test';
            $severity = 'moderate';
        } else if ($physicalExam->general_appearance === 'ill-looking') {
            $assessment['general_appearance'] = 'Possible infection or systemic illness';
            $recommendations[] = 'Recommend full physical workup';
            $severity = 'moderate';
        } else if ($physicalExam->general_appearance === 'dehydrated') {
            $assessment['general_appearance'] = 'Possible dehydration';
            $recommendations[] = 'Start oral/IV rehydration therapy';
            $severity = 'moderate';
        } else if ($physicalExam->general_appearance === 'lethargic') {
            $assessment['general_appearance'] = 'Possible CNS involvement or severe infection';
            $recommendations[] = 'Neurological exam, rule out meningitis/encephalitis';
            $severity = 'severe';
        } else if ($physicalExam->general_appearance === 'well') {
            $assessment['general_appearance'] = 'Normal finding';
            $recommendations[] = 'No immediate action needed';
            $severity = 'low';
        } else {
            $assessment['general_appearance'] = 'Unspecified/Unknown condition';
            $recommendations[] = 'Further evaluation required';
            $severity = 'moderate';
        }

         // Skin
        if ($physicalExam->skin_condition === 'rashes') {
        $assessment['skin'] = 'Possible allergic reaction';
        $recommendations[] = 'Check allergy history, prescribe antihistamine';
        $severity = 'moderate';
        } else if ($physicalExam->skin_condition === 'pallor') {
            $assessment['skin'] = 'Possible anemia';
            $recommendations[] = 'Order CBC test to confirm anemia';
            $severity = 'moderate';
        } else if ($physicalExam->skin_condition === 'jaundice') {
            $assessment['skin'] = 'Possible liver disease or hemolysis';
            $recommendations[] = 'Order liver function test, bilirubin levels';
            $severity = 'severe';
        } else if ($physicalExam->skin_condition === 'cyanosis') {
            $assessment['skin'] = 'Possible hypoxia';
            $recommendations[] = 'Check oxygen saturation, consider urgent referral';
            $severity = 'severe';
        } else if ($physicalExam->skin_condition === 'normal') {
            $assessment['skin'] = 'Normal finding';
            $recommendations[] = 'No action needed';
            $severity = 'mild';
        } else {
            $assessment['skin'] = 'Unspecified/Unknown condition';
            $recommendations[] = 'Further evaluation required';
            $severity = 'mild';
        }

    //  Eyes
        if ($physicalExam->eye_condition === 'redness') {
        $assessment['eyes'] = 'Possible conjunctivitis or infection';
        $recommendations[] = 'Recommend ophthalmology consult, eye drops if needed';
        $severity = 'moderate';
        } else if ($physicalExam->eye_condition === 'discharge') {
            $assessment['eyes'] = 'Possible bacterial or viral infection';
            $recommendations[] = 'Swab if purulent, hygiene advice, possible antibiotics';
            $severity = 'moderate';
        } else if ($physicalExam->eye_condition === 'strabismus') {
            $assessment['eyes'] = 'Possible eye muscle imbalance';
            $recommendations[] = 'Refer to ophthalmology for further evaluation';
            $severity = 'moderate';
        } else if ($physicalExam->eye_condition === 'vision_loss') {
            $assessment['eyes'] = 'Possible severe eye pathology';
            $recommendations[] = 'Urgent ophthalmology referral';
            $severity = 'severe';
        } else if ($physicalExam->eye_condition === 'normal') {
            $assessment['eyes'] = 'Normal finding';
            $recommendations[] = 'No action needed';
            $severity = 'mild';
        } else {
            $assessment['eyes'] = 'Unspecified/Unknown condition';
            $recommendations[] = 'Further evaluation required';
            $severity = 'mild';
        }

    //  Oral Cavity
        if ($physicalExam->oral_condition === 'lesion') {
        $assessment['oral_cavity'] = 'Possible oral lesion';
        $recommendations[] = 'Advise dental or ENT check-up';
        $severity = 'mild';
        } else if ($physicalExam->oral_condition === 'ulcer') {
            $assessment['oral_cavity'] = 'Possible stomatitis or viral infection (e.g. HFMD)';
            $recommendations[] = 'Supportive care, check hydration, refer if severe';
            $severity = 'moderate';
        } else if ($physicalExam->oral_condition === 'swelling') {
            $assessment['oral_cavity'] = 'Possible infection or abscess';
            $recommendations[] = 'ENT/dental referral, possible antibiotics';
            $severity = 'moderate';
        } else if ($physicalExam->oral_condition === 'bleeding_gums') {
            $assessment['oral_cavity'] = 'Possible gingivitis or bleeding disorder';
            $recommendations[] = 'Order CBC, check platelets, dental referral';
            $severity = 'moderate';
        } else if ($physicalExam->oral_condition === 'normal') {
            $assessment['oral_cavity'] = 'Normal finding';
            $recommendations[] = 'No action needed';
            $severity = 'mild';
        } else {
            $assessment['oral_cavity'] = 'Unspecified/Unknown condition';
            $recommendations[] = 'Further evaluation required';
            $severity = 'mild';
        }

    //  Cardiovascular
        if ($physicalExam->cardiovascular === 'murmur') {
        $assessment['cardiovascular'] = 'Possible congenital heart disease';
        $recommendations[] = 'Refer to pediatric cardiology, consider echocardiogram';
        $severity = 'moderate';
        } 
        else if ($physicalExam->cardiovascular === 'tachycardia') {
            $assessment['cardiovascular'] = 'Possible fever, dehydration, anemia, or sepsis';
            $recommendations[] = 'Check temperature, hydration status, CBC';
            $severity = 'moderate';
        } 
        else if ($physicalExam->cardiovascular === 'bradycardia') {
            $assessment['cardiovascular'] = 'Possible hypoxia or congenital heart block';
            $recommendations[] = 'Check oxygen saturation, order ECG';
            $severity = 'severe';
        } 
        else if ($physicalExam->cardiovascular === 'cyanosis') {
            $assessment['cardiovascular'] = 'Possible congenital heart disease (cyanotic type)';
            $recommendations[] = 'Immediate referral to pediatric cardiology';
            $severity = 'severe';
        } 
        else if ($physicalExam->cardiovascular === 'normal') {
            $assessment['cardiovascular'] = 'No abnormal cardiovascular findings';
            $recommendations[] = 'Continue routine monitoring';
            $severity = 'mild';
        }
    //  Abdomen
        if ($physicalExam->oral_condition === 'lesion') {
        $assessment['oral_cavity'] = 'Possible oral lesion';
        $recommendations[] = 'Advise dental or ENT check-up';
        $severity = 'mild';
        } else if ($physicalExam->oral_condition === 'ulcer') {
            $assessment['oral_cavity'] = 'Possible stomatitis or viral infection (e.g. HFMD)';
            $recommendations[] = 'Supportive care, check hydration, refer if severe';
            $severity = 'moderate';
        } else if ($physicalExam->oral_condition === 'swelling') {
            $assessment['oral_cavity'] = 'Possible infection or abscess';
            $recommendations[] = 'ENT/dental referral, possible antibiotics';
            $severity = 'moderate';
        } else if ($physicalExam->oral_condition === 'bleeding_gums') {
            $assessment['oral_cavity'] = 'Possible gingivitis or bleeding disorder';
            $recommendations[] = 'Order CBC, check platelets, dental referral';
            $severity = 'moderate';
        } else if ($physicalExam->oral_condition === 'normal') {
            $assessment['oral_cavity'] = 'Normal finding';
            $recommendations[] = 'No action needed';
            $severity = 'mild';
        } else {
            $assessment['oral_cavity'] = 'Unspecified/Unknown condition';
            $recommendations[] = 'Further evaluation required';
            $severity = 'mild';
        }

    //  Extremities
        if ($physicalExam->extremities === 'swelling') {
            $assessment['extremities'] = 'Possible edema';
            $recommendations[] = 'Check for kidney or cardiac issues';
            $severity = 'moderate';
        } else if ($physicalExam->extremities === 'weakness') {
            $assessment['extremities'] = 'Possible neuromuscular problem';
            $recommendations[] = 'Neurological evaluation, consider physiotherapy';
            $severity = 'moderate';
        } else if ($physicalExam->extremities === 'deformity') {
            $assessment['extremities'] = 'Possible fracture or congenital deformity';
            $recommendations[] = 'X-ray, orthopedic consult';
            $severity = 'severe';
        } else if ($physicalExam->extremities === 'pallor') {
            $assessment['extremities'] = 'Possible anemia';
            $recommendations[] = 'Check CBC, consider hematology referral';
            $severity = 'moderate';
        } else if ($physicalExam->extremities === 'cyanosis') {
            $assessment['extremities'] = 'Possible hypoxia or cardiac/pulmonary issue';
            $recommendations[] = 'Immediate oxygen assessment, cardiology/pulmonology consult';
            $severity = 'severe';
        } else {
            $assessment['extremities'] = 'Normal findings';
            $recommendations[] = 'No immediate action needed';
            $severity = 'low';
        }

    //  Neurological
        if ($physicalExam->neurological === 'weakness') {
        $assessment['neurological'] = 'Possible neurological deficit';
        $recommendations[] = 'Order neurological exam / imaging';
        $severity = 'severe';
        } else if ($physicalExam->neurological === 'seizures') {
            $assessment['neurological'] = 'Possible seizure disorder';
            $recommendations[] = 'Refer to pediatric neurology, consider EEG';
            $severity = 'severe';
        } else if ($physicalExam->neurological === 'tremors') {
            $assessment['neurological'] = 'Possible movement disorder';
            $recommendations[] = 'Neurology consult, assess medications and metabolic causes';
            $severity = 'moderate';
        } else if ($physicalExam->neurological === 'hypotonia') {
            $assessment['neurological'] = 'Possible muscle tone abnormality';
            $recommendations[] = 'Neurological and physiotherapy assessment';
            $severity = 'moderate';
        } else if ($physicalExam->neurological === 'hypertonia') {
            $assessment['neurological'] = 'Possible spasticity';
            $recommendations[] = 'Neurology / physiotherapy consult';
            $severity = 'moderate';
        } else {
            $assessment['neurological'] = 'Normal findings';
            $recommendations[] = 'No immediate action needed';
            $severity = 'low';
        }
   $recommendationText = !empty($recommendations)
    ? implode('; ', $recommendations)
    : 'No significant findings';


    foreach ($assessment as $part => $finding) {
        CdssPhysicalExam::create([
            'physical_exam_id'              => $physicalExam->id,
            'patient_id'                    => $physicalExam->patient_id,
            'alerts'                        => $finding, 
            'risk_level'                    => $severity, 
            'requires_immediate_attention'  => ($severity === 'severe') ? true : false,
            'abnormal_findings'             => $finding, 
            'triggered_rules'               => $part,  
        ]);
    }
}
}

