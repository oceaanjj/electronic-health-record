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

class PatientController extends Controller
{
    // Show lahat ng patient
    public function index()
    {
        $patients = Patient::all();
        return view('patients.index', compact('patients'));
    }

    // Show specific patient
    public function show($id)
    {
        $patient = Patient::findOrFail($id);
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
        $id = $request->input('input'); // the input from the search bar

        $patients = Patient::query()
            ->when($id, function ($q) use ($id) {
                $q->where('patient_id', $id);
            })
            ->get();

        // Log patient search
        AuditLogController::log('Patient Searched', 'User ' . Auth::user()->username . ' searched for a patient with ID: ' . $id, ['search_term' => $id]);


        //show results: Keith pa fix nalang kung where yung patient search result

        return view('patients.search', compact('patients', 'id'));
    }
}
