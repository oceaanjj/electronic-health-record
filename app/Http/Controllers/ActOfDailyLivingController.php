<?php

namespace App\Http\Controllers;

use App\Models\ActOfDailyLiving;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\AdlCdssService;
use Illuminate\Validation\Rule;

class ActOfDailyLivingController extends Controller
{
    /**
     * Display the ADL form with patient data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        // Fetch all patients for the dropdown menu.
        $patients = Patient::all();
        $patientId = $request->query('patient_id');
        $date = $request->query('date');
        $adlData = null;

        if ($patientId && $date) {
            // Find the ADL record for the selected patient and date to pre-populate the form.
            $adlData = ActOfDailyLiving::where('patient_id', $patientId)
                ->where('date', $date)
                ->first();
        }

        return view('act-of-daily-living', [
            'patients' => $patients,
            'adlData' => $adlData,
        ]);
    }

    /**
     * Store a new or update an existing ADL record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validate the incoming data.
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

        // 2. Use updateOrCreate to handle both new and existing records for the specific date.
        ActOfDailyLiving::updateOrCreate(
            ['patient_id' => $validatedData['patient_id'], 'date' => $validatedData['date']],
            $validatedData
        );

        $message = 'ADL data saved successfully!';

        // 3. Filter out null or empty assessment fields before running the CDSS analysis.
        $filteredData = array_filter($validatedData);

        // 4. Run the CDSS analysis using the service class.
        $cdssService = new AdlCdssService();
        $alerts = $cdssService->analyzeFindings($filteredData);

        // 5. Redirect back to the form with a success message and the selected patient_id and date.
        return redirect()->route('adl.show', [
            'patient_id' => $validatedData['patient_id'],
            'date' => $validatedData['date']
        ])->with('cdss', $alerts)->with('success', $message);
    }

    /**
     * Run the CDSS analysis on the ADL data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function runCdssAnalysis(Request $request)
    {
        // 1. Perform the validation for the form inputs.
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

        // 2. Check for existing record and create or update it.
        $adl = ActOfDailyLiving::updateOrCreate(
            ['patient_id' => $validatedData['patient_id'], 'date' => $validatedData['date']],
            $validatedData
        );

        // 3. Run the CDSS analysis using the service class.
        $cdssService = new AdlCdssService();
        $analysisResults = $cdssService->analyzeFindings($adl->toArray());

        // 4. Redirect with the analysis results stored in the session.
        return redirect()->route('adl.show', [
            'patient_id' => $validatedData['patient_id'],
            'date' => $validatedData['date']
        ])->with('cdss', $analysisResults)
            ->with('success', 'CDSS Analysis complete!');
    }
}
