<?php

namespace App\Http\Controllers;

use App\Models\DiagnosticImage;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DiagnosticImageController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'image_type' => 'required|in:xray,ultrasound,ct_scan_mri,echocardiogram',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // Max 10MB
        ]);

        // Store the uploaded image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('diagnostic_images', 'public');
            $data['image_path'] = $imagePath;
        }

       //reset path
        unset($data['image']);

        DiagnosticImage::create($data);

        return redirect()->back()->with('success', 'Diagnostic image uploaded successfully!');
    }

    public function showPatientImages($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $diagnosticImages = $patient->diagnosticImages()->orderBy('created_at', 'desc')->get();
        
        return view('diagnostic-images.patient-images', compact('patient', 'diagnosticImages'));
    }


    // Deltee
    public function destroy($id)
    {
        $image = DiagnosticImage::findOrFail($id);
        
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $image->delete();

        return redirect()->back()->with('success', 'Diagnostic image deleted successfully!');
    }
}
