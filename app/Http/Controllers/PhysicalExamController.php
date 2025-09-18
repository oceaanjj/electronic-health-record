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

        // Run CDSS and redirect with results
        list($cdssPerSection, $alerts) = $this->runCdssAnalysis($physicalExam);

        return redirect()->route('physical-exam.index')
            ->with('success', 'Physical exam registered successfully')
            ->with('cdss_alerts', $alerts)
            ->with('cdss', $cdssPerSection)
            ->withInput();
    }

    private function runCdssAnalysis($physicalExam)
    {
        $cdss = [
            'general_appearance' => null,
            'skin' => null,
            'eyes' => null,
            'oral' => null,
            'cardiovascular' => null,
            'abdomen' => null,
            'extremities' => null,
            'neurological' => null,
        ];

        // general appearance
        $ga = strtolower((string) ($physicalExam->general_appearance ?? ''));
        if ($ga !== '') {
            if (strpos($ga, 'weak') !== false) {
                $cdss['general_appearance'] = 'Monitor for fatigue';
            } elseif (strpos($ga, 'dehydrat') !== false) {
                $cdss['general_appearance'] = 'Start oral/IV rehydration therapy';
            } elseif (strpos($ga, 'pale') !== false) {
                $cdss['general_appearance'] = 'Order CBC test';
            } elseif (strpos($ga, 'letharg') !== false) {
                $cdss['general_appearance'] = 'Neurological exam, rule out meningitis/encephalitis';
            } elseif (strpos($ga, 'ill') !== false) {
                $cdss['general_appearance'] = 'Recommend full physical workup';
            } else {
                $cdss['general_appearance'] = 'Further evaluation required';
            }
        }

        // skin
        $skin = strtolower((string) ($physicalExam->skin_condition ?? ''));
        if ($skin !== '') {
            if (strpos($skin, 'petech') !== false) {
                $cdss['skin'] = 'Risk for bleeding';
            } elseif (strpos($skin, 'cyanosis') !== false) {
                $cdss['skin'] = 'Check oxygen saturation, consider urgent referral';
            } elseif (strpos($skin, 'jaundice') !== false) {
                $cdss['skin'] = 'Order liver function test, bilirubin levels';
            } elseif (strpos($skin, 'rash') !== false || strpos($skin, 'rashes') !== false) {
                $cdss['skin'] = 'Check allergy history, prescribe antihistamine';
            } elseif (strpos($skin, 'flushed') !== false) {
                $cdss['skin'] = 'Assess for fever or inflammation';
            } elseif (strpos($skin, 'warm') !== false) {
                $cdss['skin'] = 'Monitor temperature';
            } elseif (strpos($skin, 'pallor') !== false) {
                $cdss['skin'] = 'Order CBC test to confirm anemia';
            } elseif (strpos($skin, 'normal') !== false) {
                $cdss['skin'] = 'No action needed';
            } else {
                $cdss['skin'] = 'Further evaluation required';
            }
        }

        // eyes
        $eyes = strtolower((string) ($physicalExam->eye_condition ?? ''));
        if ($eyes !== '') {
            if (strpos($eyes, 'vision') !== false && strpos($eyes, 'loss') !== false) {
                $cdss['eyes'] = 'Urgent ophthalmology referral';
            } elseif (strpos($eyes, 'discharge') !== false) {
                $cdss['eyes'] = 'Swab if purulent, hygiene advice, possible antibiotics';
            } elseif (strpos($eyes, 'red') !== false || strpos($eyes, 'redness') !== false) {
                $cdss['eyes'] = 'Recommend ophthalmology consult, eye drops if needed';
            } elseif (strpos($eyes, 'strabismus') !== false) {
                $cdss['eyes'] = 'Refer to ophthalmology for further evaluation';
            } elseif (strpos($eyes, 'normal') !== false) {
                $cdss['eyes'] = 'No action needed';
            } else {
                $cdss['eyes'] = 'Further evaluation required';
            }
        }

        // oral cavity
        $oral = strtolower((string) ($physicalExam->oral_condition ?? ''));
        if ($oral !== '') {
            if (strpos($oral, 'bleeding') !== false || strpos($oral, 'bleeding_gums') !== false || (strpos($oral, 'gum') !== false && strpos($oral, 'bleed') !== false)) {
                $cdss['oral'] = 'Order CBC, check platelets, dental referral';
            } elseif (strpos($oral, 'swell') !== false) {
                $cdss['oral'] = 'ENT/dental referral, possible antibiotics';
            } elseif (strpos($oral, 'ulcer') !== false) {
                $cdss['oral'] = 'Supportive care, check hydration, refer if severe';
            } elseif (strpos($oral, 'lesion') !== false) {
                $cdss['oral'] = 'Advise dental or ENT check-up';
            } elseif (strpos($oral, 'normal') !== false) {
                $cdss['oral'] = 'No action needed';
            } else {
                $cdss['oral'] = 'Further evaluation required';
            }
        }

       

        // combine
        $alerts = '';
        foreach ($cdss as $msg) {
            if (!empty($msg)) {
                if ($alerts !== '') {
                    $alerts .= '; ';
                }
                $alerts .= $msg;
            }
        }
        if ($alerts === '') {
            $alerts = 'No significant findings';
        }

        // Store cdss
        CdssPhysicalExam::create([
            'physical_exam_id' => $physicalExam->id,
            'patient_id'       => $physicalExam->patient_id,
            'alerts'           => $alerts,
        ]);

        return [$cdss, $alerts];
    }
}
