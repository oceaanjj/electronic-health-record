@extends('layouts.app')

@section('title', 'Patient Report')

@section('content')
<style>
    .section {
        margin-bottom: 10px;
        border: 1px solid #eee;
        padding: 8px;
        border-radius: 5px;
    }
    .section-title {
        background-color: #f9f9f9;
        padding: 5px;
        margin: -8px -8px 8px -8px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
        font-weight: bold;
    }
    h3 {
        font-size: 12px;
        font-weight: bold;
        margin-top: 10px;
    }
    table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        margin-top: 5px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 5px;
        text-align: left;
        word-break: break-word;
        vertical-align: top;
        width: 33.33%;
    }
    th {
        background-color: #f2f2f2;
    }
    .no-data {
        color: #777;
        font-style: italic;
    }
</style>
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Patient Report</h1>
            <a href="{{ route('doctor.report.pdf', ['patient_id' => $patient->patient_id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Download PDF</a>
        </div>

        <div class="section">
            @include('doctor.reports.partials._patient_demographics', ['patient' => $patient])
        </div>
        <div class="section">
            @include('doctor.reports.partials._medical_history', [
                'presentIllness' => $presentIllness,
                'pastMedicalSurgical' => $pastMedicalSurgical,
                'allergies' => $allergies,
                'vaccination' => $vaccination,
            ])
        </div>
        <div class="section">
            @include('doctor.reports.partials._physical_exam', ['physicalExam' => $physicalExam])
        </div>
        <div class="section">
            @include('doctor.reports.partials._vital_signs', ['vitals' => $vitals])
        </div>
        <div class="section">
            @include('doctor.reports.partials._intake_and_output', ['intakeAndOutput' => $intakeAndOutput])
        </div>
        <div class="section">
            @include('doctor.reports.partials._activities_of_daily_living', ['actOfDailyLiving' => $actOfDailyLiving])
        </div>
        <div class="section">
            @include('doctor.reports.partials._lab_values', ['labValues' => $labValues])
        </div>
        <div class="section">
            @include('doctor.reports.partials._diagnostics', ['diagnostics' => $diagnostics])
        </div>
        <div class="section">
            @include('doctor.reports.partials._ivs_and_lines', ['ivsAndLines' => $ivsAndLines])
        </div>
        <div class="section">
            @include('doctor.reports.partials._medication_administration')
        </div>
        <div class="section">
            @include('doctor.reports.partials._medication_reconciliation', [
                'currentMedication' => $currentMedication,
                'homeMedication' => $homeMedication,
                'changesInMedication' => $changesInMedication,
            ])
        </div>
        <div class="section">
            @include('doctor.reports.partials._discharge_planning', ['dischargePlanning' => $dischargePlanning])
        </div>
    </div>
</div>
@endsection
