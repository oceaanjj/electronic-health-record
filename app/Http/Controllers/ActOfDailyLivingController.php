<?php

namespace App\Http\Controllers;

use App\Models\ActOfDailyLiving;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\ActOfDailyLivingCdssService;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // <-- Import Carbon

class ActOfDailyLivingController extends Controller
{
    /**
     * Finds and returns the ADL record for a given patient, date, and day.
     */
    private function getAdlRecord($patientId, $date, $dayNo)
    {
        if (!$patientId || !$date || !$dayNo) {
            return null;
        }

        return ActOfDailyLiving::where('patient_id', $patientId)
            ->where('date', $date)
            ->where('day_no', $dayNo)
            ->first();
    }

    /**
     * Calculates the correct Day No. based on admission date.
     */
    private function getCalculatedDayNo($patient)
    {
        if ($patient && $patient->admission_date) {
            $admissionDate = Carbon::parse($patient->admission_date)->startOfDay();
            $today = Carbon::today();
            $calculatedDayNo = $admissionDate->diffInDays($today) + 1;
            // Ensure day is at least 1
            return ($calculatedDayNo > 0) ? $calculatedDayNo : 1;
        }
        return 1; // Default to 1
    }


    /**
     * Handles the AJAX request for patient selection.
     */
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedPatient = Patient::find($patientId);
        $adlData = null;

        // --- START: MODIFIED LOGIC ---
        // Force date to today and calculate Day No.
        $currentDate = Carbon::today()->format('Y-m-d');
        $currentDayNo = 1; // Default

        if ($selectedPatient) {
            $request->session()->put('selected_patient_id', $patientId);

            $currentDayNo = $this->getCalculatedDayNo($selectedPatient);

            // Store the authoritative values in the session
            $request->session()->put('selected_date', $currentDate);
            $request->session()->put('selected_day_no', $currentDayNo);

            // Fetch the ADL record using these new values
            $adlData = $this->getAdlRecord($patientId, $currentDate, $currentDayNo);

            // Run CDSS analysis on fetched data to display alerts
            $alerts = [];
            if ($adlData) {
                $cdssService = new ActOfDailyLivingCdssService();
                $alerts = $cdssService->analyzeFindings($adlData->toArray());
            }
            $request->session()->flash('cdss', $alerts);

        } else {
            // If patient isn't found, clear the session
            $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
        }
        // --- END: MODIFIED LOGIC ---

        $isLoading = $request->header('X-Fetch-Form-Content') === 'true';

        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
            'selectedPatient' => $selectedPatient,
            'currentDate' => $currentDate,
            'currentDayNo' => $currentDayNo,
            'isLoading' => $isLoading,
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

        // --- START: MODIFIED LOGIC ---
        $currentDate = Carbon::today()->format('Y-m-d'); // Always today
        $currentDayNo = 1; // Default

        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                $currentDayNo = $this->getCalculatedDayNo($selectedPatient);

                // Store authoritative values
                $request->session()->put('selected_date', $currentDate);
                $request->session()->put('selected_day_no', $currentDayNo);

                $adlData = $this->getAdlRecord($patientId, $currentDate, $currentDayNo);
            }
        }
        // --- END: MODIFIED LOGIC ---

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

        $cdssService = new ActOfDailyLivingCdssService();

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
            // 'day_no' and 'date' are removed from validation
            'mobility_assessment' => 'nullable|string',
            'hygiene_assessment' => 'nullable|string',
            'toileting_assessment' => 'nullable|string',
            'feeding_assessment' => 'nullable|string',
            'hydration_assessment' => 'nullable|string',
            'sleep_pattern_assessment' => 'nullable|string',
            'pain_level_assessment' => 'nullable|string',
        ]);

        // --- START: MODIFIED LOGIC ---
        // Set authoritative date and day_no
        $validatedData['date'] = Carbon::today()->format('Y-m-d');
        $validatedData['day_no'] = $this->getCalculatedDayNo($patient);
        // --- END: MODIFIED LOGIC ---

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
}