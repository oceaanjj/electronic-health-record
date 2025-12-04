<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">Activities of Daily Living</h2>

    @php
        $adl = $actOfDailyLiving;
    @endphp
    @if($adl)

        @foreach($adl as $item)
            <table>
                <tbody>
                    <tr>
                        <th colspan="2">
                            Date: {{ isset($item->date) ? \Carbon\Carbon::parse($item->date)->format('F j, Y') : '-' }}
                        </th>
                        <th colspan="1">
                            Day: {{ $item->day_no ?? '-' }}
                        </th>
                    </tr>

                    <tr>
                        <th>Category</th>
                        <th>Findings</th>
                        <th>Alerts</th>
                    </tr>


                    {{-- Mobility --}}
                    <tr>
                        <td>Mobility</td>
                        <td>{{ $item->mobility_assessment ?? '-' }}</td>
                        <td>{{ $item->mobility_alert ?? '-' }}</td>
                    </tr>

                    {{-- Hygiene --}}
                    <tr>
                        <td>Hygiene</td>
                        <td>{{ $item->hygiene_assessment ?? '-' }}</td>
                        <td>{{ $item->hygiene_alert ?? '-' }}</td>
                    </tr>

                    {{-- Toileting --}}
                    <tr>
                        <td>Toileting</td>
                        <td>{{ $item->toileting_assessment ?? '-' }}</td>
                        <td>{{ $item->toileting_alert ?? '-' }}</td>
                    </tr>

                    {{-- Feeding --}}
                    <tr>
                        <td>Feeding</td>
                        <td>{{ $item->feeding_assessment ?? '-' }}</td>
                        <td>{{ $item->feeding_alert ?? '-' }}</td>
                    </tr>

                    {{-- Hydration --}}
                    <tr>
                        <td>Hydration</td>
                        <td>{{ $item->hydration_assessment ?? '-' }}</td>
                        <td>{{ $item->hydration_alert ?? '-' }}</td>
                    </tr>

                    {{-- Sleep Pattern --}}
                    <tr>
                        <td>Sleep Pattern</td>
                        <td>{{ $item->sleep_pattern_assessment ?? '-' }}</td>
                        <td>{{ $item->sleep_pattern_alert ?? '-' }}</td>
                    </tr>

                    {{-- Pain Level --}}
                    <tr>
                        <td>Pain Level</td>
                        <td>{{ $item->pain_level_assessment ?? '-' }}</td>
                        <td>{{ $item->pain_level_alert ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>



            <!-- ADPIE Table -->

            <h2 class="section-title-adpie">ADPIE</h2>
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


            @if(!$loop->last)
                <hr class="pdf-hr">
            @endif


        @endforeach

    @else
        <p class="no-data">No Activities of Daily Living data available.</p>
    @endif
</div>