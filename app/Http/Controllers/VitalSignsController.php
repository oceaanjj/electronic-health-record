<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\LabValues;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuditLogController;

class LabValuesController extends Controller
{
    public function select(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);

        return redirect()->route('lab-values.index');
    }

    public function index(Request $request)
    {
        $patients = Patient::all();
        $labValue = null;

        if ($request->session()->has('selected_patient_id')) {
            $labValue = LabValues::where('patient_id', $request->session()->get('selected_patient_id'))->first();
        }

        return view('lab-values', [
            'patients' => $patients,
            'labValue' => $labValue
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'patient_id' => 'required|exists:patients,patient_id',
        ];

        // lahat ng lab test fields ay number only
        $labTests = [
            'wbc', 'rbc', 'hgb', 'hct', 'platelets',
            'mcv', 'mch', 'mchc', 'rdw',
            'neutrophils', 'lymphocytes', 'monocytes', 'eosinophils', 'basophils'
        ];

        foreach ($labTests as $test) {
            $rules[$test . '_result'] = 'nullable|numeric';
            $rules[$test . '_normal_range'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        LabValues::updateOrCreate(
            ['patient_id' => $validated['patient_id']],
            $validated
        );

        AuditLogController::log(
            'Lab Values Record Updated',
            'User ' . Auth::user()->username . " updated lab values",
            ['patient_id' => $validated['patient_id']]
        );

        return redirect()->route('lab-values.index')->with('success', 'Lab values saved successfully.');
    }
}
