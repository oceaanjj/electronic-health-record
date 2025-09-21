<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Vitals;

class VitalSignsController extends Controller
{
    
    public function show()
    {
        $patients = Patient::all();
        return view('vital-signs', compact('patients'));
    }

    public function store(Request $request)
    {
        // Validate and store the vital signs data
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer',
            'date' => 'required|date',
            'temperature' => 'nullable|numeric',
            'hr' => 'nullable|integer',
            'rr' => 'nullable|integer',
            'bp' => 'nullable|string',
            'spo2' => 'nullable|integer',
        ]);


        Vitals::create($data);

        return redirect()->route('vital-signs.index')
            ->with('success', 'Vital signs data saved successfully!');
    }
}
