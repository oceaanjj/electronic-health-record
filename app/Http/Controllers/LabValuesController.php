<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Services\LabValuesCdssService; 
use App\Models\LabValues;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 

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
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedPatient = null;
        $labValue = null;
        $alerts = [];
        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Auth::user()->patients()->find($patientId);

            if ($selectedPatient) {
                $labValue = LabValues::where('patient_id', $patientId)->first();
                $cdssService = new LabValuesCdssService();
                $ageGroup = $cdssService->getAgeGroup($selectedPatient);

                if ($labValue) {
                    $alerts = $cdssService->runLabCdss($labValue, $ageGroup);
                }
            } else {
                 $request->session()->forget('selected_patient_id');
                 return redirect()->route('lab-values.index')->with('error', 'Selected patient not found or not authorized.');
            }
        }

        return view('lab-values', compact('patients', 'selectedPatient', 'labValue', 'alerts'));
    }

    public function startCdss($id)
    {
        $labValues = LabValues::findOrFail($id);
        return redirect()->route('nursing-diagnosis.start', [
            'component' => 'lab-values',
            'id' => $labValues->id
        ]);
    }

    public function store(Request $request)
    {
         $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
        ], [
            'patient_id.required' => 'Please choose a patient first.',
            'patient_id.exists' => 'Please choose a patient first.',
        ]);

        $patient = Auth::user()->patients()->find($request->patient_id);
        if (!$patient) {
            return back()->with('error', 'Unauthorized patient access or patient not found.');
        }

        $data = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
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

            'wbc_normal_range' => 'nullable|string|max:50',
            'rbc_normal_range' => 'nullable|string|max:50',
            'hgb_normal_range' => 'nullable|string|max:50',
            'hct_normal_range' => 'nullable|string|max:50',
            'platelets_normal_range' => 'nullable|string|max:50',
            'mcv_normal_range' => 'nullable|string|max:50',
            'mch_normal_range' => 'nullable|string|max:50',
            'mchc_normal_range' => 'nullable|string|max:50',
            'rdw_normal_range' => 'nullable|string|max:50',
            'neutrophils_normal_range' => 'nullable|string|max:50',
            'lymphocytes_normal_range' => 'nullable|string|max:50',
            'monocytes_normal_range' => 'nullable|string|max:50',
            'eosinophils_normal_range' => 'nullable|string|max:50',
            'basophils_normal_range' => 'nullable|string|max:50',
        ]);

        $cdssService = new LabValuesCdssService();
        $ageGroup = $cdssService->getAgeGroup($patient);
        $alerts = $cdssService->runLabCdss((object) $data, $ageGroup);

        // Add alerts to the data array
        foreach ($alerts as $key => $alertInfo) {
            $fieldName = str_replace('_alerts', '_alert', $key);
            if (isset($alertInfo[0]['text'])) {
                // If there are multiple alerts for one field (e.g. correlations), join them
                $texts = array_column($alertInfo, 'text');
                $data[$fieldName] = implode(' | ', $texts);
            }
        }

        $labValue = LabValues::updateOrCreate(
            ['patient_id' => $data['patient_id']],
            $data
        );

        $message = $labValue->wasRecentlyCreated ? 'Lab values data saved successfully!' : 'Lab values data updated successfully!';
        $action = $labValue->wasRecentlyCreated ? 'Lab Values Created' : 'Lab Values Updated';

        AuditLogController::log(
            $action,
            'User ' . Auth::user()->username . ' ' . ($labValue->wasRecentlyCreated ? 'created a new' : 'updated an existing') . ' Lab Values record.',
            ['patient_id' => $data['patient_id']]
        );

        $request->session()->put('selected_patient_id', $data['patient_id']);

        if ($request->input('action') === 'cdss') {
            return redirect()->route('nursing-diagnosis.process', [
                'component' => 'lab-values',
                'id' => $labValue->id
            ]);
        }

        if ($request->input('action') == 'save_and_diagnose') {
            return redirect()->route('nursing-diagnosis.start', [
                'component' => 'lab-values',
                'id' => $labValue->id
            ]);
        }

        // Re-run CDSS to get severity for the view
        $viewAlerts = $cdssService->runLabCdss($labValue, $ageGroup);

        return redirect()->route('lab-values.index')
            ->with('alerts', $viewAlerts)
            ->with('success', $message);
    }

    public function runSingleCdssAnalysis(Request $request)
    {
        $data = $request->validate([
            'fieldName' => 'required|string',
            'finding' => 'nullable|numeric',
        ]);

        $patientId = $request->session()->get('selected_patient_id');
        if (!$patientId) {
            return response()->json(['alert' => 'No patient selected.', 'severity' => LabValuesCdssService::NONE], 400);
        }

        $patient = Auth::user()->patients()->find($patientId);
        if (!$patient) {
            return response()->json(['alert' => 'Patient not found or unauthorized.', 'severity' => LabValuesCdssService::NONE], 403);
        }

        $cdssService = new LabValuesCdssService();
        $ageGroup = $cdssService->getAgeGroup($patient);

        // Map fieldName to the parameter expected by checkLabResult
        $labParamMap = [
            'wbc_result' => 'wbc',
            'rbc_result' => 'rbc',
            'hgb_result' => 'hgb',
            'hct_result' => 'hct',
            'platelets_result' => 'platelets',
            'mcv_result' => 'mcv',
            'mch_result' => 'mch',
            'mchc_result' => 'mchc',
            'rdw_result' => 'rdw',
            'neutrophils_result' => 'neutrophils',
            'lymphocytes_result' => 'lymphocytes',
            'monocytes_result' => 'monocytes',
            'eosinophils_result' => 'eosinophils',
            'basophils_result' => 'basophils',
        ];

        $param = $labParamMap[$data['fieldName']] ?? null;
        $value = $data['finding'];

        if ($param && $value !== null) {
            $alert = $cdssService->checkLabResult($param, $value, $ageGroup);
        } else {
            $alert = ['alert' => '', 'severity' => LabValuesCdssService::NONE];
        }

        return response()->json($alert);
    }

    public function runBatchCdssAnalysis(Request $request)
    {
        $batchData = $request->input('batch');
        if (!$batchData) {
            return response()->json(['error' => 'No batch data provided.'], 400);
        }

        $patientId = $request->session()->get('selected_patient_id');
        if (!$patientId) {
            return response()->json(['error' => 'No patient selected.'], 400);
        }

        $patient = Auth::user()->patients()->find($patientId);
        if (!$patient) {
            return response()->json(['error' => 'Patient not found or unauthorized.'], 403);
        }

        $cdssService = new LabValuesCdssService();
        $ageGroup = $cdssService->getAgeGroup($patient);

        $labParamMap = [
            'wbc_result' => 'wbc',
            'rbc_result' => 'rbc',
            'hgb_result' => 'hgb',
            'hct_result' => 'hct',
            'platelets_result' => 'platelets',
            'mcv_result' => 'mcv',
            'mch_result' => 'mch',
            'mchc_result' => 'mchc',
            'rdw_result' => 'rdw',
            'neutrophils_result' => 'neutrophils',
            'lymphocytes_result' => 'lymphocytes',
            'monocytes_result' => 'monocytes',
            'eosinophils_result' => 'eosinophils',
            'basophils_result' => 'basophils',
        ];

        $results = [];

        foreach ($batchData as $item) {
            $fieldName = $item['fieldName'] ?? null;
            $finding = $item['finding'] ?? null;
            
            $param = $labParamMap[$fieldName] ?? null;

            if ($param && $finding !== null) {
                $alert = $cdssService->checkLabResult($param, $finding, $ageGroup);
                $results[] = $alert;
            } else {
                $results[] = ['alert' => '', 'severity' => LabValuesCdssService::NONE];
            }
        }

        return response()->json($results);
    }

    public function runCorrelationAnalysis(Request $request)
    {
        $batchData = $request->input('batch');
        if (!$batchData) return response()->json(['correlations' => []]);

        $patientId = $request->session()->get('selected_patient_id');
        if (!$patientId) return response()->json(['correlations' => []]);

        $patient = Auth::user()->patients()->find($patientId);
        if (!$patient) return response()->json(['correlations' => []]);

        $cdssService = new LabValuesCdssService();
        $ageGroup = $cdssService->getAgeGroup($patient);

        $tempLabValue = new \stdClass();
        foreach ($batchData as $item) {
            $field = $item['fieldName'] ?? null;
            $val = $item['finding'] ?? null;
            if ($field && $val !== null) {
                $tempLabValue->$field = $val;
            }
        }

        $correlations = $cdssService->runLabCdss($tempLabValue, $ageGroup)['correlation_alerts'] ?? [];

        return response()->json(['correlations' => $correlations]);
    }
}