<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnostic; // Siguraduhin na mayroon kang Model na 'to
use App\Models\Patient;    // Siguraduhin na mayroon kang Model na 'to
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiagnosticsController extends Controller
{

    /**
     * Store the selected patient ID in the session and redirect back to the index.
     */
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);
        
        // --- AYOS DITO ---
        // Dapat ito ay bumalik sa diagnostics page para ma-refresh ito.
        return redirect()->route('diagnostics.index');
    }

    /**
     * Show diagnostics page for selected patient (uses session('selected_patient_id'))
     */
    public function index(Request $request)
    {
        // --- AYOS DITO (1) ---
        // Kunin ang LAHAT ng pasyente para sa dropdown list.
        $patients = Patient::orderBy('name')->get(); 
        
        $patientId = $request->session()->get('selected_patient_id');

        $selectedPatient = $patientId ? Patient::where('patient_id', $patientId)->first() : null;

        $images = $patientId
            ? Diagnostic::where('patient_id', $patientId)->orderBy('created_at')->get()->groupBy('type')
            : collect();

        return view('diagnostics', [
            'patients' => $patients,          // <-- Idinagdag para sa dropdown
            'patientId' => $patientId,
            'selectedPatient' => $selectedPatient, // <-- Inayos para tumugma sa Blade
            'images' => $images,
        ]);
    }

    /**
     * Handle image upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'type' => 'required|in:xray,ultrasound,ct_scan,echocardiogram',
            'image' => 'required|image|max:8192', // max 8MB
        ]);

        $file = $request->file('image');
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/diagnostics', $filename);

        Diagnostic::create([
            'patient_id' => $request->patient_id,
            'type' => $request->type,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);

        return redirect()->back()->with('success', 'Image uploaded.');
    }

    /**
     * Delete a record and its file
     */
    public function destroy($id)
    {
        $record = Diagnostic::findOrFail($id);

        if ($record->path && Storage::exists($record->path)) {
            Storage::delete($record->path);
        }

        $record->delete();

        return redirect()->back()->with('success', 'Image deleted.');
    }
}