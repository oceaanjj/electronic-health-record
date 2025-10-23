@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    {{-- (Walang pagbabago sa header form) --}}
    <div class="header">
        <form action="{{ route('vital-signs.select') }}" method="POST" id="patient-select-form"
            class="flex items-center space-x-4">
            @csrf
            <label for="patient_id" style="color: white;">PATIENT NAME :</label>
            <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                <option value="" {{ old('patient_id', session('selected_patient_id')) == '' ? 'selected' : '' }}>-- Select
                    Patient --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->patient_id }}" {{ old('patient_id', session('selected_patient_id')) == $patient->patient_id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
            <label for="date" style="color: white;">DATE :</label>
            <input class="date" type="date" id="date_selector" name="date"
                value="{{ old('date', session('selected_date')) }}" onchange="this.form.submit()">
            <label for="day_no" style="color: white;">DAY NO :</label>
            <select id="day_no" name="day_no" onchange="this.form.submit()">
                <option value="">-- Select number --</option>
                @for ($i = 1; $i <= 30; $i++)
                    <option value="{{ $i }}" {{ old('day_no', session('selected_day_no')) == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </form>
    </div>

    <form id="vitals-form" method="POST" action="{{ route('vital-signs.store') }}">
        @csrf

        <input type="hidden" name="patient_id" value="{{ old('patient_id', session('selected_patient_id')) }}">
        <input type="hidden" name="date" value="{{ old('date', session('selected_date')) }}">
        <input type="hidden" name="day_no" value="{{ old('day_no', session('selected_day_no')) }}">

        <table>
            <tr>
                <th class="title">TIME</th>
                <th class="title">TEMPERATURE</th>
                <th class="title">HR (bpm)</th>
                <th class="title">RR (bpm)</th>
                <th class="title">BP (mmHg)</th>
                <th class="title">SpO2 (%)</th>
                <th class="title">Alerts</th>
            </tr>

            @php
                $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
            @endphp

            @foreach ($times as $time)
                @php
                    $vitalsRecord = $vitalsData->get($time);
                    // Tinanggal na ang $alertInfo = session(...)
                @endphp
                <tr>
                    {{-- (Ang mga input fields ay pareho pa rin) --}}
                    <th class="time">{{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}</th>
                    <td>
                        <input type="text" name="temperature_{{ $time }}" placeholder="temperature"
                            value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                            class="vital-input" data-param="temperature" data-time="{{ $time }}">
                    </td>
                    <td>
                        <input type="text" name="hr_{{ $time }}" placeholder="HR"
                            value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                            class="vital-input" data-param="hr" data-time="{{ $time }}">
                    </td>
                    <td>
                        <input type="text" name="rr_{{ $time }}" placeholder="RR"
                            value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                            class="vital-input" data-param="rr" data-time="{{ $time }}">
                    </td>
                    <td>
                        <input type="text" name="bp_{{ $time }}" placeholder="BP"
                            value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                            class="vital-input" data-param="bp" data-time="{{ $time }}">
                    </td>
                    <td>
                        <input type="text" name="spo2_{{ $time }}" placeholder="SpO2"
                            value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                            class="vital-input" data-param="spo2" data-time="{{ $time }}">
                    </td>

                    {{-- ====================================================== --}}
                    {{-- UPDATED ALERTS CELL (Bumabasa na sa DB)               --}}
                    {{-- ====================================================== --}}
                    <td class="alert-cell" data-time="{{ $time }}">
                        
                        {{-- Bumabasa na sa $vitalsRecord->alerts (galing DB) --}}
                        @if (optional($vitalsRecord)->alerts)
                            @php
                                // Bumabasa sa severity column mo
                                $severity = optional($vitalsRecord)->news_severity ?? 'NONE';
                                $color = $severity === 'CRITICAL' ? 'red'
                                        : ($severity === 'WARNING' ? 'orange'
                                        : ($severity === 'INFO' ? 'blue' 
                                        : ($severity === 'NONE' ? 'green' : 'black')));
                                
                                // Hahatiin ang summary string para gawing listahan
                                $alerts = explode('; ', $vitalsRecord->alerts);
                            @endphp
                            
                            @if ($severity !== 'NONE') 
                                {{-- Ipakita as list kung multiple alerts --}}
                                <ul style="margin: 0; padding-left: 15px; color: {{ $color }}; text-align: left;">
                                    @foreach($alerts as $alert)
                                        <li><span style="font-weight: bold; font-size: 0.9em;">{{ $alert }}</span></li>
                                    @endforeach
                                </ul>
                            @else
                                {{-- Ipakita lang ng simple kung "Vitals stable." --}}
                                <span style="color: {{ $color }}; font-size: 0.9em;">
                                    {{ $vitalsRecord->alerts }}
                                </span>
                            @endif
                        @endif
                    </td>
                    {{-- ====================================================== --}}

                </tr>
            @endforeach
        </table>
    </form>
    </div>

    <div class="buttons">
        <button class="btn">CDSS</button>
        <button class="btn" type="button" onclick="document.getElementById('vitals-form').submit();">Submit</button>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/vital-signs-style.css', 'resources/css/act-of-daily-living.css'])
@endpush

@push('scripts')
{{-- ====================================================== --}}
{{-- WALANG PAGBABAGO SA JAVASCRIPT --}}
{{-- ====================================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.vital-input');
        const checkVitalsUrl = '{{ route('vital-signs.check') }}'; 
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        inputs.forEach(input => {
            input.addEventListener('input', function(e) {
                const param = e.target.dataset.param;
                const time = e.target.dataset.time;
                const value = e.target.value;
                const alertCell = document.querySelector(`.alert-cell[data-time="${time}"]`);
                getRealTimeAlert(param, value, alertCell);
            });
        });

        async function getRealTimeAlert(param, value, cell) {
            // Linisin ang real-time alert kung blangko ang input
            const oldAlert = cell.querySelector('.real-time-alert');
            if (value === null || value.trim() === '') {
                 if (oldAlert) oldAlert.remove();
                 return;
            }

            try {
                const response = await fetch(checkVitalsUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        param: param,
                        value: value
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json(); 
                updateAlertCell(cell, data.alert, data.severity);

            } catch (error) {
                console.error('Error fetching vital check:', error);
                // Iwasang burahin ang laman ng cell kung mag-error
            }
        }

        function updateAlertCell(cell, alertText, severity) {
            let color = 'green';
            switch (severity) {
                case 'CRITICAL' 'SEVERE': color = 'red'; break;
                case 'WARNING': color = 'orange'; break;
                case 'INFO': color = 'blue'; break;
                case 'NONE': color = 'green'; break;
            }
            
            // Hanapin at tanggalin ang *dating* real-time alert
            const oldAlert = cell.querySelector('.real-time-alert');
            if (oldAlert) oldAlert.remove();

            // Ipakita lang ang alert kung HINDI ito 'NONE' o 'Normal'
            if (severity !== 'NONE' && alertText && !alertText.toLowerCase().includes('normal')) {
                const alertSpan = document.createElement('span');
                alertSpan.className = 'real-time-alert'; // Para madaling i-target
                alertSpan.style.color = color;
                alertSpan.style.display = 'block'; 
                alertSpan.style.fontSize = '0.9em';
                alertSpan.innerText = `(${alertText})`; // Lagyan ng parenthesis
                
                // Idagdag sa dulo ng cell
                cell.appendChild(alertSpan);
            }
        }
    });
</script>
@endpush