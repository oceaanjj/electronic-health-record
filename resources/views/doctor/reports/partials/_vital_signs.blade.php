<div class="page-break"></div>

<div class="section">
    <h2 class="section-title">3. Vital Signs</h2>

    @if($vitals->isEmpty())
        <p class="no-data">No Vital Signs data available.</p>
    @else
        <table class="data-table full-width-vitals">
            <tbody>
                @foreach($vitals as $item)
                    <tr>
                        <th colspan="4">
                            Date: {{ $item->date ?? 'N/A' }}
                        </th>
                        <th colspan="3">
                            Day: {{ $item->day_no ?? 'N/A' }}
                        </th>
                    </tr>

                    <tr class="vitals-measurements-header">
                        <th>Time</th>
                        <th>Temp (°C/°F)</th>
                        <th>HR (bpm)</th>
                        <th>RR (bpm)</th>
                        <th>BP (mmHg)</th>
                        <th>SpO2 (%)</th>
                        <th>Alert</th>
                    </tr>

                    <tr class="vitals-data-row">
                        <td>{{ $item->time ?? '' }}</td>
                        <td>{{ $item->temperature ?? 'N/A' }}</td>
                        <td>{{ $item->hr ?? 'N/A' }}</td>
                        <td>{{ $item->rr ?? 'N/A' }}</td>
                        <td>{{ $item->bp ?? 'N/A' }}</td>
                        <td>{{ $item->spo2 ?? 'N/A' }}</td>
                        <td>{{ $item->alert ?? $item->alerts ?? '' }}</td>
                    </tr>

                    @if(!$loop->last)
                        <tr class="vitals-separator">
                            <td colspan="7">
                                <hr>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
</div>