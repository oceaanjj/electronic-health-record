<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diagnostic;
use App\Models\Patient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DiagnosticsController extends Controller
{
    // Select patient and store in session 
    public function selectPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $request->session()->put('selected_patient_id', $patientId);
        return redirect()->route('diagnostics.index');
    }

    //Show diagnostics page
    public function index(Request $request)
    {
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $patientId = $request->session()->get('selected_patient_id');
        $selectedPatient = null;
        $images = [];

        if ($patientId) {
            $selectedPatient = $patients->firstWhere('patient_id', $patientId);

            if ($selectedPatient) {
                // Fetch diagnostics and group by type
                $diagnostics = Diagnostic::where('patient_id', $patientId)
                    ->where('original_name', 'not like', 'deleted-%')
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->groupBy('type');

                // Convert to array for Blade
                foreach ($diagnostics as $type => $items) {
                    $images[$type] = $items;
                }
            }
        }

        return view('diagnostics', compact('patients', 'selectedPatient', 'images', 'patientId'));
    }

    // Handle upload of diagnostic images 
    public function submit(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'images' => 'nullable|array',
            'images.*.*' => 'nullable|image|max:8192',
        ], [
            'patient_id.required' => 'ERROR: Walang napiling pasyente. Pumili muna sa dropdown.',
            'patient_id.exists' => 'ERROR: Ang napiling pasyente ay wala sa database.',
        ]);

        $patientId = $request->input('patient_id');
        $userId = Auth::id();

        $patient = Patient::where('patient_id', $patientId)
            ->where('user_id', $userId)
            ->first();

        if (!$patient) {
            return redirect()->back()->with('error', 'ERROR: Unauthorized access.');
        }

        $lastName = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($patient->last_name));
        $filesSaved = 0;
        $date = now()->format('Ymd');
        try {
            foreach ((array) $request->file('images') as $type => $files) {
                if (!is_array($files))
                    continue;

                $typeSlug = preg_replace('/[^a-zA-Z0-9]/', '_', strtolower($type));
                
                // Find the current max counter for this patient, type and date to avoid overwriting
                $existingNames = Diagnostic::where('patient_id', $patientId)
                    ->where('type', $type)
                    ->where('original_name', 'like', "{$typeSlug}_{$lastName}_{$date}_%")
                    ->pluck('original_name');

                $maxCounter = 0;
                foreach ($existingNames as $name) {
                    if (preg_match('/_(\d+)\.\w+$/', $name, $matches)) {
                        $maxCounter = max($maxCounter, intval($matches[1]));
                    }
                }
                $counter = $maxCounter + 1;

                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $extension = $file->getClientOriginalExtension() ?: 'jpg';
                        $filename = "{$typeSlug}_{$lastName}_{$date}_{$counter}.{$extension}";
                        $counter++;

                        $path = $file->storeAs('diagnostics', $filename, 'public');

                        Diagnostic::create([
                            'patient_id' => $patientId,
                            'type' => $type,
                            'path' => $path,
                            'original_name' => $filename,
                        ]);

                        $filesSaved++;
                    }
                }
            }
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'DATABASE ERROR: ' . $e->getMessage());
        }

        return redirect()->route('diagnostics.index')
            ->with('success', "Success! $filesSaved file(s) uploaded successfully.");
    }

    // Delete diagnostic image 
    public function destroy($id)
    {
        try {
            $record = Diagnostic::findOrFail($id);
            $userId = Auth::id();

            $patient = Patient::where('patient_id', $record->patient_id)
                ->where('user_id', $userId)
                ->first();

            if (!$patient) {
                return redirect()->back()->with('error', 'ERROR: Unauthorized access.');
            }

            if ($record->path && Storage::disk('public')->exists($record->path)) {
                $oldPath = $record->path;
                $newFilename = 'deleted-' . basename($oldPath);
                $newPath = 'diagnostics/' . $newFilename;
                
                Storage::disk('public')->move($oldPath, $newPath);
                
                $record->update([
                    'path' => $newPath,
                    'original_name' => 'deleted-' . ($record->original_name ?: basename($oldPath))
                ]);
            }

            return redirect()->back()->with('success', 'Image marked as deleted.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Failed to delete image: ' . $e->getMessage());
        }
    }

    // Delete all diagnostic images for a given type and patient
    public function destroyAll(Request $request, $type, $patient_id)
    {
        try {
            $userId = Auth::id();
            $patient = Patient::where('patient_id', $patient_id)
                ->where('user_id', $userId)
                ->first();

            if (!$patient) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access or patient not found.'], 403);
            }

            // Fetch all diagnostic records for the given type and patient
            $records = Diagnostic::where('patient_id', $patient_id)
                ->where('type', $type)
                ->where('original_name', 'not like', 'deleted-%')
                ->get();

            if ($records->isEmpty()) {
                return response()->json(['success' => true, 'message' => 'No images found to delete for this type.'], 200);
            }

            DB::beginTransaction();

            foreach ($records as $record) {
                if ($record->path && Storage::disk('public')->exists($record->path)) {
                    $oldPath = $record->path;
                    $newFilename = 'deleted-' . basename($oldPath);
                    $newPath = 'diagnostics/' . $newFilename;
                    
                    Storage::disk('public')->move($oldPath, $newPath);
                    
                    $record->update([
                        'path' => $newPath,
                        'original_name' => 'deleted-' . ($record->original_name ?: basename($oldPath))
                    ]);
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'All images for ' . $type . ' marked as deleted.'], 200);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Error deleting all diagnostics for patient {$patient_id}, type {$type}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete images: ' . $e->getMessage()], 500);
        }
    }
}
