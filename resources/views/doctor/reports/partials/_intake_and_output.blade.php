<div class="page-break"></div>

<div>
    <h2 class="section-title">Intake and Output</h2>

    @if($intakeAndOutput->isEmpty())
        <p>No Intake and Output data available.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Date</th>
                    <th>Oral Intake</th>
                    <th>IV Fluids Volume</th>
                    <th>Urine Output</th>
                    <th>Alert</th>
                </tr>
            </thead>
            <tbody>
                @foreach($intakeAndOutput as $item)
                    <tr>
                        <td>{{ $item->day_no ?? 'N/A' }}</td>
                        <td>{{ $item->date ?? 'N/A' }}</td>
                        <td>{{ $item->oral_intake ?? 'N/A' }}</td>
                        <td>{{ $item->iv_fluids_volume ?? 'N/A' }}</td>
                        <td>{{ $item->urine_output ?? 'N/A' }}</td>
                        <td>{{ $item->alert ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>