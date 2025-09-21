<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DischargePlan;
use App\Models\Patient;
use Throwable;

class DischargePlanningController extends Controller
{
        public function show()
    {
        $patients = Patient::all();
        return view('discharge-planning', compact('patients'));
    }

       public function store(Request $request)
    {
        try {
    if ($request->has('discharge_planning')) {
            DischargePlan::create([
                'patient_id' => $request->patient_id,
                'criteria_feverRes' => $request->criteria_feverRes,
                'criteria_patientCount' => $request->criteria_patientCount,
                'criteria_manageFever' => $request->criteria_manageFever,
                'criteria_manageFever2' => $request->criteria_manageFever2,
                'instruction_med' => $request->instruction_med,
                'instruction_appointment' => $request->instruction_appointment,
                'instruction_fluidIntake' => $request->instruction_fluidIntake,
                'instruction_exposure' => $request->instruction_exposure,
                'instruction_complications' => $request->instruction_complications,
            ]);
            }
             DischargePlan::create($request->all());

            return redirect()->back()->with('success', 'Discharge Planning record saved successfully.');

            } catch (Throwable $e) {
            // Check for the specific 'patient_id' cannot be null error
            if (str_contains($e->getMessage(), '1048 Column \'patient_id\' cannot be null')) {
                return back()->with('error', 'Please select a patient before saving the discharge planning record.');
            }

            // Check for any general database integrity constraint violation
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getCode(), '23000')) {
                return back()->with('error', 'There was a problem saving your data due to missing or invalid information. Please check your inputs and try again.');
            }

            // For any other unexpected errors, provide a generic message
            return back()->with('error', 'An unexpected error occurred while saving the Discharge Planning record. Please try again or contact support.');
        }
    }
}
