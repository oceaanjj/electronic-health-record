@extends('layouts.doctor')
@section('title', 'Patient Report')
@section('content')

<!-- nasa doctor/report/partials yung mga components -->
    <style>
      
        .section {
            margin-bottom: 10px;
            border: 1px solid #eee;
            padding: 8px;
            border-radius: 5px;
        }

        .section-title {    /* component title */
            background-color: #ffe070ff;
            padding: 5px;
            margin-top:10px;
            margin-botom:10px;
            border-bottom: 1px solid #eee;
            font-size: 16px;
            font-weight: bold;
        }

        h2{  /* ex: patient-information text */
            font-weight: bold;
            font-size:16px;
        }
       
        h3 {  /* ex: present illness, sa table */
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            word-break: break-word;
            vertical-align: top;
            width: 33.33%;
        }

        th {
            background-color: #f2f2f2;
            font-size:14px;
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
                <a href="{{ route('doctor.report.pdf', ['patient_id' => $patient->patient_id]) }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">Download PDF</a>
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
                @include('doctor.reports.partials._developmental-history', ['developmentalHistory' => $developmentalHistory])
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
