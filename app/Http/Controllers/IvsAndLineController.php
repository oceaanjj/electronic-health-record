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
    public function show()
    {
        $patients = Patient::all();
        return view('ivs-and-lines', compact('patients'));
    }

    public function store(Request $request)
    {
        try {

            if ($request->has('present_condition_name')) {
                IvsAndLine::create([
                    'patient_id' => $request->patient_id,
                    'iv_fluid' => $request->iv_fluid,
                    'rate' => $request->present_description,
                    'site' => $request->present_medication,
                    'status' => $request->present_dosage,
                ]);
            }

            IvsAndLine::create($request->all());

            AuditLogController::log(
                'IVs & Lines created',
                'User ' . Auth::user()->username . ' created an IVs & Lines record.',
                ['patient_id' => $request['patient_id']]
            );

            return redirect()->back()->with('success', 'IVs and Lines record saved successfully.');

        } catch (Throwable $e) {
            // Check for the specific 'patient_id' cannot be null error
            if (str_contains($e->getMessage(), '1048 Column \'patient_id\' cannot be null')) {
                return back()->with('error', 'Please select a patient before submitting the IVs and Lines record.');
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
