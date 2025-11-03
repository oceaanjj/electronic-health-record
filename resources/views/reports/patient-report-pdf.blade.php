<!DOCTYPE html>
<html>
<head>
    <title>Patient Report</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 10mm 15mm; }
        h1, h2, h3 { color: #333; }
        .section { margin-bottom: 15px; border: 1px solid #eee; padding: 10px; border-radius: 5px; }
        .section-title { background-color: #f9f9f9; padding: 8px; margin: -10px -10px 10px -10px; border-bottom: 1px solid #eee; }
        ul { list-style-type: disc; margin-left: 15px; padding: 0; }
        ul ul { list-style-type: circle; margin-left: 15px; }
        li { margin-bottom: 3px; }
        .no-data { color: #777; font-style: italic; }
        .diagnostic-image { max-width: 100%; height: auto; margin-top: 8px; border: 1px solid #ddd; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>Patient Report for {{ $patient->name }}</h1>

    <div class="section">
        <h2 class="section-title">1. Medical History</h2>
        <h3>Present Illness</h3>
        @if($presentIllness->isEmpty())
            <p class="no-data">No Present Illness data available.</p>
        @else
            @foreach($presentIllness as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Past Medical / Surgical</h3>
        @if($pastMedicalSurgical->isEmpty())
            <p class="no-data">No Past Medical / Surgical data available.</p>
        @else
            @foreach($pastMedicalSurgical as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Known Conditions or Allergies</h3>
        @if($allergies->isEmpty())
            <p class="no-data">No Known Conditions or Allergies data available.</p>
        @else
            @foreach($allergies as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Vaccination</h3>
        @if($vaccination->isEmpty())
            <p class="no-data">No Vaccination data available.</p>
        @else
            @foreach($vaccination as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">2. Physical Exam</h2>
        @if($physicalExam->isEmpty())
            <p class="no-data">No Physical Exam data available.</p>
        @else
            @foreach($physicalExam as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">3. Vital Signs</h2>
        @if($vitals->isEmpty())
            <p class="no-data">No Vital Signs data available.</p>
        @else
            @foreach($vitals as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">4. Intake and Output</h2>
        @if($intakeAndOutput->isEmpty())
            <p class="no-data">No Intake and Output data available.</p>
        @else
            @foreach($intakeAndOutput as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">5. Activities of Daily Living</h2>
        @if($actOfDailyLiving->isEmpty())
            <p class="no-data">No Activities of Daily Living data available.</p>
        @else
            @foreach($actOfDailyLiving as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">6. Lab Values</h2>
        @if($labValues->isEmpty())
            <p class="no-data">No Lab Values data available.</p>
        @else
            @foreach($labValues as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">7. Diagnostics</h2>
        @if($diagnostics->isEmpty())
            <p class="no-data">No Diagnostics data available.</p>
        @else
            @foreach($diagnostics as $item)
                <ul>
                    @if(!in_array('type', ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                        <li><strong>Type:</strong> {{ $item->type }}</li>
                    @endif
                    @if(!in_array('original_name', ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                        <li><strong>Original Name:</strong> {{ $item->original_name }}</li>
                    @endif
                    @if($item->path && !in_array('path', ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                        <li><strong>Image:</strong> <img src="{{ asset('storage/' . $item->path) }}" alt="Diagnostic Image" class="diagnostic-image"></li>
                    @elseif(!in_array('path', ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                        <li>No image available for this diagnostic entry.</li>
                    @endif
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">8. IV's & Lines</h2>
        @if($ivsAndLines->isEmpty())
            <p class="no-data">No IV's & Lines data available.</p>
        @else
            @foreach($ivsAndLines as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">9. Medication Administration</h2>
        <p class="no-data">Medication Administration data is not available in this report.</p>
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">10. Medication Reconciliation</h2>
        <h3>Patient's Current Medication (Upon Admission)</h3>
        @if($currentMedication->isEmpty())
            <p class="no-data">No Current Medication data available.</p>
        @else
            @foreach($currentMedication as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Patient's Home Medication (If Any)</h3>
        @if($homeMedication->isEmpty())
            <p class="no-data">No Home Medication data available.</p>
        @else
            @foreach($homeMedication as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Changes in Medication During Hospitalization</h3>
        @if($changesInMedication->isEmpty())
            <p class="no-data">No Changes in Medication data available.</p>
        @else
            @foreach($changesInMedication as $item)
                <ul>
                    @foreach($item->getAttributes() as $column => $value)
                        @if(!in_array($column, ['id', 'patient_id', 'created_at', 'updated_at', 'deleted_at']))
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $column)) }}:</strong> {{ $value }}</li>
                        @endif
                    @endforeach
                </ul>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>
</body>
</html>