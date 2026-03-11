<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Diagnostic;
use Illuminate\Support\Facades\Storage;
use App\Models\Patient;

class DiagnosticApiController extends Controller
{
    /**
     * Get all diagnostic images for a patient.
     * Returns absolute URLs for easy mobile display.
     */
    public function getDiagnostics($patient_id) {
        $records = Diagnostic::where('patient_id', $patient_id)->latest()->get();
        
        $records->transform(function($record) {
            $record->image_url = url('storage/' . $record->path);
            return $record;
        });

        return response()->json($records);
    }

    /**
     * Upload diagnostic images from mobile.
     */
    public function storeDiagnostic(Request $request) {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'type' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|max:8192'
        ]);

        $savedFiles = [];
        $patientId = $request->patient_id;

        $patient = Patient::where('patient_id', $patientId)->firstOrFail();
        $lastName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($patient->last_name));
        $typeSlug = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($request->type));
        $date = now()->format('Ymd');
        $counter = 1;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $extension = $file->getClientOriginalExtension() ?: 'jpg';
                    $filename = "{$typeSlug}_{$lastName}_{$date}_{$counter}.{$extension}";
                    $counter++;

                    $path = $file->storeAs('diagnostics', $filename, 'public');

                    $record = Diagnostic::create([
                        'patient_id' => $patientId,
                        'type' => $request->type,
                        'path' => $path,
                        'original_name' => $filename,
                    ]);

                    $record->image_url = url('storage/' . $path);
                    $savedFiles[] = $record;
                }
            }
        }

        return response()->json([
            'message' => count($savedFiles) . ' images uploaded and synced to website.',
            'data' => $savedFiles
        ], 201);
    }

    /**
     * Delete a diagnostic record.
     */
    public function destroy($id) {
        $record = Diagnostic::findOrFail($id);
        
        if (Storage::disk('public')->exists($record->path)) {
            Storage::disk('public')->delete($record->path);
        }
        
        $record->delete();
        return response()->json(['message' => 'Image deleted from database and server.']);
    }
}
