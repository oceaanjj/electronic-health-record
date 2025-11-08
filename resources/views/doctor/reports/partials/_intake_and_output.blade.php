<div class="page-break"></div>

<div>
    <h2 class="section-title">Intake and Output</h2>

    @if($intakeAndOutput->isEmpty())
        <p>No Intake and Output data available.</p>
    @else
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
                @foreach($intakeAndOutput as $item)
                    <tr>
                        <td style="width: 12.5%;">{{ $item->day_no ?? 'N/A' }}</td>
                        <td style="width: 12.5%;">{{ $item->oral_intake ?? 'N/A' }}</td>
                        <td style="width: 12.5%;">{{ $item->iv_fluids_volume ?? 'N/A' }}</td>
                        <td style="width: 12.5%;">{{ $item->urine_output ?? 'N/A' }}</td>
                        <td style="width: 50%;">{{ $item->alert ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>