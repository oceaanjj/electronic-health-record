<?php

/* TO:DO
[] Adding Patient
[] Removing Patient
[] Updating Patient
[] Read Patient Data (All and Specific Patient)
*/


namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientController extends Controller
{

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'user_id', 'id');
    }


    // Show lahat ng patient
    public function index()
    {
        $patients = Auth::user()->patients()->get();
        return view('patients.index', compact('patients'));
    }

    // Show specific patient
    public function show($id)
    {
        // $patient = Patient::findOrFail($id);
        $patient = Auth::user()->patients()->findOrFail($id);

        // Log patient viewing
        AuditLogController::log('Patient Viewed', 'User ' . Auth::user()->username . ' viewed patient record.', ['patient_id' => $patient->patient_id]);

        return view('patients.show', compact('patient'));
    }

    // redirect sa form na magiinput ng patient
    public function create()
    {
        return view('patients.create');
    }

    // SAVE
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'sex' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'birthplace' => 'nullable|string',
            'religion' => 'nullable|string',
            'ethnicity' => 'nullable|string',
            'chief_complaints' => 'nullable|string',
            'admission_date' => 'required|date',
        ]);

        //*
        $data['user_id'] = Auth::id();


        $patient = Patient::create($data);

        // Log patient creation
        AuditLogController::log('Patient Created', 'User ' . Auth::user()->username . ' created a new patient record.', ['patient_id' => $patient->patient_id]);

        return redirect()->route('patients.index')->with('success', 'Patient registered successfully');
    }

    // REDIRECT SA EDIT PAGE
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        // Log access to edit form
        // AuditLogController::log('Edit Form Accessed', 'User ' . Auth::user()->username . ' accessed edit form for patient ID ' . $id, ['patient_id' => $patient->id]);
        return view('patients.edit', compact('patient'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'sex' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'birthplace' => 'nullable|string',
            'religion' => 'nullable|string',
            'ethnicity' => 'nullable|string',
            'chief_complaints' => 'nullable|string',
            'admission_date' => 'required|date',
        ]);

        $patient->update($data);

        // Log patient update
        AuditLogController::log('Patient Updated', 'User ' . Auth::user()->username . ' updated patient record.', ['patient_id' => $patient->patient_id]);

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully');
    }

    // DELETE
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        // Log patient deletion
        AuditLogController::log('Patient Deleted', 'User ' . Auth::user()->username . ' deleted patient record.', ['patient_id' => $id]);

        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully');
    }


    /* TODO
    [x] Get the input from front
    [x] Search by ID
    []  Search by Name *Not yet done
    [x] use get to select all rows


    */
    public function search(Request $request)
    {
        // Retrieve the input and trim any whitespace
        $search_term = trim($request->input('input'));
        // $user_id = Auth::id(); // Get the ID of the authenticated user

        $patients_query = Auth::user()->patients();

        if (!empty($search_term)) {
            $patients_query->where(function ($query) use ($search_term) {
                // 1. Search by exact patient_id
                $query->where('patient_id', $search_term)
                    // 2. Search by partial name match (case-insensitive)
                    ->orWhere('name', 'LIKE', $search_term . '%');
            });
        }

        $patients = $patients_query->get();



        // Return the view. Using $search_term is better for view clarity.
        return view('patients.search', [
            'patients' => $patients,
            'input' => $search_term
        ]);
    }
}
