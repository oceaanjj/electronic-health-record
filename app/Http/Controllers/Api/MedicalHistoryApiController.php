<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicalHistory\PresentIllness;
use App\Models\MedicalHistory\PastMedicalSurgical;
use App\Models\MedicalHistory\Allergy;
use App\Models\MedicalHistory\Vaccination;
use App\Models\MedicalHistory\DevelopmentalHistory;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class MedicalHistoryApiController extends Controller
{
    private function sanitize($data)
    {
        foreach ($data as $k => $v) {
            if ($v === '' || $v === null)
                $data[$k] = 'N/A';
        }
        return $data;
    }

    /**
     * Get all history for a patient
     */
    public function show($patient_id)
    {
        return response()->json([
            'present_illness' => PresentIllness::where('patient_id', $patient_id)->first(),
            'past_history' => PastMedicalSurgical::where('patient_id', $patient_id)->first(),
            'allergies' => Allergy::where('patient_id', $patient_id)->first(),
            'vaccination' => Vaccination::where('patient_id', $patient_id)->first(),
            'developmental' => DevelopmentalHistory::where('patient_id', $patient_id)->first(),
        ]);
    }

    // --- PRESENT ILLNESS ---
    public function getPresentIllness($id)
    {
        return response()->json(PresentIllness::findOrFail($id));
    }
    public function storePresentIllness(Request $request)
    {
        $record = PresentIllness::updateOrCreate(['patient_id' => $request->patient_id], $this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Present Illness) for patient ID: " . $request->patient_id . ".",
            ['patient_id' => $request->patient_id]
        );

        return response()->json($record);
    }
    public function updatePresentIllness(Request $request, $id)
    {
        $record = PresentIllness::findOrFail($id);
        $record->update($this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Present Illness) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id]
        );

        return response()->json($record);
    }

    // --- PAST HISTORY ---
    public function getPastHistory($id)
    {
        return response()->json(PastMedicalSurgical::findOrFail($id));
    }
    public function storePastHistory(Request $request)
    {
        $record = PastMedicalSurgical::updateOrCreate(['patient_id' => $request->patient_id], $this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Past History) for patient ID: " . $request->patient_id . ".",
            ['patient_id' => $request->patient_id]
        );

        return response()->json($record);
    }
    public function updatePastHistory(Request $request, $id)
    {
        $record = PastMedicalSurgical::findOrFail($id);
        $record->update($this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Past History) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id]
        );

        return response()->json($record);
    }

    // --- ALLERGIES ---
    public function getAllergies($id)
    {
        return response()->json(Allergy::findOrFail($id));
    }
    public function storeAllergies(Request $request)
    {
        $record = Allergy::updateOrCreate(['patient_id' => $request->patient_id], $this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Allergies) for patient ID: " . $request->patient_id . ".",
            ['patient_id' => $request->patient_id]
        );

        return response()->json($record);
    }
    public function updateAllergies(Request $request, $id)
    {
        $record = Allergy::findOrFail($id);
        $record->update($this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Allergies) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id]
        );

        return response()->json($record);
    }

    // --- VACCINATION ---
    public function getVaccination($id)
    {
        return response()->json(Vaccination::findOrFail($id));
    }
    public function storeVaccination(Request $request)
    {
        $record = Vaccination::updateOrCreate(['patient_id' => $request->patient_id], $this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Vaccination) for patient ID: " . $request->patient_id . ".",
            ['patient_id' => $request->patient_id]
        );

        return response()->json($record);
    }
    public function updateVaccination(Request $request, $id)
    {
        $record = Vaccination::findOrFail($id);
        $record->update($this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Vaccination) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id]
        );

        return response()->json($record);
    }

    // --- DEVELOPMENTAL ---
    public function getDevelopmental($id)
    {
        return response()->json(DevelopmentalHistory::findOrFail($id));
    }
    public function storeDevelopmental(Request $request)
    {
        $record = DevelopmentalHistory::updateOrCreate(['patient_id' => $request->patient_id], $this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Developmental) for patient ID: " . $request->patient_id . ".",
            ['patient_id' => $request->patient_id]
        );

        return response()->json($record);
    }
    public function updateDevelopmental(Request $request, $id)
    {
        $record = DevelopmentalHistory::findOrFail($id);
        $record->update($this->sanitize($request->all()));

        AuditLogController::log(
            'MEDICAL HISTORY UPDATED',
            "Nurse " . Auth::user()->username . " updated the medical history (Developmental) for patient ID: " . $record->patient_id . ".",
            ['patient_id' => $record->patient_id]
        );

        return response()->json($record);
    }
}
