<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">Physical Exam</h2>

    @php
        $item = $physicalExam->first() ?? null;
    @endphp

    @if($item)
        <table class="data-table">
            <thead>
                <tr>
                    <th>System</th>
                    <th>Findings</th>
                    <th>Alerts</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>General Appearance</td>
                    <td>{{ $item->general_appearance ?? '-' }}</td>
                    <td>{{ $item->general_appearance_alert ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Skin Condition</td>
                    <td>{{ $item->skin_condition ?? '-' }}</td>
                    <td>{{ $item->skin_alert ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Eye Condition</td>
                    <td>{{ $item->eye_condition ?? '-' }}</td>
                    <td>{{ $item->eye_alert ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Oral Condition</td>
                    <td>{{ $item->oral_condition ?? '-' }}</td>
                    <td>{{ $item->oral_alert ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Cardiovascular</td>
                    <td>{{ $item->cardiovascular ?? '-' }}</td>
                    <td>{{ $item->cardiovascular_alert ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Abdomen Condition</td>
                    <td>{{ $item->abdomen_condition ?? '-' }}</td>
                    <td>{{ $item->abdomen_alert ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Extremities</td>
                    <td>{{ $item->extremities ?? '-' }}</td>
                    <td>{{ $item->extremities_alert ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Neurological</td>
                    <td>{{ $item->neurological ?? '-' }}</td>
                    <td>{{ $item->neurological_alert ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="no-data">No Physical Exam data available.</p>
    @endif
</div>