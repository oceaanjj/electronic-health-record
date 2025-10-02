<?php

namespace App\Http\Controllers;

use App\Models\IntakeAndOutput;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IntakeAndOutputController extends Controller
{

    public function showPatientIntakeOutputs($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $intakeOutputs = $patient->intakeAndOutputs;
        return view('patient-intake-outputs', compact('patient', 'intakeOutputs'));
    }
    public function generatedAlerts(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'oral_intake' => 'nullable|integer',
            'iv_fluids_volume' => 'nullable|integer',
            'iv_fluids_type' => 'nullable|string',
            'urine_output' => 'nullable|integer',
        ]);
        $cdssAlerts = new IntakeAndOutputCdssService();
        $alerts = $cdssAlerts->analyzeIntakeOutput($data);
        $patients = Patient::all();
        $request->flash();
        return view('intake-and-output', [
            'patients' => $patients,
            'cdss_alerts' => $alerts,
        ]);



    }






    public function selectPatientAndDate(Request $request)
    {
        $patientId = $request->input('patient_id');
        $date = $request->input('date');
        $dayNo = $request->input('day_no');

        $request->session()->put('selected_patient_id', $patientId);
        $request->session()->put('selected_date', $date);
        $request->session()->put('selected_day_no', $dayNo);

        return redirect()->route('io.show');
    }

    public function show(Request $request)
    {
        $patients = Patient::all();
        $ioData = null;

        $patientId = $request->session()->get('selected_patient_id');
        $date = $request->session()->get('selected_date');
        $dayNo = $request->session()->get('selected_day_no');

        if ($patientId && $date && $dayNo) {
            $ioData = IntakeAndOutput::where('patient_id', $patientId)
                ->where('date', $date)
                ->where('day_no', $dayNo)
                ->first();
        }

        return view('intake-and-output', [
            'patients' => $patients,
            'ioData' => $ioData,
            'selectedDate' => $date,
            'selectedDayNo' => $dayNo,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
        ], [
            'patient_id.required' => 'Please choose a patient first.',
            'patient_id.exists' => 'Please choose a patient first.',
        ]);

        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|between:1,30',
            'date' => 'required|date',
            'oral_intake' => 'nullable|integer',
            'iv_fluids_volume' => 'nullable|integer',
            'iv_fluids_type' => 'nullable|string',
            'urine_output' => 'nullable|integer',
            'other_output' => 'nullable|integer',
        ]);

        $existingIo = IntakeAndOutput::where('patient_id', $validatedData['patient_id'])
            ->where('date', $validatedData['date'])
            ->where('day_no', $validatedData['day_no'])
            ->first();

        if ($existingIo) {
            $existingIo->update($validatedData);
            $message = 'Intake and Output data updated successfully!';
        } else {
            IntakeAndOutput::create($validatedData);
            $message = 'Intake and Output data saved successfully!';
        }

        return redirect()->route('io.show')
            ->with('success', $message);
    }









}
