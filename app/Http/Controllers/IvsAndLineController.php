<?php

namespace App\Http\Controllers;

use App\Models\IvsAndLine;
use App\Models\Patient;
use Illuminate\Http\Request;
use Throwable;

class IvsAndLineController extends Controller
{
         public function show()
    {
        $patients = Patient::all();
        return view('ivs-and-lines', compact('patients'));
    }
    
    public function store(Request $request)
    {
        try {
        if ($request->validate)([
            'patient_id' => 'required|exists:patients,id',
            'iv_fluid' => 'nullable|string',
            'rate' => 'nullable|string',
            'site' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        IvsAndLine::create($request->all());

        return redirect()->back()->with('success', 'IVs and Lines record saved successfully.');
    
            } catch (Throwable $e) {
            // Check for the specific 'patient_id' cannot be null error
            if (str_contains($e->getMessage(), '1048 Column \'patient_id\' cannot be null')) {
                return back()->with('error', 'Please select a patient before saving the medical history.');
            }

            // Check for any general database integrity constraint violation
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getCode(), '23000')) {
                return back()->with('error', 'There was a problem saving your data due to missing or invalid information. Please check your inputs and try again.');
            }

            // For any other unexpected errors, provide a generic message
            return back()->with('error', 'An unexpected error occurred while saving the IVs and Lines record. Please try again or contact support.');
        }
    }
}
