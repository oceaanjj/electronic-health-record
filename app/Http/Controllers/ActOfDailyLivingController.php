<?php

namespace App\Http\Controllers;

use App\Models\ActOfDailyLiving;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\ActOfDailyLivingCdssService;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use app\Http\Controllers\ADPIE\NursingDiagnosisController;

class ActOfDailyLivingController extends Controller
{


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
     * 
     * 
     * Handles the AJAX request for both patient selection AND date/day change.
     */
    public function selectPatient(Request $request)
    {
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedPatient = null;
        $adlData = null;
        $totalDaysSinceAdmission = 1; // Default

        // Default values
        $currentDate = now()->format('Y-m-d');
        $currentDayNo = 1;

        $patientId = $request->input('patient_id') ?? $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);

            if ($selectedPatient) {
                $request->session()->put('selected_patient_id', $patientId);

                $admissionDate = Carbon::parse($selectedPatient->admission_date)->startOfDay();
                $today = now()->startOfDay();

                // Calculate exact days since admission (1-based index)
                $totalDaysSinceAdmission = intval($admissionDate->diffInDays($today)) + 1;
                if ($totalDaysSinceAdmission < 1)
                    $totalDaysSinceAdmission = 1;

                // Check inputs
                $reqDate = $request->input('date');
                $reqDayNo = $request->input('day_no');

                // Logic: Is this a fresh patient selection? (No date/day in request)
                $isNewPatientSelection = is_null($reqDate) && is_null($reqDayNo) && $request->has('patient_id');

                if ($isNewPatientSelection) {
                    // CASE 1: User just selected a patient. Force "Latest Day" (Today).
                    $date = $today->format('Y-m-d');
                    $dayNo = $totalDaysSinceAdmission;
                } else {
                    // CASE 2: User is navigating (changing date/day or loading from session)
                    $date = $reqDate ?? $request->session()->get('selected_date');
                    $dayNo = $reqDayNo ?? $request->session()->get('selected_day_no');

                    // Fallback if session empty
                    if (!$date || !$dayNo) {
                        $date = $today->format('Y-m-d');
                        $dayNo = $totalDaysSinceAdmission;
                    }
                }

                // Ensure we don't go into the future
                $selectedDateCarbon = Carbon::parse($date)->startOfDay();
                if ($selectedDateCarbon->isAfter($today)) {
                    $date = $today->format('Y-m-d');
                    $dayNo = $totalDaysSinceAdmission;
                }

                // Save to session
                $request->session()->put('selected_date', $date);
                $request->session()->put('selected_day_no', $dayNo);

                $currentDate = $date;
                $currentDayNo = $dayNo;

                // 3. Fetch the ADL record
                $adlData = $this->getAdlRecord($patientId, $currentDate, $currentDayNo);

                // 4. Run CDSS analysis on fetched data
                $alerts = [];
                $nursingDiagnosisId = null; // Initialize
                if ($adlData) {
                    $cdssService = new ActOfDailyLivingCdssService();
                    $alerts = $cdssService->analyzeFindings($adlData->toArray());

                    // Check for existing NursingDiagnosis
                    $nursingDiagnosis = \App\Models\NursingDiagnosis::where('adl_id', $adlData->id)->first();
                    $nursingDiagnosisId = $nursingDiagnosis ? $nursingDiagnosis->id : null;
                }
                $request->session()->flash('cdss', $alerts);

            } else {
                $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
            }
        } else {
            $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
        }

        // Check if this is an AJAX request for a content refresh
        $isLoading = $request->header('X-Fetch-Form-Content') === 'true';

        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
            'selectedPatient' => $selectedPatient,
            'currentDate' => $currentDate,
            'currentDayNo' => $currentDayNo,
            'isLoading' => $isLoading,
            'totalDaysSinceAdmission' => $totalDaysSinceAdmission,
            'alerts' => $alerts ?? [], // Pass alerts
            'nursingDiagnosisId' => $nursingDiagnosisId ?? null,
        ]);
    }

    /**
     * 
     * 
     * 
     * 
     * 
     * Displays the initial page, loading data based on session state.
     */
    public function show(Request $request)
    {
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $adlData = null;
        $selectedPatient = null;
        $currentDate = now()->format('Y-m-d'); // Default
        $currentDayNo = 1; // Default
        $alerts = []; // Initialize alerts array
        $totalDaysSinceAdmission = 0;

        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                $admissionDate = Carbon::parse($selectedPatient->admission_date)->startOfDay();
                $today = now()->startOfDay();

                $totalDaysSinceAdmission = intval($admissionDate->diffInDays($today)) + 1;
                if ($totalDaysSinceAdmission < 1)
                    $totalDaysSinceAdmission = 1;

                // Get date/day from session or default to latest
                $date = $request->session()->get('selected_date', $today->format('Y-m-d'));
                $dayNo = $request->session()->get('selected_day_no', $totalDaysSinceAdmission);

                // Ensure date is not in future
                $selectedDateCarbon = Carbon::parse($date)->startOfDay();
                if ($selectedDateCarbon->isAfter($today)) {
                    $date = $today->format('Y-m-d');
                    $dayNo = $totalDaysSinceAdmission;
                }

                // Save/update session
                $request->session()->put('selected_date', $date);
                $request->session()->put('selected_day_no', $dayNo);

                $currentDate = $date;
                $currentDayNo = $dayNo;
                $adlData = $this->getAdlRecord($patientId, $currentDate, $currentDayNo);

                if ($adlData) {
                    $cdssService = new ActOfDailyLivingCdssService();
                    $alerts = $cdssService->analyzeFindings($adlData->toArray());

                    // Check for existing NursingDiagnosis
                    $nursingDiagnosis = \App\Models\NursingDiagnosis::where('adl_id', $adlData->id)->first();
                    $nursingDiagnosisId = $nursingDiagnosis ? $nursingDiagnosis->id : null;
                }
            }
        }

        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
            'selectedPatient' => $selectedPatient,
            'currentDate' => $currentDate,
            'currentDayNo' => $currentDayNo,
            'alerts' => $alerts, // Pass alerts to the view
            'totalDaysSinceAdmission' => $totalDaysSinceAdmission,
            'nursingDiagnosisId' => $nursingDiagnosisId ?? null,
        ]);
    }




    // old method for single-field CDSS analysis, called by alert.js
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




    // new method for batch CDSS analysis of multiple fields, called by alert.js
    public function runBatchCdssAnalysis(Request $request)
    {
        $data = $request->validate([
            'batch' => 'required|array',
            'batch.*.fieldName' => 'required|string',
            'batch.*.finding' => 'nullable|string',
        ]);

        $cdssService = new ActOfDailyLivingCdssService();
        $results = [];

        foreach ($data['batch'] as $item) {
            // Re-use your existing single-field analysis logic
            $alert = $cdssService->analyzeSingleFinding(
                $item['fieldName'],
                $item['finding'] ?? ''
            );
            $results[] = $alert;
        }

        return response()->json($results);
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

        if ($request->input('action') === 'cdss') {
            $adl = ActOfDailyLiving::where('patient_id', $validatedData['patient_id'])
                ->where('date', $validatedData['date'])
                ->where('day_no', $validatedData['day_no'])
                ->first();
            return redirect()->route('nursing-diagnosis.process', [
                'component' => 'adl',
                'id' => $adl->id
            ]);
        }

        return redirect()->route('adl.show')
            ->with('success', $message);
    }



    public function runCdssAnalysis(Request $request)
    {
        $patient = Patient::where('patient_id', $request->patient_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$patient) {
            return back()->with('error', 'Unauthorized patient access.');
        }

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
