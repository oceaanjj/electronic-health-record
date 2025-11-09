@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    {{-- FORM OVERLAY (ALERT) --}}
    <div id="form-content-container">
        @if (!session('selected_patient_id'))
            <div
                class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                <span class="text-gray-600 font-creato">Please select a patient to input</span>
            </div>
        @endif

   {{-- NEW SEARCHABLE PATIENT DROPDOWN FOR VITAL SIGNS --}}
<div class="header flex items-center gap-6 my-10 mx-auto w-[80%]">
    <form action="{{ route('vital-signs.select') }}" method="POST" id="patient-select-form" class="flex items-center gap-6 w-full">
        @csrf

        {{-- PATIENT NAME --}}
        <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
            PATIENT NAME :
        </label>

        <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('vital-signs.select') }}">
            {{-- Text input for search --}}
            <input
                type="text"
                id="patient_search_input"
                placeholder="Select or type Patient Name"
                value="{{ trim($selectedPatient->name ?? '') }}"
                autocomplete="off"
                class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
            >

            {{-- Dropdown list --}}
            <div
                id="patient_options_container"
                class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"
            >
                @foreach ($patients as $patient)
                    <div
                        class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150"
                        data-value="{{ $patient->patient_id }}"
                    >
                        {{ trim($patient->name) }}
                    </div>
                @endforeach
            </div>

            {{-- Hidden input to store selected patient ID --}}
            <input type="hidden" id="patient_id_hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
        </div>

        {{-- DATE --}}
        <label for="date_selector" class="whitespace-nowrap font-alte font-bold text-dark-green">
            DATE :
        </label>
        <input
            type="date"
            id="date_selector"
            name="date"
            value="{{ $currentDate ?? now()->format('Y-m-d') }}"
            @if (!$selectedPatient) disabled @endif
            class="text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
        >

        {{-- DAY NO --}}
        <label for="day_no" class="whitespace-nowrap font-alte font-bold text-dark-green">
            DAY NO :
        </label>
        <select
            id="day_no_selector"
            name="day_no"
            @if (!$selectedPatient) disabled @endif
            class="w-[120px] text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                   focus:ring-2 focus->ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
        >
            <option value="">-- Select number --</option>
            @for ($i = 1; $i <= 30; $i++)
                <option
                    value="{{ $i }}"
                    @if(($currentDayNo ?? 1) == $i) selected @endif
                >
                    {{ $i }}
                </option>
            @endfor
        </select>
    </form>
</div>
{{-- END SEARCHABLE PATIENT DROPDOWN FOR VITAL SIGNS --}}

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <form id="vitals-form" class="cdss-form" method="POST" action="{{ route('vital-signs.store') }}" data-analyze-url="{{ route('vital-signs.check') }}">
                @csrf

                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
                <input type="hidden" name="date" value="{{ $currentDate ?? now()->format('Y-m-d') }}">
                <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}">

                <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
                    <div class="w-[68%] rounded-[15px] overflow-hidden">

                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="w-[15%] main-header rounded-tl-lg">TIME
                                </th>
                                <th class="w-[18%] main-header">TEMPERATURE</th>
                                <th class="w-[10%] main-header">HR</th>
                                <th class="w-[10%] main-header">RR</th>
                                <th class="w-[10%] main-header">BP</th>
                                <th class="w-[10%] main-header">SpO₂</th>

                            {{-- NOTE: paki-explain saakin ito kasi gagawin kong input text ito--}}
                            @php
                                $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
                            @endphp

                            @foreach ($times as $index => $time)
                                @php
                                    $vitalsRecord = $vitalsData->get($time);
                                    $isLast = $index === count($times) - 1;
                                    $borderClass = $isLast ? '' : 'border-b-2 border-line-brown/70';
                                @endphp

                                <tr class="{{ $borderClass }}">
                                    {{-- TIME COLUMN --}}
                                    <th class="text-center font-semibold py-2 bg-yellow-light text-brown {{ $borderClass }}">
                                        {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                    </th>

                                    {{-- TEMPERATURE --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="number" step="0.1" name="temperature_{{ $time }}" placeholder="temperature"
                                            value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="temperature" data-time="{{ $time }}" pattern="\d*" inputmode="numeric">
                                    </td>

                                    {{-- HR --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="number" name="hr_{{ $time }}" placeholder="bpm"
                                            value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="hr" data-time="{{ $time }}" pattern="\d*" inputmode="numeric">
                                    </td>

                                    {{-- RR --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="number" name="rr_{{ $time }}" placeholder="bpm"
                                            value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="rr" data-time="{{ $time }}" pattern="\d*" inputmode="numeric">
                                    </td>

                                    {{-- BP --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="number" name="bp_{{ $time }}" placeholder="mmHg"
                                            value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="bp" data-time="{{ $time }}" pattern="\d*" inputmode="numeric">
                                    </td>

                                    {{-- SpO₂ --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="number" name="spo2_{{ $time }}" placeholder="%"
                                            value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="spo2" data-time="{{ $time }}" pattern="\d*" inputmode="numeric">
                                </tr>
                            @endforeach

                        </table>
                    </div>

                    <div class="w-[25%] rounded-[15px] overflow-hidden">
                        <div class="main-header rounded-[15px]">
                            ALERTS
                        </div>

                        <table class="w-full border-collapse">
                            @foreach ($times as $time)
                                @php
                                    $vitalsRecord = $vitalsData->get($time);
                                    $severity = optional($vitalsRecord)->news_severity ?? 'NONE';
                                    $color = $severity === 'CRITICAL' ? 'text-red-600'
                                        : ($severity === 'WARNING' ? 'text-orange-500'
                                            : ($severity === 'INFO' ? 'text-blue-500'
                                                : ($severity === 'NONE' ? 'text-white' : 'text-black')));
                                    $alerts = $vitalsRecord ? explode('; ', $vitalsRecord->alerts) : [];
                                @endphp

                                <tr>
                                    <td class="align-middle" data-alert-for-time="{{ $time }}">
                                        <div class="alert-box my-1 py-4 px-3 flex justify-center items-center w-full h-[53px]" data-alert-for-time="{{ $time }}">
                                            {{-- Dynamic alert content will load here --}}
                                            <span class="opacity-70 text-white font-semibold">NO ALERTS</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                </table>
            </div>
        </div>        
    <div class="w-[66%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
            <button type="button" class="button-default">CDSS</button>
            <button type="submit" class="button-default">SUBMIT</button>       
    </div>
 </form>

    <div class="vital-chart-container w-[50%] mx-auto mt-0 mb-20">
            <h2 class="text-center text-dark-green font-bold text-xl mb-4">Vital Sign Trend</h2>
            <canvas id="vitalSignChart" height="120"></canvas>
    </div>
</div>

@push('scripts')
@vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- vital sign chart  -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('vitalSignChart').getContext('2d');
    const labels = ['TEMP', 'HR (bpm)', 'RR (bpm)', 'BP (mmHg)', 'SpO₂ (%)'];
    const timePoints = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];

    const lineColors = [
        '#0D47A1', // rich deep blue
        '#7B1FA2', // royal violet
        '#1B5E20', // dark green
        '#B71C1C', // rich red
        '#37474F', // steel gray
        '#4E342E', // cocoa brown
        '#006064', // deep cyan
        '#512DA8'  // indigo
    ];

    const datasets = [
        @foreach ($times as $index => $time)
        {
            label: '{{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}',
            data: [
                {{ optional($vitalsData->get($time))->temperature ?? 'null' }},
                {{ optional($vitalsData->get($time))->hr ?? 'null' }},
                {{ optional($vitalsData->get($time))->rr ?? 'null' }},
                {{ optional($vitalsData->get($time))->bp ?? 'null' }},
                {{ optional($vitalsData->get($time))->spo2 ?? 'null' }},
            ],
            borderColor: lineColors[{{ $index }} % lineColors.length],
            backgroundColor: lineColors[{{ $index }} % lineColors.length],
            borderWidth: 2.5,
            tension: 0, 
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: false
        },
        @endforeach
    ];


    const vitalChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            animation: {
                duration: 800,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#2c3e50', font: { size: 13, weight: 'bold' } }
                },
                tooltip: {
                    backgroundColor: '#333',
                    titleColor: '#fff',
                    bodyColor: '#f0f0f0'
                }
            },
            scales: {
                x: {
                    ticks: { color: '#2c3e50', font: { weight: 'bold' } },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#2c3e50', font: { weight: 'bold' } },
                    grid: { color: 'rgba(0,0,0,0.1)' }
                }
            }
        }
    });

    document.querySelectorAll('.vital-input').forEach(input => {
        input.addEventListener('input', () => {
            const time = input.getAttribute('data-time');
            const param = input.getAttribute('data-field-name');
            const value = parseFloat(input.value) || null;
            const paramIndex = { temperature: 0, hr: 1, rr: 2, bp: 3, spo2: 4 }[param];
            const datasetIndex = timePoints.indexOf(time);

            if (datasetIndex !== -1 && paramIndex !== undefined) {
                vitalChart.data.datasets[datasetIndex].data[paramIndex] = value;
                vitalChart.update('active');
            }


                let bg = '';
                let color = '#000';

                if (param === 'temperature') {
                    if (value > 37.0) { bg = '#B71C1C'; color = '#fff'; }          
                    else if (value >= 36.3 && value <= 37.0) { bg = '#fff6cf'; }  
                }
                if (param === 'hr') {
                    if (value > 110) { bg = '#B71C1C'; color = '#fff'; }
                    else if (value >= 70 && value <= 110) { bg = '#fff6cf'; }
                }
                if (param === 'rr') {
                    if (value > 22) { bg = '#B71C1C'; color = '#fff'; }
                    else if (value >= 16 && value <= 22) { bg = '#fff6cf'; }
                }
                if (param === 'bp') {
                    if (value > 120) { bg = '#B71C1C'; color = '#fff'; }
                    else if (value >= 90 && value <= 120) { bg = '#fff6cf'; }
                }
                if (param === 'spo2') {
                    if (value < 95) { bg = '#B71C1C'; color = '#fff'; }
                    else if (value >= 95 && value <= 100) { bg = '#fff6cf'; }
                }

                input.style.backgroundColor = bg;
                input.style.color = color;

                        });
                    });
});
</script>
<!-- end of vital sign chart  -->


@endpush

   

@endsection
@push('scripts')
    {{-- Load all necessary script files --}}
    @vite(['resources/js/patient-loader.js', 'resources/js/date-day-loader.js', 'resources/js/vital-signs-alerts.js', 'resources/js/init-searchable-dropdown.js', 'resources/js/page-initializer.js'])

    {{-- Define the specific initializers for this page --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.pageInitializers = [
                window.initializeSearchableDropdown,
                window.initializeDateDayLoader,
                window.initializeVitalSignsAlerts
            ];
        });
    </script>
@endpush