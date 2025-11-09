<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">Physical Exam</h2>

    {{--
    FIX: Check if the whole list is empty, not just the first item.
    --}}
    @if($physicalExam->isNotEmpty())

        {{--
        FIX: Loop through EACH physical exam.
        $item is now one exam in the loop.
        --}}
        @foreach($physicalExam as $item)

            {{-- This table shows the findings for this specific exam --}}
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

            {{--
            This ADPIE table is now INSIDE the loop,
            so it shows the diagnosis for THIS exam.
            --}}
            <h2 class="section-title">ADPIE
            </h2>
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
                        <td>{{ $item->nursingDiagnoses->diagnosis ?? '-' }}</td>
                        <td>{{ $item->nursingDiagnoses->diagnosis_alert ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Planning</td>
                        <td>{{ $item->nursingDiagnoses->planning ?? '-' }}</td>
                        <td>{{ $item->nursingDiagnoses->planning_alert ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Interventions</td>
                        <td>{{ $item->nursingDiagnoses->intervention ?? '-' }}</td>
                        <td>{{ $item->nursingDiagnoses->intervention_alert ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Evaluation</td>
                        <td>{{ $item->nursingDiagnoses->evaluation ?? '-' }}</td>
                        <td>{{ $item->nursingDiagnoses->evaluation_alert ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- If this is NOT the last exam, add a page break --}}
            @if(!$loop->last)
                <div class="page-break"></div>
            @endif

        @endforeach

    @else
        <p class="no-data">No Physical Exam data available.</p>
    @endif
</div>