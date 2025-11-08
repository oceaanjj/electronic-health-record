@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')

    {{-- HEADER SECTION --}}
    <div class="header flex items-center gap-4 my-10 mx-auto w-[70%]">
        <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
            PATIENT NAME :
        </label>

        {{-- NEW SEARCHABLE DROPDOWN --}}
        <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('adl.select') }}">
            {{-- TEXT INPUT --}}
            <input 
                type="text" 
                id="patient_search_input"
                placeholder="-Select or type to search-" 
                value="{{ trim($selectedPatient->name ?? '') }}"
                autocomplete="off"
                class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
            >

            {{-- DROPDOWN OPTIONS --}}
            <div id="patient_options_container" 
                class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                @foreach ($patients as $patient)
                    <div 
                        class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150" 
                        data-value="{{ $patient->patient_id }}">
                        {{ trim($patient->name) }}
                    </div>
                @endforeach
            </div>

            {{-- HIDDEN INPUT --}}
            <input type="hidden" name="patient_id_for_form" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">
        </div>
    </div>

    {{-- FORM OVERLAY --}}
    <div id="form-content-container">
        @if (!session('selected_patient_id'))
            <div class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                <span class="text-gray-600 font-creato">Please select a patient to input</span>
            </div>
        @endif
    </div>

    {{-- MAIN FORM --}}
    <form action="{{ route('adl.store') }}" method="POST" class="cdss-form"
        data-analyze-url="{{ route('adl.analyze-field') }}">
        @csrf
        <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">
        <input type="hidden" name="date" value="{{ $currentDate ?? session('selected_date') }}">
        <input type="hidden" name="day_no" value="{{ $currentDayNo ?? session('selected_day_no') }}">

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <center>
                <div class="w-[70%] mx-auto flex justify-center items-start gap-0 mt-6">

                    {{-- LEFT TABLE (INPUTS) --}}
                    <div class="w-[68%] rounded-[15px] overflow-hidden">
                        <table class="w-full border-separate border-spacing-0">
                            <tr>
                                <th class="w-[40%] main-header rounded-tl-lg">CATEGORY</th>
                                <th class="w-[60%] main-header rounded-tr-lg">ASSESSMENT</th>
                            </tr>

                            {{-- REPEATED ROWS --}}
                            @foreach ([
                                'mobility_assessment' => 'MOBILITY',
                                'hygiene_assessment' => 'HYGIENE',
                                'toileting_assessment' => 'TOILETING',
                                'feeding_assessment' => 'FEEDING',
                                'hydration_assessment' => 'HYDRATION',
                                'sleep_pattern_assessment' => 'SLEEP PATTERN',
                                'pain_level_assessment' => 'PAIN LEVEL'
                            ] as $field => $label)
                                <tr>
                                    <th class="table-header border-b-2 border-line-brown">{{ $label }}</th>
                                    <td class="bg-beige border-b-2 border-line-brown">
                                        <textarea 
                                            name="{{ $field }}" 
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="{{ $field }}"
                                            placeholder="Type here..">{{ old($field, $adlData->$field ?? '') }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    {{-- RIGHT TABLE (ALERTS) --}}
                    <div class="w-[25%] rounded-[15px] overflow-hidden">
                        <div class="main-header rounded-[15px]">
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
                                    <td class="align-middle">
                                        <div class="alert-box my-0.5 py-4 px-3 flex justify-center items-center w-full h-[90px]"
                                            data-alert-for="{{ $field }}">
                                            <span class="opacity-70 text-white font-semibold">No Alerts</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </center>

            {{-- BUTTONS --}}
            <div class="w-[70%] mx-auto flex justify-end mt-5 mb-30 space-x-4">
                <button type="button" class="button-default">CDSS</button>
                <button type="submit" class="button-default">SUBMIT</button>
            </div>
        </fieldset>
    </form>
@endsection

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush
