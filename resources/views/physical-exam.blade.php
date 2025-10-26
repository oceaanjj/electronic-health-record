@extends('layouts.app')
@section('title', 'Physical Exam')
@section('content')

    {{-- NEW SEARCHABLE PATIENT DROPDOWN --}}
    <div class="header" style="margin-left:15rem;">
        <label for="patient_search_input" style="color: white;">PATIENT NAME :</label>

        {{-- The data-select-url attribute is crucial for patient-loader.js --}}
        <div class="searchable-dropdown" data-select-url="{{ route('physical-exam.select') }}">

            {{-- This is the text input the user interacts with --}}
            <input type="text" id="patient_search_input" placeholder="-Select or type to search-"
                value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off">

            {{-- This container will hold the list of selectable patients --}}
            <div id="patient_options_container">
                @foreach ($patients as $patient)
                    <div class="option" data-value="{{ $patient->patient_id }}">
                        {{ trim($patient->name) }}
                    </div>
                @endforeach
            </div>
        </div>

        {{-- This hidden input will hold the selected patient's ID for the main form --}}
        <input type="hidden" name="patient_id_for_form" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">
    </div>
    {{-- NEW SEARCHABLE PATIENT DROPDOWN --}}


    <div id="form-content-container">
        {{-- DISABLED input overlay --}}
        @if (!session('selected_patient_id'))
            <div class="form-overlay" style="margin-left:15rem;">
                <span>Please select a patient to input</span> {{-- message --}}
            </div>
        @endif

        <form action="{{ route('physical-exam.store') }}" method="POST" class="cdss-form"
            data-analyze-url="{{ route('physical-exam.analyze-field') }}">
            @csrf

            {{-- Hidden input for the patient ID --}}
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

            {{-- Wrap the form content in a fieldset --}}
            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <table>
                        <tr>
                            <th class="bg-dark-green text-white rounded-tl-lg">SYSTEM</th>
                            <th class="bg-dark-green text-white">FINDINGS</th>
                            <th class="bg-dark-green text-white rounded-tr-lg">ALERTS</th>
                        </tr>
                        {{-- GENERAL APPEARANCE --}}
                        <tr>
                            <th class="bg-yellow-light text-brown">GENERAL APPEARANCE</th>
                            <td>
                                <textarea name="general_appearance" class="notepad-lines cdss-input"
                                    data-field-name="general_appearance"
                                    placeholder="Enter GENERAL APPEARANCE findings">{{ old('general_appearance', $physicalExam->general_appearance ?? '') }}</textarea>
                            </td>
                            <td class="alert-box" data-alert-for="general_appearance">
                                {{-- Alert content will be dynamically loaded --}}
                            </td>
                        </tr>

                        {{-- SKIN --}}
                        <tr>
                            <th class="bg-yellow-light text-brown">SKIN</th>
                            <td>
                                <textarea name="skin_condition" class="notepad-lines cdss-input"
                                    data-field-name="skin_condition"
                                    placeholder="Enter SKIN findings">{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}</textarea>
                            </td>
                            <td class="alert-box" data-alert-for="skin_condition">
                                {{-- Alert content will be dynamically loaded --}}
                            </td>
                        </tr>

                        {{-- EYES --}}
                        <tr>
                            <th class="bg-yellow-light text-brown">EYES</th>
                            <td>
                                <textarea name="eye_condition" class="notepad-lines cdss-input"
                                    data-field-name="eye_condition"
                                    placeholder="Enter EYES findings">{{ old('eye_condition', $physicalExam->eye_condition ?? '') }}</textarea>
                            </td>
                            <td class="alert-box" data-alert-for="eye_condition">
                                {{-- Alert content will be dynamically loaded --}}
                            </td>
                        </tr>

                        {{-- ORAL CAVITY --}}
                        <tr>
                            <th class="bg-yellow-light text-brown">ORAL CAVITY</th>
                            <td>
                                <textarea name="oral_condition" class="notepad-lines cdss-input"
                                    data-field-name="oral_condition"
                                    placeholder="Enter ORAL CAVITY findings">{{ old('oral_condition', $physicalExam->oral_condition ?? '') }}</textarea>
                            </td>
                            <td class="alert-box" data-alert-for="oral_condition">
                                {{-- Alert content will be dynamically loaded --}}
                            </td>
                        </tr>

                        {{-- CARDIOVASCULAR --}}
                        <tr>
                            <th class="bg-yellow-light text-brown">CARDIOVASCULAR</th>
                            <td>
                                <textarea name="cardiovascular" class="notepad-lines cdss-input"
                                    data-field-name="cardiovascular"
                                    placeholder="Enter CARDIOVASCULAR findings">{{ old('cardiovascular', $physicalExam->cardiovascular ?? '') }}</textarea>
                            </td>
                            <td class="alert-box" data-alert-for="cardiovascular">
                                {{-- Alert content will be dynamically loaded --}}
                            </td>
                        </tr>

                        {{-- ABDOMEN --}}
                        <tr>
                            <th class="bg-yellow-light text-brown">ABDOMEN</th>
                            <td>
                                <textarea name="abdomen_condition" class="notepad-lines cdss-input"
                                    data-field-name="abdomen_condition"
                                    placeholder="Enter ABDOMEN findings">{{ old('abdomen_condition', $physicalExam->abdomen_condition ?? '') }}</textarea>
                            </td>
                            <td class="alert-box" data-alert-for="abdomen_condition">
                                {{-- Alert content will be dynamically loaded --}}
                            </td>
                        </tr>

                        {{-- EXTREMITIES --}}
                        <tr>
                            <th class="bg-yellow-light text-brown">EXTREMITIES</th>
                            <td>
                                <textarea name="extremities" class="notepad-lines cdss-input" data-field-name="extremities"
                                    placeholder="Enter EXTREMITIES findings">{{ old('extremities', $physicalExam->extremities ?? '') }}</textarea>
                            </td>
                            <td class="alert-box" data-alert-for="extremities">
                                {{-- Alert content will be dynamically loaded --}}
                            </td>
                        </tr>

                        {{-- NEUROLOGICAL --}}
                        <tr>
                            <th class="bg-yellow-light text-brown">NEUROLOGICAL</th>
                            <td>
                                <textarea name="neurological" class="notepad-lines cdss-input"
                                    data-field-name="neurological"
                                    placeholder="Enter NEUROLOGICAL findings">{{ old('neurological', $physicalExam->neurological ?? '') }}</textarea>
                            </td>
                            <td class="alert-box" data-alert-for="neurological">
                                {{-- Alert content will be dynamically loaded --}}
                            </td>
                        </tr>

                    </table>
                </center>

                <div class="buttons">
                    <button type="submit" class="btn">Submit</button>
                    <button type="button" class="btn">CDSS</button>
                </div>
            </fieldset>
        </form>
    </div>
@endsection

@push('styles')
    @vite('resources/css/physical-exam-style.css')
@endpush

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
@endpush