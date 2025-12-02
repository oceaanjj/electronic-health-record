<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">Intake and Output</h2>

    @if($intakeAndOutput->isNotEmpty())

        @foreach($intakeAndOutput as $item)

            <table style="width: 100%;">
                <thead>
                    <tr>
                        {{-- Smaller Columns (12.5% each) --}}
                        <th style="width: 12.5%;">Day</th>
                        <th style="width: 12.5%;">Oral Intake</th>
                        <th style="width: 12.5%;">IV Fluids</th>
                        <th style="width: 12.5%;">Urine Output</th>
                        {{-- Alert Column (50%) - 4 times 12.5% --}}
                        <th style="width: 50%;">Alert</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 12.5%;">{{ $item->day_no ?? '-' }}</td>
                        <td style="width: 12.5%;">{{ $item->oral_intake ?? '-' }}</td>
                        <td style="width: 12.5%;">{{ $item->iv_fluids_volume ?? '-' }}</td>
                        <td style="width: 12.5%;">{{ $item->urine_output ?? '-' }}</td>
                        <td style="width: 50%;">{{ $item->alert ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>


            <!-- ADPIE Table -->

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


        @endforeach

    @else
        <p class="no-data">No Intake and Output data available.</p>
    @endif

</div>