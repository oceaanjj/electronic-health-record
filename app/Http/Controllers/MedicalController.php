<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PresentIllness;
use App\Models\PastMedicalSurgical;
use App\Models\Allergy;
use App\Models\Vaccination;
use App\Models\DevelopmentalHistory;
use App\Models\Patient;
use App\Http\Controllers\AuditLogController;
use Throwable;

class MedicalController extends Controller
{

    public function show()
    {
        $patients = Patient::all();
        return view('medical-history', compact('patients'));
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            // Present Illness
            if ($request->has('present_condition_name')) {
                PresentIllness::create([
                    'patient_id' => $request->patient_id,
                    'condition_name' => $request->present_condition_name,
                    'description' => $request->present_description,
                    'medication' => $request->present_medication,
                    'dosage' => $request->present_dosage,
                    'side_effect' => $request->present_side_effect,
                    'comment' => $request->present_comment,
                ]);

                // AuditLogController::log(
                //     'Present Illness Added',
                //     "User {$user->username} added a new present illness record.",
                //     ['patient_id' => $request->patient_id]
                // );
            }

            // Past Medical / Surgical
            if ($request->has('past_condition_name')) {
                PastMedicalSurgical::create([
                    'patient_id' => $request->patient_id,
                    'condition_name' => $request->past_condition_name,
                    'description' => $request->past_description,
                    'medication' => $request->past_medication,
                    'dosage' => $request->past_dosage,
                    'side_effect' => $request->past_side_effect,
                    'comment' => $request->past_comment,
                ]);

                // AuditLogController::log(
                //     'Past Medical/Surgical Added',
                //     "User {$user->username} added a new past medical/surgical record.",
                //     ['patient_id' => $request->patient_id]
                // );
            }

            // Allergies
            if ($request->has('allergy_condition_name')) {
                Allergy::create([
                    'patient_id' => $request->patient_id,
                    'condition_name' => $request->allergy_condition_name,
                    'description' => $request->allergy_description,
                    'medication' => $request->allergy_medication,
                    'dosage' => $request->allergy_dosage,
                    'side_effect' => $request->allergy_side_effect,
                    'comment' => $request->allergy_comment,
                ]);

                // AuditLogController::log(
                //     'Allergy Added',
                //     "User {$user->username} added a new allergy record.",
                //     ['patient_id' => $request->patient_id]
                // );
            }

            // Vaccination
            if ($request->has('vaccine_name')) {
                Vaccination::create([
                    'patient_id' => $request->patient_id,
                    'condition_name' => $request->vaccine_name,
                    'description' => $request->vaccine_description,
                    'medication' => $request->vaccine_medication,
                    'dosage' => $request->vaccine_dosage,
                    'side_effect' => $request->vaccine_side_effect,
                    'comment' => $request->vaccine_comment,
                ]);

                // AuditLogController::log(
                //     'Vaccination Added',
                //     "User {$user->username} added a new vaccination record.",
                //     ['patient_id' => $request->patient_id]
                // );
            }

            // Developmental History
            if ($request->has('gross_motor')) {
                DevelopmentalHistory::create([
                    'patient_id' => $request->patient_id,
                    'gross_motor' => $request->gross_motor,
                    'fine_motor' => $request->fine_motor,
                    'language' => $request->language,
                    'cognitive' => $request->cognitive,
                    'social' => $request->social,
                ]);

                // AuditLogController::log(
                //     'Developmental History Added',
                //     "User {$user->username} added a new developmental history record.",
                //     ['patient_id' => $request->patient_id]
                // );
            }

            AuditLogController::log(
                'Medical History',
                "User {$user->username} added a new medical history record.",
                ['patient_id' => $request->patient_id]
            );

            return back()->with('success', 'Medical history saved successfully!');

        } catch (Throwable $e) {
            // Check for the specific 'patient_id' cannot be null error
            if (str_contains($e->getMessage(), '1048 Column \'patient_id\' cannot be null')) {
                return back()->with('error', 'Please select a patient before saving the medical history.');
            }

            // Check for any general database integrity constraint violation
            if ($e instanceof \Illuminate\Database\QueryException && str_contains($e->getCode(), '23000')) {
                return back()->with('error', 'There was a problem saving your data due to missing or invalid information. Please check your inputs and try again.');
            }

            // For any other unexpected errors, provide a generic message
            return back()->with('error', 'An unexpected error occurred while saving the medical history. Please try again or contact support.');
        }
    }
}
