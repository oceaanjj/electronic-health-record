<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DischargePlan;
use App\Models\Patient;
use Throwable;

class DischargePlanningController extends Controller
{
    /**
     * Display the Discharge Planning form and optionally populate it with patient data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $dischargePlan = null;

        // Check if a patient ID is selected from the dropdown
        if ($request->has('patient_id')) {
            $patientId = $request->input('patient_id');
            $selectedPatient = Patient::find($patientId);

            if ($selectedPatient) {
                // Fetch the discharge planning record for the selected patient
                $dischargePlan = DischargePlan::where('patient_id', $patientId)->first();
            }
        }

        // Pass all necessary data to the view
        return view('discharge-planning', compact('patients', 'selectedPatient', 'dischargePlan'));
    }

    /**
     * Store a new or update an existing Discharge Planning record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Check if patient_id is present and not null
            if (empty($request->patient_id)) {
                return back()->with('error', 'Please select a patient before saving the discharge planning record.');
            }

            // The updateOrCreate method will either find the record for the patient
            // and update it, or create a new one if it doesn't exist.
            DischargePlan::updateOrCreate(
                ['patient_id' => $request->patient_id],
                [
                    'criteria_feverRes' => $request->criteria_feverRes,
                    'criteria_patientCount' => $request->criteria_patientCount,
                    'criteria_manageFever' => $request->criteria_manageFever,
                    'criteria_manageFever2' => $request->criteria_manageFever2,
                    'instruction_med' => $request->instruction_med,
                    'instruction_appointment' => $request->instruction_appointment,
                    'instruction_fluidIntake' => $request->instruction_fluidIntake,
                    'instruction_exposure' => $request->instruction_exposure,
                    'instruction_complications' => $request->instruction_complications,
                ]
            );

            return redirect()->back()->with('success', 'Discharge Planning record saved successfully.');

        } catch (Throwable $e) {
            // General error handling
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
