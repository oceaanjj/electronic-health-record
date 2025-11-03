<!DOCTYPE html>
<html>
<head>
    <title>Patient Report</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 5mm 10mm; font-size: 10px; }
        h1 { color: #333; font-size: 18px; }
        h2 { color: #333; font-size: 14px; }
        h3 { color: #333; font-size: 12px; }
        .section { margin-bottom: 10px; border: 1px solid #eee; padding: 8px; border-radius: 5px; }
        .section-title { background-color: #f9f9f9; padding: 5px; margin: -8px -8px 8px -8px; border-bottom: 1px solid #eee; }
        .table-responsive { overflow-x: auto; }
                table {
                    width: 100%;
                    table-layout: fixed; /* Crucial for fixed column widths */
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
                    width: 33.33%; /* Equal width for 3 columns */
                }
        th { background-color: #f2f2f2; }
        .no-data { color: #777; font-style: italic; }
        .diagnostic-image { max-width: 100%; height: auto; margin-top: 5px; border: 1px solid #ddd; }
        img { max-width: 100%; height: auto; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="patient-demographics">
        <h2>Patient Information</h2>
        <p><strong>Name:</strong> {{ $patient->name }}</p>
        <p><strong>Age:</strong> {{ $patient->age }}</p>
        <p><strong>Sex:</strong> {{ $patient->sex }}</p>
        <p><strong>Address:</strong> {{ $patient->address }}</p>
        <p><strong>Chief of Complaints:</strong> {{ $patient->chief_of_complaints }}</p>
        <p><strong>Room No:</strong> {{ $patient->room_no }}</p>
        <p><strong>Bed No:</strong> {{ $patient->bed_no }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">1. Medical History</h2>
        <h3>Present Illness</h3>
        @if($presentIllness->isEmpty())
            <p class="no-data">No Present Illness data available.</p>
        @else
            @foreach($presentIllness as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Past Medical / Surgical</h3>
        @if($pastMedicalSurgical->isEmpty())
            <p class="no-data">No Past Medical / Surgical data available.</p>
        @else
            @foreach($pastMedicalSurgical as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Known Conditions or Allergies</h3>
        @if($allergies->isEmpty())
            <p class="no-data">No Known Conditions or Allergies data available.</p>
        @else
            @foreach($allergies as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Vaccination</h3>
        @if($vaccination->isEmpty())
            <p class="no-data">No Vaccination data available.</p>
        @else
            @foreach($vaccination as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
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
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
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
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
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
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
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
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
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
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
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
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            if($column == 'path'){
                                $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = '<img src="' . asset('storage/' . $item->path) . '">';
                            } else {
                                $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                            }
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{!! isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' !!}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
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
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
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
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Patient's Home Medication (If Any)</h3>
        @if($homeMedication->isEmpty())
            <p class="no-data">No Home Medication data available.</p>
        @else
            @foreach($homeMedication as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif

        <h3>Changes in Medication During Hospitalization</h3>
        @if($changesInMedication->isEmpty())
            <p class="no-data">No Changes in Medication data available.</p>
        @else
            @foreach($changesInMedication as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
                    $filteredAttributes = [];
                    foreach($item->getAttributes() as $column => $value) {
                        if(!in_array($column, $excludedColumns)) {
                            $filteredAttributes[ucfirst(str_replace('_', ' ', $column))] = $value;
                        }
                    }
                    $attributeChunks = array_chunk($filteredAttributes, 3, true);
                @endphp
                <div class="table-responsive">
                    @foreach($attributeChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <h2 class="section-title">11. Discharge Planning</h2>
        @if($dischargePlanning->isEmpty())
            <p class="no-data">No Discharge Planning data available.</p>
        @else
            @foreach($dischargePlanning as $item)
                @php
                    $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];

                    $dischargePlanningHeaderMap = [
                        'criteria_feverRes' => 'Fever Resolution',
                        'criteria_patientCount' => 'Normalization of Patient Count',
                        'criteria_manageFever' => 'Manage Fever Effectively',
                        'criteria_manageFever2' => 'Manage Fever Effectively 2',
                        'instruction_med' => 'Medications',
                        'instruction_appointment' => 'Follow-Up Appointment',
                        'instruction_fluidIntake' => 'Fluid Intake',
                        'instruction_exposure' => 'Avoid Mosquito Exposure',
                        'instruction_complications' => 'Monitor for Signs of Complications',
                    ];

                    // Discharge Criteria
                    $criteriaColumns = [
                        'criteria_feverRes',
                        'criteria_patientCount',
                        'criteria_manageFever',
                        'criteria_manageFever2',
                    ];
                    $filteredCriteria = [];
                    foreach ($criteriaColumns as $col) {
                        if (!in_array($col, $excludedColumns)) {
                            $displayName = $dischargePlanningHeaderMap[$col] ?? ucfirst(str_replace('_', ' ', $col));
                            $filteredCriteria[$displayName] = $item->$col ?? 'N/A';
                        }
                    }
                    $criteriaChunks = array_chunk($filteredCriteria, 3, true);

                    // Discharge Instructions
                    $instructionColumns = [
                        'instruction_med',
                        'instruction_appointment',
                        'instruction_fluidIntake',
                        'instruction_exposure',
                        'instruction_complications',
                    ];
                    $filteredInstructions = [];
                    foreach ($instructionColumns as $col) {
                        if (!in_array($col, $excludedColumns)) {
                            $displayName = $dischargePlanningHeaderMap[$col] ?? ucfirst(str_replace('_', ' ', $col));
                            $filteredInstructions[$displayName] = $item->$col ?? 'N/A';
                        }
                    }
                    $instructionChunks = array_chunk($filteredInstructions, 3, true);
                @endphp

                <h3>Discharge Criteria:</h3>
                <div class="table-responsive">
                    @foreach($criteriaChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>

                <h3>Discharge Instructions:</h3>
                <div class="table-responsive">
                    @foreach($instructionChunks as $chunk)
                        <table>
                            <thead>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <th>{{ isset(array_keys($chunk)[$i]) ? array_keys($chunk)[$i] : '' }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for($i = 0; $i < 3; $i++)
                                        <td>{{ isset(array_values($chunk)[$i]) ? array_values($chunk)[$i] : '' }}</td>
                                    @endfor
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                @if(!$loop->last)<hr>@endif
            @endforeach
        @endif
    </div>
</body>
</html>