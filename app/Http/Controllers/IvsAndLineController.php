<?php

namespace App\Http\Controllers;

use App\Models\IvsAndLine;
use App\Models\Patient;
use Illuminate\Http\Request;
use Throwable;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;

class IvsAndLineController extends Controller
{
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);
        return redirect()->route('ivs-and-lines');
    }

    public function show(Request $request)
    {
        $patients = Patient::all();
        $selectedPatient = null;
        $ivsAndLineRecord = null;
        $patientId = $request->session()->get('selected_patient_id');
        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if ($selectedPatient) {
                $ivsAndLineRecord = IvsAndLine::where('patient_id', $patientId)->first();
            }
        }
        return view('ivs-and-lines', compact('patients', 'selectedPatient', 'ivsAndLineRecord'));
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
            'iv_fluid' => 'nullable|string',
            'rate' => 'nullable|string',
            'site' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $username = Auth::user() ? Auth::user()->username : 'Guest';
        $existingRecord = IvsAndLine::where('patient_id', $data['patient_id'])->first();

        if ($existingRecord) {
            $existingRecord->update($data);
            $message = 'IVs and Lines record updated successfully.';
            AuditLogController::log(
                'IVs and Lines Record Updated',
                'User ' . $username . ' updated an existing IVs and Lines record.',
                ['patient_id' => $data['patient_id']]
            );
        } else {
            IvsAndLine::create($data);
            $message = 'IVs and Lines record created successfully.';
            AuditLogController::log(
                'IVs and Lines Record Created',
                'User ' . $username . ' created a new IVs and Lines record.',
                ['patient_id' => $data['patient_id']]
            );
        }

        return redirect()->back()->with('success', $message);
    }
}
