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
        $user = Auth::user();
        $query = Patient::query();

        // Doctors and Admins see all patients; Nurses see only their own.
        if (strtolower($user->role) === 'nurse') {
            $query->where('user_id', $user->id);
        }

        if (!$request->has('all')) {
            $query->where('is_active', true);
        } else {
            $query->withTrashed();
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
            // Required fields
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'required|string|max:255',
            'age'                   => 'required|numeric|min:0',
            'birthdate'             => 'required|date',
            'sex'                   => 'required|string|in:Male,Female,Other',
            'admission_date'        => 'required|date',

            // Optional demographic fields
            'middle_name'           => 'nullable|string|max:255',
            'address'               => 'nullable|string|max:500',
            'birthplace'            => 'nullable|string|max:255',
            'religion'              => 'nullable|string|max:255',
            'ethnicity'             => 'nullable|string|max:255',

            // Optional clinical fields
            'chief_complaints'      => 'nullable|string',
            'room_no'               => 'nullable|string|max:50',
            'bed_no'                => 'nullable|string|max:50',

            // Optional contact fields (stored as arrays)
            'contact_name'          => 'nullable|array',
            'contact_name.*'        => 'nullable|string|max:255',
            'contact_relationship'  => 'nullable|array',
            'contact_relationship.*'=> 'nullable|string|max:255',
            'contact_number'        => 'nullable|array',
            'contact_number.*'      => 'nullable|string|max:50',
        ]);

        try {
            $validated['user_id']   = Auth::id();
            $validated['is_active'] = true;
            $patient = Patient::create($validated);
            AuditLogController::log(
                'Patient Created (Mobile)',
                'User ' . Auth::user()->username . ' registered a patient via mobile.',
                ['patient_id' => $patient->patient_id]
            );
            return response()->json([
                'message' => 'Patient registered successfully',
                'patient' => $this->transformPatient($patient),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Patient store failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error creating patient', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $query = Patient::where('patient_id', $id);

        if (strtolower($user->role) === 'nurse') {
            $query->where('user_id', $user->id);
        }

        $patient = $query->firstOrFail();
        return response()->json($this->transformPatient($patient));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $query = Patient::where('patient_id', $id);

        if (strtolower($user->role) === 'nurse') {
            $query->where('user_id', $user->id);
        }

        $patient = $query->firstOrFail();

        $validated = $request->validate([
            'first_name'            => 'sometimes|string|max:255',
            'last_name'             => 'sometimes|string|max:255',
            'middle_name'           => 'nullable|string|max:255',
            'age'                   => 'sometimes|numeric|min:0',
            'birthdate'             => 'sometimes|date',
            'sex'                   => 'sometimes|string|in:Male,Female,Other',
            'admission_date'        => 'sometimes|date',
            'address'               => 'nullable|string|max:500',
            'birthplace'            => 'nullable|string|max:255',
            'religion'              => 'nullable|string|max:255',
            'ethnicity'             => 'nullable|string|max:255',
            'chief_complaints'      => 'nullable|string',
            'room_no'               => 'nullable|string|max:50',
            'bed_no'                => 'nullable|string|max:50',
            'contact_name'          => 'nullable|array',
            'contact_name.*'        => 'nullable|string|max:255',
            'contact_relationship'  => 'nullable|array',
            'contact_relationship.*'=> 'nullable|string|max:255',
            'contact_number'        => 'nullable|array',
            'contact_number.*'      => 'nullable|string|max:50',
        ]);

        $patient->update($validated);
        AuditLogController::log(
            'Patient Updated (Mobile)',
            'User ' . Auth::user()->username . ' updated patient details via mobile.',
            ['patient_id' => $patient->patient_id]
        );
        return response()->json([
            'message' => 'Patient updated successfully',
            'patient' => $this->transformPatient($patient),
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $user = Auth::user();
        $query = Patient::withTrashed()->where('patient_id', $id);

        if (strtolower($user->role) === 'nurse') {
            $query->where('user_id', $user->id);
        }

        $patient = $query->firstOrFail();
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
