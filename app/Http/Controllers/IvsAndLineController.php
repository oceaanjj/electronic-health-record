<?php

namespace App\Http\Controllers;

use App\Models\IvsAndLine;
use App\Models\Patient;
use Illuminate\Http\Request;
use Throwable;

class IvsAndLineController extends Controller
{
    /**
     * Display the IVs and Lines form and optionally populate it with patient data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $ivsAndLineRecord = null;

        // Check if a patient ID is selected from the dropdown
        if ($request->has('patient_id')) {
            $patientId = $request->input('patient_id');
            $selectedPatient = Patient::find($patientId);

            if ($selectedPatient) {
                // Fetch the IVs and Lines record for the selected patient
                $ivsAndLineRecord = IvsAndLine::where('patient_id', $patientId)->first();
            }
        }

        // Pass all necessary data to the view
        return view('ivs-and-lines', compact('patients', 'selectedPatient', 'ivsAndLineRecord'));
    }
    
    /**
     * Store a new or update an existing IVs and Lines record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Check if patient_id is present and not null
            if (empty($request->patient_id)) {
                return back()->with('error', 'Please select a patient before saving the record.');
            }

            // The updateOrCreate method will either find the record for the patient
            // and update it, or create a new one if it doesn't exist.
            IvsAndLine::updateOrCreate(
                ['patient_id' => $request->patient_id],
                [
                    'iv_fluid' => $request->iv_fluid,
                    'rate' => $request->rate,
                    'site' => $request->site,
                    'status' => $request->status,
                ]
            );

            return redirect()->back()->with('success', 'IVs and Lines record saved successfully.');
        
        } catch (Throwable $e) {
            // Check for the specific 'patient_id' cannot be null error
            if (str_contains($e->getMessage(), '1048 Column \'patient_id\' cannot be null')) {
                return back()->with('error', 'Please select a patient before submitting the IVs and Lines record.');
            }

            // Check for any general database integrity constraint violation
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getCode(), '23000')) {
                return back()->with('error', 'There was a problem saving your data due to missing or invalid information. Please check your inputs and try again.');
            }

            // For any other unexpected errors, provide a generic message
            return back()->with('error', 'An unexpected error occurred while saving the IVs and Lines record. Please try again or contact support.');
        }
    }
}