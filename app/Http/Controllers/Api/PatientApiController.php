<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Log;

class PatientApiController extends Controller
{
    private function transformPatient($patient)
    {
        $data = $patient->toArray();
        $data['id'] = $patient->patient_id;
        return $data;
    }

    public function index(Request $request)
    {
        $query = Patient::query();
        if (!$request->has('all')) {
            $query->where('is_active', true);
        }
        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('patient_id', 'like', "%$search%");
            });
        }
        $patients = $query->orderBy('last_name')->get();
        return response()->json($patients->map(fn($p) => $this->transformPatient($p)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'age' => 'required|integer',
            'birthdate' => 'required|date',
            'sex' => 'required|string',
            'admission_date' => 'required|date',
        ]);

        try {
            $validated['user_id'] = Auth::id();
            $validated['is_active'] = true;
            $patient = Patient::create($validated);
            AuditLogController::log('Patient Created (Mobile)', 'User ' . Auth::user()->username . ' registered a patient via mobile.', ['patient_id' => $patient->patient_id]);
            return response()->json(['message' => 'Patient registered successfully', 'patient' => $this->transformPatient($patient)], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error creating patient', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $patient = Patient::where('patient_id', $id)->firstOrFail();
        return response()->json($this->transformPatient($patient));
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::where('patient_id', $id)->firstOrFail();
        $patient->update($request->all());
        AuditLogController::log('Patient Updated (Mobile)', 'User ' . Auth::user()->username . ' updated patient details via mobile.', ['patient_id' => $patient->patient_id]);
        return response()->json(['message' => 'Patient updated successfully', 'patient' => $this->transformPatient($patient)]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $patient = Patient::withTrashed()->where('patient_id', $id)->firstOrFail();
        $isActive = $request->input('is_active', false);
        if ($isActive) {
            $patient->restore();
            $patient->update(['is_active' => true]);
            $action = 'Activated';
        } else {
            $patient->update(['is_active' => false]);
            $patient->delete();
            $action = 'Deactivated';
        }
        AuditLogController::log("Patient $action (Mobile)", "User " . Auth::user()->username . " toggled status via mobile.", ['patient_id' => $id]);
        return response()->json(['message' => "Patient $action successfully"]);
    }
}
