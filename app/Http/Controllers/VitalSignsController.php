<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Vitals;
use Illuminate\Http\Request;
use App\Services\VitalCdssService;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Log;

class VitalSignsController extends Controller
{


    /**
     * Finds and returns the Vitals record for a given patient, date, and day.
     *
     * @param string $patientId
     * @param string $date (format Y-m-d)
     * @param int $dayNo
     * @return \Illuminate\Support\Collection
     */
    private function getVitalsRecord(string $patientId, string $date, int $dayNo): \Illuminate\Support\Collection
    {
        return Vitals::where('patient_id', $patientId)
            ->where('date', $date)
            ->where('day_no', $dayNo)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->time)->format('H:i');
            });
    }






    public function selectPatient(Request $request)
    {
        $patients = Auth::user()->patients()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedPatient = null;
        $vitalsData = collect();
        $totalDaysSinceAdmission = 1; // Default to 1

        // Default values
        $currentDate = now()->format('Y-m-d');
        $currentDayNo = 1;
        $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];

        $patientId = $request->input('patient_id') ?? $request->session()->get('selected_patient_id');

        if ($patientId) {
            $selectedPatient = Patient::find($patientId);

            if ($selectedPatient) {
                $request->session()->put('selected_patient_id', $patientId);

                $admissionDate = Carbon::parse($selectedPatient->admission_date)->startOfDay();
                $today = now()->startOfDay();

                // Calculate exact days since admission (1-based index)
                // If admitted today, diff is 0, so Day No is 1.
                $totalDaysSinceAdmission = intval($admissionDate->diffInDays($today)) + 1;
                // Safety check: ensure it's at least 1
                if ($totalDaysSinceAdmission < 1)
                    $totalDaysSinceAdmission = 1;

                // Check inputs
                $reqDate = $request->input('date');
                $reqDayNo = $request->input('day_no');

                // Logic: Is this a fresh patient selection? (No date/day in request)
                $isNewPatientSelection = is_null($reqDate) && is_null($reqDayNo) && $request->has('patient_id');

                if ($isNewPatientSelection) {
                    // CASE 1: User just selected a patient. 
                    // FORCE "Latest Day" (Today) and calculated Day No.
                    $date = $today->format('Y-m-d');
                    $dayNo = $totalDaysSinceAdmission;
                } else {
                    // CASE 2: User is navigating (changing date/day or loading from session)
                    $date = $reqDate ?? $request->session()->get('selected_date');
                    $dayNo = $reqDayNo ?? $request->session()->get('selected_day_no');

                    // Fallback if session empty
                    if (!$date || !$dayNo) {
                        $date = $today->format('Y-m-d');
                        $dayNo = $totalDaysSinceAdmission;
                    }
                }

                // Ensure we don't go into the future
                $selectedDateCarbon = Carbon::parse($date)->startOfDay();
                if ($selectedDateCarbon->isAfter($today)) {
                    $date = $today->format('Y-m-d');
                    $dayNo = $totalDaysSinceAdmission;
                }

                // Save to session
                $request->session()->put('selected_date', $date);
                $request->session()->put('selected_day_no', $dayNo);

                $currentDate = $date;
                $currentDayNo = $dayNo;

                // Fetch Data
                $vitalsData = $this->getVitalsRecord($patientId, $currentDate, (int) $currentDayNo);

                // CDSS Analysis
                $cdssService = new VitalCdssService();
                foreach ($vitalsData as $time => $vitalRecord) {
                    $vitalsArray = $vitalRecord->toArray();
                    $alertResult = $cdssService->analyzeVitalsForAlerts($vitalsArray);
                    $vitalsData[$time]->alerts = $alertResult['alert'];
                    $vitalsData[$time]->news_severity = $alertResult['severity'];
                }
            } else {
                $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
            }
        } else {
            $request->session()->forget(['selected_patient_id', 'selected_date', 'selected_day_no']);
        }

        return view('vital-signs', [
            'patients' => $patients,
            'vitalsData' => $vitalsData,
            'selectedPatient' => $selectedPatient,
            'currentDate' => $currentDate,
            'currentDayNo' => $currentDayNo,
            'totalDaysSinceAdmission' => $totalDaysSinceAdmission,
            'times' => $times,
        ]);
    }




    /**
     * Handles AJAX request to fetch vital signs data for a specific patient, date, and day.
     * Returns data as JSON.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchVitalSignsData(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'date' => 'required|date',
            'day_no' => 'required|integer',
        ]);

        $patientId = $validatedData['patient_id'];
        $date = $validatedData['date'];
        $dayNo = (int) $validatedData['day_no'];

        // Fetch the Vitals record
        $vitalsData = $this->getVitalsRecord($patientId, $date, $dayNo);

        // Re-run CDSS analysis on fetched data
        $cdssService = new VitalCdssService();
        foreach ($vitalsData as $time => $vitalRecord) {
            $vitalsArray = $vitalRecord->toArray();
            $alertResult = $cdssService->analyzeVitalsForAlerts($vitalsArray);
            $vitalsData[$time]->alerts = $alertResult['alert'];
            $vitalsData[$time]->news_severity = $alertResult['severity'];
        }

        return response()->json($vitalsData);
    }






    public function show(Request $request)
    {
        // The logic for displaying the initial page is now handled by selectPatient
        return $this->selectPatient($request);
    }




    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'date' => 'required|date',
            'day_no' => 'required|integer|min:1', // Changed from between:1,30
        ]);

        $user_id = Auth::id();
        $patient = Patient::where('patient_id', $request->patient_id)
            ->where('user_id', $user_id)
            ->first();
        if (!$patient)
            return back()->with('error', 'Unauthorized patient access.');

        // Check if date is not in the future
        if (Carbon::parse($validatedData['date'])->isFuture()) {
            return back()->with('error', 'Cannot save vital signs for a future date.');
        }

        // Check if date is before admission date
        if (Carbon::parse($validatedData['date'])->isBefore(Carbon::parse($patient->admission_date)->startOfDay())) {
            return back()->with('error', 'Cannot save vital signs for a date before admission.');
        }


        $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
        $anyCreated = false;
        $anyUpdated = false;
        $cdssService = new VitalCdssService();

        foreach ($times as $time) {
            $dbTime = Carbon::createFromFormat('H:i', $time)->format('H:i:s');

            $vitalsForTime = [
                'temperature' => $request->input("temperature_{$time}"),
                'hr' => $request->input("hr_{$time}"),
                'rr' => $request->input("rr_{$time}"),
                'bp' => $request->input("bp_{$time}"),
                'spo2' => $request->input("spo2_{$time}"),
            ];

            $hasData = count(array_filter($vitalsForTime, fn($v) => $v !== null && $v !== '')) > 0;

            $queryConditions = [
                'patient_id' => $validatedData['patient_id'],
                'date' => $validatedData['date'],
                'day_no' => $validatedData['day_no'],
                'time' => $dbTime,
            ];

            if ($hasData) {
                $alertResult = $cdssService->analyzeVitalsForAlerts($vitalsForTime);
                $vitalsForTime['alerts'] = $alertResult['alert'];
                $vitalsForTime['news_severity'] = $alertResult['severity'];

                $vitalRecord = Vitals::updateOrCreate(
                    $queryConditions,
                    $vitalsForTime
                );

                if ($vitalRecord->wasRecentlyCreated) {
                    AuditLogController::log(
                        'Vital Signs Record Created',
                        'User ' . Auth::user()->username . " created a new Vital Signs record",
                        ['patient_id' => $validatedData['patient_id']]
                    );
                    $anyCreated = true;
                } elseif ($vitalRecord->wasChanged()) {
                    AuditLogController::log(
                        'Vital Signs Record Updated',
                        'User ' . Auth::user()->username . " updated a Vital Signs record",
                        ['patient_id' => $validatedData['patient_id']]
                    );
                    $anyUpdated = true;
                }
            } else {
                // If no data is submitted for this time slot, delete the existing record
                $deletedCount = Vitals::where($queryConditions)->delete();
                if ($deletedCount > 0) {
                    AuditLogController::log(
                        'Vital Signs Record Deleted',
                        'User ' . Auth::user()->username . " deleted a Vital Signs record for time " . $time,
                        ['patient_id' => $validatedData['patient_id'], 'time' => $time]
                    );
                }
            }
        }

        $message = $anyCreated ? 'Vital Signs data saved successfully.'
            : ($anyUpdated ? 'Vital Signs data updated successfully.' : 'No changes made.');

        return redirect()->route('vital-signs.show', [
            'date' => $validatedData['date'],
            'day_no' => $validatedData['day_no'],
        ])->with('success', $message);
    }




    public function checkVitals(Request $request)
    {
        $time = $request->input('time');
        $vitals = $request->input('vitals'); // This will be an array of vital signs for the time slot

        if (!$time || !is_array($vitals)) {
            return response()->json(['alert' => '', 'severity' => 'NONE']);
        }

        $cdssService = new VitalCdssService();

        // Call analyzeVitalsForAlerts with the complete set of vitals for the time slot
        $result = $cdssService->analyzeVitalsForAlerts($vitals);

        $result['severity'] = strtoupper($result['severity']);

        return response()->json($result);
    }



    //new
    public function runBatchCdssAnalysis(Request $request)
    {
        $data = $request->validate([
            'batch' => 'required|array',
            'batch.*.time' => 'required|string',
            'batch.*.vitals' => 'required|array',
        ]);

        $cdssService = new VitalCdssService();
        $results = [];

        foreach ($data['batch'] as $item) {
            // Re-use your existing group analysis logic from checkVitals
            $result = $cdssService->analyzeVitalsForAlerts($item['vitals']);
            $result['severity'] = strtoupper($result['severity']);
            $results[] = $result;
        }

        return response()->json($results);
    }






    public function runCdssAnalysis(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,patient_id',
            'date' => 'required|date',
            'day_no' => 'required|integer|min:1', // Changed from between:1,30
        ]);

        $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
        $cdssService = new VitalCdssService();
        $allFindings = [];

        foreach ($times as $time) {
            $vitalsForTime = [
                'temperature' => $request->input("temperature_{$time}"),
                'hr' => $request->input("hr_{$time}"),
                'rr' => $request->input("rr_{$time}"),
                'bp' => $request->input("bp_{$time}"),
                'spo2' => $request->input("spo2_{$time}"),
            ];

            // Only run analysis if there's at least one piece of data for the time slot
            if (count(array_filter($vitalsForTime)) > 0) {
                $result = $cdssService->analyzeVitalsForAlerts($vitalsForTime);
                if ($result['severity'] !== VitalCdssService::NONE) {
                    $allFindings[] = "At " . \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') . ": " . $result['alert'];
                }
            }
        }

        // Redirect to the nursing diagnosis page with the findings
        return redirect()->route('nursing-diagnosis.start', [
            'component' => 'vital-signs',
            'id' => $validatedData['patient_id']
        ])->with('findings', $allFindings);
    }

    public function analyzeDiagnosisForNursing(Request $request)
    {
        $vitals = $request->input('vitals', []);

        // Use existing CDSS to create a combined alert string
        $cdssService = new \App\Services\VitalCdssService();
        $result = $cdssService->analyzeVitalsForAlerts($vitals);
        $alertText = trim(strtolower($result['alert'] ?? ''));

        // Load rules YAML
        $rulesPath = storage_path('app/private/adpie/vital-signs/rules/diagnosis.yaml');
        $recommendations = [];

        if (file_exists($rulesPath)) {
            try {
                $yaml = Yaml::parseFile($rulesPath);
                $rules = $yaml['diagnosis'] ?? $yaml;
                foreach ($rules as $rule) {
                    $matched = false;
                    // FIXED SYNTAX ERROR HERE: changed 'is - array' to 'is_array'
                    if (!empty($rule['keywords']) && is_array($rule['keywords'])) {
                        foreach ($rule['keywords'] as $kw) {
                            $kwClean = trim(strtolower($kw));
                            if ($kwClean === '')
                                continue;
                            // match if keyword appears in CDSS alert summary OR appears as a vitals key condition
                            if (strpos($alertText, $kwClean) !== false) {
                                $matched = true;
                                break;
                            }
                            // also allow simple threshold text matches when user typed e.g. "temp > 39"
                            if (preg_match('/([a-z0-9\_]+)\s*>\s*([0-9\.]+)/i', $kwClean, $m)) {
                                $field = $m[1];
                                $threshold = (float) $m[2];
                                $value = isset($vitals[$field]) ? (float) $vitals[$field] : null;
                                if ($value !== null && $value > $threshold) {
                                    $matched = true;
                                    break;
                                }
                            }
                        }
                    }
                    if ($matched) {
                        $recommendations[] = [
                            'alert' => $rule['alert'] ?? '',
                            'severity' => strtoupper($rule['severity'] ?? 'INFO'),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // parsing error - return empty recommendations
                Log::error('Failed to parse vital-signs diagnosis rules: ' . $e->getMessage());
            }
        }

        // Fallback: if no matches but CDSS produced an alert, return it
        if (empty($recommendations) && !empty($alertText)) {
            $recommendations[] = [
                'alert' => $result['alert'] ?? '',
                'severity' => strtoupper($result['severity'] ?? 'NONE'),
            ];
        }

        return response()->json([
            'recommendations' => $recommendations,
            'raw_alert' => $result['alert'] ?? '',
            'severity' => strtoupper($result['severity'] ?? 'NONE'),
        ]);
    }
}