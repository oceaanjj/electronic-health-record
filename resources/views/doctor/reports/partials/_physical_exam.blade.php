<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">2. Physical Exam</h2>

    @forelse($physicalExam as $item)
        @php
            $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
            $rawAttributes = $item->getAttributes();
            $examRows = []; // Array to hold the final 3-column rows

            // Define the base categories and their desired System label.
            $systemMap = [
                'general_appearance' => 'General Appearance',
                'skin_condition' => 'Skin Condition',
                'eye_condition' => 'Eye Condition',
                'oral_condition' => 'Oral Condition',
                'cardiovascular' => 'Cardiovascular',
                'abdomen_condition' => 'Abdomen Condition',
                'extremities' => 'Extremities',
                'neurological' => 'Neurological',
            ];

            foreach ($systemMap as $baseName => $systemLabel) {
                // 1. Determine the name of the findings/condition column (usually the base name itself)
                $findingsName = $baseName;

                // 2. Determine the name of the corresponding alert column
                $alertName = $baseName . '_alert';

                // Handle specific mappings for alert columns where the base name has a suffix
                if (in_array($baseName, ['skin_condition', 'eye_condition', 'oral_condition', 'abdomen_condition'])) {
                    // Example: 'skin_condition' becomes 'skin_alert'
                    $alertName = str_replace('_condition', '_alert', $baseName);
                }

                // --- Data Extraction and Filtering ---

                $findingsValue = isset($rawAttributes[$findingsName]) ? $rawAttributes[$findingsName] : '';
                $alertValue = isset($rawAttributes[$alertName]) ? $rawAttributes[$alertName] : '';

                // Check if the base column exists and is not excluded, AND has a value OR an alert value
                if (
                    isset($rawAttributes[$findingsName]) &&
                    !in_array($findingsName, $excludedColumns) &&
                    ($findingsValue !== null && $findingsValue !== '' || $alertValue !== null && $alertValue !== '')
                ) {
                    // Structure for the consolidated 3-column row
                    $examRows[] = [
                        'system' => $systemLabel,
                        'findings' => $findingsValue,
                        'alerts' => $alertValue,
                    ];
                }
            }
        @endphp

        <div class="table-responsive">
            <table>
                {{-- Single Header Row for the entire table --}}
                <thead>
                    <tr>
                        <th>System</th>
                        <th>Findings</th>
                        <th>Alerts</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop through the prepared 3-column rows --}}
                    @foreach($examRows as $row)
                        <tr>
                            <td>{{ $row['system'] }}</td>
                            <td>{{ $row['findings'] }}</td>
                            <td>{{ $row['alerts'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(!$loop->last)
        <hr>@endif
    @empty
        <p class="no-data">No Physical Exam data available.</p>
    @endforelse
</div>