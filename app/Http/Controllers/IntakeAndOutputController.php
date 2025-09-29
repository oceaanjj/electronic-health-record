<?php

namespace App\Http\Controllers;

use App\Services\IntakeAndOutputCdssService;

use App\Models\IntakeAndOutput;
use App\Models\Patient;
use Illuminate\Http\Request;

class IntakeAndOutputController extends Controller
{
    public function store(Request $request)
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
        IntakeAndOutput::create([
            'patient_id' => $data['patient_id'],
            'oral_intake' => $data['oral_intake'],
            'iv_fluids_volume' => $data['iv_fluids_volume'],
            'iv_fluids_type' => $data['iv_fluids_type'],
            'urine_output' => $data['urine_output'],
            'cdss_alerts' => $alerts,
        ]);

        return redirect()->route('Intake-and-Output.index')
            ->with('success', 'Intake and Output registered successfully')
            ->withInput();
    }

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
}
