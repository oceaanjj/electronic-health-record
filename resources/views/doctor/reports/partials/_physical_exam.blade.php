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

        <h2 class="section-title" style="background-color: #a97c00ff;">ADPIE</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Nurse Assessment</th>
                    <th>CDSS Recommendation</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Diagnosis</td>
                    <td>{{ $item->nurse_diagnosis ?? '-' }}</td>
                    <td>{{ $item->cdss_diagnosis_recommendation ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Planning</td>
                    <td>{{ $item->nurse_planning ?? '-' }}</td>
                    <td>{{ $item->cdss_planning_recommendation ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Interventions</td>
                    <td>{{ $item->nurse_interventions ?? '-' }}</td>
                    <td>{{ $item->cdss_interventions_recommendation ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Evaluation</td>
                    <td>{{ $item->nurse_evaluation ?? '-' }}</td>
                    <td>{{ $item->cdss_evaluation_recommendation ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="no-data">No Physical Exam data available.</p>
    @endif
</div>