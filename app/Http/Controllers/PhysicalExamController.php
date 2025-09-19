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

    public function runCdssAnalysis($physicalExam)
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
            } elseif (strpos($ga, 'irritable') !== false) {
                $cdss['general_appearance'] = 'Assess for pain, infection, or possible otitis media';
            } elseif (strpos($ga, 'cyanosis') !== false || strpos($ga, 'bluish') !== false || strpos($ga, 'blue') !== false) {
                $cdss['general_appearance'] = 'Check oxygen saturation, possible congenital heart or respiratory issue';
            } elseif (strpos($ga, 'jaundice') !== false || strpos($ga, 'yellow') !== false) {
                $cdss['general_appearance'] = 'Check bilirubin levels, evaluate for neonatal jaundice or liver disease';
            } elseif (strpos($ga, 'seizure') !== false || strpos($ga, 'convulsion') !== false || strpos($ga, 'seizing') !== false) {
                $cdss['general_appearance'] = 'Immediate neuro evaluation, rule out febrile seizures or epilepsy';
            } elseif (strpos($ga, 'tachypnea') !== false || strpos($ga, 'rapid breathing') !== false) {
                $cdss['general_appearance'] = 'Check for pneumonia, asthma, or respiratory distress';
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
            } elseif (strpos($skin, 'diaper') !== false) {
                $cdss['skin'] = 'Diaper rash - advise barrier creams, frequent diaper change';
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
            } elseif (strpos($eyes, 'watery') !== false || strpos($eyes, 'tearing') !== false) {
                $cdss['eyes'] = 'Consider congenital nasolacrimal duct obstruction; advise lacrimal massage';
            } elseif (strpos($eyes, 'swelling') !== false || strpos($eyes, 'puffy') !== false) {
                $cdss['eyes'] = 'Possible periorbital cellulitis vs. allergy; urgent evaluation if fever present';
            } elseif (strpos($eyes, 'itchy') !== false || strpos($eyes, 'allergy') !== false) {
                $cdss['eyes'] = 'Likely allergic conjunctivitis; antihistamine drops may be considered';
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
            } elseif (strpos($oral, 'thrush') !== false || strpos($oral, 'candida') !== false || strpos($oral, 'candidiasis') !== false) {
                $cdss['oral'] = 'Oral thrush - antifungal (nystatin), check feeding and hygiene';
            } elseif (strpos($oral, 'teething') !== false) {
                $cdss['oral'] = 'Teething - reassure parents, supportive care (cold teether, analgesics if needed)';
            } elseif (strpos($oral, 'aphthous') !== false || strpos($oral, 'canker sore') !== false) {
                $cdss['oral'] = 'Aphthous ulcers - supportive care, hydration, monitor for recurrence';
            } elseif (strpos($oral, 'normal') !== false) {
                $cdss['oral'] = 'No action needed';
            } else {
                $cdss['oral'] = 'Further evaluation required';
            }
        }

        $cardiovascular = strtolower((string) ($physicalExam->cardiovascular ?? ''));
        if ($cardiovascular !== '') {
            if (strpos($cardiovascular, 'murmur') !== false) {
                $cdss['cardiovascular'] = 'Echocardiogram, cardiology referral';
            } elseif (strpos($cardiovascular, 'irregular') !== false || strpos($cardiovascular, 'arrhythmia') !== false) {
                $cdss['cardiovascular'] = 'ECG, cardiology referral';
            } elseif (strpos($cardiovascular, 'tachycardia') !== false) {
                $cdss['cardiovascular'] = 'Check for fever, dehydration, anxiety';
            } elseif (strpos($cardiovascular, 'bradycardia') !== false) {
                $cdss['cardiovascular'] = 'Assess for fatigue, dizziness, syncope';
            } elseif (strpos($cardiovascular, 'bounding pulses') !== false) {
                $cdss['cardiovascular'] = 'Consider PDA or hyperdynamic circulation';
            } elseif (strpos($cardiovascular, 'weak pulses') !== false || strpos($cardiovascular, 'diminished pulses') !== false) {
                $cdss['cardiovascular'] = 'Rule out coarctation of aorta, shock, sepsis';
            } elseif (strpos($cardiovascular, 'normal') !== false) {
                $cdss['cardiovascular'] = 'No action needed';
            } else {
                $cdss['cardiovascular'] = 'Further evaluation required';
            }
        }

        $abdomen = strtolower((string) ($physicalExam->abdomen_condition ?? ''));
        if ($abdomen !== '') {
            if (strpos($abdomen, 'tender') !== false) {
                $cdss['abdomen'] = 'Abdominal ultrasound, consider surgical consult';
            } elseif (strpos($abdomen, 'distend') !== false) {
                $cdss['abdomen'] = 'Check for bowel obstruction, consider imaging';
            } elseif (strpos($abdomen, 'mass') !== false) {
                $cdss['abdomen'] = 'Urgent imaging, surgical consult';
            } elseif (strpos($abdomen, 'guarding') !== false || strpos($abdomen, 'rebound') !== false) {
                $cdss['abdomen'] = 'Urgent surgical consult';
            } elseif (strpos($abdomen, 'normal') !== false) {
                $cdss['abdomen'] = 'No action needed';
            } else {
                $cdss['abdomen'] = 'Further evaluation required';
            }
        }

        $extremities = strtolower((string) ($physicalExam->extremities ?? ''));
        if ($extremities !== '') {
            if (strpos($extremities, 'edema') !== false) {
                $cdss['extremities'] = 'Check cardiac/renal function, consider diuretics';
            } elseif (strpos($extremities, 'deform') !== false) {
                $cdss['extremities'] = 'Orthopedic referral, possible imaging';
            } elseif (strpos($extremities, 'cyanosis') !== false) {
                $cdss['extremities'] = 'Assess oxygenation, consider urgent referral';
            } elseif (strpos($extremities, 'clubbing') !== false) {
                $cdss['extremities'] = 'Evaluate for chronic hypoxia, consider chest imaging';
            } elseif (strpos($extremities, 'fracture') !== false || strpos($extremities, 'injury') !== false) {
                $cdss['extremities'] = 'Suspected fracture — immobilize, request X-ray, orthopedic referral';
            } elseif (strpos($extremities, 'limp') !== false || strpos($extremities, 'gait') !== false) {
                $cdss['extremities'] = 'Assess gait abnormality — rule out hip dysplasia, trauma, or infection';
            } elseif (strpos($extremities, 'bow legs') !== false || strpos($extremities, 'genu varum') !== false) {
                $cdss['extremities'] = 'Common in toddlers; assess if physiologic or rickets';
            } elseif (strpos($extremities, 'knock knees') !== false || strpos($extremities, 'genu valgum') !== false) {
                $cdss['extremities'] = 'Usually physiologic at age 3–7; consider rickets if persistent/severe';
            } elseif (strpos($extremities, 'swelling') !== false) {
                $cdss['extremities'] = 'Rule out infection (cellulitis, septic arthritis) or trauma';
            } elseif (strpos($extremities, 'congenital') !== false) {
                $cdss['extremities'] = 'Consider congenital limb anomaly — orthopedic/pediatric genetics referral';
            } elseif (strpos($extremities, 'normal') !== false) {
                $cdss['extremities'] = 'No action needed';
            } else {
                $cdss['extremities'] = 'Further evaluation required';
            }
        }

        $neurological = strtolower((string) ($physicalExam->neurological ?? ''));
        if ($neurological !== '') {
            if (strpos($neurological, 'weak') !== false || strpos($neurological, 'paralysis') !== false) {
                $cdss['neurological'] = 'Urgent neuroimaging, neurology consult';
            } elseif (strpos($neurological, 'sensory') !== false || strpos($neurological, 'numb') !== false || strpos($neurological, 'tingl') !== false) {
                $cdss['neurological'] = 'Neurology referral, consider MRI';
            } elseif (strpos($neurological, 'seizure') !== false) {
                $cdss['neurological'] = 'EEG, neurology consult';
            } elseif (strpos($neurological, 'altered') !== false || strpos($neurological, 'confused') !== false || strpos($neurological, 'letharg') !== false) {
                $cdss['neurological'] = 'Urgent neuroimaging, check glucose, electrolytes';
            } elseif (strpos($neurological, 'developmental delay') !== false || strpos($neurological, 'milestone') !== false) {
                $cdss['neurological'] = 'Refer to developmental pediatrician, consider PT/OT';
            } elseif (strpos($neurological, 'regression') !== false) {
                $cdss['neurological'] = 'Rule out metabolic/degenerative disorder, refer to specialist';
            } elseif (strpos($neurological, 'headache') !== false) {
                $cdss['neurological'] = 'Evaluate for migraine vs intracranial pressure, consider imaging';
            } elseif (strpos($neurological, 'gait') !== false || strpos($neurological, 'walking') !== false) {
                $cdss['neurological'] = 'Assess motor function, PT referral, consider MRI if persistent';
            } elseif (strpos($neurological, 'normal') !== false) {
                $cdss['neurological'] = 'No action needed';
            } else {
                $cdss['neurological'] = 'Further evaluation required';
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
            'patient_id' => $physicalExam->patient_id,
            'alerts' => $alerts,
        ]);

        return [$cdss, $alerts];
    }
}
