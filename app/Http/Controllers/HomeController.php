<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Vitals;
use App\Models\PhysicalExam;
use App\Models\ActOfDailyLiving;
use App\Models\IntakeAndOutput;
use App\Models\LabValues;
use App\Models\MedicationAdministration;
use App\Models\IvsAndLine;
use App\Models\AuditLog;
use App\Models\FormRead;
use App\Models\MedicalHistory\PresentIllness;
use App\Models\Diagnostic;
use App\Models\MedicalReconciliation\CurrentMedication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    private $NurseSessionKeyMap = [
        'medical-history' => ['selected_patient_id'],
        'physical-exam' => ['selected_patient_id'],
        'vital-signs' => ['selected_patient_id', 'selected_date', 'selected_day_no'],
        'adl' => ['selected_patient_id', 'selected_date', 'selected_day_no'],
        'lab-values' => ['selected_patient_id'],
        'ivs-and-lines' => ['selected_patient_id'],
        'medication-reconciliation' => ['selected_patient_id'],
        'discharge-planning' => ['selected_patient_id'],
    ];

    public function handleHomeRedirect()
    {
        if (Auth::check()) {
            $user = Auth::user();

            switch (strtolower((string) $user->role)) {
                case 'nurse':
                    return redirect()->route('nurse-home');
                case 'doctor':
                    return redirect()->route('doctor-home');
                case 'admin':
                    return redirect()->route('admin-home');
                default:
                    Auth::logout();
                    return redirect()->route('login');
            }
        }
        return view('landing-page');
    }

    public function nurseHome(Request $request)
    {
        $allKeysToClear = array_merge(...array_values($this->NurseSessionKeyMap));
        $request->session()->forget($allKeysToClear);
        return view('nurse-home');
    }

    public function doctorHome()
    {
        $totalPatients = Patient::count();
        $activePatients = Patient::where('is_active', true)->count();

        $assessmentModels = [
            Vitals::class,
            PhysicalExam::class,
            ActOfDailyLiving::class,
            IntakeAndOutput::class,
            LabValues::class,
            MedicationAdministration::class,
            IvsAndLine::class,
            PresentIllness::class,
            Diagnostic::class,
            CurrentMedication::class,
            \App\Models\MedicalHistory\PastMedicalSurgical::class,
            \App\Models\MedicalHistory\Allergy::class,
            \App\Models\MedicalHistory\Vaccination::class,
            \App\Models\DiagnosticImage::class,
            \App\Models\DischargePlan::class,
        ];
        $todayForms = collect($assessmentModels)
            ->sum(fn($m) => $m::whereDate('updated_at', today())->count());

        $recentForms = $this->getRecentFormSubmissions(15);
        $unreadCount = $recentForms->where('is_read', false)->count();

        return view('doctor.home', compact(
            'totalPatients',
            'activePatients',
            'todayForms',
            'recentForms',
            'unreadCount'
        ));
    }

    public function markFormRead(Request $request)
    {
        $allowed = [
            'App\\Models\\Vitals',
            'App\\Models\\PhysicalExam',
            'App\\Models\\ActOfDailyLiving',
            'App\\Models\\IntakeAndOutput',
            'App\\Models\\LabValues',
            'App\\Models\\MedicationAdministration',
            'App\\Models\\IvsAndLine',
            'App\\Models\\MedicalHistory\\PresentIllness',
            'App\\Models\\Diagnostic',
            'App\\Models\\MedicalReconciliation\\CurrentMedication',
            'App\\Models\\MedicalHistory\\PastMedicalSurgical',
            'App\\Models\\MedicalHistory\\Allergy',
            'App\\Models\\MedicalHistory\\Vaccination',
            'App\\Models\\DiagnosticImage',
            'App\\Models\\DischargePlan',
        ];

        $validated = $request->validate([
            'model_type' => ['required', 'string', \Illuminate\Validation\Rule::in($allowed)],
            'model_id' => 'required|integer|min:1',
        ]);

        FormRead::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'model_type' => $validated['model_type'],
                'model_id' => $validated['model_id'],
            ],
            ['read_at' => now()]
        );

        return response()->json(['success' => true]);
    }

    private function getRecentFormSubmissions(int $perModel = 5): \Illuminate\Support\Collection
    {
        $modelMap = [
            [Vitals::class, 'Vital Signs', 'monitor_heart', '#EF4444', 'vital-signs'],
            [PhysicalExam::class, 'Physical Exam', 'person_search', '#8B5CF6', 'physical-exam'],
            [ActOfDailyLiving::class, 'Activities of Daily Living', 'self_improvement', '#F97316', 'adl'],
            [IntakeAndOutput::class, 'Intake & Output', 'water_drop', '#3B82F6', 'intake-output'],
            [LabValues::class, 'Lab Values', 'biotech', '#0D9488', 'lab-values'],
            [MedicationAdministration::class, 'Medication Administration', 'medication', '#10B981', 'medication'],
            [IvsAndLine::class, 'IVs & Lines', 'vaccines', '#6366F1', 'ivs-lines'],
            [PresentIllness::class, 'Medical History', 'history_edu', '#6366F1', 'medical-history'],
            [Diagnostic::class, 'Diagnostics', 'biotech', '#0D9488', 'diagnostics'],
            [CurrentMedication::class, 'Medication Reconciliation', 'rebase_edit', '#10B981', 'med-reconciliation'],
            [\App\Models\MedicalHistory\PastMedicalSurgical::class, 'Past Medical History', 'history', '#64748b', 'medical-history'],
            [\App\Models\MedicalHistory\Allergy::class, 'Allergy Record', 'warning', '#f43f5e', 'medical-history'],
            [\App\Models\MedicalHistory\Vaccination::class, 'Vaccination', 'vaccines', '#06b6d4', 'medical-history'],
            [\App\Models\DiagnosticImage::class, 'Diagnostic Image', 'image', '#8b5cf6', 'diagnostics'],
            [\App\Models\DischargePlan::class, 'Discharge Planning', 'exit_to_app', '#f59e0b', 'discharge-plan'],
        ];

        $raw = [];
        foreach ($modelMap as [$modelClass, $label, $icon, $color, $slug]) {
            try {
                $records = $modelClass::latest()->take($perModel)->get();
                foreach ($records as $record) {
                    $raw[] = [
                        'type' => $label,
                        'slug' => $slug,
                        'icon' => $icon,
                        'color' => $color,
                        'patient_id' => $record->patient_id,
                        'time' => $record->updated_at,
                        'record_id' => $record->id,
                        'model_class' => $modelClass,
                    ];
                }
            } catch (\Exception $e) {
            }
        }

        $patientIds = array_values(array_unique(array_filter(array_column($raw, 'patient_id'))));
        $patients = Patient::whereIn('patient_id', $patientIds)->get()->keyBy('patient_id');

        $userId = Auth::id();
        $readMap = collect();
        if (!empty($raw)) {
            $readMap = FormRead::where('user_id', $userId)
                ->get()
                ->mapWithKeys(fn($r) => [$r->model_type . ':' . $r->model_id => true]);
        }

        $feeds = collect($raw)->map(function ($item) use ($patients, $readMap) {
            $item['patient_name'] = $patients->get($item['patient_id'])?->name ?? 'Unknown Patient';
            $item['is_read'] = $readMap->has($item['model_class'] . ':' . $item['record_id']);
            $item['is_today'] = \Carbon\Carbon::parse($item['time'])->isToday();
            return $item;
        });

        return $feeds->sortByDesc('time')->take(50)->values();
    }

    public function adminHome()
    {
        $users = User::all();
        return view('admin.home', compact('users'));
    }

    public function adminData()
    {
        try {
            $stats = [
                'total_users' => User::whereRaw('LOWER(role) != ?', ['admin'])->count(),
                'total_doctors' => User::whereRaw('LOWER(role) = ?', ['doctor'])->count(),
                'total_nurses' => User::whereRaw('LOWER(role) = ?', ['nurse'])->count(),
            ];

            $logs = AuditLog::with('user')->latest()->take(5)->get()->map(function ($log) {
                $details = is_string($log->details) ? json_decode($log->details, true) : $log->details;
                $sentence = is_array($details) ? ($details['details'] ?? 'Activity recorded.') : 'Activity recorded.';

                return [
                    'id' => $log->id,
                    'username' => $log->user->username ?? $log->user_name ?? 'System',
                    'action' => $log->action,
                    'sentence' => $sentence,
                    'created_at' => $log->created_at->format('M d, Y h:i A'),
                ];
            });

            return response()->json([
                'stats' => $stats,
                'logs' => $logs,
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Dashboard Polling Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function clearSessionAndRedirect(Request $request, $formName)
    {
        $keysToClear = $this->sessionKeyMap[$formName] ?? [];
        if (!empty($keysToClear)) {
            $request->session()->forget($keysToClear);
        }
        return redirect()->route('nurse-home');
    }
}
