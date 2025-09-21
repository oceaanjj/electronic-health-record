<?php

namespace App\Http\Controllers;

use App\Models\ActOfDailyLiving;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\AdlCdssService;
use Illuminate\Validation\Rule;

class ActOfDailyLivingController extends Controller
{

    public function show(Request $request)
    {
        $patients = Patient::all();
        $patientId = $request->query('patient_id');
        $adlData = null;

        if ($patientId) {
            $adlData = ActOfDailyLiving::where('patient_id', $patientId)->first();
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

        //  Check for an existing ADL record.
        $adl = ActOfDailyLiving::where('patient_id', $validatedData['patient_id'])->first();

        // Filter out any null or empty assessment fields before running the CDSS analysis.
        // This ensures the CDSS service only analyzes fields that were actually filled in.
        $filteredData = array_filter($validatedData);

        if ($adl) {
            // If an ADL record exists, update it.
            $adl->update($validatedData);
            $message = 'ADL data updated successfully!';
        } else {
            // If no record exists, create a new one.
            ActOfDailyLiving::create($validatedData);
            $message = 'ADL data created successfully!';
        }

        $cdssService = new AdlCdssService();
        $alerts = $cdssService->analyzeFindings($filteredData);


        return redirect()->route('adl.show', ['patient_id' => $validatedData['patient_id']])
            ->with('cdss', $alerts)
            ->with('success', $message);
    }



    public function runCdssAnalysis(Request $request)
    {
        $patientId = $request->input('patient_id');

        $validatedData = $request->validate([
            'patient_id' => ['required', 'string', Rule::exists('patients', 'patient_id')],
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

        // //Check for existing record and create or update it.
        // $adl = ActOfDailyLiving::updateOrCreate(
        //     ['patient_id' => $patientId],
        //     $validatedData
        // );

        // $cdssService = new AdlCdssService();
        // $analysisResults = $cdssService->analyzeFindings($adl->toArray());

        return redirect()->route('adl.show', ['patient_id' => $patientId])
            ->with('success', 'CDSS Analysis complete!');
    }
}
