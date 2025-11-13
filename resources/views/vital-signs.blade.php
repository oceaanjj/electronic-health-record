@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

<!-- HEADER -->
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

        <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('vital-signs.select') }}" data-admission-date="{{ $selectedPatient->admission_date ?? '' }}">
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
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm bg-gray-100"
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
            @for ($i = 1; $i <= ($totalDaysSinceAdmission ?? 30); $i++)
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
{{-- END OF HEADER--}}

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <form id="vitals-form" class="cdss-form" method="POST" action="{{ route('vital-signs.store') }}"
                data-analyze-url="{{ route('vital-signs.check') }}"
                data-times="{{ json_encode($times) }}"
                data-fetch-url="{{ route('vital-signs.fetch-data') }}"
                data-alert-height-class="h-[55px]">
                @csrf


                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
                <input type="hidden" id="hidden_date_for_vitals_form" name="date" value="{{ $currentDate ?? now()->format('Y-m-d') }}">
                <input type="hidden" id="hidden_day_no_for_vitals_form" name="day_no" value="{{ $currentDayNo ?? 1 }}">

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
                                    <input type="text" name="temperature_{{ $time }}" placeholder="temperature"
                                        value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                                        class="cdss-input vital-input h-[60px]" data-field-name="temperature" data-time="{{ $time }}">
                                </td>

                                {{-- HR --}}
                                <td class="bg-beige {{ $borderClass }}">
                                    <input type="text" name="hr_{{ $time }}" placeholder="bpm"
                                        value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                                        class="cdss-input vital-input h-[60px]" data-field-name="hr" data-time="{{ $time }}">
                                </td>

                                {{-- RR --}}
                                <td class="bg-beige {{ $borderClass }}">
                                    <input type="text" name="rr_{{ $time }}" placeholder="bpm"
                                        value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                                        class="cdss-input vital-input h-[60px]" data-field-name="rr" data-time="{{ $time }}">
                                </td>

                                {{-- BP --}}
                                <td class="bg-beige {{ $borderClass }}">
                                    <input type="text" name="bp_{{ $time }}" placeholder="mmHg"
                                        value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                                        class="cdss-input vital-input h-[60px]" data-field-name="bp" data-time="{{ $time }}">
                                </td>

                                {{-- SpO₂ --}}
                                <td class="bg-beige {{ $borderClass }}">
                                    <input type="text" name="spo2_{{ $time }}" placeholder="%"
                                        value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                                        class="cdss-input vital-input h-[60px]" data-field-name="spo2" data-time="{{ $time }}">
                                </td>

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
                @if (isset($vitalsData) && $vitalsData->count() > 0)
        <button type="submit" formaction="{{ route('vital-signs.cdss') }}"
            class="button-default text-center">
            CDSS
        </button>
    @endif
            <button type="submit" class="button-default">SUBMIT</button>       
    </div>


 </form>

 {{--  <div class="vital-chart-container w-[50%] mx-auto mt-0 mb-20">
            <h2 class="text-center text-dark-green font-bold text-xl mb-4">Vital Sign Trend</h2>
            <canvas id="vitalSignChart" height="120"></canvas>
    </div> --}}
    


            <div class="vital-chart-container w-[50%] mx-auto mt-10 mb-20 space-y-12">
                <div>
                    <h2 class="text-center text-dark-green font-bold text-xl mb-4">Temperature Trend</h2>
                    <canvas id="tempChart" height="120"></canvas>
                </div>
            </div>

            <div class="vital-chart-container w-[50%] mx-auto mt-10 mb-20 space-y-12">
                <div>
                    <h2 class="text-center text-dark-green font-bold text-xl mb-4">Heart Rate Trend</h2>
                    <canvas id="hrChart" height="120"></canvas>
                </div>
            </div>

            <div class="vital-chart-container w-[50%] mx-auto mt-10 mb-20 space-y-12">
                <div>
                    <h2 class="text-center text-dark-green font-bold text-xl mb-4">Respiratory Rate Trend</h2>
                    <canvas id="rrChart" height="120"></canvas>
                </div>
            </div>

            <div class="vital-chart-container w-[50%] mx-auto mt-10 mb-20 space-y-12">
                <div>
                    <h2 class="text-center text-dark-green font-bold text-xl mb-4">Blood Pressure Trend</h2>
                    <canvas id="bpChart" height="120"></canvas>
                </div>
            </div>


            <div class="vital-chart-container w-[50%] mx-auto mt-10 mb-20 space-y-12">
                <div>
                    <h2 class="text-center text-dark-green font-bold text-xl mb-4">SpO₂ Trend</h2>
                    <canvas id="spo2Chart" height="120"></canvas>
                </div>
            </div>



</div>

@push('scripts')
@vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- vital sign chart  -->
<!-- vital sign charts -->
<!-- vital sign charts -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const timePoints = @json($times); // from PHP
    const vitalsData = @json($vitalsData); // convert your PHP collection to JSON safely

    const lineColors = [
        '#0D47A1', '#7B1FA2', '#1B5E20', '#B71C1C',
        '#37474F', '#4E342E', '#006064', '#512DA8'
    ];

    // Define all vitals
    const vitals = {
        temperature: { label: 'Temperature (°C)', elementId: 'tempChart', field: 'temperature' },
        hr:          { label: 'Heart Rate (bpm)', elementId: 'hrChart', field: 'hr' },
        rr:          { label: 'Respiratory Rate (bpm)', elementId: 'rrChart', field: 'rr' },
        bp:          { label: 'Blood Pressure (mmHg)', elementId: 'bpChart', field: 'bp' },
        spo2:        { label: 'SpO₂ (%)', elementId: 'spo2Chart', field: 'spo2' }
    };

    // Create chart for each vital type
    Object.entries(vitals).forEach(([key, vital]) => {
        const ctx = document.getElementById(vital.elementId)?.getContext('2d');
        if (!ctx) return;

        // Build data values for this vital from PHP JSON
        const dataValues = timePoints.map(time => {
            const record = vitalsData?.[time];
            return record ? parseFloat(record[vital.field]) || null : null;
        });

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: timePoints.map(t => {
                    if (!t) return 'N/A';
                    const [hour, minute] = t.split(':');
                    if (hour === undefined || minute === undefined) return t;
                    const h = ((+hour + 11) % 12) + 1;
                    const suffix = +hour >= 12 ? 'PM' : 'AM';
                    return `${h}:${minute} ${suffix}`;
                }),
                datasets: [{
                    label: vital.label,
                    data: dataValues,
                    borderColor: lineColors[0],
                    backgroundColor: lineColors[0],
                    borderWidth: 2.5,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                animation: { duration: 800, easing: 'easeOutQuart' },
                plugins: {
                    legend: {
                        position: 'top',
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

        // 🔄 Live update chart when user edits input fields
        document.querySelectorAll(`input[data-field-name="${vital.field}"]`).forEach(input => {
            input.addEventListener('input', () => {
                const time = input.getAttribute('data-time');
                const value = parseFloat(input.value) || null;
                const index = timePoints.indexOf(time);
                if (index !== -1) {
                    chart.data.datasets[0].data[index] = value;
                    chart.update('active');
                }
            });
        });
    });
});
</script>
@endpush
@endsection


@push('scripts')
    {{-- Load all necessary script files --}}
    @vite([
    'resources/js/patient-loader.js', 
    'resources/js/date-day-loader.js', 
    'resources/js/alert.js', 
    'resources/js/init-searchable-dropdown.js', 
    'resources/js/vital-signs-date-sync.js'
    ])

    {{-- Define the specific initializers for this page --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.pageInitializers = [
                window.initializeSearchableDropdown,
                window.initializeDateDayLoader,
                // window.initializeVitalSignsAlerts,
                window.initializeVitalSignsDateSync
            ];
        });
    </script>
@endpush