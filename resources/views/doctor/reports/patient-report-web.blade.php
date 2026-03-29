@extends('layouts.doctor')
@section('title', 'Patient Web Report')

@section('content')
    <style>
        .section {
            margin-bottom: 10px;
            border: 1px solid #eee;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .dark .section {
            border-color: #334155;
            background-color: rgba(30, 41, 59, 0.5);
        }

        .section-title {
            background: linear-gradient(180deg, #065f46, #064e3b);
            color: white;
            padding: 8px 12px;
            margin-top: 10px;
            margin-bottom: 8px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            font-family: 'Alte Haas Grotesk Bold', sans-serif;
        }

        .section-title-adpie {
            background-color: #f1f5f9;
            color: #475569;
            padding: 6px 12px;
            border-radius: 4px;
            margin-top: 12px;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            font-family: 'Alte Haas Grotesk Bold', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dark .section-title-adpie {
            background-color: #1e293b;
            color: #94a3b8;
        }

        h2, h3 {
            font-weight: bold;
            font-family: 'Alte Haas Grotesk Bold', sans-serif;
            color: #1e293b;
        }
        .dark h2, .dark h3 {
            color: #f1f5f9;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 8px;
            font-family: 'Alte Haas Grotesk', sans-serif;
        }

        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            text-align: left;
            word-break: break-word;
            vertical-align: top;
        }
        .dark th, .dark td {
            border-color: #334155;
        }

        th {
            background-color: #f8fafc;
            font-size: 13px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .dark th {
            background-color: #0f172a;
            color: #94a3b8;
        }
        
        td {
            color: #334155;
            font-size: 14px;
        }
        .dark td {
            color: #cbd5e1;
        }

        .no-data {
            padding: 12px;
            color: #94a3b8;
            font-style: italic;
            font-size: 14px;
            text-align: center;
        }

        .pdf-hr {
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 20px 0;
        }
        .dark .pdf-hr {
            border-top-color: #334155;
        }
    </style>

    <div class="mx-auto max-w-7xl py-8 px-4 sm:px-6 lg:px-8 transition-colors duration-300">
        <div class="rounded-2xl bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-xl border border-slate-200 dark:border-slate-800 transition-all duration-300">
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white font-alte tracking-tight">Patient Clinical Report</h1>
                    <p class="text-slate-500 dark:text-slate-400 font-alte-regular mt-1">Comprehensive assessment history for <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $patient->name }}</span></p>
                </div>

                <a
                    href="{{ route('doctor.report.pdf', ['patient_id' => $patient->patient_id]) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 dark:bg-emerald-600 px-6 py-3 font-bold text-white hover:bg-emerald-800 dark:hover:bg-emerald-700 shadow-lg shadow-emerald-200 dark:shadow-none transition-all"
                >
                    <span class="material-symbols-outlined">picture_as_pdf</span>
                    Download PDF Report
                </a>
            </div>

            <div class="space-y-6">
                <div class="section">
                    @include('doctor.reports.partials._patient_demographics', ['patient' => $patient])
                </div>

                <div class="section">
                    @include(
                        'doctor.reports.partials._medical_history',
                        [
                            'presentIllness' => $presentIllness,
                            'pastMedicalSurgical' => $pastMedicalSurgical,
                            'allergies' => $allergies,
                            'vaccination' => $vaccination,
                        ]
                    )
                </div>

                <div class="section">
                    @include(
                        'doctor.reports.partials._developmental-history',
                        [
                            'developmentalHistory' => $developmentalHistory,
                        ]
                    )
                </div>

                <div class="section">
                    @include(
                        'doctor.reports.partials._physical_exam',
                        [
                            'physicalExam' => $physicalExam,
                        ]
                    )
                </div>

                <div class="section">
                    @include('doctor.reports.partials._vital_signs', ['vitals' => $vitals])
                </div>

                <div class="section">
                    @include(
                        'doctor.reports.partials._intake_and_output',
                        [
                            'intakeAndOutput' => $intakeAndOutput,
                        ]
                    )
                </div>

                <div class="section">
                    @include(
                        'doctor.reports.partials._activities_of_daily_living',
                        [
                            'actOfDailyLiving' => $actOfDailyLiving,
                        ]
                    )
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
                    @include(
                        'doctor.reports.partials._medication_administrations',
                        [
                            'medicationAdministrations' => $medicationAdministrations,
                        ]
                    )
                </div>

                <div class="section">
                    @include(
                        'doctor.reports.partials._medication_reconciliation',
                        [
                            'currentMedication' => $currentMedication,
                            'homeMedication' => $homeMedication,
                            'changesInMedication' => $changesInMedication,
                        ]
                    )
                </div>
            </div>
        </div>
    </div>
@endsection
