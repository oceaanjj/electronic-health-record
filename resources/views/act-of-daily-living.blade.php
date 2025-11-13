@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')


<div id="form-content-container">
    {{-- This container is now the main wrapper for all dynamic content --}}

    @if (!session('selected_patient_id'))
        <div class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
            <span class="text-gray-600 font-creato">Please select a patient to input</span>
        </div>
    @endif

    {{-- SEARCHABLE PATIENT DROPDOWN & DATE/DAY SELECTOR (from vital-signs) --}}
    <div class="header flex items-center gap-6 my-10 mx-auto w-[80%]">
        <div class="flex items-center gap-6 w-full">
            @csrf

            {{-- PATIENT NAME --}}
            <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
                PATIENT NAME :
            </label>

            <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('adl.select') }}" data-admission-date="{{ $selectedPatient->admission_date ?? '' }}">
                <input
                    type="text"
                    id="patient_search_input"
                    placeholder="Select or type Patient Name"
                    value="{{ trim($selectedPatient->name ?? '') }}"
                    autocomplete="off"
                    class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                >
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
                value="{{ $currentDate  ?? now()->format('Y-m-d') }}"
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
                @for ($i = 1; $i <= ($totalDaysSinceAdmission ?? 30); $i++)
                    <option
                        value="{{ $i }}"
                        @if(($currentDayNo ?? 1) == $i) selected @endif
                    >
                        {{  $i }}
                    </option>
                @endfor
            </select>
        </div>
       </div>
        
    {{-- END HEADER --}}

    <form id="adl-form" method="POST" class="cdss-form" data-analyze-url="{{ route('adl.analyze-field') }}"
      data-alert-height-class="h-[55px]"> 
    
<!-- data-alert-height-class = set alert box height -->

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            @csrf

            <input type="hidden" name="patient_id" class="patient-id-input" value="{{ session('selected_patient_id') }}">
            <input type="hidden" name="date" class="date-input" value="{{ $currentDate ?? session('selected_date') }}">
            <input type="hidden" name="day_no" class="day-no-input" value="{{ $currentDayNo ?? session('selected_day_no') }}">

            <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
                {{-- LEFT SIDE TABLE (INPUTS) --}}
                <div class="w-[68%] rounded-[15px] overflow-hidden">
                    <table class="w-full table-fixed border-collapse border-spacing-y-0">
                        <tr>
                            <th class="w-[40%] bg-dark-green text-white font-bold py-2 text-center rounded-tl-lg">CATEGORY</th>
                            <th class="w-[60%] bg-dark-green text-white rounded-tr-lg">ASSESSMENT</th>
                        </tr>

                        @foreach ([
                            'mobility_assessment' => 'MOBILITY',
                            'hygiene_assessment' => 'HYGIENE',
                            'toileting_assessment' => 'TOILETING',
                            'feeding_assessment' => 'FEEDING',
                            'hydration_assessment' => 'HYDRATION',
                            'sleep_pattern_assessment' => 'SLEEP PATTERN',
                            'pain_level_assessment' => 'PAIN LEVEL',
                        ] as $field => $label)
                            <tr class="border-b-2 border-line-brown/70">
                                <th class="text-center font-semibold py-2 bg-yellow-light text-brown">
                                    {{ $label }}
                                </th>
                                <td class="bg-beige">
                                    <input type="text" name="{{ $field }}" placeholder="Type here..."
                                        class="cdss-input vital-input h-[60px] w-full border-none bg-transparent focus:ring-0"
                                        data-field-name="{{ $field }}"
                                        value="{{ old($field, $adlData->$field ?? '') }}">
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                {{-- ALERTS TABLE (JAVASCRIPT-CONTROLLED) --}}
                <div class="w-[25%] rounded-[15px] overflow-hidden">
                    <div class="bg-dark-green text-white font-bold py-2 mb-1 text-center rounded-[15px]">
                        ALERTS
                    </div>
                    <table class="w-full border-collapse">
                        @foreach ([
                            'mobility_assessment',
                            'hygiene_assessment',
                            'toileting_assessment',
                            'feeding_assessment',
                            'hydration_assessment',
                            'sleep_pattern_assessment',
                            'pain_level_assessment',
                        ] as $field)
                            <tr>
                                <td class="align-middle" data-alert-for="{{ $field }}">
                                    @php
                                        $alertText = 'NO ALERTS';
                                        $alertSeverity = 'none';
                                        if (isset($alerts[$field]) && $alerts[$field]['alert'] !== 'No Findings') {
                                            $alertText = $alerts[$field]['alert'];
                                            $alertSeverity = strtolower($alerts[$field]['severity']);
                                        }
                                    @endphp
                                    <div class="alert-box my-[3px] h-[53px] flex justify-center items-center alert-{{ $alertSeverity }}">
                                        <span class="opacity-70 text-white font-semibold">{{ $alertText }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>


<div class="w-[66%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
    @if (isset($adlData))
        <a href="{{ route('nursing-diagnosis.start', ['component' => 'adl', 'id' => $adlData->id]) }}"
            class="button-default text-center">
            CDSS
        </a>
    @endif
    <button type="submit" form="adl-form" class="button-default">SUBMIT</button>
</div>
                
        </fieldset>
    </form>
</div>
@endsection

@push('scripts')
    {{-- Load all necessary script files, replacing generic alert.js with the specific one --}}
    @vite([
        'resources/js/patient-loader.js',
        'resources/js/date-day-loader.js',
        'resources/js/init-searchable-dropdown.js',
        'resources/js/alert.js',
        'resources/js/act-of-daily-living-date-sync.js'
    ])

    {{-- Define the specific initializers for this page, following the vital-signs pattern --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.pageInitializers = [
                window.initializeSearchableDropdown,
                window.initializeDateDayLoader,
                window.initializeAdlAlerts,
                window.initializeAdlDateSync
            ];
        });
    </script>
@endpush