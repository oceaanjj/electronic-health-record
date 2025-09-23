<?php

namespace App\Http\Controllers;

use App\Models\MedicalReconciliation;
use App\Models\HomeMedication;
use App\Models\ChangesInMedication;
use App\Models\Patient;
use Illuminate\Http\Request;
use Throwable;

class MedReconciliationController extends Controller
{
         public function show()
    {
        $patients = Patient::all();
        return view('medication-reconciliation', compact('patients'));
    }
    
    public function store(Request $request)
    {
        try {

            if ($request->has('current_medication')) {
            MedicalReconciliation::create([
                'patient_id' => $request->patient_id,
                'current_med' => $request->current_med,
                'current_dose' => $request->current_dose,
                'current_route' => $request->current_route,
                'current_frequency' => $request->current_frequency,
                'current_indication' => $request->current_indication,
                'current_text' => $request->current_text,
            ]);
            }

            if ($request->has('home_medication')) {
            HomeMedication::create([
                'patient_id' => $request->patient_id,
                'home_med' => $request->home_med,
                'home_dose' => $request->home_dose,
                'home_route' => $request->home_route,
                'home_frequency' => $request->home_frequency,
                'home_indication' => $request->home_indication,
                'home_text' => $request->home_text,
            ]);
            }

            if ($request->has('changes_in_medication')) {
            ChangesInMedication::create([
                'patient_id' => $request->patient_id,
                'change_med' => $request->change_med,
                'change_dose' => $request->change_dose,
                'change_route' => $request->change_route,
                'change_frequency' => $request->change_frequency,
                'change_indication' => $request->change_indication,
                'change_text' => $request->change_text,
            ]);
            }

            MedicalReconciliation::create($request->all());

           return redirect()->back()->with('success', 'Medication Reconciliation record saved successfully.');

            } catch (Throwable $e) {
            // Check for the specific 'patient_id' cannot be null error
            if (str_contains($e->getMessage(), '1048 Column \'patient_id\' cannot be null')) {
                return back()->with('error', 'Please select a patient before saving the medication reconciliation record.');
            }

            // Check for any general database integrity constraint violation
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getCode(), '23000')) {
                return back()->with('error', 'There was a problem saving your data due to missing or invalid information. Please check your inputs and try again.');
            }

            // For any other unexpected errors, provide a generic message
            return back()->with('error', 'An unexpected error occurred while saving the Medication Reconciliation record. Please try again or contact support.');
        }
    }
}
