<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ActOfDailyLiving;
use Illuminate\Http\Request;
use App\Services\AdlCdssService;
class ActOfDailyLivingController extends Controller
{
    // Display the form for the Activities of Daily Living.
    public function show()
    {
        $patients = Patient::all();
        return view('act-of-daily-living', compact('patients'));
    }

    // Store a new Activities of Daily Living record.
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|min:1',
            'date' => 'required|date',
            'mobility_assessment' => 'nullable|string',
            'hygiene_assessment' => 'nullable|string',
            'toileting_assessment' => 'nullable|string',
            'feeding_assessment' => 'nullable|string',
            'hydration_assessment' => 'nullable|string',
            'sleep_pattern_assessment' => 'nullable|string',
            'pain_level_assessment' => 'nullable|string',
        ]);

        // Create the new Activities of Daily Living record in the database
        $adl = ActOfDailyLiving::create($validatedData);

        // Filter out any null or empty assessment fields before running the CDSS analysis.
        // This ensures the CDSS service only analyzes fields that were actually filled in.
        $dataForAnalysis = array_filter($validatedData);

        // Run the CDSS analysis on the findings
        $cdssService = new AdlCdssService();
        $alerts = $cdssService->analyzeFindings($dataForAnalysis);

        // Redirect back to the form with success message and alerts
        return redirect()->route('adl.show')
            ->withInput()
            ->with('cdss', $alerts)
            ->with('success', 'Activities of daily living data saved successfully!');
    }

    // Run CDSS analysis on findings without storing the data.
    public function runCdssAnalysis(Request $request)
    {
        // Define the list of fields to validate and analyze
        $assessmentFields = [
            'mobility_assessment',
            'hygiene_assessment',
            'toileting_assessment',
            'feeding_assessment',
            'hydration_assessment',
            'sleep_pattern_assessment',
            'pain_level_assessment'
        ];

        // Prepare the data for analysis from the request.
        // We use array_filter to remove any fields that are null or empty.
        $dataForAnalysis = array_filter($request->only($assessmentFields));

        // Call the CDSS service
        $cdssService = new AdlCdssService();
        $alerts = $cdssService->analyzeFindings($dataForAnalysis);

        // Redirect back to the form with the alerts
        return redirect()->route('adl.show')
            ->withInput()
            ->with('cdss', $alerts);
    }
}
