<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    // /* The `` array in the `HomeController` class is used to define which session
    // data should be cleared every time the nurse goes back to the `nurse-home` page. Each key in the
    // array corresponds to a specific route, and the associated value is an array of session keys that
    // should be cleared when that route is accessed. */
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

    //   /**
//    * The function `handleHomeRedirect` checks the user's role and redirects them to specific home pages
//    * based on their role, logging out the user if the role is not recognized.
//    * 
//    * @return The `handleHomeRedirect` function is returning a redirect response based on the user's
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
        return view('doctor.home');
    }

    public function adminHome()
    {
        $users = User::all();
        return view('admin.home', compact('users'));
    }

    //   /**
//    * The function clears specific session keys based on a given form name and then redirects to the
//    * nurse home route.
//    * 
//    * @param Request request The `` parameter in the `clearSessionAndRedirect` function is an
//    * instance of the `Illuminate\Http\Request` class. It represents the current HTTP request and
//    * contains information about the request such as input data, headers, and more.
//    * @param formName The `formName` parameter in the `clearSessionAndRedirect` function is used to
//    * determine which session keys to clear based on the `` array. It is assumed that
//    * `formName` corresponds to a key in the `` array, and the associated value is
//    * 
//    * @return The `clearSessionAndRedirect` function is returning a redirect response to the
//    * 'nurse-home' route after clearing specific keys from the session based on the provided
//    * ``.
//    */
    public function clearSessionAndRedirect(Request $request, $formName)
    {
        $keysToClear = $this->sessionKeyMap[$formName] ?? [];
        if (!empty($keysToClear)) {
            $request->session()->forget($keysToClear);
        }
        return redirect()->route('nurse-home');
    }
}
