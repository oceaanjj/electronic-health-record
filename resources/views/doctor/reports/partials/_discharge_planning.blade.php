<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">Discharge Planning</h2>

    @if($dischargePlanning->isEmpty())
        <p class="no-data">No Discharge Planning data available.</p>
    @else

        @foreach($dischargePlanning as $item)

            <table>
                <thead>
                    <tr>
                        <th>Discharge Criteria</th>
                        <th>Required Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Fever Resolution</td>
                        <td>{{ $item->criteria_feverRes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Normalization of Patient Count</td>
                        <td>{{ $item->criteria_patientCount ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Manage Fever Effectively</td>
                        <td>{{ $item->criteria_manageFever ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Manage Fever Effectively 2</td>
                        <td>{{ $item->criteria_manageFever2 ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>


            <h2 class="section-title">Discharge Instructions</h2>

            <table>
                <thead>
                    <tr>
                        <th>Instruction</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Medications</td>
                        <td>{{ $item->instruction_med ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Follow-Up Appointment</td>
                        <td>{{ $item->instruction_appointment ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Fluid Intake</td>
                        <td>{{ $item->instruction_fluidIntake ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Avoid Mosquito Exposure</td>
                        <td>{{ $item->instruction_exposure ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Monitor for Signs of Complications</td>
                        <td>{{ $item->instruction_complications ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            @if(!$loop->last)
                <hr>
            @endif
        @endforeach
    @endif
</div>