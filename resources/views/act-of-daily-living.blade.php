@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')

<div id="form-content-container">

    {{-- SEARCHABLE PATIENT DROPDOWN & DATE/DAY SELECTOR --}}
    {{-- Kept OUTSIDE the form so the disabled overlay doesn't block it --}}
    <div class="header flex items-center gap-6 my-10 mx-auto w-[80%]">
        <div class="flex items-center gap-6 w-full">
            @csrf

            {{-- PATIENT NAME --}}
            <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
                PATIENT NAME :
            </label>

            <div class="searchable-dropdown relative w-[400px]" 
                 data-select-url="{{ route('adl.select') }}" 
                 data-admission-date="{{ $selectedPatient ? \Carbon\Carbon::parse($selectedPatient->admission_date)->format('Y-m-d') : '' }}"
                 data-sync-mode="html-reload">
                <input type="text" id="patient_search_input" placeholder="Select or type Patient Name"
                    value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off"
                    class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                
                <div id="patient_options_container"
                    class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                    @foreach ($patients as $patient)
                        <div class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150"
                            data-value="{{ $patient->patient_id }}">
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
            <input type="date" id="date_selector" name="date"
                value="{{ $currentDate  ?? now()->format('Y-m-d') }}"
                @if (!$selectedPatient) disabled @endif
                class="text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 outline-none shadow-sm bg-gray-100">

            {{-- DAY NO --}}
            <label for="day_no" class="whitespace-nowrap font-alte font-bold text-dark-green">
                DAY NO :
            </label>
            <select id="day_no_selector" name="day_no"
                @if (!$selectedPatient) disabled @endif
                class="w-[120px] text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                @for ($i = 1; $i <= $totalDaysSinceAdmission; $i++)
                    <option value="{{ $i }}" @if($currentDayNo == $i) selected @endif>
                        {{  $i }}
                    </option>
                @endfor
            </select>
        </div>
    </div>
    
    {{-- 
        FIX: 
        1. Added 'relative', 'w-[70%]', 'mx-auto' to <form>.
        2. Added 'cdss-form' for JS targeting.
        This creates the correct "box" for the disabled overlay to appear in.
    --}}
    <form id="adl-form" method="POST" class="cdss-form relative w-[70%] mx-auto" 
          action="{{ route('adl.store') }}"
          data-analyze-url="{{ route('adl.analyze-field') }}"
          data-batch-analyze-url="{{ route('adl.analyze-batch') }}"
          data-alert-height-class="h-[96px]"> 
    
        <fieldset @if (!$selectedPatient) disabled @endif>
            @csrf

            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
            <input type="hidden" name="date" value="{{ $currentDate ?? now()->format('Y-m-d') }}">
            <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}">

            <div class="w-[85%] mx-auto flex justify-center items-start gap-1 mt-6">
                {{-- LEFT SIDE TABLE (INPUTS) --}}
                <div class="w-[68%] rounded-[15px] overflow-hidden">
                    <table class="w-full table-fixed border-collapse border-spacing-y-0">
                        <tr>
                            <th class="w-[30%] main-header py-2 text-center rounded-tl-lg">CATEGORY</th>
                            <th class="w-[60%] main-header rounded-tr-lg">ASSESSMENT</th>
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
                            <tr class="border-b-2 border-line-brown/50">
                                <th class="text-center font-semibold py-2 bg-yellow-light text-brown">
                                    {{ $label }}
                                </th>
                                <td class="bg-beige">
                                    <textarea name="{{ $field }}" placeholder="Type here..."
                                        class="notepad-lines cdss-input w-full h-[95px]"
                                        data-field-name="{{ $field }}">{{ old($field, $adlData->$field ?? '') }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                {{-- ALERTS TABLE --}}
                <div class="w-[25%] rounded-[15px]">
                    <div class="main-header text-center rounded-[15px]">
                        ALERTS
                    </div>
                    <table class="w-full border-collapse">
                        @foreach ([
                            'mobility_assessment', 'hygiene_assessment', 'toileting_assessment',
                            'feeding_assessment', 'hydration_assessment', 'sleep_pattern_assessment',
                            'pain_level_assessment'
                        ] as $field)
                            @php
                                $alertText = 'NO ALERTS';
                                $alertSeverity = 'none';
                                if (isset($alerts[$field]) && !empty($alerts[$field]['alert']) && $alerts[$field]['alert'] !== 'No Findings') {
                                    $alertText = $alerts[$field]['alert'];
                                    $alertSeverity = strtolower($alerts[$field]['severity']);
                                }
                            @endphp
                             <tr>
                                <td class="align-middle" data-alert-for="{{ $field }}">
                                    <div class="alert-box h-[96px] flex justify-center items-center alert-{{ $alertSeverity }}">
                                        <span class="opacity-70 text-white font-semibold">{{ $alertText }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="w-[80%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
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
    {{-- FIX: Using 'searchable-dropdown.js' (the robust one) instead of 'init.searchable-dropdown.js' --}}
    @vite([
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js', 
        'resources/js/alert.js',
        'resources/js/date-day-sync.js' 
    ])

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // FIX: Using the correct function name exposed by the robust JS
            if (window.initSearchableDropdown) {
                window.initSearchableDropdown();
            }
        });
    </script>
@endpush