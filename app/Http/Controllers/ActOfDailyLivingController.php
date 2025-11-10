<?php

namespace App\Http\Controllers;

use App\Models\ActOfDailyLiving;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\ActOfDailyLivingCdssService;
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
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedPatient = Patient::find($patientId);
        $adlData = null;

        // Default values for rendering the view if selection fails
        $currentDate = now()->format('Y-m-d');
        $currentDayNo = 1;

        if ($selectedPatient) {
            $request->session()->put('selected_patient_id', $patientId);

            // 1.Get date/day from request (for date/day change) or from session (for page reload or fresh patient selection)
            $date = $request->input('date');
            $dayNo = $request->input('day_no');

            // Check if the request is ONLY a patient selection (no date/day change)
            $isNewPatientSelection = is_null($date) && is_null($dayNo);

            if ($isNewPatientSelection) {
                // If a patient is newly selected (no date/day in request), reset to admission date and day 1
                // Determine the correct default date (Admission Date)
                $admissionDate = \Carbon\Carbon::parse($selectedPatient->admission_date);
                $dayNo = $admissionDate->diffInDays(now()) + 1;
                $date = $admissionDate->copy()->addDays($dayNo - 1)->format('Y-m-d');
            } else {
                // This is a date/day change request. Fallback to session if request values are missing.
                $date = $date ?? $request->session()->get('selected_date');
                $dayNo = $dayNo ?? $request->session()->get('selected_day_no');

                // Final fallback if session is also empty (unlikely after the above block)
                if (!$date || !$dayNo) {
                    $date = $selectedPatient->admission_date ? \Carbon\Carbon::parse($selectedPatient->admission_date)->format('Y-m-d') : now()->format('Y-m-d');
                    $admissionDate = \Carbon\Carbon::parse($selectedPatient->admission_date);
                    $dayNo = $admissionDate->diffInDays(now()) + 1;
                }
            }


            // 2. Store the determined date/day selection in the session (CRITICAL STEP)
            $request->session()->put('selected_date', $date);
            $request->session()->put('selected_day_no', $dayNo);

            // Set variables for the view
            $currentDate = $date;
            $currentDayNo = $dayNo;
            $totalDaysSinceAdmission = 0;
            if ($selectedPatient && $selectedPatient->admission_date) {
                $admissionDate = \Carbon\Carbon::parse($selectedPatient->admission_date);
                $totalDaysSinceAdmission = $admissionDate->diffInDays(now()) + 1;
            }

            // 3. Fetch the ADL record
            $adlData = $this->getAdlRecord($patientId, $currentDate, $currentDayNo);

            // 4. Run CDSS analysis on fetched data to display alerts
            $alerts = [];
            if ($adlData) {
                $cdssService = new \App\Services\ActOfDailyLivingCdssService();
                $alerts = $cdssService->analyzeFindings($adlData->toArray());
            }
            $request->session()->flash('cdss', $alerts);

        } else {
            // If patient isn't found, clear the session
            $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
        }

        // Check if this is an AJAX request for a content refresh
        $isLoading = $request->header('X-Fetch-Form-Content') === 'true';

        // Return the rendered view. JS extracts the #form-content-container from this.
        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
            'selectedPatient' => $selectedPatient,
            // Pass the explicit variables for the Blade template to ensure immediate update
            'currentDate' => $currentDate,
            'currentDayNo' => $currentDayNo,
            'isLoading' => $isLoading,
            'totalDaysSinceAdmission' => $totalDaysSinceAdmission,
        ]);
    }

    /**
     * Displays the initial page, loading data based on session state.
     */
    public function show(Request $request)
    {
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $adlData = null;
        $selectedPatient = null;
        $currentDate = now()->format('Y-m-d'); // Default
        $currentDayNo = 1; // Default
        $totalDaysSinceAdmission = 0;

        $patientId = $request->session()->get('selected_patient_id');
        $date = $request->session()->get('selected_date');
        $dayNo = $request->session()->get('selected_day_no');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                $admissionDate = \Carbon\Carbon::parse($selectedPatient->admission_date);
                $totalDaysSinceAdmission = $admissionDate->diffInDays(now()) + 1;
                // Ensure default date is set if patient is selected but date/day is missing (e.g., initial load)
                if (!$date || !$dayNo) {
                    $dayNo = $admissionDate->diffInDays(now()) + 1;
                    $date = $admissionDate->copy()->addDays($dayNo - 1)->format('Y-m-d');
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
            'totalDaysSinceAdmission' => $totalDaysSinceAdmission,
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

        $cdssService = new \App\Services\ActOfDailyLivingCdssService();

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
        $admissionDate = \Carbon\Carbon::parse($patient->admission_date);
        $daysSinceAdmission = $admissionDate->diffInDays(now()) + 1;

        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|between:1,' . $daysSinceAdmission,
            'date' => 'required|date',
            'mobility_assessment' => 'nullable|string',
            'hygiene_assessment' => 'nullable|string',
            'toileting_assessment' => 'nullable|string',
            'feeding_assessment' => 'nullable|string',
            'hydration_assessment' => 'nullable|string',
            'sleep_pattern_assessment' => 'nullable|string',
            'pain_level_assessment' => 'nullable|string',
        ]);

        // Run CDSS Analysis
        $cdssService = new ActOfDailyLivingCdssService();
        $analysisResults = $cdssService->analyzeFindings($validatedData);

        // Map analysis results to the correct database columns
        $alertMapping = [
            'mobility_assessment' => 'mobility_alert',
            'hygiene_assessment' => 'hygiene_alert',
            'toileting_assessment' => 'toileting_alert',
            'feeding_assessment' => 'feeding_alert',
            'hydration_assessment' => 'hydration_alert',
            'sleep_pattern_assessment' => 'sleep_pattern_alert',
            'pain_level_assessment' => 'pain_level_alert',
        ];

        foreach ($alertMapping as $assessmentKey => $alertKey) {
            if (isset($analysisResults[$assessmentKey])) {
                $validatedData[$alertKey] = $analysisResults[$assessmentKey]['alert'];
            }
        }

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

        // Update session with current data for a seamless post-submission view
        $request->session()->put('selected_patient_id', $validatedData['patient_id']);
        $request->session()->put('selected_date', $validatedData['date']);
        $request->session()->put('selected_day_no', $validatedData['day_no']);

        return redirect()->route('adl.show')
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

        $cdssService = new ActOfDailyLivingCdssService(); // Use the dedicated service
        $analysisResults = $cdssService->analyzeFindings($adl->toArray());

        return redirect()->route('adl.show', [
            'patient_id' => $validatedData['patient_id'],
            'date' => $validatedData['date']
        ])->with('cdss', $analysisResults)
            ->with('success', 'CDSS Analysis complete!');
    }

}
