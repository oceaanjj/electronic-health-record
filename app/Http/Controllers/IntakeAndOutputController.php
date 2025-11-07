<?php

namespace App\Http\Controllers;

use App\Models\IntakeAndOutput;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\IntakeAndOutputCdssService;

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
        $date = now()->format('Y-m-d');
        $dayNo = 1;

        $patientId = $request->input('patient_id') ?? $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if (!$selectedPatient) {
                $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
                return view('intake-and-output', compact('patients', 'selectedPatient', 'ioData'));
            }
            $request->session()->put('selected_patient_id', $patientId);

            $date = $request->input('date');
            $dayNo = $request->input('day_no');

            if (is_null($date) || is_null($dayNo)) {
                // Try to get date and day from session first
                $date = $request->session()->get('selected_date', now()->format('Y-m-d'));
                $dayNo = $request->session()->get('selected_day_no', 1);

                // If still no date/day (e.g., first load, or session cleared), then try latest IO
                if ($date === now()->format('Y-m-d') && $dayNo === 1) { // Check if defaults were used from session
                    $latestIo = IntakeAndOutput::where('patient_id', $patientId)
                        ->orderBy('date', 'desc')
                        ->orderBy('day_no', 'desc')
                        ->first();

                    if ($latestIo) {
                        $date = $latestIo->date;
                        $dayNo = $latestIo->day_no;
                    }
                }
            }

            $request->session()->put('selected_date', $date);
            $request->session()->put('selected_day_no', $dayNo);

            $ioData = IntakeAndOutput::where('patient_id', $patientId)
                ->where('date', $date)
                ->where('day_no', (int) $dayNo)
                ->first();
        } else {
            $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
        }

        $currentDate = $date;
        $currentDayNo = $dayNo;

        if ($request->ajax() && $request->header('X-Fetch-Form-Content')) {
            // Render the full view and extract the specific section
            $view = view('intake-and-output', [
                'patients' => $patients,
                'ioData' => $ioData,
                'selectedPatient' => $selectedPatient,
                'currentDate' => $currentDate,
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
                'currentDate' => $currentDate,
                'currentDayNo' => $currentDayNo,
            ]);
        }

        return view('intake-and-output', [
            'patients' => $patients,
            'ioData' => $ioData,
            'selectedPatient' => $selectedPatient,
            'currentDate' => $currentDate,
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


        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|between:1,30',
            'date' => 'required|date',
            'oral_intake' => 'nullable|integer',
            'iv_fluids_volume' => 'nullable|integer',
            'iv_fluids_type' => 'nullable|string',
            'urine_output' => 'nullable|integer',
            'other_output' => 'nullable|integer',
        ]);

        $cdss = new IntakeAndOutputCdssService();
        $analysis = $cdss->analyzeIntakeOutput($validatedData);
        $validatedData['alert'] = $analysis['alert'];

        $existingIo = IntakeAndOutput::where('patient_id', $validatedData['patient_id'])
            ->where('date', $validatedData['date'])
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

        $request->session()->put('selected_date', $validatedData['date']);
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
