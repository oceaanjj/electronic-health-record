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
            font-size: 14px;
        }

        h1 {
            font-size: 18px;
        }

        h2 {
            font-size: 16px;
        }

        h3 {
            font-size: 15px;
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
            margin-top: 10px;
            margin-botom: 10px;
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
            margin: auto;
            border: 1px solid #ddd;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            line-height: 35px;
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

    @include('doctor.reports.partials._developmental-history', ['developmentalHistory' => $developmentalHistory])

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

    <footer>
        <script type="text/php">
            if (isset($pdf)) {
                $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                $size = 9;




                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width) / 2;
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size, [0.3, 0.3, 0.3]);
            }
        </script>
    </footer>
</body>
</html>