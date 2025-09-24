<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DischargePlan;
use App\Models\Patient;
use Throwable;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class DischargePlanningController extends Controller
{

    public function selectPatient(Request $request)
    {
        // Get the selected patient ID from the POST request
        $patientId = $request->input('patient_id');

        // Redirect to the show method, passing the patient_id as a query parameter in the session flash
        return redirect()->route('discharge-planning')->with('selected_patient_id', $patientId);
    }

    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $dischargePlan = null;

        // Get the patient_id from the session, not the GET request
        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                $dischargePlan = DischargePlan::where('patient_id', $patientId)->first();
            }
            if ($dischargePlan) {
                $request->session()->flash('old', $dischargePlan->toArray());
            }
        }

        return view('discharge-planning', compact('patients', 'selectedPatient', 'dischargePlan'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
        ], [
            'patient_id.required' => 'Please choose a patient first.',
            'patient_id.exists' => 'Please choose a patient first.',
        ]);


        $data = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'criteria_feverRes' => 'nullable|string',
            'criteria_patientCount' => 'nullable|string',
            'criteria_manageFever' => 'nullable|string',
            'criteria_manageFever2' => 'nullable|string',
            'instruction_med' => 'nullable|string',
            'instruction_appointment' => 'nullable|string',
            'instruction_fluidIntake' => 'nullable|string',
            'instruction_exposure' => 'nullable|string',
            'instruction_complications' => 'nullable|string',
        ]);

        $existingPlan = DischargePlan::where('patient_id', $data['patient_id'])->first();

        if ($existingPlan) {
            $existingPlan->update($data);
            $message = 'Discharge Planning record updated successfully!';

            //  audit log for update
            AuditLogController::log(
                'Discharge Plan Updated',
                'User ' . Auth::user()->username . ' updated an existing Discharge Plan record.',
                ['patient_id' => $data['patient_id']]
            );

        } else {
            DischargePlan::create($data);
            $message = 'Discharge Planning record created successfully.';

            // audit log for creation
            AuditLogController::log(
                'Discharge Plan Created',
                'User ' . Auth::user()->username . ' created a new Discharge Plan record.',
                ['patient_id' => $data['patient_id']]
            );
        }

        return redirect()->route('discharge-planning')->with('success', $message);
    }
}
