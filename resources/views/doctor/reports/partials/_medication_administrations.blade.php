<div class="page-break"></div>
<div class="section">
    <h2 class="section-title">Medication Administrations</h2>

    @if($medicationAdministrations->isEmpty())
        <p>No Medication Administration data available.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Medication</th>
                    <th>Dose</th>
                    <th>Route</th>
                    <th>Frequency</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicationAdministrations as $item)
                    <tr>
                        <td>{{ isset($item->date) ? \Carbon\Carbon::parse($item->date)->format('F j, Y') : '-' }}</td>
                        <td>{{ isset($item->time) ? \Carbon\Carbon::parse($item->time)->format('h:i A') : '-' }}</td>
                        <td>{{ $item->medication ?? '-' }}</td>
                        <td>{{ $item->dose ?? '-' }}</td>
                        <td>{{ $item->route ?? '-' }}</td>
                        <td>{{ $item->frequency ?? '-' }}</td>
                        <td>{{ $item->comments ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>