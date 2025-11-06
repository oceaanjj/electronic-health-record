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
    public function selectPatientAndDate(Request $request)
    {
        $patientId = $request->input('patient_id');
        // Always set the date to the current date when a patient is selected
        $currentDate = now()->format('Y-m-d');
        // Always set the day number to 1 when a patient is selected
        $defaultDayNo = 1;

        $request->session()->put('selected_patient_id', $patientId);
        $request->session()->put('selected_date', $currentDate);
        $request->session()->put('selected_day_no', $defaultDayNo);

        return redirect()->route('vital-signs.show');
    }

    public function show(Request $request)
    {
        $patients = Auth::user()->patients;
        $vitalsData = collect();

        $patientId = $request->session()->get('selected_patient_id');
        $date = $request->session()->get('selected_date');
        $dayNo = $request->session()->get('selected_day_no');

        if ($patientId && $date && $dayNo) {
            $vitals = Vitals::where('patient_id', $patientId)
                ->where('date', $date)
                ->where('day_no', $dayNo)
                ->get();

            $vitalsData = $vitals->keyBy(function ($item) {
                return Carbon::parse($item->time)->format('H:i');
            });
        }

        $selectedPatient = null;
        if ($patientId) {
            $selectedPatient = Patient::where('patient_id', $patientId)->first();
        }

        return view('vital-signs', [
            'patients' => $patients,
            'vitalsData' => $vitalsData,
            'selectedDate' => $date,
            'selectedDayNo' => $dayNo,
            'selectedPatient' => $selectedPatient,
        ]);
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