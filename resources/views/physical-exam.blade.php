@extends('layouts.app')
@section('title', 'Physical Exam')
@section('content')


{{-- NOTE : sa css ko a-add pa ko my-1 py-4 px-3 each alerts tenks wag niyo burahin to makakalimutan ko --}}

    {{-- NEW SEARCHABLE PATIENT DROPDOWN --}}
    <div class="header" style="margin-left:15rem;">
        <label for="patient_search_input">PATIENT NAME :</label>

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
                    <div class="w-[70%] mx-auto border-line-brown rounded-[15px] overflow-hidden">
                        <table class="w-full border-separate border-spacing-0">
                            <tr>
                                <th class="w-[20%] bg-dark-green text-white rounded-tl-lg">SYSTEM</th>
                                <th class="w-[45%] bg-dark-green text-white">FINDINGS</th>
                                <th class="w-[25%] bg-dark-green text-white rounded-tr-lg">ALERTS</th>
                            </tr>
                            {{-- GENERAL APPEARANCE --}}
                            <tr class="border-2 ">
                                <th class=" bg-yellow-light text-brown border-t-0 border-l-0 border-r-2 border-b-2 border-line-brown">GENERAL<br>APPEARANCE</th>
                                <td>
                                    <textarea name="general_appearance" class="notepad-lines cdss-input"
                                        data-field-name="general_appearance"
                                        placeholder="Type here..">{{ old('general_appearance', $physicalExam->general_appearance ?? '') }}</textarea>
                                </td>
                                <td class="alert-box" data-alert-for="general_appearance">
                                    {{-- Alert content will be dynamically loaded --}}
                                </td>
                            </tr>

                            {{-- SKIN --}}
                            <tr class="border-2 ">
                                <th class="bg-yellow-light text-brown">SKIN</th>
                                <td>
                                    <textarea name="skin_condition" class="notepad-lines cdss-input "
                                        data-field-name="skin_condition"
                                        placeholder="Type here..">{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}</textarea>
                                </td>
                                <td class="alert-box" data-alert-for="skin_condition">
                                    {{-- Alert content will be dynamically loaded --}}
                                </td>
                            </tr>

                            {{-- EYES --}}
                            <tr class="border-2 ">
                                <th class="bg-yellow-light text-brown">EYES</th>
                                <td>
                                    <textarea name="eye_condition" class="notepad-lines cdss-input"
                                        data-field-name="eye_condition"
                                        placeholder="Type here..">{{ old('eye_condition', $physicalExam->eye_condition ?? '') }}</textarea>
                                </td>
                                <td class="alert-box" data-alert-for="eye_condition">
                                    {{-- Alert content will be dynamically loaded --}}
                                </td>
                            </tr>

                            {{-- ORAL CAVITY --}}
                            <tr class="border-2 border-line-brown">
                                <th class="bg-yellow-light text-brown">ORAL CAVITY</th>
                                <td>
                                    <textarea name="oral_condition" class="notepad-lines cdss-input"
                                        data-field-name="oral_condition"
                                        placeholder="Type here..">{{ old('oral_condition', $physicalExam->oral_condition ?? '') }}</textarea>
                                </td>
                                <td class="alert-box" data-alert-for="oral_condition">
                                    {{-- Alert content will be dynamically loaded --}}
                                </td>
                            </tr>

                            {{-- CARDIOVASCULAR --}}
                            <tr class="border-2 border-line-brown">
                                <th class="bg-yellow-light text-brown">CARDIOVASCULAR</th>
                                <td>
                                    <textarea name="cardiovascular" class="notepad-lines cdss-input"
                                        data-field-name="cardiovascular"
                                        placeholder="Type here..">{{ old('cardiovascular', $physicalExam->cardiovascular ?? '') }}</textarea>
                                </td>
                                <td class="alert-box" data-alert-for="cardiovascular">
                                    {{-- Alert content will be dynamically loaded --}}
                                </td>
                            </tr>

                            {{-- ABDOMEN --}}
                            <tr class="border-2 border-line-brown">
                                <th class="bg-yellow-light text-brown">ABDOMEN</th>
                                <td>
                                    <textarea name="abdomen_condition" class="notepad-lines cdss-input"
                                        data-field-name="abdomen_condition"
                                        placeholder="Type here..">{{ old('abdomen_condition', $physicalExam->abdomen_condition ?? '') }}</textarea>
                                </td>
                                <td class="alert-box" data-alert-for="abdomen_condition">
                                    {{-- Alert content will be dynamically loaded --}}
                                </td>
                            </tr>

                            {{-- EXTREMITIES --}}
                            <tr class="border-2 border-line-brown">
                                <th class="bg-yellow-light text-brown">EXTREMITIES</th>
                                <td>
                                    <textarea name="extremities" class="notepad-lines cdss-input" data-field-name="extremities"
                                        placeholder="Type here..">{{ old('extremities', $physicalExam->extremities ?? '') }}</textarea>
                                </td>
                                <td class="alert-box" data-alert-for="extremities">
                                    {{-- Alert content will be dynamically loaded --}}
                                </td>
                            </tr>

                            {{-- NEUROLOGICAL --}}
                            <tr class="border-2 border-line-brown">
                                <th class=" bg-yellow-light text-brown rounded-bl-lg">NEUROLOGICAL</th>
                                <td>
                                    <textarea name="neurological" class="notepad-lines cdss-input"
                                        data-field-name="neurological"
                                        placeholder="Type here..">{{ old('neurological', $physicalExam->neurological ?? '') }}</textarea>
                                </td>
                                <td class="rounded-br-lg alert-box" data-alert-for="neurological">
                                    {{-- Alert content will be dynamically loaded --}}
                                </td>
                            </tr>

                        </table>
                    </div>
                </center>

                <div class="w-[70%] mx-auto flex justify-end mt-5 mb-30 space-x-4">
                    <button type="button" class="button-default">CDSS</button>
                    <button type="submit" class="button-default">SUBMIT</button>       
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