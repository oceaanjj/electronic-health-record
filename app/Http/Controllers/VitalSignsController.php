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
        $patients = Auth::user()->patients;
        $selectedPatient = null;
        $alerts = [];
        $vitalsData = collect();

        $patientId = $request->input('patient_id') ?? $request->session()->get('selected_patient_id');
        
        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if (!$selectedPatient) {
                // Patient not found, clear session and reset
                $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
                return view('vital-signs', compact('patients', 'selectedPatient', 'vitalsData'));
            }
            $request->session()->put('selected_patient_id', $patientId);

            // Determine date and day_no
            $date = $request->input('date');
            $dayNo = $request->input('day_no');

            // If a new patient is selected or if date/day_no are not provided in the request,
            // set defaults based on patient admission date or current date.
            $isNewPatientSelection = (is_null($date) && is_null($dayNo)) || !$request->session()->has('selected_date');

            if ($isNewPatientSelection) {
                $date = now()->format('Y-m-d');
                $dayNo = 1;
            } else {
                // Existing patient or date/day changed. Use request values or fall back to session.
                $date = $date ?? $request->session()->get('selected_date');
                $dayNo = $dayNo ?? $request->session()->get('selected_day_no');
                
                // Final fallback if session also empty (e.g., first load after session clear)
                if (is_null($date) || is_null($dayNo)) {
                    $date = now()->format('Y-m-d');
                    $dayNo = 1;
                }
            }

            // Store the determined date/day selection in the session
            $request->session()->put('selected_date', $date);
            $request->session()->put('selected_day_no', $dayNo);

            // Fetch the Vitals record
            $vitalsData = $this->getVitalsRecord($patientId, $date, (int)$dayNo);
            
            // Re-run CDSS for all fetched vitals to get alerts
            $cdssService = new \App\Services\VitalCdssService();
            foreach($vitalsData as $time => $vitalRecord) {
                $vitalsArray = $vitalRecord->toArray();
                $alertResult = $cdssService->analyzeVitalsForAlerts($vitalsArray);
                $vitalsData[$time]->alerts = $alertResult['alert'];
                $vitalsData[$time]->news_severity = $alertResult['severity'];
            }

        } else {
            // No patient selected, clear session
            $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
        }

        // Pass the explicit variables for the Blade template to ensure immediate update
        $currentDate = $request->session()->get('selected_date', now()->format('Y-m-d'));
        $currentDayNo = $request->session()->get('selected_day_no', 1);


        return view('vital-signs', [
            'patients' => $patients,
            'vitalsData' => $vitalsData,
            'selectedPatient' => $selectedPatient,
            'currentDate' => $currentDate,
            'currentDayNo' => $currentDayNo,
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
        if (!$patient) return back()->with('error', 'Unauthorized patient access.');

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

            if ($hasData) {
                $alertResult = $cdssService->analyzeVitalsForAlerts($vitalsForTime);
                $vitalsForTime['alerts'] = $alertResult['alert']; 
                $vitalsForTime['news_severity'] = $alertResult['severity'];
                
                $vitalRecord = Vitals::updateOrCreate(
                    [
                        'patient_id' => $validatedData['patient_id'],
                        'date' => $validatedData['date'],
                        'day_no' => $validatedData['day_no'],
                        'time' => $dbTime,
                    ],
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
            }
        }

        $message = $anyCreated ? 'Vital Signs data saved successfully.'
            : ($anyUpdated ? 'Vital Signs data updated successfully.' : 'No changes made.');

        return redirect()->route('vital-signs.show')
            ->with('success', $message);
    }

    public function checkVitals(Request $request)
    {
        $param = $request->input('param');
        $value = $request->input('value');

        if (!$param) {
            return response()->json(['alert' => '', 'severity' => 'NONE']);
        }

        $cdssService = new VitalCdssService();
        $result = $cdssService->getAlertForVital($param, $value);

        $result['severity'] = strtoupper($result['severity']);

        return response()->json($result);
    }
}