<?php

namespace App\Http\Controllers;

use App\Models\ActOfDailyLiving;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\AdlCdssService; // Corrected usage for consistency
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class ActOfDailyLivingController extends Controller
{
    /**
     * Finds and returns the ADL record for a given patient, date, and day.
     */
    private function getAdlRecord($patientId, $date, $dayNo)
    {
        if (!$patientId || (!$date && $date !== 0) || (!$dayNo && $dayNo !== 0)) {
            return null;
        }

        return ActOfDailyLiving::where('patient_id', $patientId)
            ->where('date', $date)
            ->where('day_no', $dayNo)
            ->first();
    }

    /**
     * Handles the AJAX request for both patient selection AND date/day change.
     */
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $patients = Auth::user()->patients;
        $selectedPatient = Patient::find($patientId);
        $adlData = null;

        // Default values for rendering the view if selection fails
        $currentDate = now()->format('Y-m-d');
        $currentDayNo = 1;

        if ($selectedPatient) {
            $request->session()->put('selected_patient_id', $patientId);

            // 1. Determine Date and Day No
            $date = $request->input('date') ?? $request->session()->get('selected_date');
            $dayNo = $request->input('day_no') ?? $request->session()->get('selected_day_no');

            // If a patient is newly selected (no date/day in session/request), reset to admission date and day 1
            if (!$date || !$dayNo) {
                // Determine the correct default date (Admission Date)
                $date = $selectedPatient->admission_date ? $selectedPatient->admission_date->format('Y-m-d') : now()->format('Y-m-d');
                $dayNo = 1;
            }

            // 2. Store the determined date/day selection in the session (CRITICAL STEP)
            $request->session()->put('selected_date', $date);
            $request->session()->put('selected_day_no', $dayNo);

            // Set variables for the view
            $currentDate = $date;
            $currentDayNo = $dayNo;

            // 3. Fetch the ADL record
            $adlData = $this->getAdlRecord($patientId, $currentDate, $currentDayNo);

            // 4. Run CDSS analysis on fetched data to display alerts
            $alerts = [];
            if ($adlData) {
                $cdssService = new \App\Services\AdlCdssService();
                $alerts = $cdssService->analyzeFindings($adlData->toArray());
            }
            $request->session()->flash('cdss', $alerts);

        } else {
            // If patient isn't found, clear the session
            $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
        }

        // Return the rendered view. JS extracts the #form-content-container from this.
        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
            'selectedPatient' => $selectedPatient,
            // Pass the explicit variables for the Blade template to ensure immediate update
            'currentDate' => $currentDate,
            'currentDayNo' => $currentDayNo,
        ]);
    }

    /**
     * Displays the initial page, loading data based on session state.
     */
    public function show(Request $request)
    {
        $patients = Auth::user()->patients;
        $adlData = null;
        $selectedPatient = null;
        $currentDate = now()->format('Y-m-d'); // Default
        $currentDayNo = 1; // Default

        $patientId = $request->session()->get('selected_patient_id');
        $date = $request->session()->get('selected_date');
        $dayNo = $request->session()->get('selected_day_no');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                // Ensure default date is set if patient is selected but date/day is missing (e.g., initial load)
                if (!$date || !$dayNo) {
                    $date = $selectedPatient->admission_date ? $selectedPatient->admission_date->format('Y-m-d') : now()->format('Y-m-d');
                    $dayNo = 1;
                    $request->session()->put('selected_date', $date);
                    $request->session()->put('selected_day_no', $dayNo);
                }

                $currentDate = $date;
                $currentDayNo = $dayNo;
                $adlData = $this->getAdlRecord($patientId, $currentDate, $currentDayNo);
            }
        }

        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
            'selectedPatient' => $selectedPatient,
            'currentDate' => $currentDate,
            'currentDayNo' => $currentDayNo,
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

        $cdssService = new \App\Services\AdlCdssService();

        $alert = $cdssService->analyzeSingleFinding($data['fieldName'], $data['finding'] ?? '');

        return response()->json($alert);
    }

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
        $cdssService = new \App\Services\AdlCdssService(); // Use the dedicated service
        $alerts = $cdssService->analyzeFindings($filteredData);

        // Update session with current data for a seamless post-submission view
        $request->session()->put('selected_patient_id', $validatedData['patient_id']);
        $request->session()->put('selected_date', $validatedData['date']);
        $request->session()->put('selected_day_no', $validatedData['day_no']);


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

        $cdssService = new \App\Services\AdlCdssService(); // Use the dedicated service
        $analysisResults = $cdssService->analyzeFindings($adl->toArray());

        return redirect()->route('adl.show', [
            'patient_id' => $validatedData['patient_id'],
            'date' => $validatedData['date']
        ])->with('cdss', $analysisResults)
            ->with('success', 'CDSS Analysis complete!');
    }

}
