<?php

namespace App\Http\Controllers;

use App\Models\ActOfDailyLiving;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\CdssService;

class ActOfDailyLivingController extends Controller
{

    public function show(Request $request)
    {
        // Fetch all patients for the dropdown menu.
        $patients = Patient::all();
        $patientId = $request->query('patient_id');
        $date = $request->query('date');
        $adlData = null;

        if ($patientId && $date) {
            $adlData = ActOfDailyLiving::where('patient_id', $patientId)
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
            'mobility_assessment' => 'nullable|string',
            'hygiene_assessment' => 'nullable|string',
            'toileting_assessment' => 'nullable|string',
            'feeding_assessment' => 'nullable|string',
            'hydration_assessment' => 'nullable|string',
            'sleep_pattern_assessment' => 'nullable|string',
            'pain_level_assessment' => 'nullable|string',
        ]);

        ActOfDailyLiving::updateOrCreate(
            ['patient_id' => $validatedData['patient_id'], 'date' => $validatedData['date']],
            $validatedData
        );

        $message = 'ADL data saved successfully!';

        $filteredData = array_filter($validatedData);

        $cdssService = new CdssService('adl_rules'); //<-- Rules folder name (storage>app>private> *here* )
        $alerts = $cdssService->analyzeFindings($filteredData);

        return redirect()->route('adl.show', [
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

        $cdssService = new CdssService('adl_rules');
        $analysisResults = $cdssService->analyzeFindings($adl->toArray());

        return redirect()->route('adl.show', [
            'patient_id' => $validatedData['patient_id'],
            'date' => $validatedData['date']
        ])->with('cdss', $analysisResults)
            ->with('success', 'CDSS Analysis complete!');
    }
}
