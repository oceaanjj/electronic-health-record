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
    /**
     * Display the Medication Reconciliation form and optionally populate it with patient data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $currentMedication = null;
        $homeMedication = null;
        $changesInMedication = null;

        // Check if a patient ID is selected from the dropdown
        if ($request->has('patient_id')) {
            $patientId = $request->input('patient_id');
            $selectedPatient = Patient::find($patientId);

            if ($selectedPatient) {
                // Fetch the medical records for the selected patient
                $currentMedication = MedicalReconciliation::where('patient_id', $patientId)->first();
                $homeMedication = HomeMedication::where('patient_id', $patientId)->first();
                $changesInMedication = ChangesInMedication::where('patient_id', $patientId)->first();
            }
        }

        // Pass all necessary data to the view
        return view('medication-reconciliation', compact('patients', 'selectedPatient', 'currentMedication', 'homeMedication', 'changesInMedication'));
    }
    
    /**
     * Store a new or update an existing Medication Reconciliation record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Check if patient_id is present and not null
            if (empty($request->patient_id)) {
                return back()->with('error', 'Please select a patient before saving the medication reconciliation record.');
            }

            // Update or create the record for "Patient's Current Medication"
            MedicalReconciliation::updateOrCreate(
                ['patient_id' => $request->patient_id],
                [
                    'current_med' => $request->current_med,
                    'current_dose' => $request->current_dose,
                    'current_route' => $request->current_route,
                    'current_frequency' => $request->current_frequency,
                    'current_indication' => $request->current_indication,
                    'current_text' => $request->current_text,
                ]
            );

            // Update or create the record for "Patient's Home Medication"
            HomeMedication::updateOrCreate(
                ['patient_id' => $request->patient_id],
                [
                    'home_med' => $request->home_med,
                    'home_dose' => $request->home_dose,
                    'home_route' => $request->home_route,
                    'home_frequency' => $request->home_frequency,
                    'home_indication' => $request->home_indication,
                    'home_text' => $request->home_text,
                ]
            );

            // Update or create the record for "Changes in Medication During Hospitalization"
            ChangesInMedication::updateOrCreate(
                ['patient_id' => $request->patient_id],
                [
                    'change_med' => $request->change_med,
                    'change_dose' => $request->change_dose,
                    'change_route' => $request->change_route,
                    'change_frequency' => $request->change_frequency,
                    'change_indication' => $request->change_indication,
                    'change_text' => $request->change_text,
                ]
            );

            return redirect()->back()->with('success', 'Medication Reconciliation record saved successfully.');

        } catch (Throwable $e) {
            // General error handling
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}