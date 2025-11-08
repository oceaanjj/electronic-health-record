<div class="page-break"></div>
<div class="section">
    <h2 class="section-title">Vital Signs</h2>

    @if($vitals->isEmpty())
        <p class="no-data">No Vital Signs data available.</p>
    @else
        @php
            $currentGroupKey = null;
        @endphp

        <table class="">
            <tbody>
                @foreach($vitals as $item)
                    @php
                        $groupKey = $item->date . '|' . $item->day_no;
                    @endphp

                    @if ($groupKey !== $currentGroupKey)

                        @if ($currentGroupKey !== null)
                                </tbody>
                            </table>
                            <!-- seperator for the next day -->
                            <div style="height: 15px;"></div>
                            <table>
                                <tbody>
                        @endif

                        @php
                            $currentGroupKey = $groupKey;
                        @endphp

                        {{-- HEADER ROW (Date and Day): Colspans adjusted to total 10 (4 + 6) --}}
                        <tr>
                            <th colspan="4">
                                Date: {{ isset($item->date) ? \Carbon\Carbon::parse($item->date)->format('F j, Y') : 'N/A' }}
                            </th>
                            <th colspan="6">
                                Day: {{ $item->day_no ?? 'N/A' }}
                            </th>
                        </tr>

                        {{-- MEASUREMENTS HEADER ROW: Alert colspan set to 4 --}}
                        <tr>
                            <th>Time</th>
                            <th>Temp (°C/°F)</th>
                            <th>HR (bpm)</th>
                            <th>RR (bpm)</th>
                            <th>BP (mmHg)</th>
                            <th>SpO2 (%)</th>
                            <th colspan="4">Alert</th>
                        </tr>
                    @endif

                    {{-- DATA ROW: Alert colspan set to 4 --}}
                    <tr>
                        <td>{{ isset($item->time) ? \Carbon\Carbon::parse($item->time)->format('h:i A') : '' }}</td>

                        <td>{{ $item->temperature ?? 'N/A' }}</td>
                        <td>{{ $item->hr ?? 'N/A' }}</td>
                        <td>{{ $item->rr ?? 'N/A' }}</td>
                        <td>{{ $item->bp ?? 'N/A' }}</td>
                        <td>{{ $item->spo2 ?? 'N/A' }}</td>
                        <td colspan="4">
                            @php
                                $alertsString = $item->alert ?? $item->alerts ?? '';
                                $alertsArray = array_filter(explode('; ', $alertsString));
                            @endphp
                            @if (!empty($alertsArray))
                                <ul>
                                    @foreach ($alertsArray as $alert)
                                        <li> • {{ trim($alert) }}</li>
                                    @endforeach
                                </ul>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>

                    {{-- SEPARATOR: Colspan set to 10 --}}
                    @if ($groupKey === $currentGroupKey && !$loop->last)
                        <tr>
                            <td colspan="10">
                                <hr>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
</div>