<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">Medication Reconciliation</h2>

    <h3>Patient's Current Medication</h3>

    @if($currentMedication->isEmpty())
        <p class="no-data">No Current Medication data available.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Dose</th>
                    <th>Route</th>
                    <th>Frequency</th>
                    <th>Indication</th>
                    <th>Administered during stay?</th>
                </tr>
            </thead>
            <tbody>
                @foreach($currentMedication as $item)
                    <tr>
                        <td>{{ $item->current_med ?? '-' }}</td>
                        <td>{{ $item->current_dose ?? '-' }}</td>
                        <td>{{ $item->current_route ?? '-' }}</td>
                        <td>{{ $item->current_frequency ?? '-' }}</td>
                        <td>{{ $item->current_indication ?? '-' }}</td>
                        <td>{{ $item->current_text ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h3>Patient's Home Medication</h3>

    @if($homeMedication->isEmpty())
        <p>No Home Medication data available.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Dose</th>
                    <th>Route</th>
                    <th>Frequency</th>
                    <th>Indication</th>
                    <th>Discontinued on admission?</th>
                </tr>
            </thead>
            <tbody>
                @foreach($homeMedication as $item)
                    <tr>
                        <td>{{ $item->home_med ?? '-' }}</td>
                        <td>{{ $item->home_dose ?? '-' }}</td>
                        <td>{{ $item->home_route ?? '-' }}</td>
                        <td>{{ $item->home_frequency ?? '-' }}</td>
                        <td>{{ $item->home_indication ?? '-' }}</td>
                        <td>{{ $item->home_text ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h3>Changes in Medication During Hospitalization</h3>

    @if($changesInMedication->isEmpty())
        <p>No Changes in Medication data available.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Dose</th>
                    <th>Route</th>
                    <th>Frequency</th>
                    <th>Reason for change</th>
                </tr>
            </thead>
            <tbody>
                @foreach($changesInMedication as $item)
                    <tr>
                        <td>{{ $item->change_med ?? '-' }}</td>
                        <td>{{ $item->change_dose ?? '-' }}</td>
                        <td>{{ $item->change_route ?? '-' }}</td>
                        <td>{{ $item->change_frequency ?? '-' }}</td>
                        <td>{{ $item->change_text ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>