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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    //  The `` array in the `HomeController` class is used to define which session
    // data should be cleared every time the nurse goes back to the `nurse-home` page. Each key in the
    // array corresponds to a specific route, and the associated value is an array of session keys that
    // should be cleared when that route is accessed.
    // forget (clear) the session everytime the nurse go back to nurse-home page
    private $NurseSessionKeyMap = [
        //route   => (session data u want to clear)
        'medical-history' => ['selected_patient_id'],
        'physical-exam' => ['selected_patient_id'],
        'vital-signs' => ['selected_patient_id', 'selected_date', 'selected_day_no'],
        //intake and output
        'adl' => ['selected_patient_id', 'selected_date', 'selected_day_no'],
        'lab-values' => ['selected_patient_id'],
        //diagnostics
        'ivs-and-lines' => ['selected_patient_id'],
        //med-administration
        'medication-reconciliation' => ['selected_patient_id'],
        'discharge-planning' => ['selected_patient_id'],
    ];

    //    * The function `handleHomeRedirect` checks the user's role and redirects them to specific home pages
//    * based on their role, logging out the user if the role is not recognized.
//    
//    * The `handleHomeRedirect` function is returning a redirect response based on the user's
//    * role. If the user is authenticated, it checks the user's role and redirects them to a specific
//    * route based on their role (Nurse, Doctor, Admin). If the user's role does not match any of these
//    * cases, it logs the user out and redirects them to the default 'home' route. If
//    */
    public function handleHomeRedirect()
    {
        if (Auth::check()) {
            $user = Auth::user();

            switch ($user->role) {
                case 'Nurse':
                    return redirect()->route('nurse-home');
                case 'Doctor':
                    return redirect()->route('doctor-home');
                case 'Admin':
                    return redirect()->route('admin-home');
                default:
                    Auth::logout();
                    return redirect()->route('login');
            }
        }

        return view('login.login');
        // return view('home');
    }

    public function nurseHome(Request $request)
    {
        $allKeysToClear = array_merge(...array_values($this->NurseSessionKeyMap));
        $request->session()->forget($allKeysToClear);
        return view('nurse-home');
    }

    public function doctorHome()
    {
        $totalPatients  = Patient::count();
        $activePatients = Patient::where('is_active', true)->count();

        $assessmentModels = [
            Vitals::class, PhysicalExam::class, ActOfDailyLiving::class,
            IntakeAndOutput::class, LabValues::class,
            MedicationAdministration::class, IvsAndLine::class,
        ];
        $todayForms = collect($assessmentModels)
            ->sum(fn($m) => $m::whereDate('updated_at', today())->count());

        $recentForms = $this->getRecentFormSubmissions(6);

        return view('doctor.home', compact(
            'totalPatients',
            'activePatients',
            'todayForms',
            'recentForms'
        ));
    }

    private function getRecentFormSubmissions(int $perModel = 5): \Illuminate\Support\Collection
    {
        $modelMap = [
            [Vitals::class,                  'Vital Signs',                 'monitor_heart',    '#EF4444', 'vital-signs'],
            [PhysicalExam::class,            'Physical Exam',               'person_search',    '#8B5CF6', 'physical-exam'],
            [ActOfDailyLiving::class,        'Activities of Daily Living',  'self_improvement', '#F97316', 'adl'],
            [IntakeAndOutput::class,         'Intake & Output',             'water_drop',       '#3B82F6', 'intake-output'],
            [LabValues::class,               'Lab Values',                  'biotech',          '#0D9488', 'lab-values'],
            [MedicationAdministration::class,'Medication Administration',   'medication',       '#10B981', 'medication'],
            [IvsAndLine::class,              'IVs & Lines',                 'vaccines',         '#6366F1', 'ivs-lines'],
        ];

        // Step 1: Gather raw records (no relationship eager-load)
        $raw = [];
        foreach ($modelMap as [$modelClass, $label, $icon, $color, $slug]) {
            try {
                $records = $modelClass::latest()->take($perModel)->get();
                foreach ($records as $record) {
                    $raw[] = [
                        'type'       => $label,
                        'slug'       => $slug,
                        'icon'       => $icon,
                        'color'      => $color,
                        'patient_id' => $record->patient_id,
                        'time'       => $record->updated_at,
                    ];
                }
            } catch (\Exception $e) {
                // Skip unavailable models gracefully
            }
        }

        // Step 2: Batch-load patient names in ONE query (bypasses relationship issues)
        $patientIds = array_values(array_unique(array_filter(array_column($raw, 'patient_id'))));
        $patients   = Patient::whereIn('patient_id', $patientIds)
                              ->get()
                              ->keyBy('patient_id');

        // Step 3: Attach names and sort
        $feeds = collect($raw)->map(function ($item) use ($patients) {
            $item['patient_name'] = $patients->get($item['patient_id'])?->name ?? 'Unknown Patient';
            return $item;
        });

        return $feeds->sortByDesc('time')->take(20)->values();
    }

    public function adminHome()
    {
        $users = User::all();
        return view('admin.home', compact('users'));
    }


    //    * The function clears specific session keys based on a given form name and then redirects to the nurse home route.
//    * @param Request request The `` parameter in the `clearSessionAndRedirect` function is an
//    * instance of the `Illuminate\Http\Request` class. It represents the current HTTP request and
//    * contains information about the request such as input data, headers, and more.
//    * @param formName The `formName` parameter in the `clearSessionAndRedirect` function is used to
//    * determine which session keys to clear based on the `` array. It is assumed that
//    * `formName` corresponds to a key in the `` array, and the associated value is
//    * 
//    * @return The `clearSessionAndRedirect` function is returning a redirect response to the
//    * 'nurse-home' route after clearing specific keys from the session based on the provided

    public function clearSessionAndRedirect(Request $request, $formName)
    {
        $keysToClear = $this->sessionKeyMap[$formName] ?? [];
        if (!empty($keysToClear)) {
            $request->session()->forget($keysToClear);
        }
        return redirect()->route('nurse-home');
    }
}
