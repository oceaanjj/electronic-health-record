@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')

    {{-- NEW SEARCHABLE PATIENT DROPDOWN, DATE, AND DAY SELECTORS (in one row) --}}
    <div class="header" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px;">
        {{-- PATIENT SELECTOR (Copied from physical-exam.blade.php) --}}
        <label for="patient_search_input" style="color: white; white-space: nowrap;">PATIENT NAME :</label>
        <div class="searchable-dropdown" data-select-url="{{ route('adl.select') }}" style="min-width: 250px;">
            <input type="text" id="patient_search_input" placeholder="-Select or type to search-"
                value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off">
            <div id="patient_options_container">
                @foreach ($patients as $patient)
                    <div class="option" data-value="{{ $patient->patient_id }}">
                        {{ trim($patient->name) }}
                    </div>
                @endforeach
            </div>
        </div>
        {{-- This hidden input will hold the selected patient's ID for the main form and for the Date/Day logic --}}
        <input type="hidden" name="patient_id_for_form" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">

        {{-- DATE INPUT --}}
        <label for="date_selector" style="color: white; white-space: nowrap;">DATE :</label>
        <input class="date" type="date" id="date_selector" name="date"
            {{-- CRITICAL FIX: Use the reliable $currentDate variable passed from the controller --}}
            value="{{ $currentDate ?? now()->format('Y-m-d') }}"
            @if (!$selectedPatient) disabled @endif>

        {{-- DAY NO SELECTOR --}}
        <label for="day_no_selector" style="color: white; white-space: nowrap;">DAY NO :</label>
        <select id="day_no_selector" name="day_no" @if (!$selectedPatient) disabled @endif>
            <option value="">-- Day --</option>
            @for ($i = 1; $i <= 30; $i++)
                <option value="{{ $i }}" @if(($currentDayNo ?? 1) == $i) selected @endif>
                    {{ $i }}
                </option>
            @endfor
        </select>
    </div>
    {{-- END HEADER --}}


    <div id="form-content-container">
        {{-- DISABLED input overlay --}}
        @if (!session('selected_patient_id'))
            <div class="form-overlay">
                <span>Please select a patient to input</span> {{-- message --}}
            </div>
        @endif

        {{-- MAIN FORM (sumbit) with CDSS setup --}}
        <form id="adl-form" method="POST" action="{{ route('adl.store') }}" class="cdss-form"
            data-analyze-url="{{ route('adl.analyze-field') }}">
            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                @csrf

                {{-- Hidden PATIENT_ID AND DATE/DAY from header elements --}}
                <input type="hidden" name="patient_id" class="patient-id-input" value="{{ session('selected_patient_id') }}">
                {{-- Use the explicit variables for consistency in form submission --}}
                <input type="hidden" name="date" class="date-input" value="{{ $currentDate ?? session('selected_date') }}">
                <input type="hidden" name="day_no" class="day-no-input" value="{{ $currentDayNo ?? session('selected_day_no') }}">

                <table>
                    <tr>
                        <th class="title">CATEGORY</th>
                        <th class="title">ASSESSMENT</th>
                        <th class="title">ALERTS</th>
                    </tr>

                    @php
                        // Helper to get alert data and color
                        function getAlertData($field, $session)
                        {
                            $alertData = $session->get("cdss.$field");
                            if (!$alertData)
                                return ['alert' => null, 'color' => null];

                            $color = 'alert-green';
                            if ($alertData['severity'] === 'CRITICAL')
                                $color = 'alert-red';
                            elseif ($alertData['severity'] === 'WARNING')
                                $color = 'alert-orange';

                            return ['alert' => $alertData['alert'], 'color' => $color];
                        }
                    @endphp

                    @foreach ([
                            'mobility_assessment' => 'MOBILITY',
                            'hygiene_assessment' => 'HYGIENE',
                            'toileting_assessment' => 'TOILETING',
                            'feeding_assessment' => 'FEEDING',
                            'hydration_assessment' => 'HYDRATION',
                            'sleep_pattern_assessment' => 'SLEEP PATTERN',
                            'pain_level_assessment' => 'PAIN LEVEL',
                        ] as $field => $label)
                        @php
                            $alert = getAlertData($field, session());
                        @endphp
                        <tr>
                            <th class="title">{{ $label }}</th>
                            <td>
                                {{-- Added cdss-input and data-field-name classes for alert.js --}}
                                <input type="text" name="{{ $field }}" placeholder="{{ strtolower($label) }}"
                                    class="cdss-input" data-field-name="{{ $field }}"
                                    value="{{ old($field, $adlData->$field ?? '') }}">
                            </td>
                            {{-- Added data-alert-for for alert.js to place alerts --}}
                            <td data-alert-for="{{ $field }}">
                                @if (isset($adlData) && session()->has('cdss'))
                                    {{-- Initial alert rendering for pre-filled data. Will be overwritten by JS on page load/reload --}}
                                @elseif ($alert['alert'])
                                    <div class="alert-box {{ $alert['color'] }}">
                                        <span class="alert-message">{{ $alert['alert'] }}</span>
                                    </div>
                                @endif
                                @error($field)
                                    <div class="alert-box alert-red">
                                        <span class="alert-message">{{ $message }}</span>
                                    </div>
                                @enderror
                            </td>
                        </tr>
                    @endforeach

                </table>
            </fieldset>

            <div class="buttons">
                <button class="btn" type="button">CDSS</button>
                <button class="btn" type="submit">Submit</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    @vite('resources/css/act-of-daily-living.css')
@endpush


@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush
