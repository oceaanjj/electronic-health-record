<?php

namespace App\Http\Controllers;

use App\Models\ActOfDailyLiving;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\CdssService;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class ActOfDailyLivingController extends Controller
{
    /**
     * Handles the AJAX request when a patient is selected from the dropdown.
     * It returns the full view, and patient-loader.js will extract the needed content.
     */
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);

        // Clear old date/day to use the new patient's defaults
        $request->session()->forget(['selected_date', 'selected_day_no']);

        // Fetch all data needed to render the full view for the AJAX response
        $patients = Auth::user()->patients;
        $selectedPatient = Patient::find($patientId);
        $adlData = null; // Reset ADL data for the newly selected patient

        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
            'selectedPatient' => $selectedPatient,
        ]);
    }

    /**
     * Handles form submission from date/day changes for a full page reload.
     */
    public function selectDateAndDay(Request $request)
    {
        $request->session()->put('selected_patient_id', $request->input('patient_id'));
        $request->session()->put('selected_date', $request->input('date'));
        $request->session()->put('selected_day_no', $request->input('day_no'));

        return redirect()->route('adl.show');
    }

    /**
     * Displays the initial page or the page after a date/day change.
     */
    public function show(Request $request)
    {
        $patients = Auth::user()->patients;
        $adlData = null;
        $selectedPatient = null;

        $patientId = $request->session()->get('selected_patient_id');
        $date = $request->session()->get('selected_date');
        $dayNo = $request->session()->get('selected_day_no');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient && $date && $dayNo) {
                $adlData = ActOfDailyLiving::where('patient_id', $patientId)
                    ->where('date', $date)
                    ->where('day_no', $dayNo)
                    ->first();
            }
        }

        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
            'selectedPatient' => $selectedPatient,
        ]);
    }

    /**
     * New method for real-time CDSS analysis of a single field, called by alert.js.
     */
    public function analyzeField(Request $request)
    {
        $data = $request->validate([
            'fieldName' => 'required|string',
            'finding' => 'nullable|string',
        ]);

        $cdssService = new CdssService('adl_rules');

        // This method should exist in your CdssService to analyze one field
        $alert = $cdssService->analyzeSingleFinding($data['fieldName'], $data['finding'] ?? '');

        return response()->json($alert);
    }

    //-----------------OLD---------------
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
        ], [
            'patient_id.required' => 'Please choose a patient first.',
            'patient_id.exists' => 'Please choose a patient first.',
        ]);

        //****
        $user_id = Auth::id();
        $patient = Patient::where('patient_id', $request->patient_id)
            ->where('user_id', $user_id)
            ->first();
        if (!$patient) {
            return back()->with('error', 'Unauthorized patient access.');
        }

        if (!$request->has('patient_id')) {
            return back()->with('error', 'No patient selected.');
        }
        //****

        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|between:1,30',
            'date' => 'required|date',
            'mobility_assessment' => 'nullable|string',
            'hygiene_assessment' => 'nullable|string',
            'toileting_assessment' => 'nullable|string',
            'feeding_assessment' => 'nullable|string',
            'hydration_assessment' => 'nullable|string',
            'sleep_pattern_assessment' => 'nullable|string',
            'pain_level_assessment' => 'nullable|string',
        ]);

        $existingAdl = ActOfDailyLiving::where('patient_id', $validatedData['patient_id'])
            ->where('date', $validatedData['date'])
            ->where('day_no', $validatedData['day_no'])
            ->first();

        if ($existingAdl) {
            $existingAdl->update($validatedData);
            $message = 'ADL data updated successfully!';

            AuditLogController::log(
                'ADL Record Updated',
                'User ' . Auth::user()->username . ' updated an existing ADL record.',
                ['patient_id' => $validatedData['patient_id']]
            );
        } else {
            ActOfDailyLiving::create($validatedData);
            $message = 'ADL data saved successfully!';

            AuditLogController::log(
                'ADL Record Created',
                'User ' . Auth::user()->username . ' created a new ADL record.',
                ['patient_id' => $validatedData['patient_id']]
            );
        }

        // Run CDSS Analysis
        $filteredData = array_filter($validatedData);
        $cdssService = new CdssService('adl_rules');
        $alerts = $cdssService->analyzeFindings($filteredData);

        return redirect()->route('adl.show')
            ->with('cdss', $alerts)
            ->with('success', $message);
    }



    public function runCdssAnalysis(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|between:1,30',
            'date' => 'required|date',
            'mobility_assessment' => 'nullable|string',
            'hygiene_assessment' => 'nullable|string',
            'toileting_assessment' => 'nullable|string',
            'feeding_assessment' => 'nullable|string',
            'hydration_assessment' => 'nullable|string',
            'sleep_pattern_assessment' => 'nullable|string',
            'pain_level_assessment' => 'nullable|string',
        ]);

        $adl = ActOfDailyLiving::updateOrCreate(
            ['patient_id' => $validatedData['patient_id'], 'date' => $validatedData['date']],
            $validatedData
        );

        $cdssService = new CdssService('adl_rules');
        $analysisResults = $cdssService->analyzeFindings($adl->toArray());

        return redirect()->route('adl.show', [
            'patient_id' => $validatedData['patient_id'],
            'date' => $validatedData['date']
        ])->with('cdss', $analysisResults)
            ->with('success', 'CDSS Analysis complete!');
    }
    //-----------------OLD---------------

}


