<?php

namespace App\Http\Controllers;

use App\Models\LabValues;
use App\Models\Patient;
use App\Services\LabValueCdssService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabValuesController extends Controller
{
    /**
     * Display the lab values form.
     */
    public function index(Request $request)
    {
        $patients = Patient::orderBy('name')->get();
        $labValues = null;

        // This is only for the initial GET request
        $patientId = $request->get('patient_id');
        $recordDate = $request->get('record_date');

        if ($patientId && $recordDate) {
            $labValues = LabValues::where('patient_id', $patientId)
                ->where('record_date', $recordDate)
                ->first();
        }

        return view('lab-values', compact('patients', 'labValues', 'patientId', 'recordDate'));
    }

    /**
     * Handle the patient/date filter submission (POST request).
     */
    public function filter(Request $request)
    {
        $patientId = $request->input('patient_id');
        $recordDate = $request->input('record_date');

        $patients = Patient::orderBy('name')->get();
        $labValues = null;

        if ($patientId && $recordDate) {
            $labValues = LabValues::where('patient_id', $patientId)
                ->where('record_date', $recordDate)
                ->first();
        }

        return view('lab-values', compact('patients', 'labValues'))
            ->with('patientId', $patientId)
            ->with('recordDate', $recordDate);
    }

    /**
     * Store or update lab values.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'record_date' => 'required|date',
            'lab_tests' => 'array',
            'lab_tests.*.result' => 'nullable|string|max:255',
            'lab_tests.*.normal_range' => 'nullable|string|max:255',
        ]);

        $patientId = $validatedData['patient_id'];
        $recordDate = $validatedData['record_date'];

        $labData = [
            'patient_id' => $patientId,
            'record_date' => $recordDate,
        ];

        // Prepare data for the model from the lab_tests array
        foreach ($validatedData['lab_tests'] as $key => $values) {
            $labData[$key . '_result'] = $values['result'] ?? null;
            $labData[$key . '_normal_range'] = $values['normal_range'] ?? null;
        }

        DB::beginTransaction();
        try {
            $existingLabValues = LabValues::where('patient_id', $patientId)
                ->where('record_date', $recordDate)
                ->first();

            if ($existingLabValues) {
                $existingLabValues->update($labData);
                $message = 'Lab values updated successfully!';
            } else {
                LabValues::create($labData);
                $message = 'Lab values saved successfully!';
            }
            DB::commit();

            return redirect()->route('lab-values.index', [
                'patient_id' => $patientId,
                'record_date' => $recordDate
            ])->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while saving the data. Please try again.']);
        }
    }

    /**
     * Run CDSS analysis on lab values.
     */
    public function runCdssAnalysis(Request $request)
    {
        $validatedData = $request->validate([
            'lab_tests' => 'array',
            'lab_tests.*.result' => 'nullable|string|max:255',
        ]);

        $cdssService = new LabValueCdssService();

        // Extract only the result values for the CDSS service
        $results = array_column($validatedData['lab_tests'], 'result', 'lab_test_key');

        $alerts = $cdssService->analyzeValues($results);

        return redirect()->back()->withInput($request->all())->with('cdss', $alerts);
    }
}
