<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Vitals;
use Illuminate\Http\Request;
use App\Services\VitalCdssService;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VitalSignsController extends Controller
{
    /**
     * Finds and returns the Vitals record for a given patient, date, and day.
     *
     * @param string $patientId
     * @param string $date (format Y-m-d)
     * @param int $dayNo
     * @return \Illuminate\Support\Collection
     */
    private function getVitalsRecord(string $patientId, string $date, int $dayNo): \Illuminate\Support\Collection
    {
        return Vitals::where('patient_id', $patientId)
            ->where('date', $date)
            ->where('day_no', $dayNo)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->time)->format('H:i');
            });
    }

    /**
     * Handles the AJAX request for patient selection and date/day changes.
     * Also displays the initial page, loading data based on session state.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function selectPatient(Request $request)
    {
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedPatient = null;
        $vitalsData = collect();
        $totalDaysSinceAdmission = 0;

        // Default values for rendering the view if selection fails
        $currentDate = now()->format('Y-m-d');
        $currentDayNo = 1;
        $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];

        $patientId = $request->input('patient_id') ?? $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);

            if ($selectedPatient) {
                $request->session()->put('selected_patient_id', $patientId);

                $admissionDate = Carbon::parse($selectedPatient->admission_date);
                $totalDaysSinceAdmission = $admissionDate->diffInDays(now()) + 1;

                // Get date/day from request or session
                $date = $request->input('date');
                $dayNo = $request->input('day_no');

                $isNewPatientSelection = is_null($date) && is_null($dayNo) && $request->has('patient_id');

                if ($isNewPatientSelection) {
                    // New patient selected, default to the latest day
                    $dayNo = $totalDaysSinceAdmission;
                    $date = $admissionDate->copy()->addDays($dayNo - 1)->format('Y-m-d');
                } else {
                    // Use date/day from request or fall back to session
                    $date = $date ?? $request->session()->get('selected_date');
                    $dayNo = $dayNo ?? $request->session()->get('selected_day_no');

                    // Final fallback if session is also empty
                    if (!$date || !$dayNo) {
                        $dayNo = $totalDaysSinceAdmission;
                        $date = $admissionDate->copy()->addDays($dayNo - 1)->format('Y-m-d');
                    }
                }

                // Store the determined date/day in the session
                $request->session()->put('selected_date', $date);
                $request->session()->put('selected_day_no', $dayNo);

                // Set variables for the view
                $currentDate = $date;
                $currentDayNo = $dayNo;

                // Fetch the Vitals record
                $vitalsData = $this->getVitalsRecord($patientId, $currentDate, (int) $currentDayNo);

                // Re-run CDSS analysis on fetched data
                $cdssService = new VitalCdssService();
                foreach ($vitalsData as $time => $vitalRecord) {
                    $vitalsArray = $vitalRecord->toArray();
                    $alertResult = $cdssService->analyzeVitalsForAlerts($vitalsArray);
                    $vitalsData[$time]->alerts = $alertResult['alert'];
                    $vitalsData[$time]->news_severity = $alertResult['severity'];
                }
            } else {
                // Patient not found, clear session
                $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
            }
        } else {
            // No patient selected, clear session
            $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
        }

        return view('vital-signs', [
            'patients' => $patients,
            'vitalsData' => $vitalsData,
            'selectedPatient' => $selectedPatient,
            'currentDate' => $currentDate,
            'currentDayNo' => $currentDayNo,
            'totalDaysSinceAdmission' => $totalDaysSinceAdmission,
            'times' => $times,
        ]);
    }

    public function show(Request $request)
    {
        // The logic for displaying the initial page is now handled by selectPatient
        return $this->selectPatient($request);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'date' => 'required|date',
            'day_no' => 'required|integer|between:1,30',
        ]);

        $user_id = Auth::id();
        $patient = Patient::where('patient_id', $request->patient_id)
            ->where('user_id', $user_id)
            ->first();
        if (!$patient)
            return back()->with('error', 'Unauthorized patient access.');

        $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
        $anyCreated = false;
        $anyUpdated = false;
        $cdssService = new VitalCdssService();

        foreach ($times as $time) {
            $dbTime = Carbon::createFromFormat('H:i', $time)->format('H:i:s');

            $vitalsForTime = [
                'temperature' => $request->input("temperature_{$time}"),
                'hr' => $request->input("hr_{$time}"),
                'rr' => $request->input("rr_{$time}"),
                'bp' => $request->input("bp_{$time}"),
                'spo2' => $request->input("spo2_{$time}"),
            ];

            $hasData = count(array_filter($vitalsForTime, fn($v) => $v !== null && $v !== '')) > 0;

            $queryConditions = [
                'patient_id' => $validatedData['patient_id'],
                'date' => $validatedData['date'],
                'day_no' => $validatedData['day_no'],
                'time' => $dbTime,
            ];

            if ($hasData) {
                $alertResult = $cdssService->analyzeVitalsForAlerts($vitalsForTime);
                $vitalsForTime['alerts'] = $alertResult['alert'];
                $vitalsForTime['news_severity'] = $alertResult['severity'];

                $vitalRecord = Vitals::updateOrCreate(
                    $queryConditions,
                    $vitalsForTime
                );

                if ($vitalRecord->wasRecentlyCreated) {
                    AuditLogController::log(
                        'Vital Signs Record Created',
                        'User ' . Auth::user()->username . " created a new Vital Signs record",
                        ['patient_id' => $validatedData['patient_id']]
                    );
                    $anyCreated = true;
                } elseif ($vitalRecord->wasChanged()) {
                    AuditLogController::log(
                        'Vital Signs Record Updated',
                        'User ' . Auth::user()->username . " updated a Vital Signs record",
                        ['patient_id' => $validatedData['patient_id']]
                    );
                    $anyUpdated = true;
                }
            } else {
                // If no data is submitted for this time slot, delete the existing record
                Vitals::where($queryConditions)->delete();
                AuditLogController::log(
                    'Vital Signs Record Deleted',
                    'User ' . Auth::user()->username . " deleted a Vital Signs record for time " . $time,
                    ['patient_id' => $validatedData['patient_id'], 'time' => $time]
                );
            }
        }

        $message = $anyCreated ? 'Vital Signs data saved successfully.'
            : ($anyUpdated ? 'Vital Signs data updated successfully.' : 'No changes made.');

        return redirect()->route('vital-signs.show', [
            'date' => $validatedData['date'],
            'day_no' => $validatedData['day_no'],
        ])->with('success', $message);
    }

    public function checkVitals(Request $request)
    {
        $time = $request->input('time');
        $vitals = $request->input('vitals'); // This will be an array of vital signs for the time slot

        if (!$time || !is_array($vitals)) {
            return response()->json(['alert' => '', 'severity' => 'NONE']);
        }

        $cdssService = new VitalCdssService();

        // Call analyzeVitalsForAlerts with the complete set of vitals for the time slot
        $result = $cdssService->analyzeVitalsForAlerts($vitals);

        $result['severity'] = strtoupper($result['severity']);

        return response()->json($result);
    }

    /**
     * Fetches vital signs data for a given patient, date, and day number.
     * Used for AJAX requests to dynamically update the form.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchVitalSignsData(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'date' => 'required|date_format:Y-m-d',
            'day_no' => 'required|integer',
        ]);

        $patientId = $request->input('patient_id');
        $date = $request->input('date');
        $dayNo = $request->input('day_no');

        $vitalsData = $this->getVitalsRecord($patientId, $date, (int) $dayNo);

        // Re-run CDSS analysis on fetched data
        $cdssService = new VitalCdssService();
        foreach ($vitalsData as $time => $vitalRecord) {
            $vitalsArray = $vitalRecord->toArray();
            $alertResult = $cdssService->analyzeVitalsForAlerts($vitalsArray);
            $vitalsData[$time]->alerts = $alertResult['alert'];
            $vitalsData[$time]->news_severity = $alertResult['severity'];
        }

        return response()->json($vitalsData);
    }
}