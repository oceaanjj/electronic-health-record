<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalHistory\PresentIllness;
use App\Models\MedicalHistory\PastMedicalSurgical;
use App\Models\MedicalHistory\Allergy;
use App\Models\MedicalHistory\Vaccination;
use App\Models\MedicalHistory\DevelopmentalHistory;
use App\Models\Patient;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Throwable;

class MedicalController extends Controller
{

    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);
        return redirect()->route('medical-history');
    }


    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $presentIllness = null;
        $pastMedicalSurgical = null;
        $allergy = null;
        $vaccination = null;
        $developmentalHistory = null;
        $medicalHistory = null;

        // Get the patient ID from the session
        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);

            if ($selectedPatient) {
                $presentIllness = PresentIllness::where('patient_id', $patientId)->first();
                $pastMedicalSurgical = PastMedicalSurgical::where('patient_id', $patientId)->first();
                $allergy = Allergy::where('patient_id', $patientId)->first();
                $vaccination = Vaccination::where('patient_id', $patientId)->first();
                $developmentalHistory = DevelopmentalHistory::where('patient_id', $patientId)->first();
            }
        }

        return view('medical-history', compact('patients', 'selectedPatient', 'presentIllness', 'pastMedicalSurgical', 'allergy', 'vaccination', 'developmentalHistory'));
    }


    public function store(Request $request)
    {
        $createdFlag = false;
        $updatedFlag = false;

        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
        ], [
            'patient_id.required' => 'Please choose a patient first.',
            'patient_id.exists' => 'The selected patient is invalid.',
        ]);

        try {
            $username = Auth::user() ? Auth::user()->username : 'Guest';
            if ($request->has('present_condition_name')) {
                $data = [
                    'patient_id' => $request->patient_id,
                    'condition_name' => $request->present_condition_name,
                    'description' => $request->present_description,
                    'medication' => $request->present_medication,
                    'dosage' => $request->present_dosage,
                    'side_effect' => $request->present_side_effect,
                    'comment' => $request->present_comment,
                ];
                if ($this->handleRecord(PresentIllness::class, $data)) {
                    $createdFlag = true;
                } else {
                    $updatedFlag = true;
                }
            }

            if ($request->has('past_condition_name')) {
                $data = [
                    'patient_id' => $request->patient_id,
                    'condition_name' => $request->past_condition_name,
                    'description' => $request->past_description,
                    'medication' => $request->past_medication,
                    'dosage' => $request->past_dosage,
                    'side_effect' => $request->past_side_effect,
                    'comment' => $request->past_comment,
                ];
                if ($this->handleRecord(PastMedicalSurgical::class, $data)) {
                    $createdFlag = true;
                } else {
                    $updatedFlag = true;
                }
            }

            if ($request->has('allergy_condition_name')) {
                $data = [
                    'patient_id' => $request->patient_id,
                    'condition_name' => $request->allergy_condition_name,
                    'description' => $request->allergy_description,
                    'medication' => $request->allergy_medication,
                    'dosage' => $request->allergy_dosage,
                    'side_effect' => $request->allergy_side_effect,
                    'comment' => $request->allergy_comment,
                ];
                if ($this->handleRecord(Allergy::class, $data)) {
                    $createdFlag = true;
                } else {
                    $updatedFlag = true;
                }
            }

            if ($request->has('vaccine_name')) {
                $data = [
                    'patient_id' => $request->patient_id,
                    'condition_name' => $request->vaccine_name,
                    'description' => $request->vaccine_description,
                    'medication' => $request->vaccine_medication,
                    'dosage' => $request->vaccine_dosage,
                    'side_effect' => $request->vaccine_side_effect,
                    'comment' => $request->vaccine_comment,
                ];
                if ($this->handleRecord(Vaccination::class, $data)) {
                    $createdFlag = true;
                } else {
                    $updatedFlag = true;
                }
            }

            if ($request->has('gross_motor')) {
                $data = [
                    'patient_id' => $request->patient_id,
                    'gross_motor' => $request->gross_motor,
                    'fine_motor' => $request->fine_motor,
                    'language' => $request->language,
                    'cognitive' => $request->cognitive,
                    'social' => $request->social,
                ];
                if ($this->handleRecord(DevelopmentalHistory::class, $data)) {
                    $createdFlag = true;
                } else {
                    $updatedFlag = true;
                }
            }

            // audit log
            $message = '';
            $alert = '';
            $action = '';
            if ($createdFlag) {
                $message = 'created a new Medical History record.';
                $alert = 'Medical History data saved successfully!';
                $action = 'Created';
            } elseif ($updatedFlag) {
                $message = 'updated an exising Medical History record.';
                $alert = 'Medical History data updated successfully!';
                $action = 'Updated';

            }

            if ($message) {
                AuditLogController::log(
                    'Medical History ' . $action,
                    'User ' . Auth::user()->username . ' ' . $message,
                    ['patient_id' => $request->patient_id]
                );
            }

            return redirect()->route('medical-history')
                ->with('success', $alert);

        } catch (Throwable $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }


    private function handleRecord(string $modelClass, array $data): bool
    {
        $existingRecord = $modelClass::where('patient_id', $data['patient_id'])->first();

        // if ($existingRecord) {
        //     $hasContent = false;
        //     foreach ($existingRecord->getAttributes() as $key => $value) {
        //         if (!in_array($key, ['patient_id', 'created_at', 'updated_at']) && !empty($value)) {
        //             $hasContent = true;
        //             break;
        //         }
        //     }
        //     // If the record exists but has no meaningful content, treat it as a new record
        //     if (!$hasContent) {
        //         $existingRecord = null;
        //     }
        // }

        if ($existingRecord) {
            $existingRecord->update($data);
            return false;
        } else {
            $modelClass::create($data);
            return true;
        }
    }
}
