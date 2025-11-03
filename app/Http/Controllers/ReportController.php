<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
use App\Models\MedicalReconciliation;
use App\Models\MedicalReconciliation\ChangesInMedication;
use App\Models\MedicalReconciliation\CurrentMedication;
use App\Models\MedicalReconciliation\HomeMedication;
use App\Models\Diagnostic;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
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
            'physicalExam' => PhysicalExam::where('patient_id', $patient_id)->get(),
            'vitals' => Vitals::where('patient_id', $patient_id)->get(),
            'intakeAndOutput' => IntakeAndOutput::where('patient_id', $patient_id)->get(),
            'actOfDailyLiving' => ActOfDailyLiving::where('patient_id', $patient_id)->get(),
            'labValues' => LabValues::where('patient_id', $patient_id)->get(),
            'diagnostics' => Diagnostic::where('patient_id', $patient_id)->get(),
            'ivsAndLines' => IvsAndLine::where('patient_id', $patient_id)->get(),
            'currentMedication' => CurrentMedication::where('patient_id', $patient_id)->get(),
            'homeMedication' => HomeMedication::where('patient_id', $patient_id)->get(),
            'changesInMedication' => ChangesInMedication::where('patient_id', $patient_id)->get(),
        ];
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
            'physicalExam' => PhysicalExam::where('patient_id', $patient_id)->get(),
            'vitals' => Vitals::where('patient_id', $patient_id)->get(),
            'intakeAndOutput' => IntakeAndOutput::where('patient_id', $patient_id)->get(),
            'actOfDailyLiving' => ActOfDailyLiving::where('patient_id', $patient_id)->get(),
            'labValues' => LabValues::where('patient_id', $patient_id)->get(),
            'diagnostics' => Diagnostic::where('patient_id', $patient_id)->get(),
            'ivsAndLines' => IvsAndLine::where('patient_id', $patient_id)->get(),
            'currentMedication' => CurrentMedication::where('patient_id', $patient_id)->get(),
            'homeMedication' => HomeMedication::where('patient_id', $patient_id)->get(),
            'changesInMedication' => ChangesInMedication::where('patient_id', $patient_id)->get(),
        ];

        $pdf = Pdf::loadView('reports.patient-report-pdf', $data);
        return $pdf->download($patient->name . '_Results.pdf');
    }
}
