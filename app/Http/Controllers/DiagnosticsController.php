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
        // 1. Kunin ang patient_id mula sa SESSION (ito ang pinaka-sigurado)
        $patientId = $request->session()->get('selected_patient_id');

        // 2. Suriin muna kung may naka-select na pasyente sa session
        if (!$patientId) {
            return redirect()->back()
                ->with('error', 'ERROR: Walang napiling pasyente. Pumili muna sa dropdown.')
                ->withInput(); // withInput() para hindi mawala ang data
        }

        // 3. I-validate lang ang images
        $request->validate([
            'images.*' => 'nullable|array',
            'images.*.*' => 'nullable|image|max:8192', // 8MB Max
        ]);

        // 4. Suriin kung may file na in-upload
        if (!$request->hasFile('images')) {
            return redirect()->back()
                ->with('error', 'ERROR: Walang file na napiling i-upload. Pindutin ang "INSERT PHOTO" at pumili ng file.')
                ->withInput();
        }

        $filesSaved = 0; // Magbilang tayo kung may na-save

        try {
            // 5. I-proseso ang bawat file
            foreach ($request->file('images') as $type => $files) {
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if ($file && $file->isValid()) { // Check kung valid ang file
                            $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                            $path = $file->storeAs('public/diagnostics', $filename);

                            Diagnostic::create([
                                'patient_id' => $patientId, // Galing sa Session
                                'type' => $type,
                                'path' => $path,
                                'original_name' => $file->getClientOriginalName(),
                            ]);

                            $filesSaved++; // May na-save! Bilangin
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // 6. Kung may error sa database (e.g., maling column name, atbp.)
            // DITO NATIN MAKIKITA ANG TUNAY NA ERROR
            return redirect()->back()
                ->with('error', 'DATABASE ERROR: ' . $e->getMessage())
                ->withInput();
        }

        // 7. I-check kung may na-save ba talaga
        if ($filesSaved > 0) {
            return redirect()->back()
                ->with('success', "Success! $filesSaved na file/s ang na-save.");
        } else {
            // Dito mapupunta kung may 'images' sa request pero
            // sa-loob ng loop ay walang 'valid' na file.
            return redirect()->back()
                ->with('error', 'Walang valid na file na na-proseso. Paki-check ang iyong files.')
                ->withInput();
        }
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