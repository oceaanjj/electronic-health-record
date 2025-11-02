@extends('layouts.app')

@section('title', 'Patient Report')

@section('content')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
        /* Hide common icon elements */
        .fa, .fas, .far, .fal, .fab, /* Font Awesome */
        .glyphicon, /* Bootstrap Glyphicons */
        [class*="icon-"], /* Generic icon classes */
        i[class*="fa-"], /* Font Awesome icons using <i> tag */
        i[class*="glyphicon-"] /* Bootstrap Glyphicons using <i> tag */
        {
            display: none !important;
        }
        /* Also hide any elements that might be used as icons via pseudo-elements */
        *:before, *:after {
            content: none !important;
        }
    }
</style>
<div class="container">
    <h1>Patient Report for {{ $patient->name }}</h1>

    <div class="actions no-print">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="{{ route('doctor.report.pdf', ['patient_id' => $patient->patient_id]) }}" class="btn btn-success">Save as PDF</a>
    </div>

    <div class="report-section">
        <h2>Medical History</h2>
        
        <h3>Allergies</h3>
        <ul>
            @foreach($allergies as $allergy)
                <li>{{ $allergy->allergy_name }} - {{ $allergy->reaction }}</li>
            @endforeach
        </ul>

        <h3>Developmental History</h3>
        <p>{{ $developmentalHistory->first()->history_details ?? 'N/A' }}</p>

        <h3>Past Medical/Surgical History</h3>
        <p>{{ $pastMedicalSurgical->first()->history_details ?? 'N/A' }}</p>

        <h3>Present Illness</h3>
        <p>{{ $presentIllness->first()->illness_details ?? 'N/A' }}</p>

        <h3>Vaccination</h3>
        <ul>
            @foreach($vaccination as $vax)
                <li>{{ $vax->vaccine_name }} - {{ $vax->vaccination_date }}</li>
            @endforeach
        </ul>
    </div>

    <div class="report-section">
        <h2>Physical Exam</h2>
        <p>{{ $physicalExam->first()->exam_details ?? 'N/A' }}</p>
    </div>

    <div class="report-section">
        <h2>Vital Signs</h2>
        <ul>
            @foreach($vitals as $vital)
                <li>Date: {{ $vital->created_at }} - BP: {{ $vital->blood_pressure }}, HR: {{ $vital->heart_rate }}, RR: {{ $vital->respiratory_rate }}, Temp: {{ $vital->temperature }}</li>
            @endforeach
        </ul>
    </div>

    <div class="report-section">
        <h2>Intake and Output</h2>
        <p>{{ $intakeAndOutput->first()->details ?? 'N/A' }}</p>
    </div>

    <div class="report-section">
        <h2>Activities of Daily Living</h2>
        <p>{{ $actOfDailyLiving->first()->details ?? 'N/A' }}</p>
    </div>

    <div class="report-section">
        <h2>Lab Values</h2>
        <p>{{ $labValues->first()->details ?? 'N/A' }}</p>
    </div>

    <div class="report-section">
        <h2>IVs and Lines</h2>
        <p>{{ $ivsAndLines->first()->details ?? 'N/A' }}</p>
    </div>

    <div class="report-section">
        <h2>Medical Reconciliation</h2>
        <p>{{ $medicalReconciliation->first()->details ?? 'N/A' }}</p>
    </div>

    <div class="report-section">
        <h2>Discharge Plan</h2>
        <p>{{ $dischargePlan->first()->plan_details ?? 'N/A' }}</p>
    </div>

</div>
@endsection
