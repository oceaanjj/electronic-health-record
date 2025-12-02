<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PatientController extends Controller
{

    public function patients(): HasMany
    {
        return $this->HasMany(Patient::class, 'user_id', 'id');
    }


    // Show lahat ng patient
    public function index()
    {
        $patients = Auth::user()->patients()->withTrashed()->get();
        return view('patients.index', compact('patients'));
    }

    // Show specific patient
    public function show($id)
    {
        try {
            // Find a single patient, even if they are inactive
            $patient = Auth::user()->patients()->withTrashed()->findOrFail($id);

            // Log patient viewing
            AuditLogController::log('Patient Viewed', 'User ' . Auth::user()->username . ' viewed patient record.', ['patient_id' => $patient->patient_id]);
            return view('patients.show', compact('patient'));

        } catch (ModelNotFoundException $e) {
            // If patient ID doesn't exist or doesn't belong to this user
            abort(404, 'Patient not found');
        } catch (\Exception $e) {
            // Catch any other unexpected errors
            Log::error('Error in PatientController@show: ' . $e->getMessage());
            return redirect()->route('patients.index')->with('error', 'An error occurred while trying to view the patient.');
        }
    }

    // redirect sa form na magiinput ng patient
    public function create()
    {
        $currentDate = Carbon::today()->format('Y-m-d');
        return view('patients.create', compact('currentDate'));
    }

    // SAVE
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'age' => 'required|integer',
            'birthdate' => 'required|date',
            'sex' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'birthplace' => 'nullable|string',
            'religion' => 'nullable|string',
            'ethnicity' => 'nullable|string',
            'chief_complaints' => 'nullable|string',
            'admission_date' => 'required|date',
            'room_no' => 'nullable|string',
            'bed_no' => 'nullable|string',
            'contact_name' => 'nullable|array',
            'contact_name.*' => 'nullable|string',
            'contact_relationship' => 'nullable|array',
            'contact_relationship.*' => 'nullable|string',
            'contact_number' => 'nullable|array',
            'contact_number.*' => 'nullable|string',
        ]);

        try {
            $data['user_id'] = Auth::id();

            // Filter out empty contact values and ensure proper array structure
            if (isset($data['contact_name'])) {
                $data['contact_name'] = array_filter($data['contact_name'], function ($value) {
                    return !empty($value);
                });
                $data['contact_name'] = !empty($data['contact_name']) ? array_values($data['contact_name']) : null;
            }

            if (isset($data['contact_relationship'])) {
                $data['contact_relationship'] = array_filter($data['contact_relationship'], function ($value) {
                    return !empty($value);
                });
                $data['contact_relationship'] = !empty($data['contact_relationship']) ? array_values($data['contact_relationship']) : null;
            }

            if (isset($data['contact_number'])) {
                $data['contact_number'] = array_filter($data['contact_number'], function ($value) {
                    return !empty($value);
                });
                $data['contact_number'] = !empty($data['contact_number']) ? array_values($data['contact_number']) : null;
            }

            $patient = Patient::create($data);

            // Log patient creation
            AuditLogController::log('Patient Created', 'User ' . Auth::user()->username . ' created a new patient record.', ['patient_id' => $patient->patient_id]);

            return redirect()->route('patients.index')->with('success', 'Patient registered successfully');

        } catch (\Exception $e) {
            // Catch any database or other errors
            Log::error('Error in PatientController@store: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'An error occurred while creating the patient. Please try again.');
        }
    }

    // REDIRECT SA EDIT PAGE
    public function edit($id)
    {
        try {
            // Find patient, even if inactive
            $patient = Patient::withTrashed()->findOrFail($id);
            return view('patients.edit', compact('patient'));

        } catch (ModelNotFoundException $e) {
            // If patient ID doesn't exist
            abort(404, 'Patient not found');
        } catch (\Exception $e) {
            Log::error('Error in PatientController@edit: ' . $e->getMessage());
            return redirect()->route('patients.index')->with('error', 'An error occurred while trying to edit the patient.');
        }
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'age' => 'required|integer',
            'birthdate' => 'required|date',
            'sex' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'birthplace' => 'nullable|string',
            'religion' => 'nullable|string',
            'ethnicity' => 'nullable|string',
            'chief_complaints' => 'nullable|string',
            'admission_date' => 'required|date',
            'room_no' => 'nullable|string',
            'bed_no' => 'nullable|string',
            'contact_name' => 'nullable|array',
            'contact_name.*' => 'nullable|string',
            'contact_relationship' => 'nullable|array',
            'contact_relationship.*' => 'nullable|string',
            'contact_number' => 'nullable|array',
            'contact_number.*' => 'nullable|string',
        ]);

        try {
            // Filter out empty contact values and ensure proper array structure
            if (isset($data['contact_name'])) {
                $data['contact_name'] = array_filter($data['contact_name'], function ($value) {
                    return !empty($value);
                });
                $data['contact_name'] = !empty($data['contact_name']) ? array_values($data['contact_name']) : null;
            }

            if (isset($data['contact_relationship'])) {
                $data['contact_relationship'] = array_filter($data['contact_relationship'], function ($value) {
                    return !empty($value);
                });
                $data['contact_relationship'] = !empty($data['contact_relationship']) ? array_values($data['contact_relationship']) : null;
            }

            if (isset($data['contact_number'])) {
                $data['contact_number'] = array_filter($data['contact_number'], function ($value) {
                    return !empty($value);
                });
                $data['contact_number'] = !empty($data['contact_number']) ? array_values($data['contact_number']) : null;
            }

            // Find patient, even if inactive
            $patient = Patient::withTrashed()->findOrFail($id);
            $patient->update($data);

            // Log patient update
            AuditLogController::log('Patient Updated', 'User ' . Auth::user()->username . ' updated patient record.', ['patient_id' => $patient->patient_id]);

            return redirect()->route('patients.index')->with('success', 'Patient updated successfully');

        } catch (ModelNotFoundException $e) {
            abort(404, 'Patient not found');
        } catch (\Exception $e) {
            Log::error('Error in PatientController@update: ' . $e->getMessage());
            return redirect()->route('patients.index')->with('error', 'An error occurred while updating the patient.');
        }
    }

    // SET TO INACTIVE
    public function deactivate($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->delete();


            AuditLogController::log('Patient Deactivated', 'User ' . Auth::user()->username . ' set patient record to inactive.', ['patient_id' => $id]);

            return response()->json(['success' => true, 'message' => 'Patient set to inactive successfully', 'patient' => $patient->fresh()]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Patient not found or already inactive'], 404);
        } catch (\Exception $e) {
            Log::error('Error in PatientController@deactivate: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while deactivating the patient.'], 500);
        }
    }

    // SET TO ACTIVE
    public function activate($id)
    {
        try {
            $patient = Patient::withTrashed()->findOrFail($id);
            $patient->restore(); // This restores the soft-deleted record

            // Log patient activation
            AuditLogController::log('Patient Activated', 'User ' . Auth::user()->username . ' set patient record to active.', ['patient_id' => $id]);

            return response()->json(['success' => true, 'message' => 'Patient set to active successfully', 'patient' => $patient->fresh()]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Patient not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error in PatientController@activate: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while activating the patient.'], 500);
        }
    }


    // ... (your search functions remain the same) ...

    public function search(Request $request)
    {
        // Retrieve the input and trim any whitespace
        $search_term = trim($request->input('input'));
        $patients_query = Auth::user()->patients()->withTrashed();

        if (!empty($search_term)) {
            $patients_query->where(function ($query) use ($search_term) {
                $query->where('patient_id', $search_term)
                    // 2. Search by partial name match (case-insensitive)
                    ->orWhere('first_name', 'LIKE', $search_term . '%')
                    ->orWhere('last_name', 'LIKE', $search_term . '%');
            });
        }
        $patients = $patients_query->get();

        return view('patients.search', [
            'patients' => $patients,
            'input' => $search_term
        ]);
    }

    public function liveSearch(Request $request)
    {
        $search_term = trim($request->input('input'));
        $patients_query = Auth::user()->patients()->withTrashed();

        if (!empty($search_term)) {
            $patients_query->where(function ($query) use ($search_term) {
                $query->where('patient_id', 'LIKE', $search_term . '%')
                    ->orWhere('first_name', 'LIKE', '%' . $search_term . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $search_term . '%');
            });
        }
        $patients = $patients_query->get();

        $patients->each(function ($patient) {
            $patient->name = $patient->name;
        });

        return response()->json($patients);
    }
}
