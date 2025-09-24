<?php

namespace App\Http\Controllers;

use App\Models\ActOfDailyLiving;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Services\CdssService;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class ActOfDailyLivingController extends Controller
{

    public function selectPatientAndDate(Request $request)
    {
        $patientId = $request->input('patient_id');
        $date = $request->input('date');

        // Store the selected patient's ID and date in the session
        $request->session()->put('selected_patient_id', $patientId);
        $request->session()->put('selected_date', $date);

        // Redirect back to the main ADL page
        return redirect()->route('adl.show');
    }


    public function show(Request $request)
    {
        $patients = Patient::all();
        $adlData = null;

        $patientId = $request->session()->get('selected_patient_id');
        $date = $request->session()->get('selected_date');

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

        $existingAdl = ActOfDailyLiving::where('patient_id', $validatedData['patient_id'])
            ->where('date', $validatedData['date'])
            ->first();

        if ($existingAdl) {
            // If it exists, update the record
            $existingAdl->update($validatedData);
            $message = 'ADL data updated successfully!';
            // Add audit log for update
            AuditLogController::log(
                'ADL Record Updated',
                'User ' . Auth::user()->username . ' updated an existing ADL record.',
                ['patient_id' => $validatedData['patient_id']]
            );

        } else {
            // Otherwise, create a new record
            ActOfDailyLiving::create($validatedData);
            $message = 'ADL data saved successfully!';
            // Add audit log for creation
            AuditLogController::log(
                'ADL Record Created',
                'User ' . Auth::user()->username . ' created a new ADL record.',
                ['patient_id' => $validatedData['patient_id']]
            );
        }

        $filteredData = array_filter($validatedData);

        $cdssService = new CdssService('adl_rules');

        $alerts = $cdssService->analyzeFindings($filteredData);

        return redirect()->route('adl.show')
            ->with('cdss', $alerts)
            ->with('success', $message);
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
