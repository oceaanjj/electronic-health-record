<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Vitals;
use App\Services\CdssService;

class VitalSignsController extends Controller
{
    
    public function show(Request $request)
    {
        // Fetch all patients for the dropdown menu.
        $patients = Patient::all();
        $patientId = $request->query('patient_id');
        $date = $request->query('date');
        $adlData = null;

        if ($patientId && $date) {
            $adlData = Vitals::where('patient_id', $patientId)
                ->where('date', $date)
                ->first();
        }

        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
        ]);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|between:1,30',
            'date' => 'required|date',
            'temperature' => 'nullable|string',
            'hr' => 'nullable|string',
            'rr' => 'nullable|string',
            'bp' => 'nullable|string',
            'spo2' => 'nullable|string',
        ]);

        Vitals::updateOrCreate(
            ['patient_id' => $validatedData['patient_id'], 'date' => $validatedData['date']],
            $validatedData
        );

        $message = 'Vital signs data saved successfully!';

        $filteredData = array_filter($validatedData);

        $cdssService = new CdssService('vital_sign_rules'); //<-- Rules folder name (storage>app>private> *here* )
        $alerts = $cdssService->analyzeFindings($filteredData);

        return redirect()->route('vital_signs.show', [
            'patient_id' => $validatedData['patient_id'],
            'date' => $validatedData['date']
        ])->with('cdss', $alerts)->with('success', $message);
    }


    public function runCdssAnalysis(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|between:1,30',
            'date' => 'required|date',
            'temperature' => 'nullable|string',
            'hr' => 'nullable|string',
            'rr' => 'nullable|string',
            'bp' => 'nullable|string',
            'spo2' => 'nullable|string',
        ]);

        $vitalSigns = Vitals::updateOrCreate(
            ['patient_id' => $validatedData['patient_id'], 'date' => $validatedData['date']],
            $validatedData
        );

        $cdssService = new CdssService('vital_signs_rules');
        $analysisResults = $cdssService->analyzeFindings($vitalSigns->toArray());

        return redirect()->route('vital_signs.show', [
            'patient_id' => $validatedData['patient_id'],
            'date' => $validatedData['date']
        ])->with('cdss', $analysisResults)
            ->with('success', 'CDSS Analysis complete!');
    }
}
