<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Diagnostic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DiagnosticController extends Controller
{
    /**
     * Display the diagnostic imaging management page. This method handles both the
     * initial view and reloading the view after a patient is selected.
     */
    public function index(Request $request)
    {
        // 1. Get all patients assigned to the nurse for the dropdown menu
        $allPatients = Auth::user()->patients;

        $selectedPatient = null;
        $diagnosticsByType = collect(); // Default to an empty collection

        // 2. Check if a patient was selected from the dropdown
        if ($request->has('patient_id') && $request->filled('patient_id')) {
            // Find the selected patient
            $selectedPatient = Patient::findOrFail($request->patient_id);

            // Fetch their images and group them by type.
            // This assumes you have a 'diagnostics' relationship on your Patient model.
            if (method_exists($selectedPatient, 'diagnostic')) {
                 $diagnosticsByType = $selectedPatient->diagnostics()->get()->groupBy('diagnostic_type');
            } else {
                // Fallback if the relationship doesn't exist on the Patient model
                $diagnosticsByType = Diagnostic::where('patient_id', $selectedPatient->patient_id)->get()->groupBy('diagnostic_type');
            }
        }

        // 3. Return the single view with all the necessary data
        return view('diagnostic', [
            'allPatients'     => $allPatients,
            'selectedPatient' => $selectedPatient,
            'diagnosticsByType' => $diagnosticsByType,
        ]);
    }

    /**
     * Update the diagnostic images for a patient by handling new uploads and deleting marked images.
     */
    /**
     * Update the diagnostic images for a patient (handle uploads and deletions).
     */
    public function update(Request $request, Patient $patient)
    {
        // --- ADD THIS VALIDATION BLOCK ---
        $request->validate([
            // This ensures that if 'images' is present, it's an array.
            'images' => 'nullable|array', 
            // This validates each file inside the 'images' array.
            'images.*.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB per image
            // Ensures 'delete_images' is an array if present.
            'delete_images' => 'nullable|array'
        ]);
        // --- END VALIDATION BLOCK ---

        // 1. Handle Deletions
        if ($request->has('delete_images')) {
            $imagesToDelete = Diagnostic::whereIn('id', $request->input('delete_images'))->get();
            foreach ($imagesToDelete as $image) {
                if ($image->patient_id == $patient->patient_id) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        // 2. Handle New Uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $type => $files) {
                foreach($files as $file) {
                    $path = $file->store("patient_{$patient->patient_id}/diagnostics", 'public');
                    
                    Diagnostic::create([
                        'patient_id' => $patient->patient_id,
                        'uploader_id' => Auth::id(),
                        'diagnostic_type' => strtoupper(str_replace('_', ' / ', $type)),
                        'path' => $path,
                        'filename' => $file->getClientOriginalName(),
                    ]);
                }
            }
        }
        
        return back()->with('success', 'Diagnostic images updated successfully.');
    }
}