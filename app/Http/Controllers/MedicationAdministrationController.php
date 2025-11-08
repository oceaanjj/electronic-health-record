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

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'medication.*' => 'nullable|string',
            'dose.*' => 'nullable|string',
            'route.*' => 'nullable|string',
            'frequency.*' => 'nullable|string',
            'comments.*' => 'nullable|string',
            'time.*' => 'nullable|string',
        ]);

        try {
            $patient = Patient::where('patient_id', $request->patient_id)
                ->first();

            if (!$patient) {
                return back()->with('error', 'Patient not found.');
            }

            $count = count($request->input('medication', []));
            $createdCount = 0;

            for ($i = 0; $i < $count; $i++) {
                if (
                    empty($request->medication[$i]) &&
                    empty($request->dose[$i]) &&
                    empty($request->route[$i]) &&
                    empty($request->frequency[$i]) &&
                    empty($request->comments[$i])
                ) {
                    continue;
                }

                MedicationAdministration::create([
                    'patient_id' => $request->patient_id,
                    'medication' => $request->medication[$i] ?? null,
                    'dose' => $request->dose[$i] ?? null,
                    'route' => $request->route[$i] ?? null,
                    'frequency' => $request->frequency[$i] ?? null,
                    'comments' => $request->comments[$i] ?? null,
                    'time' => $request->time[$i] ?? null,
                ]);

                $createdCount++;
            }

            if ($createdCount > 0) {
                $username = Auth::user()->username ?? 'Unknown';
                AuditLogController::log(
                    'Medication Administration Created',
                    "User {$username} recorded {$createdCount} medication entries for patient ID {$request->patient_id}.",
                    ['patient_id' => $request->patient_id]
                );
            }

            return redirect()->route('medication-administration')->with('success', 'Medication Administration data saved successfully!');

        } catch (Throwable $e) {
            return back()->with('error', 'Error saving data: ' . $e->getMessage());
        }
    }
}