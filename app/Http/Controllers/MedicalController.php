<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalModel;
use App\Models\DevelopmentalHistory;

class MedicalHistoryController extends Controller
{
    public function storePresentIllness(Request $request)
    {
        MedicalModel::create([
            'type' => 'present_illness',
            'condition_name' => $request->condition_name,
            'description' => $request->description,
            'medication' => $request->medication,
            'dosage' => $request->dosage,
            'side_effect' => $request->side_effect,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Present illness saved!');
    }

    public function storePastMedicalSurgical(Request $request)
    {
        MedicalModel::create([
            'type' => 'past_medical_surgical',
            'condition_name' => $request->condition_name,
            'description' => $request->description,
            'medication' => $request->medication,
            'dosage' => $request->dosage,
            'side_effect' => $request->side_effect,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Past Medical / Surgical saved!');
    }

    public function storeAllergies(Request $request)
    {
        MedicalModel::create([
            'type' => 'allergies',
            'condition_name' => $request->condition_name,
            'description' => $request->description,
            'medication' => $request->medication,
            'dosage' => $request->dosage,
            'side_effect' => $request->side_effect,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Allergies saved!');
    }

    public function storeVaccination(Request $request)
    {
        MedicalModel::create([
            'type' => 'vaccination',
            'condition_name' => $request->condition_name,
            'description' => $request->description,
            'medication' => $request->medication,
            'dosage' => $request->dosage,
            'side_effect' => $request->side_effect,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Vaccination saved!');
    }
}



?>