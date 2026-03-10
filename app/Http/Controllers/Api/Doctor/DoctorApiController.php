<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Vitals;
use App\Models\PhysicalExam;
use App\Models\ActOfDailyLiving;
use App\Models\IntakeAndOutput;
use App\Models\LabValues;
use App\Models\IvsAndLine;
use App\Models\MedicationAdministration;
use App\Models\NursingDiagnosis;

class DoctorApiController extends Controller
{
    // ─────────────────────────────────────────────
    // Dashboard Stats
    // GET /api/doctor/stats
    // ─────────────────────────────────────────────
    public function stats()
    {
        $total   = Patient::count();
        $active  = Patient::where('is_active', true)->count();
        $today   = $this->getTodayUpdatesCount();

        return response()->json([
            'total_patients'   => $total,
            'active_patients'  => $active,
            'today_updates'    => $today,
        ]);
    }

    private function getTodayUpdatesCount(): int
    {
        $models = [
            Vitals::class, PhysicalExam::class, ActOfDailyLiving::class,
            IntakeAndOutput::class, LabValues::class,
            IvsAndLine::class, MedicationAdministration::class,
        ];
        $count = 0;
        foreach ($models as $model) {
            try { $count += $model::whereDate('updated_at', today())->count(); } catch (\Exception $e) {}
        }
        return $count;
    }

    // ─────────────────────────────────────────────
    // Recent Forms Feed
    // GET /api/doctor/recent-forms?type=all&patient=&date=&page=1&per_page=20
    // ─────────────────────────────────────────────
    public function recentForms(Request $request)
    {
        $filterType    = $request->query('type', 'all');
        $filterPatient = $request->query('patient', '');
        $filterDate    = $request->query('date', '');
        $page          = max(1, (int) $request->query('page', 1));
        $perPage       = min(50, max(1, (int) $request->query('per_page', 20)));

        $modelMap = [
            'vital-signs'   => [Vitals::class,                 'Vital Signs'],
            'physical-exam' => [PhysicalExam::class,           'Physical Exam'],
            'adl'           => [ActOfDailyLiving::class,       'Activities of Daily Living'],
            'intake-output' => [IntakeAndOutput::class,        'Intake & Output'],
            'lab-values'    => [LabValues::class,              'Lab Values'],
            'medication'    => [MedicationAdministration::class,'Medication Administration'],
            'ivs-lines'     => [IvsAndLine::class,             'IVs & Lines'],
        ];

        $modelsToQuery = ($filterType !== 'all' && isset($modelMap[$filterType]))
            ? [$filterType => $modelMap[$filterType]]
            : $modelMap;

        $rawFeeds = [];
        foreach ($modelsToQuery as $key => [$modelClass, $label]) {
            try {
                $query = $modelClass::latest();
                if ($filterDate) $query->whereDate('updated_at', $filterDate);
                foreach ($query->take(50)->get() as $record) {
                    $rawFeeds[] = [
                        'type'       => $label,
                        'type_key'   => $key,
                        'patient_id' => $record->patient_id ?? null,
                        'time'       => $record->updated_at,
                        'record_id'  => $record->getKey(),
                    ];
                }
            } catch (\Exception $e) {}
        }

        $patientIds = array_values(array_unique(array_filter(array_column($rawFeeds, 'patient_id'))));
        $patients   = Patient::whereIn('patient_id', $patientIds)->get()->keyBy('patient_id');

        $feeds = collect($rawFeeds)
            ->map(fn($item) => array_merge($item, [
                'patient_name' => $patients->get($item['patient_id'])?->name ?? 'Unknown Patient',
                'time'         => (string) $item['time'],
            ]))
            ->when($filterPatient, fn($c) => $c->filter(
                fn($item) => stripos($item['patient_name'], $filterPatient) !== false
            ))
            ->sortByDesc('time')
            ->values();

        $total    = $feeds->count();
        $items    = $feeds->slice(($page - 1) * $perPage, $perPage)->values();
        $lastPage = (int) ceil($total / $perPage);

        return response()->json([
            'data'      => $items,
            'total'     => $total,
            'page'      => $page,
            'per_page'  => $perPage,
            'last_page' => $lastPage,
        ]);
    }

    // ─────────────────────────────────────────────
    // Today's Updates Feed
    // GET /api/doctor/today-updates
    // ─────────────────────────────────────────────
    public function todayUpdates()
    {
        $modelMap = [
            'vital-signs'   => [Vitals::class,                 'Vital Signs'],
            'physical-exam' => [PhysicalExam::class,           'Physical Exam'],
            'adl'           => [ActOfDailyLiving::class,       'Activities of Daily Living'],
            'intake-output' => [IntakeAndOutput::class,        'Intake & Output'],
            'lab-values'    => [LabValues::class,              'Lab Values'],
            'medication'    => [MedicationAdministration::class,'Medication Administration'],
            'ivs-lines'     => [IvsAndLine::class,             'IVs & Lines'],
        ];

        $rawFeeds = [];
        foreach ($modelMap as $key => [$modelClass, $label]) {
            try {
                foreach ($modelClass::whereDate('updated_at', today())->latest()->get() as $record) {
                    $rawFeeds[] = [
                        'type'       => $label,
                        'type_key'   => $key,
                        'patient_id' => $record->patient_id ?? null,
                        'time'       => (string) $record->updated_at,
                        'record_id'  => $record->getKey(),
                    ];
                }
            } catch (\Exception $e) {}
        }

        $patientIds = array_values(array_unique(array_filter(array_column($rawFeeds, 'patient_id'))));
        $patients   = Patient::whereIn('patient_id', $patientIds)->get()->keyBy('patient_id');

        $items = collect($rawFeeds)
            ->map(fn($item) => array_merge($item, [
                'patient_name' => $patients->get($item['patient_id'])?->name ?? 'Unknown Patient',
            ]))
            ->sortByDesc('time')
            ->values();

        return response()->json(['data' => $items, 'total' => $items->count()]);
    }

    // ─────────────────────────────────────────────
    // All Patients (Total Patients)
    // GET /api/doctor/patients?search=
    // ─────────────────────────────────────────────
    public function allPatients(Request $request)
    {
        $query = Patient::orderBy('last_name')->orderBy('first_name');

        if ($request->query('search')) {
            $s = $request->query('search');
            $query->where(fn($q) => $q
                ->where('first_name', 'like', "%$s%")
                ->orWhere('last_name', 'like', "%$s%")
            );
        }

        return response()->json($query->get()->map(fn($p) => $this->transformPatient($p)));
    }

    // ─────────────────────────────────────────────
    // Active Patients
    // GET /api/doctor/patients/active
    // ─────────────────────────────────────────────
    public function activePatients()
    {
        $patients = Patient::where('is_active', true)
            ->orderByDesc('admission_date')
            ->orderBy('last_name')
            ->get()
            ->map(fn($p) => array_merge($this->transformPatient($p), [
                'days_admitted' => $p->admission_date
                    ? (int) $p->admission_date->diffInDays(now())
                    : null,
            ]));

        return response()->json($patients);
    }

    // ─────────────────────────────────────────────
    // Patient Details
    // GET /api/doctor/patient/{id}
    // ─────────────────────────────────────────────
    public function patientDetails($id)
    {
        $patient = Patient::where('patient_id', $id)->firstOrFail();
        return response()->json($this->transformPatient($patient));
    }

    // ─────────────────────────────────────────────
    // Patient Form Records by Type
    // GET /api/doctor/patient/{id}/forms/{type}
    // type: vital-signs | physical-exam | adl | intake-output | lab-values | medication | ivs-lines
    // ─────────────────────────────────────────────
    public function patientForms(Request $request, $patient_id, $type)
    {
        Patient::where('patient_id', $patient_id)->firstOrFail();

        $typeMap = [
            'vital-signs'   => Vitals::class,
            'physical-exam' => PhysicalExam::class,
            'adl'           => ActOfDailyLiving::class,
            'intake-output' => IntakeAndOutput::class,
            'lab-values'    => LabValues::class,
            'medication'    => MedicationAdministration::class,
            'ivs-lines'     => IvsAndLine::class,
        ];

        abort_unless(isset($typeMap[$type]), 422, 'Invalid form type. Valid: ' . implode(', ', array_keys($typeMap)));

        $modelClass = $typeMap[$type];
        $hasNd = in_array($type, ['vital-signs', 'physical-exam', 'adl', 'intake-output', 'lab-values']);

        $query = $modelClass::where('patient_id', $patient_id)->latest();
        if ($hasNd) $query->with('nursingDiagnoses');

        return response()->json($query->get());
    }

    // ─────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────
    private function transformPatient($patient): array
    {
        $data = $patient->toArray();
        $data['id']   = $patient->patient_id;
        $data['name'] = $patient->name;
        $data['days_admitted'] = $patient->admission_date
            ? (int) $patient->admission_date->diffInDays(now())
            : null;
        return $data;
    }
}
