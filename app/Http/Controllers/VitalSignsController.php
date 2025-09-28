<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Vitals;
use Illuminate\Http\Request;
use App\Services\CdssService;
use App\Services\VitalCdssService;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VitalSignsController extends Controller
{
    public function selectPatientAndDate(Request $request)
    {
        $patientId = $request->input('patient_id');
        $date = $request->input('date');
        $dayNo = $request->input('day_no');

        $request->session()->put('selected_patient_id', $patientId);
        $request->session()->put('selected_date', $date);
        $request->session()->put('selected_day_no', $dayNo);

        return redirect()->route('vital-signs.show');
    }

    public function show(Request $request)
    {
        $patients = Patient::all();
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

        return view('vital-signs', [
            'patients' => $patients,
            'vitalsData' => $vitalsData,
            'selectedDate' => $date,
            'selectedDayNo' => $dayNo,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'date' => 'required|date',
            'day_no' => 'required|integer|between:1,30',
        ]);

        $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
        $anyCreated = false;
        $anyUpdated = false;

        foreach ($times as $time) {
            $dbTime = Carbon::createFromFormat('H:i', $time)->format('H:i:s');

            $vitalsForTime = [
                'temperature' => $request->input("temperature_{$time}"),
                'hr' => $request->input("hr_{$time}"),
                'rr' => $request->input("rr_{$time}"),
                'bp' => $request->input("bp_{$time}"),
                'spo2' => $request->input("spo2_{$time}"),
            ];

            if (count(array_filter($vitalsForTime)) > 0) {
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

        // ✅ Run CDSS alerts right after saving
        $cdssService = new VitalCdssService();
        $allAlerts = $cdssService->analyzeVitals($request->all());

        $message = $anyCreated ? 'Vital Signs data saved successfully.' 
            : ($anyUpdated ? 'Vital Signs data updated successfully.' 
            : 'No changes made.');

        return redirect()->route('vital-signs.show')
            ->with('success', $message)
            ->with('cdss', $allAlerts);
    }
}