<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Vitals;
use Illuminate\Http\Request;
use Throwable;

class VitalSignsController extends Controller
{
    public function show()
    {
        $patients = Patient::all();
        return view('vital-signs', compact('patients'));
    }

    public function store(Request $request)
    {
        try {
            // Add validation to ensure essential fields are not empty
      $validatedData = $request->validate([
'patient_id' => 'required|exists:patients,patient_id',
    'date' => 'required|date',
    'day' => 'required|integer',
]); 
            // Define the specific times to loop through
            $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];

            // Loop through each time to save the vital signs data
            foreach ($times as $time) {
                // Check if ANY of the fields for this time slot are filled before creating a record
                if (
                    $request->filled("temperature_{$time}") ||
                    $request->filled("hr_{$time}") ||
                    $request->filled("rr_{$time}") ||
                    $request->filled("bp_{$time}") ||
                    $request->filled("spo2_{$time}") ||
                    $request->filled("alerts_{$time}")
                ) {
                    Vitals::create([
                        'patient_id' => $validatedData['patient_id'],
                        'date' => $validatedData['date'],
                        'day_no' => $validatedData['day'],
                        'time' => $time,
                        'temperature' => $request->input("temperature_{$time}"),
                        'hr' => $request->input("hr_{$time}"),
                        'rr' => $request->input("rr_{$time}"),
                        'bp' => $request->input("bp_{$time}"),
                        'spo2' => $request->input("spo2_{$time}"),
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Vital Signs records saved successfully.');

        } catch (Throwable $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
