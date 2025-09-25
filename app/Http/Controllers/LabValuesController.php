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
        $alerts = [];
        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                $labValue = LabValues::where('patient_id', $patientId)->first();

                if ($labValue) {
                    $cdssService = new LabValuesCdssService();
                    $alerts = $this->runLabCdss($labValue, $cdssService, 'child'); // default = child
                }
            }
        }

        return view('lab-values', compact('patients', 'selectedPatient', 'labValue', 'alerts'));
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

            // numeric only
            'wbc_result' => 'nullable|numeric',
            'rbc_result' => 'nullable|numeric',
            'hgb_result' => 'nullable|numeric',
            'hct_result' => 'nullable|numeric',
            'platelets_result' => 'nullable|numeric',
            'mcv_result' => 'nullable|numeric',
            'mch_result' => 'nullable|numeric',
            'mchc_result' => 'nullable|numeric',
            'rdw_result' => 'nullable|numeric',
            'neutrophils_result' => 'nullable|numeric',
            'lymphocytes_result' => 'nullable|numeric',
            'monocytes_result' => 'nullable|numeric',
            'eosinophils_result' => 'nullable|numeric',
            'basophils_result' => 'nullable|numeric',

            // normal ranges (string)
            'wbc_normal_range' => 'nullable|string',
            'rbc_normal_range' => 'nullable|string',
            'hgb_normal_range' => 'nullable|string',
            'hct_normal_range' => 'nullable|string',
            'platelets_normal_range' => 'nullable|string',
            'mcv_normal_range' => 'nullable|string',
            'mch_normal_range' => 'nullable|string',
            'mchc_normal_range' => 'nullable|string',
            'rdw_normal_range' => 'nullable|string',
            'neutrophils_normal_range' => 'nullable|string',
            'lymphocytes_normal_range' => 'nullable|string',
            'monocytes_normal_range' => 'nullable|string',
            'eosinophils_normal_range' => 'nullable|string',
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
                'Lab Values Created',
                'User ' . Auth::user()->username . ' created a new Lab Values record.',
                ['patient_id' => $data['patient_id']]
            );
        }

        // Run CDSS after save
        $cdssService = new LabValuesCdssService();
        $alerts = $this->runLabCdss((object)$data, $cdssService, 'child');

        return redirect()->route('lab-values.index')
            ->withInput()
            ->with('alerts', $alerts)
            ->with('success', $message);
    }

    private function runLabCdss($labValue, $cdssService, $ageGroup)
    {
        $alerts = [];

        $lab = [
            'wbc' => 'wbc_result',
            'rbc' => 'rbc_result',
            'hgb' => 'hgb_result',
            'hct' => 'hct_result',
            'platelet' => 'platelets_result',
            'mcv' => 'mcv_result',
            'mch' => 'mch_result',
            'mchc' => 'mchc_result',
            'rdw' => 'rdw_result',
            'neutrophils' => 'neutrophils_result',
            'lymphocytes' => 'lymphocytes_result',
            'monocytes' => 'monocytes_result',
            'eosinophils' => 'eosinophils_result',
            'basophils' => 'basophils_result',
        ];

        foreach ($lab as $param => $field) {
            if ($labValue->$field !== null) {
                $alerts[$param . '_alerts'][] =
                    $cdssService->checkLabResult($param, $labValue->$field, $ageGroup)['alert'];
            }
        }

        return $alerts;
    }
}
