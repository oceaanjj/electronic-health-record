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
        $dayNo = 1;
        $daysSinceAdmission = 30; // Default value

        $patientId = $request->input('patient_id') ?? $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);
            if (!$selectedPatient) {
                $request->session()->forget(['selected_patient_id', 'selected_day_no']);
                return view('intake-and-output', compact('patients', 'selectedPatient', 'ioData', 'daysSinceAdmission'));
            }
            $request->session()->put('selected_patient_id', $patientId);

            // Calculate days since admission
            $admissionDate = Carbon::parse($selectedPatient->admission_date);
            $daysSinceAdmission = $admissionDate->diffInDays(Carbon::now()) + 1;


            $dayNo = $request->input('day_no');

            if (is_null($dayNo)) {
                // Try to get day from session first
                $dayNo = $request->session()->get('selected_day_no', 1);

                // If still no day (e.g., first load, or session cleared), then try latest IO
                if ($dayNo === 1) { // Check if defaults were used from session
                    $latestIo = IntakeAndOutput::where('patient_id', $patientId)
                        ->orderBy('day_no', 'desc')
                        ->first();

                    if ($latestIo) {
                        $dayNo = $latestIo->day_no;
                    }
                }
            }

            $request->session()->put('selected_day_no', $dayNo);

            $ioData = IntakeAndOutput::where('patient_id', $patientId)
                ->where('day_no', (int) $dayNo)
                ->first();

            Log::info('IntakeAndOutputController@selectPatientAndDate Debug:', [
                'patient_id' => $patientId,
                'day_no' => $dayNo,
                'ioData_found' => $ioData ? 'true' : 'false',
                'ioData_content' => $ioData ? $ioData->toArray() : null,
            ]);
        } else {
            $request->session()->forget(['selected_patient_id', 'selected_day_no']);
            Log::info('IntakeAndOutputController@selectPatientAndDate Debug: No patient ID found, session cleared.');
        }

        $currentDayNo = $dayNo;

        if ($request->ajax() && $request->header('X-Fetch-Form-Content')) {
            // Render the full view and extract the specific section
            $view = view('intake-and-output', [
                'patients' => $patients,
                'ioData' => $ioData,
                'selectedPatient' => $selectedPatient,
                'currentDayNo' => $currentDayNo,
                'daysSinceAdmission' => $daysSinceAdmission,
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
                'currentDayNo' => $currentDayNo,
            ]);
        }

        return view('intake-and-output', [
            'patients' => $patients,
            'ioData' => $ioData,
            'selectedPatient' => $selectedPatient,
            'currentDayNo' => $currentDayNo,
            'daysSinceAdmission' => $daysSinceAdmission,
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

        $admissionDate = Carbon::parse($patient->admission_date);
        $daysSinceAdmission = $admissionDate->diffInDays(Carbon::now()) + 1;

        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|between:1,' . $daysSinceAdmission,
            'oral_intake' => 'nullable|integer',
            'iv_fluids_volume' => 'nullable|integer',
            'iv_fluids_type' => 'nullable|string',
            'urine_output' => 'nullable|integer',
            'other_output' => 'nullable|integer',
        ]);

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


    //new
    public function runBatchCdssAnalysis(Request $request)
    {
        // The payload from JS will be { batch: [ { time: "1", vitals: { field: value, ... } }, ... ] }
        $data = $request->validate([
            'batch' => 'required|array',
            'batch.*.time' => 'required|string', // This is the day_no
            'batch.*.vitals' => 'required|array', // This holds the I/O fields
        ]);

        $cdssService = new IntakeAndOutputCdssService();
        $results = [];

        foreach ($data['batch'] as $item) {
            // Re-used existing group analysis logic from analyzeIntakeOutput
            // $item['vitals'] will be [ 'oral_intake' => '500', 'urine_output' => '300', ... ]
            $result = $cdssService->analyzeIntakeOutput($item['vitals']);
            $result['severity'] = strtoupper($result['severity']);
            $results[] = $result;
        }

        return response()->json($results);
    }


    public function runCdssAnalysis(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'day_no' => 'required|integer|between:1,30',
        ]);

        $ioData = [
            'oral_intake' => $request->input('oral_intake'),
            'iv_fluids_volume' => $request->input('iv_fluids_volume'),
            'urine_output' => $request->input('urine_output'),
        ];

        $cdssService = new IntakeAndOutputCdssService();
        $result = $cdssService->analyzeIntakeOutput($ioData);

        $findings = [];
        if ($result['severity'] !== IntakeAndOutputCdssService::NONE) {
            $findings[] = $result['alert'];
        }

        $ioRecord = IntakeAndOutput::firstOrCreate(
            [
                'patient_id' => $validatedData['patient_id'],
                'day_no' => $validatedData['day_no'],
            ],
            $ioData
        );

        return redirect()->route('nursing-diagnosis.start', [
            'component' => 'intake-and-output',
            'id' => $ioRecord->id
        ])->with('findings', $findings);
    }
}
