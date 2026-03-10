<!DOCTYPE html>
<html>
    <head>
        <title>Patient Medical Record</title>

        <style>
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
                font-size: 8.5pt;
                color: #1C2833;
                padding: 0 2mm;
                /* leave space for fixed footer */
                margin-bottom: 28px;
            }

            /* ─── LETTERHEAD ──────────────────────────────────────────── */
            .letterhead {
                width: 100%;
                padding-bottom: 8px;
                margin-bottom: 10px;
                border-bottom: 3px solid #1a6a24;
            }

            .lh-table {
                width: 100%;
                border-collapse: collapse;
            }

            /* remove borders/backgrounds from letterhead cells so the global td rule doesn't bleed in */
            .lh-table td {
                border: none;
                background-color: transparent;
            }

            .lh-logo-cell {
                width: 68px;
                vertical-align: middle;
                text-align: center;
                padding: 0 10px;
            }

            .lh-logo-cell img {
                width: 60px;
                height: auto;
            }

            .lh-name-cell {
                vertical-align: middle;
            }

            .hospital-name {
                font-size: 15pt;
                font-weight: bold;
                color: #1a6a24;
                letter-spacing: 0.4px;
            }

            .hospital-subtitle {
                font-size: 7.5pt;
                color: #5D6D7E;
                margin-top: 2px;
            }

            .lh-meta-cell {
                vertical-align: middle;
                text-align: right;
                font-size: 7.5pt;
                color: #5D6D7E;
                white-space: nowrap;
            }

            .lh-meta-cell .confidential {
                color: #C0392B;
                font-weight: bold;
                font-size: 8pt;
                margin-top: 3px;
            }

            /* ─── DOCUMENT TITLE ──────────────────────────────────────── */
            .doc-title {
                text-align: center;
                font-size: 11.5pt;
                font-weight: bold;
                color: #1a6a24;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                padding: 5px 0;
                margin-bottom: 10px;
                border-top: 1px solid #b7dfb9;
                border-bottom: 1px solid #b7dfb9;
            }

            /* ─── PATIENT INFO BLOCK ──────────────────────────────────── */
            .patient-info-block {
                width: 100%;
                border: 1.5px solid #1a6a24;
                border-radius: 3px;
                margin-bottom: 14px;
                overflow: hidden;
            }

            .patient-info-header {
                background-color: #1a6a24;
                color: #ffffff;
                font-size: 8.5pt;
                font-weight: bold;
                padding: 5px 9px;
                letter-spacing: 0.8px;
                text-transform: uppercase;
            }

            .patient-info-body {
                background-color: #f0f8f0;
                padding: 7px 9px;
            }

            .pi-table {
                width: 100%;
                border-collapse: collapse;
            }

            .pi-table td {
                padding: 3px 5px;
                font-size: 8.5pt;
                vertical-align: top;
                border: none;
            }

            .pi-table td.lbl {
                font-weight: bold;
                color: #1a6a24;
                white-space: nowrap;
                width: 120px;
            }

            .pi-table td.val {
                color: #1C2833;
            }

            /* ─── SECTION BLOCK ───────────────────────────────────────── */
            .section {
                margin-bottom: 12px;
                border: 1px solid #b7dfb9;
                border-radius: 3px;
                overflow: hidden;
            }

            .section-title {
                background-color: #1a6a24;
                color: #ffffff;
                padding: 5px 9px;
                font-size: 9.5pt;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 0.6px;
                margin: 0;
            }

            .section-title-adpie {
                background-color: #03582c;
                color: #ffffff;
                padding: 4px 9px;
                font-size: 8.5pt;
                font-weight: bold;
                letter-spacing: 0.5px;
                margin-top: 6px;
            }

            /* ─── TABLES ──────────────────────────────────────────────── */
            .table-responsive {
                overflow-x: auto;
            }

            table {
                width: 100%;
                table-layout: fixed;
                border-collapse: collapse;
                margin: 0;
                font-size: 8pt;
            }

            th {
                background-color: #2D6A4F;
                color: #ffffff;
                padding: 5px 6px;
                text-align: left;
                font-weight: bold;
                border: 1px solid #1a6a24;
                word-break: break-word;
                vertical-align: top;
            }

            td {
                padding: 4px 6px;
                text-align: left;
                border: 1px solid #c6e6c9;
                vertical-align: top;
                word-break: break-word;
            }

            tr:nth-child(even) td {
                background-color: #f0f8f0;
            }

            tr:nth-child(odd) td {
                background-color: #ffffff;
            }

            .no-data {
                color: #7F8C8D;
                font-style: italic;
                padding: 7px 9px;
                font-size: 8pt;
            }

            /* ─── MISC ────────────────────────────────────────────────── */
            .pdf-hr {
                border: none;
                border-top: 1px solid #b7dfb9;
                margin: 5px 0;
            }

            .page-break {
                page-break-after: always;
            }

            .diagnostic-image {
                max-width: 100%;
                height: auto;
                border: 1px solid #c6e6c9;
            }

            img {
                max-width: 100%;
                height: auto;
            }

            p { margin: 0; }

            h1 { font-size: 14pt; }
            h2 { font-size: 11pt; }
            h3 { font-size: 9pt; }

            .section h3 {
                padding: 4px 9px;
                font-size: 8.5pt;
                color: #1a6a24;
                background-color: #e5ffe9;
                border-bottom: 1px solid #b7dfb9;
                margin-top: 5px;
            }

            ul { margin: 0; padding-left: 14px; }
            li { margin-bottom: 1px; }

            /* ─── FOOTER (fixed, every page) ──────────────────────────── */
            .pdf-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 22px;
                border-top: 1px solid #b7dfb9;
                background-color: #f0f8f0;
            }

            .pdf-footer-left {
                position: absolute;
                left: 4px;
                bottom: 4px;
                font-size: 7pt;
                color: #95A5A6;
            }

            .pdf-footer-right {
                position: absolute;
                right: 4px;
                bottom: 4px;
                font-size: 7pt;
                color: #C0392B;
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        @php
            $logoPath = public_path('favicon.png');
            $logoData = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : null;
            $generatedAt = now()->format('F j, Y  \a\t  H:i');
        @endphp

        {{-- ── LETTERHEAD ── --}}
        <div class="letterhead">
            <table class="lh-table">
                <tr>
                    {{-- Left: hospital name & subtitle --}}
                    <td class="lh-name-cell">
                        <div class="hospital-name">Electronic Health Record System</div>
                        <div class="hospital-subtitle">Patient Health Management &amp; Nursing Documentation</div>
                    </td>
                    {{-- Middle: logo --}}
                    @if ($logoData)
                        <td class="lh-logo-cell">
                            <img src="{{ $logoData }}" alt="EHR Logo">
                        </td>
                    @endif
                    {{-- Right: date + confidential --}}
                    <td class="lh-meta-cell">
                        <div>Generated: {{ $generatedAt }}</div>
                        <div class="confidential">&#x1F512; CONFIDENTIAL</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ── DOCUMENT TITLE ── --}}
        <div class="doc-title">Patient Medical Record</div>

        {{-- ── DEMOGRAPHICS (inlined for PDF-only formal layout) ── --}}
        <div class="patient-info-block">
            <div class="patient-info-header">Patient Demographics</div>
            <div class="patient-info-body">
                <table class="pi-table">
                    <tr>
                        <td class="lbl">Full Name:</td>
                        <td class="val">{{ $patient->name ?? 'N/A' }}</td>
                        <td class="lbl">Patient ID:</td>
                        <td class="val">{{ $patient->patient_id ?? 'N/A' }}</td>
                        <td class="lbl">Sex:</td>
                        <td class="val">{{ $patient->sex ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Age:</td>
                        <td class="val">{{ $patient->age ?? 'N/A' }}</td>
                        <td class="lbl">Date of Birth:</td>
                        <td class="val">
                            @if (!empty($patient->date_of_birth))
                                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('F j, Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="lbl">Civil Status:</td>
                        <td class="val">{{ $patient->civil_status ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Address:</td>
                        <td class="val" colspan="5">{{ $patient->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Chief Complaint:</td>
                        <td class="val" colspan="5">{{ $patient->chief_complaints ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @include('doctor.reports.partials._medical_history', [
            'presentIllness'      => $presentIllness,
            'pastMedicalSurgical' => $pastMedicalSurgical,
            'allergies'           => $allergies,
            'vaccination'         => $vaccination,
        ])

        @include('doctor.reports.partials._developmental-history', [
            'developmentalHistory' => $developmentalHistory,
        ])

        @include('doctor.reports.partials._physical_exam', [
            'physicalExam' => $physicalExam,
        ])

        @include('doctor.reports.partials._vital_signs', [
            'vitals' => $vitals,
        ])

        @include('doctor.reports.partials._intake_and_output', [
            'intakeAndOutput' => $intakeAndOutput,
        ])

        @include('doctor.reports.partials._activities_of_daily_living', [
            'actOfDailyLiving' => $actOfDailyLiving,
        ])

        @include('doctor.reports.partials._lab_values', [
            'labValues' => $labValues,
        ])

        @include('doctor.reports.partials._diagnostics', [
            'diagnostics' => $diagnostics,
        ])

        @include('doctor.reports.partials._ivs_and_lines', [
            'ivsAndLines' => $ivsAndLines,
        ])

        @include('doctor.reports.partials._medication_administrations', [
            'medicationAdministrations' => $medicationAdministrations,
        ])

        @include('doctor.reports.partials._medication_reconciliation', [
            'currentMedication'     => $currentMedication,
            'homeMedication'        => $homeMedication,
            'changesInMedication'   => $changesInMedication,
        ])

        {{-- @include('doctor.reports.partials._discharge_planning', ['dischargePlanning' => $dischargePlanning]) --}}

        {{-- ── FIXED FOOTER (every page) ── --}}
        <div class="pdf-footer">
            <span class="pdf-footer-left">Electronic Health Record System &mdash; Patient Medical Record</span>
            <span class="pdf-footer-right">CONFIDENTIAL</span>
        </div>

        <footer>
            <script type="text/php">
                if (isset($pdf)) {
                    $text  = "Page {PAGE_NUM} of {PAGE_COUNT}";
                    $size  = 7;
                    $font  = $fontMetrics->getFont("DejaVu Sans");
                    $width = $fontMetrics->get_text_width($text, $font, $size) / 2;

                    $x = ($pdf->get_width() - $width) / 2;
                    $y = $pdf->get_height() - 18;

                    $pdf->page_text($x, $y, $text, $font, $size, [0.4, 0.4, 0.4]);
                }
            </script>
        </footer>
    </body>
</html>
