<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Patient;
use App\Models\MedicalHistory\Allergy;
use App\Models\MedicalHistory\DevelopmentalHistory;
use App\Models\MedicalHistory\PastMedicalSurgical;
use App\Models\MedicalHistory\PresentIllness;
use App\Models\MedicalHistory\Vaccination;
use App\Models\PhysicalExam;
use App\Models\Vitals;
use App\Models\IntakeAndOutput;
use App\Models\ActOfDailyLiving;
use App\Models\LabValues;
use App\Models\IvsAndLine;
use App\Models\MedicationAdministration;
use App\Models\MedicalReconciliation;
use App\Models\MedicalReconciliation\ChangesInMedication;
use App\Models\MedicalReconciliation\CurrentMedication;
use App\Models\MedicalReconciliation\HomeMedication;
use App\Models\DischargePlan;
use App\Models\Diagnostic;

class ReportController extends Controller
{
    public function recentForms(Request $request)
    {
        $filterType    = $request->input('type', 'all');
        $filterPatient = $request->input('patient', '');
        $filterDate    = $request->input('date', '');

        $modelMap = [
            'vital-signs'     => [Vitals::class,                 'Vital Signs',                 'monitor_heart',    '#EF4444'],
            'physical-exam'   => [PhysicalExam::class,           'Physical Exam',               'person_search',    '#8B5CF6'],
            'adl'             => [ActOfDailyLiving::class,        'Activities of Daily Living',  'self_improvement', '#F97316'],
            'intake-output'   => [IntakeAndOutput::class,         'Intake & Output',             'water_drop',       '#3B82F6'],
            'lab-values'      => [LabValues::class,               'Lab Values',                  'biotech',          '#0D9488'],
            'medication'      => [MedicationAdministration::class,'Medication Administration',   'medication',       '#10B981'],
            'ivs-lines'       => [IvsAndLine::class,              'IVs & Lines',                 'vaccines',         '#6366F1'],
        ];

        $modelsToQuery = ($filterType !== 'all' && isset($modelMap[$filterType]))
            ? [$filterType => $modelMap[$filterType]]
            : $modelMap;

        // Step 1: Gather raw records (no relationship eager-load)
        $rawFeeds = [];
        foreach ($modelsToQuery as $key => [$modelClass, $label, $icon, $color]) {
            try {
                $query = $modelClass::latest();
                if ($filterDate) {
                    $query->whereDate('updated_at', $filterDate);
                }
                $records = $query->take(30)->get();
                foreach ($records as $record) {
                    $rawFeeds[] = [
                        'type'       => $label,
                        'type_key'   => $key,
                        'icon'       => $icon,
                        'color'      => $color,
                        'patient_id' => $record->patient_id ?? null,
                        'time'       => $record->updated_at,
                        'id'         => $record->getKey(),
                    ];
                }
            } catch (\Exception $e) {
                // Skip model on error
            }
        }

        // Step 2: Batch-load patient names (bypasses relationship issues)
        $patientIds = array_values(array_unique(array_filter(array_column($rawFeeds, 'patient_id'))));
        $patients   = Patient::whereIn('patient_id', $patientIds)->get()->keyBy('patient_id');

        // Step 3: Attach names and apply patient name filter
        $feeds = collect($rawFeeds)
            ->map(function ($item) use ($patients) {
                $item['patient_name'] = $patients->get($item['patient_id'])?->name ?? 'Unknown Patient';
                return $item;
            })
            ->when($filterPatient, fn($c) => $c->filter(
                fn($item) => stripos($item['patient_name'], $filterPatient) !== false
            ))
            ->sortByDesc('time')
            ->values();

        // Manual pagination
        $page     = (int) $request->input('page', 1);
        $perPage  = 20;
        $total    = $feeds->count();
        $items    = $feeds->slice(($page - 1) * $perPage, $perPage)->values();
        $lastPage = (int) ceil($total / $perPage);

        $formTypes = array_combine(array_keys($modelMap), array_column(array_values($modelMap), 1));

        $viewData = compact('items', 'page', 'perPage', 'total', 'lastPage', 'filterType', 'filterPatient', 'filterDate', 'formTypes');

        // AJAX: return only the results partial (no layout)
        if ($request->ajax()) {
            return view('doctor.partials.recent-forms-results', $viewData);
        }

        return view('doctor.recent-forms', $viewData);
    }
    public function showPatientForm(Request $request, $type, $patient_id)
    {
        $patient = Patient::findOrFail($patient_id);

        $typeMap = [
            'vital-signs'   => [Vitals::class,                  'Vital Signs',                'monitor_heart',    '#EF4444', true],
            'physical-exam' => [PhysicalExam::class,            'Physical Exam',              'person_search',    '#8B5CF6', true],
            'adl'           => [ActOfDailyLiving::class,        'Activities of Daily Living', 'self_improvement', '#F97316', true],
            'intake-output' => [IntakeAndOutput::class,         'Intake & Output',            'water_drop',       '#3B82F6', true],
            'lab-values'    => [LabValues::class,               'Lab Values',                 'biotech',          '#0D9488', true],
            'medication'    => [MedicationAdministration::class,'Medication Administration',  'medication',       '#10B981', false],
            'ivs-lines'     => [IvsAndLine::class,              'IVs & Lines',                'vaccines',         '#6366F1', false],
        ];

        abort_unless(isset($typeMap[$type]), 404);

        [$modelClass, $label, $icon, $color, $hasNursingDiagnoses] = $typeMap[$type];

        $query = $modelClass::where('patient_id', $patient_id);
        if ($hasNursingDiagnoses) {
            $query->with('nursingDiagnoses');
        }
        $records = $query->latest()->get();

        // Breadcrumb: where did the user come from?
        $fromMap = [
            'recent-forms'    => ['label' => 'Recent Forms',    'url' => route('doctor.recent-forms')],
            'today-updates'   => ['label' => "Today's Updates", 'url' => route('doctor.stats.today-updates')],
            'total-patients'  => ['label' => 'Total Patients',  'url' => route('doctor.stats.total-patients')],
            'active-patients' => ['label' => 'Active Patients', 'url' => route('doctor.stats.active-patients')],
            'patient-details' => ['label' => 'Patient Details', 'url' => route('doctor.patient-details', ['patient_id' => $patient_id, 'from' => $request->query('prev', 'total-patients')])],
        ];
        $fromKey  = $request->query('from', 'recent-forms');
        $fromCrumb = $fromMap[$fromKey] ?? $fromMap['recent-forms'];

        return view('doctor.patient-form-detail', compact(
            'patient', 'records', 'label', 'icon', 'color', 'type', 'fromCrumb'
        ));
    }

    public function patientDetails(Request $request, $patient_id)
    {
        $patient = Patient::findOrFail($patient_id);

        $fromKey = $request->query('from', 'total-patients');

        $typeLabels = [
            'vital-signs'   => 'Vital Signs',
            'physical-exam' => 'Physical Exam',
            'adl'           => 'Activities of Daily Living',
            'intake-output' => 'Intake & Output',
            'lab-values'    => 'Lab Values',
            'medication'    => 'Medication Administration',
            'ivs-lines'     => 'IVs & Lines',
        ];

        if ($fromKey === 'form-detail') {
            $type      = $request->query('type', '');
            $typeLabel = $typeLabels[$type] ?? 'Form Detail';
            $fromCrumb = [
                'label' => $typeLabel,
                'url'   => route('doctor.form-detail', ['type' => $type, 'patient_id' => $patient_id]),
            ];
        } else {
            $fromMap = [
                'total-patients'  => ['label' => 'Total Patients',  'url' => route('doctor.stats.total-patients')],
                'active-patients' => ['label' => 'Active Patients', 'url' => route('doctor.stats.active-patients')],
            ];
            $fromCrumb = $fromMap[$fromKey] ?? $fromMap['total-patients'];
        }

        return view('doctor.patient-details', compact('patient', 'fromCrumb', 'fromKey'));
    }

    public function totalPatients()
    {
        $patients = Patient::orderBy('last_name')->orderBy('first_name')->get();
        return view('doctor.stats.total-patients', compact('patients'));
    }

    public function activePatients()
    {
        $patients = Patient::where('is_active', true)
            ->orderByDesc('admission_date')
            ->orderBy('last_name')
            ->get();
        return view('doctor.stats.active-patients', compact('patients'));
    }

    public function todayUpdates()
    {
        $modelMap = [
            'vital-signs'   => [Vitals::class,                 'Vital Signs',                'monitor_heart',    '#EF4444'],
            'physical-exam' => [PhysicalExam::class,           'Physical Exam',              'person_search',    '#8B5CF6'],
            'adl'           => [ActOfDailyLiving::class,       'Activities of Daily Living', 'self_improvement', '#F97316'],
            'intake-output' => [IntakeAndOutput::class,        'Intake & Output',            'water_drop',       '#3B82F6'],
            'lab-values'    => [LabValues::class,              'Lab Values',                 'biotech',          '#0D9488'],
            'medication'    => [MedicationAdministration::class,'Medication Administration', 'medication',       '#10B981'],
            'ivs-lines'     => [IvsAndLine::class,             'IVs & Lines',                'vaccines',         '#6366F1'],
        ];

        $rawFeeds = [];
        foreach ($modelMap as $key => [$modelClass, $label, $icon, $color]) {
            try {
                $records = $modelClass::whereDate('updated_at', today())->latest()->get();
                foreach ($records as $record) {
                    $rawFeeds[] = [
                        'type'       => $label,
                        'type_key'   => $key,
                        'icon'       => $icon,
                        'color'      => $color,
                        'patient_id' => $record->patient_id ?? null,
                        'time'       => $record->updated_at,
                    ];
                }
            } catch (\Exception $e) {}
        }

        $patientIds = array_values(array_unique(array_filter(array_column($rawFeeds, 'patient_id'))));
        $patients   = Patient::whereIn('patient_id', $patientIds)->get()->keyBy('patient_id');

        $items = collect($rawFeeds)
            ->map(function ($item) use ($patients) {
                $item['patient_name'] = $patients->get($item['patient_id'])?->name ?? 'Unknown Patient';
                return $item;
            })
            ->sortByDesc('time')
            ->values();

        $total = $items->count();

        return view('doctor.stats.today-updates', compact('items', 'total'));
    }

    public function showPatientReportForm()
    {
        $patients = Patient::orderBy('last_name')->get();
        return view('doctor.patient-report', compact('patients'));
    }
    public function generateReport(Request $request)
    {
        $patient_id = $request->input('patient_id');
        $patient = Patient::findOrFail($patient_id);

        $data = [
            'patient' => $patient,
            'presentIllness' => PresentIllness::where('patient_id', $patient_id)->get(),
            'pastMedicalSurgical' => PastMedicalSurgical::where('patient_id', $patient_id)->get(),
            'allergies' => Allergy::where('patient_id', $patient_id)->get(),
            'vaccination' => Vaccination::where('patient_id', $patient_id)->get(),
            'developmentalHistory' => DevelopmentalHistory::where('patient_id', $patient_id)->first(),
            'diagnostics' => Diagnostic::where('patient_id', $patient_id)->get(),
            'ivsAndLines' => IvsAndLine::where('patient_id', $patient_id)->get(),
            'medicationAdministrations' => MedicationAdministration::where('patient_id', $patient_id)->get(),
            'currentMedication' => CurrentMedication::where('patient_id', $patient_id)->get(),
            'homeMedication' => HomeMedication::where('patient_id', $patient_id)->get(),
            'changesInMedication' => ChangesInMedication::where('patient_id', $patient_id)->get(),
            'dischargePlanning' => DischargePlan::where('patient_id', $patient_id)->get(),
            'physicalExam' => PhysicalExam::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
            'intakeAndOutput' => IntakeAndOutput::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
            'vitals' => Vitals::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
            'actOfDailyLiving' => ActOfDailyLiving::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
            'labValues' => LabValues::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
        ];

        return view('doctor.reports.patient-report-web', $data);
    }

    public function downloadPDF($patient_id)
    {
        $patient = Patient::findOrFail($patient_id);

        $data = [
            'patient' => $patient,
            'presentIllness' => PresentIllness::where('patient_id', $patient_id)->get(),
            'pastMedicalSurgical' => PastMedicalSurgical::where('patient_id', $patient_id)->get(),
            'allergies' => Allergy::where('patient_id', $patient_id)->get(),
            'vaccination' => Vaccination::where('patient_id', $patient_id)->get(),
            'developmentalHistory' => DevelopmentalHistory::where('patient_id', $patient_id)->first(),
            'diagnostics' => Diagnostic::where('patient_id', $patient_id)->get(),
            'ivsAndLines' => IvsAndLine::where('patient_id', $patient_id)->get(),
            'medicationAdministrations' => MedicationAdministration::where('patient_id', $patient_id)->get(),
            'currentMedication' => CurrentMedication::where('patient_id', $patient_id)->get(),
            'homeMedication' => HomeMedication::where('patient_id', $patient_id)->get(),
            'changesInMedication' => ChangesInMedication::where('patient_id', $patient_id)->get(),
            'dischargePlanning' => DischargePlan::where('patient_id', $patient_id)->get(),
            'physicalExam' => PhysicalExam::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
            'intakeAndOutput' => IntakeAndOutput::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
            'vitals' => Vitals::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
            'actOfDailyLiving' => ActOfDailyLiving::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
            'labValues' => LabValues::with('nursingDiagnoses')->where('patient_id', $patient_id)->get(),
        ];

        ini_set('memory_limit', '512M');

        $pdf = Pdf::loadView('doctor.reports.patient-report-pdf', $data);

        $pdf->setPaper('folio', 'portrait');

        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);
        return $pdf->download($patient->name . '_Results.pdf');
    }
}
