<?php

namespace App\Http\Controllers;

use App\Services\LabValuesCdssService;
use App\Models\LabValues;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class LabValuesController extends Controller
{
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);
        return redirect()->route('lab-values.index');
    }

    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $labValue = null;
        $patientId = $request->session()->get('selected_patient_id');
        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                $labValue = LabValues::where('patient_id', $patientId)->first();
            }
        }
        return view('lab-values', compact('patients', 'selectedPatient', 'labValue'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
        ], [
            'patient_id.required' => 'Please choose a patient first.',
            'patient_id.exists' => 'Please choose a patient first.',
        ]);

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'wbc_result' => 'nullable|numeric',
            'wbc_normal_range' => 'nullable|string',
            'rbc_result' => 'nullable|numeric',
            'rbc_normal_range' => 'nullable|string',
            'hgb_result' => 'nullable|numeric',
            'hgb_normal_range' => 'nullable|string',
            'hct_result' => 'nullable|numeric',
            'hct_normal_range' => 'nullable|string',
            'platelets_result' => 'nullable|numeric',
            'platelets_normal_range' => 'nullable|string',
            'mcv_result' => 'nullable|numeric',
            'mcv_normal_range' => 'nullable|string',
            'mch_result' => 'nullable|numeric',
            'mch_normal_range' => 'nullable|string',
            'mchc_result' => 'nullable|numeric',
            'mchc_normal_range' => 'nullable|string',
            'rdw_result' => 'nullable|numeric',
            'rdw_normal_range' => 'nullable|string',
            'neutrophils_result' => 'nullable|numeric',
            'neutrophils_normal_range' => 'nullable|string',
            'lymphocytes_result' => 'nullable|numeric',
            'lymphocytes_normal_range' => 'nullable|string',
            'monocytes_result' => 'nullable|numeric',
            'monocytes_normal_range' => 'nullable|string',
            'eosinophils_result' => 'nullable|numeric',
            'eosinophils_normal_range' => 'nullable|string',
            'basophils_result' => 'nullable|numeric',
            'basophils_normal_range' => 'nullable|string',
        ]);
        $existingLabValue = LabValues::where('patient_id', $data['patient_id'])->first();
        if ($existingLabValue) {
            $existingLabValue->update($data);
            $message = 'Lab values data updated successfully!';
            AuditLogController::log(
                'Lab Values Updated',
                'User ' . Auth::user()->username . ' updated an existing Lab Values record.',
                ['patient_id' => $data['patient_id']]
            );
        } else {
            LabValues::create($data);
            $message = 'Lab values data saved successfully!';
            AuditLogController::log(
                'Lab Values created',
                'User ' . Auth::user()->username . ' created a new Lab Values record.',
                ['patient_id' => $data['patient_id']]
            );
        }
        $cdssService = new LabValuesCdssService();
        $alerts = $cdssService->analyzeLabValues($data);
        return redirect()->route('lab-values.index')->withInput()->with('cdss', $alerts)->with('success', $message);
    }

    public function runCdssAnalysis(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'wbc_result' => 'nullable|numeric',
            'wbc_normal_range' => 'nullable|string',
            'rbc_result' => 'nullable|numeric',
            'rbc_normal_range' => 'nullable|string',
            'hgb_result' => 'nullable|numeric',
            'hgb_normal_range' => 'nullable|string',
            'hct_result' => 'nullable|numeric',
            'hct_normal_range' => 'nullable|string',
            'platelets_result' => 'nullable|numeric',
            'platelets_normal_range' => 'nullable|string',
            'mcv_result' => 'nullable|numeric',
            'mcv_normal_range' => 'nullable|string',
            'mch_result' => 'nullable|numeric',
            'mch_normal_range' => 'nullable|string',
            'mchc_result' => 'nullable|numeric',
            'mchc_normal_range' => 'nullable|string',
            'rdw_result' => 'nullable|numeric',
            'rdw_normal_range' => 'nullable|string',
            'neutrophils_result' => 'nullable|numeric',
            'neutrophils_normal_range' => 'nullable|string',
            'lymphocytes_result' => 'nullable|numeric',
            'lymphocytes_normal_range' => 'nullable|string',
            'monocytes_result' => 'nullable|numeric',
            'monocytes_normal_range' => 'nullable|string',
            'eosinophils_result' => 'nullable|numeric',
            'eosinophils_normal_range' => 'nullable|string',
            'basophils_result' => 'nullable|numeric',
            'basophils_normal_range' => 'nullable|string',
        ]);
        $cdssService = new LabValuesCdssService();
        $alerts = $cdssService->analyzeLabValues($data);
        return redirect()->route('lab-values.index')->withInput($data)->with('cdss', $alerts)->with('success', 'CDSS analysis run successfully!');
    }
}