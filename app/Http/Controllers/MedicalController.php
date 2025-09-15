<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalHistory;
use App\Models\DevelopmentalHistory;

class MedicalHistoryController extends Controller
{
    public function store(Request $request)
    {
        $medical = new MedicalHistory();
        $medical->patient_id = 1; 
        $medical->condition_name = $request->condition_name;
        $medical->condition_description = $request->condition_description;
        $medical->medication_name = $request->medication_name;
        $medical->medication_dosage = $request->medication_dosage;
        $medical->side_effects = $request->side_effects;
        $medical->medication_comments = $request->medication_comments;
        $medical->save();

        $dev = new DevelopmentalHistory();
        $dev->patient_id = 1;
        $dev->gross_motor = $request->gross_motor;
        $dev->fine_motor = $request->fine_motor;
        $dev->language = $request->language;
        $dev->cognitive = $request->cognitive;
        $dev->social = $request->social;
        $dev->save();

        return back()->with('success', 'Medical history saved successfully!');
    }
}


?>