<!DOCTYPE html>
<html>

<head>
    <title>Patient Report</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            margin: 5mm 10mm;
            font-size: 10px;
        }

        h1 {
            color: #333;
            font-size: 18px;
        }

        h2 {
            color: #333;
            font-size: 14px;
        }

        h3 {
            color: #333;
            font-size: 12px;
        }

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
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            table-layout: fixed;
            /* Crucial for fixed column widths */
            border-collapse: collapse;
            margin-top: 5px;
            max-width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            word-break: break-word;
            vertical-align: top;
            width: 33.33%;
            /* Equal width for 3 columns */
        }

        th {
            background-color: #f2f2f2;
        }

        .no-data {
            color: #777;
            font-style: italic;
        }

        .diagnostic-image {
            max-width: 100%;
            height: auto;
            margin-top: 5px;
            border: 1px solid #ddd;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @include('doctor.reports.partials._patient_demographics', ['patient' => $patient])

    @include('doctor.reports.partials._medical_history', [
        'presentIllness' => $presentIllness,
        'pastMedicalSurgical' => $pastMedicalSurgical,
        'allergies' => $allergies,
        'vaccination' => $vaccination,
    ])

    @include('doctor.reports.partials._physical_exam', ['physicalExam' => $physicalExam])

    @include('doctor.reports.partials._vital_signs', ['vitals' => $vitals])

    @include('doctor.reports.partials._intake_and_output', ['intakeAndOutput' => $intakeAndOutput])

    @include('doctor.reports.partials._activities_of_daily_living', ['actOfDailyLiving' => $actOfDailyLiving])

    @include('doctor.reports.partials._lab_values', ['labValues' => $labValues])

    @include('doctor.reports.partials._diagnostics', ['diagnostics' => $diagnostics])

    @include('doctor.reports.partials._ivs_and_lines', ['ivsAndLines' => $ivsAndLines])

    @include('doctor.reports.partials._medication_administration')

    @include('doctor.reports.partials._medication_reconciliation', [
        'currentMedication' => $currentMedication,
        'homeMedication' => $homeMedication,
        'changesInMedication' => $changesInMedication,
    ])

    @include('doctor.reports.partials._discharge_planning', ['dischargePlanning' => $dischargePlanning])
</body>

</html>