@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')

    {{-- NEW SEARCHABLE PATIENT DROPDOWN, DATE, AND DAY SELECTORS --}}
    <div class="header" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px;">
        <x-searchable-dropdown :patients="$patients" :selectedPatient="$selectedPatient" selectUrl="{{ route('adl.select') }}" />

        <x-date-day-selector :selectedPatient="$selectedPatient" :currentDate="$currentDate" :currentDayNo="$currentDayNo" />
    </div>
    {{-- END HEADER --}}


    <div id="form-content-container">
        {{-- DISABLED input overlay --}}
        @if (!session('selected_patient_id'))
            <div class="form-overlay">
                <span>Please select a patient first to input</span> {{-- message --}}
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

                <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
                    <div class="w-[68%] rounded-[15px] overflow-hidden">
                
                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="w-[40%] bg-dark-green text-white font-bold py-2 text-center rounded-tl-lg">CATEGORY</th>
                                <th class="w-[60%] bg-dark-green text-white rounded-tr-lg">ASSESSMENT</th>
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
                                {{-- Cdss-input and data-field-name classes for alert.js --}}
                                <input type="text" name="{{ $field }}" placeholder="{{ strtolower($label) }}"
                                    class="cdss-input" data-field-name="{{ $field }}"
                                    value="{{ old($field, $adlData->$field ?? '') }}">
                            </td>
                            {{-- data-alert-for for alert.js to place alerts --}}
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
                    <div class="w-[97%] mx-auto flex justify-end mt-5 mb-30 space-x-4">
                    <button type="button" class="button-default">CDSS</button>
                    <button type="submit" class="button-default">SUBMIT</button>       
                </div>
            </fieldset>
        </form>
    </div>
@endsection

@push('styles')
    @vite('resources/css/act-of-daily-living.css')
@endpush


@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const patientIdHidden = document.getElementById("patient_id_hidden");
            const dateSelector = document.getElementById("date_selector");

            // Only initialize if we have a patient ID and the date selector exists (i.e., it's the ADL form)
            if (patientIdHidden && patientIdHidden.value && dateSelector) {
                // Explicitly call initializeDateDayLoader with the correct URL
                window.initializeDateDayLoader('{{ route('adl.select') }}');
            }
        });
    </script>
@endpush