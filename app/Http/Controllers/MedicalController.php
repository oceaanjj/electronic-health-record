<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PresentIllness;
use App\Models\PastMedicalSurgical;
use App\Models\Allergy;
use App\Models\Vaccination;
use App\Models\DevelopmentalHistory;
use App\Models\Patient;
use Throwable;

class MedicalController extends Controller
{
    /**
     * Display the medical history form with patient data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $presentIllness = null;
        $pastMedicalSurgical = null;
        $allergy = null;
        $vaccination = null;
        $developmentalHistory = null;

        // Check if a patient ID is selected from the dropdown
        if ($request->has('patient_id')) {
            $patientId = $request->input('patient_id');
            $selectedPatient = Patient::find($patientId);

            if ($selectedPatient) {
                // Fetch the medical records for the selected patient
                $presentIllness = PresentIllness::where('patient_id', $patientId)->first();
                $pastMedicalSurgical = PastMedicalSurgical::where('patient_id', $patientId)->first();
                $allergy = Allergy::where('patient_id', $patientId)->first();
                $vaccination = Vaccination::where('patient_id', $patientId)->first();
                $developmentalHistory = DevelopmentalHistory::where('patient_id', $patientId)->first();
            }
        }

        // Pass all necessary data to the view
        return view('medical-history', compact('patients', 'selectedPatient', 'presentIllness', 'pastMedicalSurgical', 'allergy', 'vaccination', 'developmentalHistory'));
    }

    /**
     * Store a new medical history record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Check if patient_id is present and not null
            if (empty($request->patient_id)) {
                return back()->with('error', 'Please select a patient before saving the medical history.');
            }

            // Present Illness
            if ($request->has('present_condition_name') && !empty($request->present_condition_name)) {
                PresentIllness::updateOrCreate(
                    ['patient_id' => $request->patient_id],
                    [
                        'condition_name' => $request->present_condition_name,
                        'description' => $request->present_description,
                        'medication' => $request->present_medication,
                        'dosage' => $request->present_dosage,
                        'side_effect' => $request->present_side_effect,
                        'comment' => $request->present_comment,
                    ]
                );
            }

            // Past Medical / Surgical
            if ($request->has('past_condition_name') && !empty($request->past_condition_name)) {
                PastMedicalSurgical::updateOrCreate(
                    ['patient_id' => $request->patient_id],
                    [
                        'condition_name' => $request->past_condition_name,
                        'description' => $request->past_description,
                        'medication' => $request->past_medication,
                        'dosage' => $request->past_dosage,
                        'side_effect' => $request->past_side_effect,
                        'comment' => $request->past_comment,
                    ]
                );
            }

            // Allergies
            if ($request->has('allergy_condition_name') && !empty($request->allergy_condition_name)) {
                Allergy::updateOrCreate(
                    ['patient_id' => $request->patient_id],
                    [
                        'condition_name' => $request->allergy_condition_name,
                        'description' => $request->allergy_description,
                        'medication' => $request->allergy_medication,
                        'dosage' => $request->allergy_dosage,
                        'side_effect' => $request->allergy_side_effect,
                        'comment' => $request->allergy_comment,
                    ]
                );
            }

            // Vaccination
            if ($request->has('vaccine_name') && !empty($request->vaccine_name)) {
                Vaccination::updateOrCreate(
                    ['patient_id' => $request->patient_id],
                    [
                        'condition_name' => $request->vaccine_name,
                        'description' => $request->vaccine_description,
                        'medication' => $request->vaccine_medication,
                        'dosage' => $request->vaccine_dosage,
                        'side_effect' => $request->vaccine_side_effect,
                        'comment' => $request->vaccine_comment,
                    ]
                );
            }

            // Developmental History
            if ($request->has('gross_motor') && !empty($request->gross_motor)) {
                DevelopmentalHistory::updateOrCreate(
                    ['patient_id' => $request->patient_id],
                    [
                        'gross_motor' => $request->gross_motor,
                        'fine_motor' => $request->fine_motor,
                        'language' => $request->language,
                        'cognitive' => $request->cognitive,
                        'social' => $request->social,
                    ]
                );
            }

            return back()->with('success', 'Medical history saved successfully!');
        } catch (Throwable $e) {
            // General error handling
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}