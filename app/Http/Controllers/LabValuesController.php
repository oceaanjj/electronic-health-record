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

                                                     $alerts = $this->runLabCdss($labValue, $cdssService, $ageGroup);

                                                  }            } else {
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
        $ageGroup = $this->getAgeGroup($patient);
        $alerts = $this->runLabCdss((object) $data, $cdssService, $ageGroup);

        // Add alerts to the data array
        foreach ($alerts as $key => $alertInfo) {
            // Assuming $key is like 'wbc_alerts' and we want to store the text in 'wbc_alert'
            $fieldName = str_replace('_alerts', '_alert', $key);
            if (isset($alertInfo[0]['text'])) {
                $data[$fieldName] = $alertInfo[0]['text'];
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

        if ($request->input('action') == 'save_and_diagnose') {
            return redirect()->route('nursing-diagnosis.start', [
                'component' => 'lab-values',
                'id' => $labValue->id
            ]);
        }

        // Re-run CDSS to get severity for the view
        $viewAlerts = $this->runLabCdss($labValue, $cdssService, $ageGroup);

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

    private function runLabCdss($labValue, $cdssService, $ageGroup)
    {
        $alerts = [];
        $lab = [
            'wbc' => 'wbc_result',
            'rbc' => 'rbc_result',
            'hgb' => 'hgb_result',
            'hct' => 'hct_result',
            'platelets' => 'platelets_result',
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
            if (property_exists($labValue, $field) && $labValue->$field !== null) {
                 $result = $cdssService->checkLabResult($param, $labValue->$field, $ageGroup);
                 if ($result['severity'] !== LabValuesCdssService::NONE) {
                    $alerts[$param . '_alerts'][] = [
                        'text' => $result['alert'],
                        'severity' => $result['severity'],
                    ];
                 } else {
                     $alerts[$param . '_alerts'][] = [
                        'text' => $result['alert'], 
                        'severity' => $result['severity'], 
                    ];
                 }
            }
        }
        return $alerts;
    }

    /**
     * Converts a patient's date_of_birth into the correct age group string.
     * Ito ang mas preferred na method.
     *
     * @param \App\Models\Patient $patient
     * @return string
     */
    private function getAgeGroup(Patient $patient): string
    {
        if (empty($patient->date_of_birth)) {
            return $this->getAgeGroupFromInteger($patient->age ?? 0);
        }

        try {
            $dob = Carbon::parse($patient->date_of_birth);
            $now = Carbon::now();

            $ageInDays = $dob->diffInDays($now);
            $ageInMonths = $dob->diffInMonths($now);
            $ageInYears = $dob->diffInYears($now);

            if ($ageInDays <= 30) {
                return 'neonate';
            }
            if ($ageInMonths < 24) {
                return 'infant';
            }
            if ($ageInYears < 12) {
                return 'child';
            }
            if ($ageInYears <= 18) {
                return 'adolescent';
            }
            return 'adult'; 

        } catch (\Exception $e) {
            Log::error("Error parsing date_of_birth for patient ID {$patient->patient_id}: " . $e->getMessage());
            return $this->getAgeGroupFromInteger($patient->age ?? 0);
        }
    }

    /**
     * Fallback function using an integer 'age' column (less accurate).
     */
    private function getAgeGroupFromInteger(int $ageInYears): string
    {
         if ($ageInYears === 0) {
             Log::warning("Cannot accurately determine age group for patient with age 0 years. Assuming 'infant'.");
             return 'infant';
         }
        if ($ageInYears < 2) {
            return 'infant';
        }
        if ($ageInYears < 12) { 
            return 'child';
        }
        if ($ageInYears <= 18) { 
            return 'adolescent';
        }
        return 'adult'; 
    }
}