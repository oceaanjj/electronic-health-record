<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresentIllness;
use App\Models\PastMedicalSurgical;
use App\Models\Allergy;
use App\Models\Vaccination;
use App\Models\DevelopmentalHistory;
use App\Models\Patient;

class MedicalController extends Controller
{

    public function show()
    {
        $patients = Patient::all();
        return view('medical-history', compact('patients'));
    }

    public function store(Request $request)
    {
        // Present Illness
        if ($request->has('present_condition_name')) {
            PresentIllness::create([
                'patient_id' => $request->patient_id,
                'condition_name' => $request->present_condition_name,
                'description' => $request->present_description,
                'medication' => $request->present_medication,
                'dosage' => $request->present_dosage,
                'side_effect' => $request->present_side_effect,
                'comment' => $request->present_comment,
            ]);
        }

        // Past Medical / Surgical
        if ($request->has('past_condition_name')) {
            PastMedicalSurgical::create([
                'patient_id' => $request->patient_id,
                'condition_name' => $request->past_condition_name,
                'description' => $request->past_description,
                'medication' => $request->past_medication,
                'dosage' => $request->past_dosage,
                'side_effect' => $request->past_side_effect,
                'comment' => $request->past_comment,
            ]);
        }

        // Allergies
        if ($request->has('allergy_condition_name')) {
            Allergy::create([
                'patient_id' => $request->patient_id,
                'condition_name' => $request->allergy_condition_name,
                'description' => $request->allergy_description,
                'medication' => $request->allergy_medication,
                'dosage' => $request->allergy_dosage,
                'side_effect' => $request->allergy_side_effect,
                'comment' => $request->allergy_comment,
            ]);
        }

        // Vaccination
        if ($request->has('vaccine_name')) {
            Vaccination::create([
                'patient_id' => $request->patient_id,
                'condition_name' => $request->vaccine_name,
                'description' => $request->vaccine_description,
                'medication' => $request->vaccine_medication,
                'dosage' => $request->vaccine_dosage,
                'side_effect' => $request->vaccine_side_effect,
                'comment' => $request->vaccine_comment,
            ]);
        }

        // Developmental History
        if ($request->has('gross_motor')) {
            DevelopmentalHistory::create([
                 'patient_id' => $request->patient_id,               
                'gross_motor' => $request->gross_motor,
                'fine_motor' => $request->fine_motor,
                'language' => $request->language,
                'cognitive' => $request->cognitive,
                'social' => $request->social,
            ]);
        }

        return back()->with('success', 'Medical history saved successfully!');
    }


}
