@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')

    {{-- NEW SEARCHABLE PATIENT DROPDOWN --}}
    <div class="header">
        <label for="patient_search_input">PATIENT NAME :</label>
        <div class="searchable-dropdown" data-select-url="{{ route('adl.select') }}">
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
        <input type="hidden" name="patient_id_for_form" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">
    </div>

    <div id="form-content-container">
        @if (!session('selected_patient_id'))
            <div class="form-overlay">
                <span>Please select a patient to input</span>
            </div>
        @endif

        <!-- DATE AND DAY SELECTOR -->
        <!-- Added data-select-url and removed onchange attributes  -->
        <form action="{{ route('adl.select-date-day') }}" method="POST" id="date-day-select-form"
            class="flex items-center space-x-4" style="margin-left: 15rem; margin-top: 1rem;"
            data-select-url="{{ route('adl.select-date-day') }}">
            @csrf
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

            <!-- date -->
            <div>
                <label for="date" style="color: white;">DATE :</label>
                <input class="date" type="date" id="date_selector" name="date"
                    value="{{ session('selected_date') ?? ($selectedPatient && $selectedPatient->admission_date ? $selectedPatient->admission_date->format('Y-m-d') : now()->format('Y-m-d')) }}">
            </div>

            <!-- day -->
            <div><label for="day_no" style="color: white;">DAY NO :</label>
                <select id="day_no" name="day_no">
                    @for ($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}" @if(session('selected_day_no') == $i) selected @endif>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
        </form>

        {{-- Main ADL form --}}
        <form id="adl-form" method="POST" action="{{ route('adl.store') }}" class="cdss-form"
            data-analyze-url="{{ route('adl.analyze-field') }}">
            @csrf
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">
            <input type="hidden" name="date"
                value="{{ session('selected_date') ?? ($selectedPatient && $selectedPatient->admission_date ? $selectedPatient->admission_date->format('Y-m-d') : now()->format('Y-m-d')) }}">
            <input type="hidden" name="day_no" value="{{ session('selected_day_no') }}">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <table>
                        <tr>
                            <th class="title">CATEGORY</th>
                            <th class="title">ASSESSMENT</th>
                            <th class="title">ALERTS</th>
                        </tr>
                        <tr>
                            <th class="title">MOBILITY</th>
                            <td>
                                <input type="text" name="mobility_assessment" placeholder="mobility" class="cdss-input"
                                    data-field-name="mobility_assessment"
                                    value="{{ old('mobility_assessment', $adlData->mobility_assessment ?? '') }}">
                            </td>
                            <td class="alert-box" data-alert-for="mobility_assessment"></td>
                        </tr>
                        <tr>
                            <th class="title">HYGIENE</th>
                            <td>
                                <input type="text" name="hygiene_assessment" placeholder="hygiene" class="cdss-input"
                                    data-field-name="hygiene_assessment"
                                    value="{{ old('hygiene_assessment', $adlData->hygiene_assessment ?? '') }}">
                            </td>
                            <td class="alert-box" data-alert-for="hygiene_assessment"></td>
                        </tr>
                        <tr>
                            <th class="title">TOILETING</th>
                            <td>
                                <input type="text" name="toileting_assessment" placeholder="toileting" class="cdss-input"
                                    data-field-name="toileting_assessment"
                                    value="{{ old('toileting_assessment', $adlData->toileting_assessment ?? '') }}">
                            </td>
                            <td class="alert-box" data-alert-for="toileting_assessment"></td>
                        </tr>
                        <tr>
                            <th class="title">FEEDING</th>
                            <td>
                                <input type="text" name="feeding_assessment" placeholder="feeding" class="cdss-input"
                                    data-field-name="feeding_assessment"
                                    value="{{ old('feeding_assessment', $adlData->feeding_assessment ?? '') }}">
                            </td>
                            <td class="alert-box" data-alert-for="feeding_assessment"></td>
                        </tr>
                        <tr>
                            <th class="title">HYDRATION</th>
                            <td>
                                <input type="text" name="hydration_assessment" placeholder="hydration" class="cdss-input"
                                    data-field-name="hydration_assessment"
                                    value="{{ old('hydration_assessment', $adlData->hydration_assessment ?? '') }}">
                            </td>
                            <td class="alert-box" data-alert-for="hydration_assessment"></td>
                        </tr>
                        <tr>
                            <th class="title">SLEEP PATTERN</th>
                            <td>
                                <input type="text" name="sleep_pattern_assessment" placeholder="sleep pattern"
                                    class="cdss-input" data-field-name="sleep_pattern_assessment"
                                    value="{{ old('sleep_pattern_assessment', $adlData->sleep_pattern_assessment ?? '') }}">
                            </td>
                            <td class="alert-box" data-alert-for="sleep_pattern_assessment"></td>
                        </tr>
                        <tr>
                            <th class="title">PAIN LEVEL</th>
                            <td>
                                <input type="text" name="pain_level_assessment" placeholder="pain level" class="cdss-input"
                                    data-field-name="pain_level_assessment"
                                    value="{{ old('pain_level_assessment', $adlData->pain_level_assessment ?? '') }}">
                            </td>
                            <td class="alert-box" data-alert-for="pain_level_assessment"></td>
                        </tr>
                    </table>
                </center>

                <div class="buttons">
                    <button class="btn" type="button">CDSS</button>
                    <button class="btn" type="submit">Submit</button>
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
@endpush