<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Patient;
use App\Models\Vitals;
use App\Models\PhysicalExam;
use App\Models\ActOfDailyLiving;
use App\Models\IntakeAndOutput;
use App\Models\LabValues;
use App\Models\IvsAndLine;
use App\Models\MedicationAdministration;
use App\Models\NursingDiagnosis;
use App\Models\FormRead;
use Illuminate\Support\Facades\Auth;

class DoctorApiController extends Controller
{
    private const MODEL_MAP = [
        'vital-signs'   => [Vitals::class,                  'Vital Signs'],
        'physical-exam' => [PhysicalExam::class,            'Physical Exam'],
        'adl'           => [ActOfDailyLiving::class,        'Activities of Daily Living'],
        'intake-output' => [IntakeAndOutput::class,         'Intake & Output'],
        'lab-values'    => [LabValues::class,               'Lab Values'],
        'medication'    => [MedicationAdministration::class, 'Medication Administration'],
        'ivs-lines'     => [IvsAndLine::class,              'IVs & Lines'],
    ];

    // ─────────────────────────────────────────────
    // Dashboard Stats
    // GET /api/doctor/stats
    // ─────────────────────────────────────────────
    public function stats()
    {
        $total   = Patient::count();
        $active  = Patient::where('is_active', true)->count();
        $today   = $this->getTodayUpdatesCount();
        $unread  = $this->getUnreadCount();

        return response()->json([
            'total_patients'  => $total,
            'active_patients' => $active,
            'today_updates'   => $today,
            'unread_count'    => $unread,
        ]);
    }

    private function getTodayUpdatesCount(): int
    {
        $count = 0;
        foreach (array_column(self::MODEL_MAP, 0) as $model) {
            try { $count += $model::whereDate('updated_at', today())->count(); } catch (\Exception $e) {}
        }
        return $count;
    }

    private function getUnreadCount(): int
    {
        $raw = [];
        foreach (self::MODEL_MAP as [$modelClass]) {
            try {
                foreach ($modelClass::latest()->take(6)->get() as $r) {
                    $raw[] = ['model_class' => $modelClass, 'record_id' => $r->getKey()];
                }
            } catch (\Exception $e) {}
        }
        if (empty($raw)) return 0;

        $readMap = FormRead::where('user_id', Auth::id())->get()
            ->mapWithKeys(fn($r) => [$r->model_type . ':' . $r->model_id => true]);

        return collect($raw)
            ->filter(fn($item) => !$readMap->has($item['model_class'] . ':' . $item['record_id']))
            ->count();
    }

    // ─────────────────────────────────────────────
    // Recent Forms Feed
    // GET /api/doctor/recent-forms
    //   ?type=all&read=all&patient=&date=&page=1&per_page=20
    // ─────────────────────────────────────────────
    public function recentForms(Request $request)
    {
        $filterType    = $request->query('type', 'all');
        $filterRead    = $request->query('read', 'all');   // all | unread | read
        $filterPatient = $request->query('patient', '');
        $filterDate    = $request->query('date', '');
        $page          = max(1, (int) $request->query('page', 1));
        $perPage       = min(50, max(1, (int) $request->query('per_page', 20)));

        $modelsToQuery = ($filterType !== 'all' && isset(self::MODEL_MAP[$filterType]))
            ? [$filterType => self::MODEL_MAP[$filterType]]
            : self::MODEL_MAP;

        $rawFeeds = [];
        foreach ($modelsToQuery as $key => [$modelClass, $label]) {
            try {
                $query = $modelClass::latest();
                if ($filterDate) $query->whereDate('updated_at', $filterDate);
                foreach ($query->take(50)->get() as $record) {
                    $rawFeeds[] = [
                        'type'        => $label,
                        'type_key'    => $key,
                        'patient_id'  => $record->patient_id ?? null,
                        'time'        => $record->updated_at,
                        'record_id'   => $record->getKey(),
                        'model_class' => $modelClass,
                    ];
                }
            } catch (\Exception $e) {}
        }

        // Batch-load patients and read status
        $patientIds = array_values(array_unique(array_filter(array_column($rawFeeds, 'patient_id'))));
        $patients   = Patient::whereIn('patient_id', $patientIds)->get()->keyBy('patient_id');

        $readMap = !empty($rawFeeds)
            ? FormRead::where('user_id', Auth::id())->get()
                ->mapWithKeys(fn($r) => [$r->model_type . ':' . $r->model_id => true])
            : collect();

        $feeds = collect($rawFeeds)
            ->map(function ($item) use ($patients, $readMap) {
                $item['patient_name'] = $patients->get($item['patient_id'])?->name ?? 'Unknown Patient';
                $item['is_read']      = $readMap->has($item['model_class'] . ':' . $item['record_id']);
                $item['is_today']     = \Carbon\Carbon::parse($item['time'])->isToday();
                $item['time']         = (string) $item['time'];
                unset($item['model_class']); // internal only
                return $item;
            })
            ->when($filterPatient, fn($c) => $c->filter(
                fn($item) => stripos($item['patient_name'], $filterPatient) !== false
            ))
            ->when($filterRead === 'unread', fn($c) => $c->filter(fn($item) => !$item['is_read']))
            ->when($filterRead === 'read',   fn($c) => $c->filter(fn($item) =>  $item['is_read']))
            ->sortByDesc('time')
            ->values();

        $total    = $feeds->count();
        $items    = $feeds->slice(($page - 1) * $perPage, $perPage)->values();
        $lastPage = max(1, (int) ceil($total / $perPage));

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
        $rawFeeds = [];
        foreach (self::MODEL_MAP as $key => [$modelClass, $label]) {
            try {
                foreach ($modelClass::whereDate('updated_at', today())->latest()->get() as $record) {
                    $rawFeeds[] = [
                        'type'        => $label,
                        'type_key'    => $key,
                        'patient_id'  => $record->patient_id ?? null,
                        'time'        => (string) $record->updated_at,
                        'record_id'   => $record->getKey(),
                        'model_class' => $modelClass,
                    ];
                }
            } catch (\Exception $e) {}
        }

        $patientIds = array_values(array_unique(array_filter(array_column($rawFeeds, 'patient_id'))));
        $patients   = Patient::whereIn('patient_id', $patientIds)->get()->keyBy('patient_id');

        $readMap = !empty($rawFeeds)
            ? FormRead::where('user_id', Auth::id())->get()
                ->mapWithKeys(fn($r) => [$r->model_type . ':' . $r->model_id => true])
            : collect();

        $items = collect($rawFeeds)
            ->map(function ($item) use ($patients, $readMap) {
                $item['patient_name'] = $patients->get($item['patient_id'])?->name ?? 'Unknown Patient';
                $item['is_read']      = $readMap->has($item['model_class'] . ':' . $item['record_id']);
                unset($item['model_class']);
                return $item;
            })
            ->sortByDesc('time')
            ->values();

        return response()->json(['data' => $items, 'total' => $items->count()]);
    }

    // ─────────────────────────────────────────────
    // Mark Form as Read
    // POST /api/doctor/mark-read
    // Body: { "model_type": "App\\Models\\Vitals", "model_id": 42 }
    // ─────────────────────────────────────────────
    public function markFormRead(Request $request)
    {
        $allowed = array_column(self::MODEL_MAP, 0);

        $validated = $request->validate([
            'model_type' => ['required', 'string', Rule::in($allowed)],
            'model_id'   => 'required|integer|min:1',
        ]);

        FormRead::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'model_type' => $validated['model_type'],
                'model_id'   => $validated['model_id'],
            ],
            ['read_at' => now()]
        );

        return response()->json(['success' => true, 'message' => 'Marked as read.']);
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

        $typeMap = array_map(fn($v) => $v[0], self::MODEL_MAP);

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
