<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Report for {{ $patient->name }}</title>
    <style>
        /* Basic styling for the PDF */
        body {
            font-family: sans-serif;
        }
        h1, h2, h3 {
            margin-bottom: 0.5em;
        }
        ul {
            padding-left: 20px;
            margin-top: 0;
        }
        .report-section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h1>Patient Report for {{ $patient->name }}</h1>

    <div class="report-section">
        <h2>Medical History</h2>
        
        <h3>Allergies</h3>
        <ul>
            @forelse($allergies as $allergy)
                <li>{{ $allergy->allergy_name }} - {{ $allergy->reaction }}</li>
            @empty
                <li>N/A</li>
            @endforelse
        </ul>

        <h3>Developmental History</h3>
        <p>{{ $developmentalHistory->first()->history_details ?? 'N/A' }}</p>

        <h3>Past Medical/Surgical History</h3>
        <p>{{ $pastMedicalSurgical->first()->history_details ?? 'N/A' }}</p>

        <h3>Present Illness</h3>
        <p>{{ $presentIllness->first()->illness_details ?? 'N/A' }}</p>

        <h3>Vaccination</h3>
        <ul>
            @forelse($vaccination as $vax)
                <li>{{ $vax->vaccine_name }} - {{ $vax->vaccination_date }}</li>
            @empty
                <li>N/A</li>
            @endforelse
        </ul>
    </div>

    <div class="report-section">
        <h2>Physical Exam</h2>
        <p>{{ $physicalExam->first()->exam_details ?? 'N/A' }}</p>
    </div>

    <div class="report-section">
        <h2>Vital Signs</h2>
        <ul>
            @forelse($vitals as $vital)
                <li>Date: {{ $vital->created_at }} - BP: {{ $vital->blood_pressure }}, HR: {{ $vital->heart_rate }}, RR: {{ $vital->respiratory_rate }}, Temp: {{ $vital->temperature }}</li>
            @empty
                <li>N/A</li>
            @endforelse
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

</body>
</html>
