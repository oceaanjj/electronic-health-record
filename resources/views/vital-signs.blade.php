@extends('layouts.app')
@section('title', 'Patient Vital Signs')
@section('content')
<style>
/*
    #chart-viewport {
    overflow: hidden;
    height: 530px; 
    position: relative;
    }

    #chart-track > div:not(:first-child) {
        margin-top: 10px;
    }

    #chart-track > div:not(:last-child) {
        margin-bottom: 10px;
    }

    #chart-track {
        padding-top: 50px;
        padding-bottom: 40px;
}*/

#chart-viewport {
    height: 530px;
    overflow: hidden;
    position: relative;
}


#chart-track {
    padding-top: 50px;
    padding-bottom: 40px;
}

#chart-track > div {
    margin: 10px 0;
}

</style>

    <div id="form-content-container">
        

        {{-- NEW SEARCHABLE PATIENT DROPDOWN FOR VITAL SIGNS --}}
        <div class="header mx-auto my-10 flex w-[80%] items-center gap-6">
            <form
                action="{{ route('vital-signs.select') }}"
                method="POST"
                id="patient-select-form"
                class="flex w-full items-center gap-6"
            >
                @csrf

                {{-- PATIENT NAME --}}
                <label for="patient_search_input" class="font-alte text-dark-green font-bold whitespace-nowrap">
                    PATIENT NAME :
                </label>

                {{--
                    UPDATED:
                    - Added data-sync-mode="json-vitals"
                --}}
                <div
                    class="searchable-dropdown relative w-[400px]"
                    data-select-url="{{ route('vital-signs.select') }}"
                    data-admission-date="{{ $selectedPatient ? \Carbon\Carbon::parse($selectedPatient->admission_date)->format('Y-m-d') : '' }}"
                    data-sync-mode="json-vitals"
                >
                    {{-- Text input for search --}}
                    <input
                        type="text"
                        id="patient_search_input"
                        placeholder="Select or type Patient Name"
                        value="{{ trim($selectedPatient->name ?? '') }}"
                        autocomplete="off"
                        class="font-creato-bold w-full rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                    />

                    {{-- Dropdown list --}}
                    <div
                        id="patient_options_container"
                        class="absolute z-50 mt-2 hidden max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
                    >
                        @foreach ($patients as $patient)
                            <div
                                class="option cursor-pointer px-4 py-2 transition duration-150 hover:bg-blue-100"
                                data-value="{{ $patient->patient_id }}"
                            >
                                {{ trim($patient->name) }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Hidden input to store selected patient ID --}}
                    <input
                        type="hidden"
                        id="patient_id_hidden"
                        name="patient_id"
                        value="{{ $selectedPatient->patient_id ?? '' }}"
                    />
                </div>

                {{-- DATE --}}
                <label for="date_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">DATE :</label>

                <input
                    type="date"
                    id="date_selector"
                    name="date"
                    value="{{ $currentDate ?? now()->format('Y-m-d') }}"
                    @if (!$selectedPatient) disabled @endif
                    class="font-creato-bold rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                />

                {{-- DAY NO --}}
                <label for="day_no" class="font-alte text-dark-green font-bold whitespace-nowrap">DAY NO :</label>

                <select
                    id="day_no_selector"
                    name="day_no"
                    @if (!$selectedPatient) disabled @endif
                    class="font-creato-bold focus->ring-blue-500 w-[120px] rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2"
                >
                    @for ($i = 1; $i <= $totalDaysSinceAdmission; $i++)
                        <option value="{{ $i }}" @if($currentDayNo == $i) selected @endif>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </form>
        </div>
        {{-- END OF HEADER --}}

        {{-- MAIN TABLE FOR INPUTS --}}

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <form
                id="vitals-form"
                class="cdss-form"
                method="POST"
                action="{{ route('vital-signs.store') }}"
                data-analyze-url="{{ route('vital-signs.check') }}"
                data-batch-analyze-url="{{ route('vital-signs.analyze-batch') }}"
                data-times="{{ json_encode($times) }}"
                data-fetch-url="{{ route('vital-signs.fetch-data') }}"
                data-alert-height-class="h-[55px]"
            >
                @csrf

                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
                <input
                    type="hidden"
                    id="hidden_date_for_vitals_form"
                    name="date"
                    value="{{ $currentDate ?? now()->format('Y-m-d') }}"
                />
                <input
                    type="hidden"
                    id="hidden_day_no_for_vitals_form"
                    name="day_no"
                    value="{{ $currentDayNo ?? 1 }}"
                />

                <div class="mx-auto mt-6 flex w-[90%] items-start justify-between gap-1">
                    <div class="relative w-[30%] mr-3">

                         <div class="relative overflow-hidden rounded-[20px]" id="chart-wrapper"></div>

                         
                            <div id="fade-top"
                                class="pointer-events-none absolute top-0 left-0 z-20 h-10 w-full
                                        bg-gradient-to-b from-white/90 to-transparent rounded-t-[20px] hidden">
                            </div>
            

                        <!-- VIEWPORT (SHOWS 3 CHARTS) -->
                        <div id="chart-viewport" class="h-[530px] overflow-hidden rounded-[25px] relative">

                            <div id="chart-track" class="space-y-6 transition-transform duration-700 ease-out">
                                <!-- ✅ REUSABLE CHART CARD -->
                                <div class="h-[220px] rounded-2xl bg-yellow-200 p-4 shadow-lg">
                                    <h2 class="text-dark-green mb-1 text-center font-bold">Temperature Trend</h2>
                                    <canvas id="tempChart"></canvas>
                                </div>

                                <div class="h-[220px] rounded-2xl bg-yellow-200 p-4 shadow-lg">
                                    <h2 class="text-dark-green mb-1 text-center font-bold">Heart Rate Trend</h2>
                                    <canvas id="hrChart"></canvas>
                                </div>

                                <div class="h-[220px] rounded-2xl bg-yellow-200 p-4 shadow-lg">
                                    <h2 class="text-dark-green mb-1 text-center font-bold">Respiratory Rate Trend</h2>
                                    <canvas id="rrChart"></canvas>
                                </div>

                                <div class="h-[220px] rounded-2xl bg-yellow-200 p-4 shadow-lg">
                                    <h2 class="text-dark-green mb-1 text-center font-bold">Blood Pressure Trend</h2>
                                    <canvas id="bpChart"></canvas>
                                </div>

                                <div class="h-[220px] rounded-2xl bg-yellow-200 p-4 shadow-lg">
                                    <h2 class="text-dark-green mb-1 text-center font-bold">SpO₂ Trend</h2>
                                    <canvas id="spo2Chart"></canvas>
                                </div>
                            </div>

                            <div id="fade-bottom"
                            class="pointer-events-none absolute bottom-0 left-0 z-20 h-10 w-full bg-gradient-to-t from-white/90 to-transparent rounded-b-[20px] hidden"></div>
                        </div>

                

                        


                        <!-- DOWN BUTTON -->
                        <button id="chart-up" type="button"
                                class="bg-dark-green absolute -top-8 left-1/2 z-30
                                    -translate-x-1/2 h-10 w-10 rounded-full flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined">arrow_drop_up</span>
                        </button>

                        <button id="chart-down" type="button"
                                class="bg-dark-green absolute -bottom-8 left-1/2 z-30
                                    -translate-x-1/2 h-10 w-10 rounded-full flex items-center justify-center text-white shadow-lg">
                            <span class="material-symbols-outlined">arrow_drop_down</span>
                        </button>
                        
                    </div>
                    <div class="w-[68%] overflow-hidden rounded-[15px]">
                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="main-header w-[15%] rounded-tl-lg">TIME</th>
                                <th class="main-header w-[18%]">TEMPERATURE</th>
                                <th class="main-header w-[10%]">HR</th>
                                <th class="main-header w-[10%]">RR</th>
                                <th class="main-header w-[10%]">BP</th>
                                <th class="main-header w-[10%]">SpO₂</th>

                                @foreach ($times as $index => $time)
                                    @php
                                        $vitalsRecord = $vitalsData->get($time);
                                        $isLast = $index === count($times) - 1;
                                        $borderClass = $isLast ? '' : 'border-line-brown/70 border-b-2';
                                    @endphp

                                    <tr class="{{ $borderClass }}">
                                        {{-- TIME COLUMN --}}
                                        <th
                                            class="bg-yellow-light text-brown {{ $borderClass }} py-2 text-center font-semibold"
                                        >
                                            {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                        </th>

                                        {{-- TEMPERATURE --}}
                                        <td class="bg-beige {{ $borderClass }}">
                                            <input
                                                type="text"
                                                name="temperature_{{ $time }}"
                                                placeholder="temperature"
                                                value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                                                class="cdss-input vital-input h-[60px]"
                                                data-field-name="temperature"
                                                data-time="{{ $time }}"
                                            />
                                        </td>

                                        {{-- HR --}}
                                        <td class="bg-beige {{ $borderClass }}">
                                            <input
                                                type="text"
                                                name="hr_{{ $time }}"
                                                placeholder="bpm"
                                                value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                                                class="cdss-input vital-input h-[60px]"
                                                data-field-name="hr"
                                                data-time="{{ $time }}"
                                            />
                                        </td>

                                        {{-- RR --}}
                                        <td class="bg-beige {{ $borderClass }}">
                                            <input
                                                type="text"
                                                name="rr_{{ $time }}"
                                                placeholder="bpm"
                                                value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                                                class="cdss-input vital-input h-[60px]"
                                                data-field-name="rr"
                                                data-time="{{ $time }}"
                                            />
                                        </td>

                                        {{-- BP --}}
                                        <td class="bg-beige {{ $borderClass }}">
                                            <input
                                                type="text"
                                                name="bp_{{ $time }}"
                                                placeholder="mmHg"
                                                value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                                                class="cdss-input vital-input h-[60px]"
                                                data-field-name="bp"
                                                data-time="{{ $time }}"
                                            />
                                        </td>

                                        {{-- SpO₂ --}}
                                        <td class="bg-beige {{ $borderClass }}">
                                            <input
                                                type="text"
                                                name="spo2_{{ $time }}"
                                                placeholder="%"
                                                value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                                                class="cdss-input vital-input h-[60px]"
                                                data-field-name="spo2"
                                                data-time="{{ $time }}"
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tr>
                        </table>
                    </div>

                    <div class="w-[25%] rounded-[15px]">
                        <div class="main-header rounded-[15px]">ALERTS</div>

                        <table class="w-full border-collapse">
                            @foreach ($times as $time)
                                @php
                                    $vitalsRecord = $vitalsData->get($time);
                                    $severity = optional($vitalsRecord)->news_severity ?? 'NONE';
                                    $color =
                                        $severity === 'CRITICAL'
                                            ? 'text-red-600'
                                            : ($severity === 'WARNING'
                                                ? 'text-orange-500'
                                                : ($severity === 'INFO'
                                                    ? 'text-blue-500'
                                                    : ($severity === 'NONE'
                                                        ? 'text-white'
                                                        : 'text-black')));
                                    $alerts = $vitalsRecord ? explode('; ', $vitalsRecord->alerts) : [];
                                @endphp

                                <tr>
                                    <td class="align-middle" data-alert-for-time="{{ $time }}">
                                        <div
                                            class="alert-box flex h-[53px] w-full items-center justify-center"
                                            data-alert-for-time="{{ $time }}"
                                        >
                                            {{-- Dynamic alert content will load here --}}
                                            <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                <div class="mx-auto mt-5 mb-20 flex w-[66%] justify-end space-x-4">
                    @if (isset($vitalsData) && $vitalsData->count() > 0)
                        <button
                            type="submit"
                            formaction="{{ route('vital-signs.cdss') }}"
                            class="button-default text-center"
                        >
                            CDSS
                        </button>
                    @endif

                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </form>
        </fieldset>

       <div id="chart-modal" 
            style="display:none;" 
            class="absolute top-0 left-0 w-full h-full z-[50] flex items-center justify-center bg-white/40 backdrop-blur-sm rounded-[25px]">
            
            <div class="relative w-[95%] h-[90%] bg-white rounded-3xl shadow-2xl p-6 flex flex-col border border-gray-100">
                
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modal-chart-title" class="text-2xl font-bold text-gray-800"></h3>
                    <button onclick="closeChartModal()" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex-grow">
                    <canvas id="modalChartCanvas"></canvas>
                </div>
            </div>
        </div>
    </div>

   
@endsection

@push('scripts')
    @vite([
        'resources/js/patient-loader.js',
        'resources/js/alert.js',
        'resources/js/init.searchable-dropdown.js',
        'resources/js/date-day-sync.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/vital-signs-charts.js'
    ])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const vitalsData = @json($vitalsData);
        
        document.addEventListener('DOMContentLoaded', function () {
            const timePoints = @json($times);

            if (window.initializeVitalSignsCharts) {
                window.initializeVitalSignsCharts(timePoints, vitalsData);
            }
            
            if (window.initializeChartScrolling) {
                window.initializeChartScrolling();
            }
            
            // Initialize searchable dropdown
            if (window.initSearchableDropdown) {
                window.initSearchableDropdown();
            }
        });
    </script>
@endpush