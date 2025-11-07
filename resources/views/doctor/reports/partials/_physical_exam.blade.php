<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">2. Physical Exam</h2>

    @forelse($physicalExam as $item)
        @php
            $excludedColumns = ['id', 'patient_id', 'medical_id', 'created_at', 'updated_at', 'deleted_at'];
            $rawAttributes = $item->getAttributes();
            $pairedAttributes = [];

            // The key is the base column name (the one without '_alert').
            // The value is an array: [Category Header, Component Header]
            $baseColumns = [
                'general_appearance' => ['General Appearance', 'Alert'],
                'skin_condition' => ['Skin Condition', 'Alert'],
                'eye_condition' => ['Eye Condition', 'Alert'],
                'oral_condition' => ['Oral Condition', 'Alert'],
                'cardiovascular' => ['Cardiovascular', 'Alert'],
                'abdomen_condition' => ['Abdomen Condition', 'Alert'],
                'extremities' => ['Extremities', 'Alert'],
                'neurological' => ['Neurological', 'Alert'],
            ];

            foreach ($baseColumns as $baseName => $labels) {
                $alertName = $baseName . '_alert';

                // Special case handling where the base column already has a suffix
                if ($baseName === 'skin_condition') {
                    $alertName = 'skin_alert';
                } elseif ($baseName === 'eye_condition') {
                    $alertName = 'eye_alert';
                } elseif ($baseName === 'oral_condition') {
                    $alertName = 'oral_alert';
                } elseif ($baseName === 'abdomen_condition') {
                    $alertName = 'abdomen_alert';
                }

                // Check if the base column exists and is not excluded
                if (isset($rawAttributes[$baseName]) && !in_array($baseName, $excludedColumns)) {

                    // Structure for the <table> with two columns
                    $pairedAttributes[] = [
                        'header1' => $labels[0], // e.g., 'Skin Condition'
                        'value1' => $rawAttributes[$baseName],
                        'header2' => $labels[1], // e.g., 'Alert'
                        'value2' => isset($rawAttributes[$alertName]) ? $rawAttributes[$alertName] : '',
                    ];
                }
            }
        @endphp

        <div class="table-responsive">
            @foreach($pairedAttributes as $pair)
                <table>
                    <thead>
                        <tr>
                            {{-- First Column Header (Category) --}}
                            <th>{{ $pair['header1'] }}</th>
                            {{-- Second Column Header (Alert/Component) --}}
                            <th>{{ $pair['header2'] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            {{-- First Column Data --}}
                            <td>{{ $pair['value1'] }}</td>
                            {{-- Second Column Data --}}
                            <td>{{ $pair['value2'] }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        </div>
        @if(!$loop->last)
        <hr>@endif
    @empty
        <p class="no-data">No Physical Exam data available.</p>
    @endforelse
</div>