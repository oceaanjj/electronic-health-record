<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnostic;
use App\Models\Patient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiagnosticsController extends Controller
{
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);

        return redirect()->route('diagnostics.index');
    }

    public function index(Request $request)
    {
        $patients = Patient::orderBy('name')->get();
        $patientId = $request->session()->get('selected_patient_id');

        $selectedPatient = $patientId ? Patient::where('patient_id', $patientId)->first() : null;

        $images = $patientId
            ? Diagnostic::where('patient_id', $patientId)->orderBy('created_at')->get()->groupBy('type')
            : collect();

        return view('diagnostics', compact('patients', 'patientId', 'selectedPatient', 'images'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'images.*' => 'nullable|array',
            'images.*.*' => 'nullable|image|max:8192', // Allow multiple images
        ]);

        $patientId = $request->input('patient_id');

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $type => $files) {
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if ($file) {
                            $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('public/diagnostics', $filename);

                            Diagnostic::create([
                                'patient_id' => $patientId,
                                'type' => $type,
                                'path' => $path,
                                'original_name' => $file->getClientOriginalName(),
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Diagnostics saved successfully.');
    }

public function destroy($id)
{
    $record = Diagnostic::findOrFail($id);

    if ($record->path && Storage::exists($record->path)) {
        Storage::delete($record->path);
    }

    $record->delete();

    return redirect()->back()->with('success', 'Image deleted successfully.');
}
}