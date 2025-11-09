<?php

namespace App\Http\Controllers;

use App\Models\IntakeAndOutput;
use App\Models\Patient;
use App\Services\IntakeAndOutputCdssService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IntakeAndOutputController extends Controller
{

    public function showPatientIntakeOutputs($patientId)
    {
        $patient = Patient::findOrFail($patientId);
        $intakeOutputs = $patient->intakeAndOutputs;
        return view('patient-intake-outputs', compact('patient', 'intakeOutputs'));
    }
    public function generatedAlerts(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'oral_intake' => 'nullable|integer',
            'iv_fluids_volume' => 'nullable|integer',
            'iv_fluids_type' => 'nullable|string',
            'urine_output' => 'nullable|integer',
        ]);
        $cdssAlerts = new IntakeAndOutputCdssService();
        $alerts = $cdssAlerts->analyzeIntakeOutput($data);
        $patients = Patient::all();
        $request->flash();
        return view('intake-and-output', [
            'patients' => $patients,
            'cdss_alerts' => $alerts,
        ]);
    }

    public function selectPatientAndDate(Request $request)
    {
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedPatient = null;
        $ioData = null;
        $currentDayNo = 1; // Default day no

        $patientId = $request->input('patient_id') ?? $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if (!$selectedPatient) {
                $request->session()->forget(['selected_patient_id', 'selected_day_no']);
                return view('intake-and-output', compact('patients', 'selectedPatient', 'ioData', 'currentDayNo'));
            }
            $request->session()->put('selected_patient_id', $patientId);

            // --- START: MODIFIED DAY NO LOGIC ---
            // Calculate Day No. based on admission date
            if ($selectedPatient && $selectedPatient->admission_date) {
                $admissionDate = Carbon::parse($selectedPatient->admission_date)->startOfDay();
                $today = Carbon::today();

                // diffInDays returns the difference. Add 1 because admission day is Day 1.
                $calculatedDayNo = $admissionDate->diffInDays($today) + 1;

                // Ensure day is at least 1 (e.g., if admission date is in the future)
                $currentDayNo = ($calculatedDayNo > 0) ? $calculatedDayNo : 1;
            } else {
                // Fallback if no admission date
                $currentDayNo = 1;
            }

            $request->session()->put('selected_day_no', $currentDayNo);
            // --- END: MODIFIED DAY NO LOGIC ---

            $ioData = IntakeAndOutput::where('patient_id', $patientId)
                ->where('day_no', $currentDayNo) // Use the calculated day_no
                ->first();

            Log::info('IntakeAndOutputController@selectPatientAndDate Debug:', [
                'patient_id' => $patientId,
                'day_no' => $currentDayNo,
                'ioData_found' => $ioData ? 'true' : 'false',
                'ioData_content' => $ioData ? $ioData->toArray() : null,
            ]);
        } else {
            $request->session()->forget(['selected_patient_id', 'selected_day_no']);
            Log::info('IntakeAndOutputController@selectPatientAndDate Debug: No patient ID found, session cleared.');
        }

        // $currentDayNo is already set from the logic above

        if ($request->ajax() && $request->header('X-Fetch-Form-Content')) {
            // Render the full view and extract the specific section
            $view = view('intake-and-output', [
                'patients' => $patients,
                'ioData' => $ioData,
                'selectedPatient' => $selectedPatient,
                'currentDayNo' => $currentDayNo,
            ])->render();

            // Use DOMDocument to parse and extract the form-content-container
            $dom = new \DOMDocument();
            // Suppress warnings about malformed HTML5
            libxml_use_internal_errors(true);
            $dom->loadHTML($view, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $container = $dom->getElementById('form-content-container');
            if ($container) {
                return response($dom->saveHTML($container));
            } else {
                return response('<div id="form-content-container">Error: Content container not found.</div>', 500);
            }
        } elseif ($request->ajax()) {
            return response()->json([
                'ioData' => $ioData,
                'currentDayNo' => $currentDayNo, // Pass the calculated day no
            ]);
        }

        return view('intake-and-output', [
            'patients' => $patients,
            'ioData' => $ioData,
            'selectedPatient' => $selectedPatient,
            'currentDayNo' => $currentDayNo,
        ]);
    }

    public function show(Request $request)
    {
        return $this->selectPatientAndDate($request);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
        ], [
            'patient_id.required' => 'Please choose a patient first.',
            'patient_id.exists' => 'Please choose a patient first.',
        ]);

        //****
        $user_id = Auth::id();
        $patient = Patient::where('patient_id', $request->patient_id)
            ->where('user_id', $user_id)
            ->first();
        if (!$patient) {
            return back()->with('error', 'Unauthorized patient access.');
        }

        if (!$request->has('patient_id')) {
            return back()->with('error', 'No patient selected.');
        }
        //****


        // --- START: MODIFIED DAY NO LOGIC FOR STORE ---
        // Calculate Day No. authoritatively from server-side
        $currentDayNo = 1; // Default
        if ($patient && $patient->admission_date) {
            $admissionDate = Carbon::parse($patient->admission_date)->startOfDay();
            $today = Carbon::today();
            $calculatedDayNo = $admissionDate->diffInDays($today) + 1;
            $currentDayNo = ($calculatedDayNo > 0) ? $calculatedDayNo : 1;
        }
        // --- END: MODIFIED DAY NO LOGIC FOR STORE ---

        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            // 'day_no' is removed from validation, we set it manually
            'oral_intake' => 'nullable|integer',
            'iv_fluids_volume' => 'nullable|integer',
            'iv_fluids_type' => 'nullable|string',
            'urine_output' => 'nullable|integer',
            'other_output' => 'nullable|integer',
        ]);

        // Manually add the server-calculated day_no
        $validatedData['day_no'] = $currentDayNo;

        // Analyze the data to get the alert
        $cdss = new IntakeAndOutputCdssService();
        $alertData = $cdss->analyzeIntakeOutput($validatedData);
        $validatedData['alert'] = $alertData['alert'];

        $existingIo = IntakeAndOutput::where('patient_id', $validatedData['patient_id'])
            ->where('day_no', $validatedData['day_no'])
            ->first();

        if ($existingIo) {
            $existingIo->update($validatedData);
            $message = 'Intake and Output data updated successfully!';
            AuditLogController::log(
                'Intake-and-Output Record Updated',
                'User ' . Auth::user()->username . ' updated an existing IO record.',
                ['patient_id' => $validatedData['patient_id']]
            );
        } else {
            IntakeAndOutput::create($validatedData);
            $message = 'Intake and Output data saved successfully!';
            AuditLogController::log(
                'Intake-and-Output Record Created',
                'User ' . Auth::user()->username . ' created a new IO record.',
                ['patient_id' => $validatedData['patient_id']]
            );
        }

        $request->session()->put('selected_day_no', $validatedData['day_no']);

        return redirect()->route('io.show')
            ->with('success', $message);
    }

    public function checkIntakeOutput(Request $request)
    {
        $oralIntake = $request->input('oral_intake');
        $ivFluidsVolume = $request->input('iv_fluids_volume');
        $urineOutput = $request->input('urine_output');

        $data = [
            'oral_intake' => $oralIntake,
            'iv_fluids_volume' => $ivFluidsVolume,
            'urine_output' => $urineOutput,
        ];

        $cdssAlerts = new IntakeAndOutputCdssService();
        $result = $cdssAlerts->analyzeIntakeOutput($data);

        $result['severity'] = strtoupper($result['severity']);

        return response()->json($result);
    }
}