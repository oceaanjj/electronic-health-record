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
                @if(!$loop->last)
                <hr>@endif
            @endforeach
        @endif
    </div>