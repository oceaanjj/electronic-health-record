<?php

namespace App\Http\Controllers;

use App\Models\MedicalReconciliation;
use App\Models\HomeMedication;
use App\Models\ChangesInMedication;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Throwable;

class MedReconciliationController extends Controller
{

    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);
        return redirect()->route('medication-reconciliation');
    }

    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $currentMedication = null;
        $homeMedication = null;
        $changesInMedication = null;

        // Get the patient ID from the session
        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
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


    public function store(Request $request)
    {

        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
        ], [
            'patient_id.required' => 'Please choose a patient first.',
            'patient_id.exists' => 'Please choose a patient first.',
        ]);


        if (!$request->has('patient_id')) {
            return back()->with('error', 'No patient selected.');
        }

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'current_med' => 'nullable|string',
            'current_dose' => 'nullable|string',
            'current_route' => 'nullable|string',
            'current_frequency' => 'nullable|string',
            'current_indication' => 'nullable|string',
            'current_text' => 'nullable|string',
            'home_med' => 'nullable|string',
            'home_dose' => 'nullable|string',
            'home_route' => 'nullable|string',
            'home_frequency' => 'nullable|string',
            'home_indication' => 'nullable|string',
            'home_text' => 'nullable|string',
            'change_med' => 'nullable|string',
            'change_dose' => 'nullable|string',
            'change_route' => 'nullable|string',
            'change_frequency' => 'nullable|string',
            'change_text' => 'nullable|string',
        ]);

        $patientId = $data['patient_id'];
        $username = Auth::user() ? Auth::user()->username : 'Guest';

        $created = false;
        $updated = false;

        // Handle Patient's Current Medication
        $currentMed = MedicalReconciliation::updateOrCreate(['patient_id' => $patientId], $request->only([
            'current_med',
            'current_dose',
            'current_route',
            'current_frequency',
            'current_indication',
            'current_text',
        ]));
        if ($currentMed->wasRecentlyCreated) {
            $created = true;
        } else {
            $updated = true;
        }

        // Handle Patient's Home Medication
        $homeMed = HomeMedication::updateOrCreate(['patient_id' => $patientId], $request->only([
            'home_med',
            'home_dose',
            'home_route',
            'home_frequency',
            'home_indication',
            'home_text',
        ]));
        if ($homeMed->wasRecentlyCreated) {
            $created = true;
        } elseif ($homeMed->wasChanged()) {
            $updated = true;
        }

        // Handle Changes in Medication During Hospitalization
        $changesInMed = ChangesInMedication::updateOrCreate(['patient_id' => $patientId], $request->only([
            'change_med',
            'change_dose',
            'change_route',
            'change_frequency',
            'change_text',
        ]));
        if ($changesInMed->wasRecentlyCreated) {
            $created = true;
        } elseif ($changesInMed->wasChanged()) {
            $updated = true;
        }

        // Audit log and alert message
        $message = '';
        $alert = '';
        $action = '';
        if ($created) {
            $message = 'created a new Medication Reconciliation record.';
            $alert = 'Medication Reconciliation data saved successfully!';
            $action = 'Created';
        } elseif ($updated) {
            $message = 'updated an existing Medication Reconciliation record.';
            $alert = 'Medication Reconciliation data updated successfully!';
            $action = 'Updated';
        }

        if ($message) {
            AuditLogController::log(
                'Medication Reconciliation ' . $action,
                'User ' . $username . ' ' . $message,
                ['patient_id' => $patientId]
            );
        }

        return redirect()->route('medication-reconciliation')
            ->with('success', $alert);

    }
}
