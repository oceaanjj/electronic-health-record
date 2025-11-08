<?php

namespace App\Http\Controllers;

use App\Models\MedicationAdministration;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Throwable;

class MedicationAdministrationController extends Controller
{
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);
        return redirect()->route('medication-administration');
    }

    public function show(Request $request)
    {
        $patients = Auth::user()->patients->sortBy('last_name');
        $selectedPatient = null;
        $administrations = collect();

        $patientId = $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);

            if ($selectedPatient) {
                $administrations = MedicationAdministration::where('patient_id', $patientId)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('medication-administration', compact('patients', 'selectedPatient', 'administrations'));
    }

    /**
     * Stores or updates medication administration entries.
     * Uses patient_id, date, and time as the unique key to determine if it's an update or create operation.
     */
    public function store(Request $request)
    {
        // Removed validation for 'id.*'
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'date' => 'required|date_format:Y-m-d',
            'medication.*' => 'nullable|string',
            'dose.*' => 'nullable|string',
            'route.*' => 'nullable|string',
            'frequency.*' => 'nullable|string',
            'comments.*' => 'nullable|string',
            'time.*' => 'nullable|string',
        ]);

        try {
            $patient = Patient::where('patient_id', $request->patient_id)->first();

            if (!$patient) {
                return back()->with('error', 'Patient not found.');
            }

            $count = count($request->input('medication', []));
            $createdCount = 0;
            $updatedCount = 0;
            $administrationDate = $request->input('date');

            for ($i = 0; $i < $count; $i++) {
                $submittedTime = $request->time[$i] ?? null;


                $allEmpty = empty($request->medication[$i]) &&
                    empty($request->dose[$i]) &&
                    empty($request->route[$i]) &&
                    empty($request->frequency[$i]) &&
                    empty($request->comments[$i]);

                if ($allEmpty || empty($submittedTime)) {
                    continue;
                }

                $attributes = [
                    'patient_id' => $request->patient_id,
                    'date' => $administrationDate,
                    'time' => $submittedTime,
                ];

                $values = [
                    'medication' => $request->medication[$i] ?? null,
                    'dose' => $request->dose[$i] ?? null,
                    'route' => $request->route[$i] ?? null,
                    'frequency' => $request->frequency[$i] ?? null,
                    'comments' => $request->comments[$i] ?? null,
                ];

                $record = MedicationAdministration::updateOrCreate($attributes, $values);

                if ($record->wasRecentlyCreated) {
                    $createdCount++;
                } else {
                    $updatedCount++;
                }
            }

            $totalAffected = $createdCount + $updatedCount;
            $message = "{$createdCount} new entries created and {$updatedCount} entries updated.";

            if ($totalAffected > 0) {
                $username = Auth::user()->username ?? 'Unknown';
                AuditLogController::log(
                    'Medication Administration Saved',
                    "User {$username} recorded {$totalAffected} medication entries (C:{$createdCount}, U:{$updatedCount}) for patient ID {$request->patient_id}.",
                    ['patient_id' => $request->patient_id]
                );
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Medication Administration data saved successfully!', 'created' => $createdCount, 'updated' => $updatedCount]);
            }

            return redirect()->route('medication-administration')->with('success', "Medication Administration data saved successfully! {$message}");

        } catch (Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error saving data: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error saving data: ' . $e->getMessage());
        }
    }

    public function getRecords(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $patientId = $request->input('patient_id');
        $date = $request->input('date');

        // Fetch records for the given patient and date using the new 'date' column
        $records = MedicationAdministration::where('patient_id', $patientId)
            ->where('date', $date) // Use the new date column for filtering
            ->orderBy('time')
            ->get();

        return response()->json($records);
    }
}