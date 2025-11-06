@extends('layouts.app')
@section('title', 'Physical Exam')
@section('content')


    {{-- NOTE : sa css ko a-add pa ko my-1 py-4 px-3 each alerts tenks wag niyo burahin to makakalimutan ko --}}

                {{-- NEW SEARCHABLE PATIENT DROPDOWN --}}
            <div class="header flex items-center gap-4 my-10 mx-auto w-[70%]">
                <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
                    PATIENT NAME :
                </label>

                {{-- Searchable dropdown --}}
                <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('physical-exam.select') }}">
                    {{-- Text input --}}
                    <input 
                        type="text" 
                        id="patient_search_input" 
                        placeholder="-Select or type to search-" 
                        value="{{ trim($selectedPatient->name ?? '') }}" 
                        autocomplete="off"
                        class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                    >

                    {{-- Dropdown options --}}
                    <div 
                        id="patient_options_container" 
                        class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"
                    >
                        @foreach ($patients as $patient)
                            <div 
                                class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150"
                                data-value="{{ $patient->patient_id }}">
                                {{ trim($patient->name) }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Hidden input for form --}}
                    <input type="hidden" name="patient_id_for_form" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">
                </div>
            </div>
            {{-- END NEW SEARCHABLE PATIENT DROPDOWN --}}

            {{-- FORM OVERLAY --}}
            <div id="form-content-container">
                @if (!session('selected_patient_id'))
                    <div class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                        <span class="text-gray-600 font-creato">Please select a patient to input</span>
                    </div>
                @endif
            </div>


    <form action="{{ route('physical-exam.store') }}" method="POST" class="cdss-form"
        data-analyze-url="{{ route('physical-exam.analyze-field') }}">
        @csrf
        <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <center>
                <div class="w-[70%] mx-auto flex justify-center items-start gap-0 mt-6">


                    {{-- LEFT SIDE: Physical Exam Inputs --}}
                    <div class="w-[68%] rounded-[15px] overflow-hidden">
                        <table class="w-full border-separate border-spacing-0">
                            <tr>
                                <th class="w-[20%] bg-dark-green py-2 text-white rounded-tl-lg">SYSTEM</th>
                                <th class="w-[45%] bg-dark-green py-2 text-white rounded-tr-lg">FINDINGS</th>
                            </tr>

                            {{-- GENERAL APPEARANCE --}}
                            <tr>
                                <th class="bg-yellow-light text-brown border-b-2 border-line-brown">
                                    GENERAL<br>APPEARANCE
                                </th>
                                <td class="bg-beige border-b-2 border-line-brown">
                                    <textarea name="general_appearance" class="notepad-lines cdss-input w-full h-[90px] border-none"
                                        data-field-name="general_appearance"
                                        placeholder="Type here..">{{ old('general_appearance', $physicalExam->general_appearance ?? '') }}</textarea>
                                </td>
                            </tr>

                            {{-- SKIN --}}
                            <tr>
                                <th class="bg-yellow-light text-brown border-b-2 border-line-brown">SKIN</th>
                                <td class="bg-beige border-b-2 border-line-brown">
                                    <textarea name="skin_condition" class="notepad-lines cdss-input w-full h-[90px] border-none"
                                        data-field-name="skin_condition"
                                        placeholder="Type here..">{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}</textarea>
                                </td>
                            </tr>

                            {{-- EYES --}}
                            <tr>
                                <th class="bg-yellow-light text-brown border-b-2 border-line-brown">EYES</th>
                                <td class="bg-beige border-b-2 border-line-brown">
                                    <textarea name="eye_condition" class="notepad-lines cdss-input w-full h-[90px] border-none"
                                        data-field-name="eye_condition"
                                        placeholder="Type here..">{{ old('eye_condition', $physicalExam->eye_condition ?? '') }}</textarea>
                                </td>
                            </tr>

                            {{-- ORAL CAVITY --}}
                            <tr>
                                <th class="bg-yellow-light text-brown border-b-2 border-line-brown">ORAL CAVITY</th>
                                <td class="bg-beige border-b-2 border-line-brown">
                                    <textarea name="oral_condition" class="notepad-lines cdss-input w-full h-[90px] border-none"
                                        data-field-name="oral_condition"
                                        placeholder="Type here..">{{ old('oral_condition', $physicalExam->oral_condition ?? '') }}</textarea>
                                </td>
                            </tr>

                            {{-- CARDIOVASCULAR --}}
                            <tr>
                                <th class="bg-yellow-light text-brown border-b-2 border-line-brown">CARDIOVASCULAR</th>
                                <td class="bg-beige border-b-2 border-line-brown">
                                    <textarea name="cardiovascular" class="notepad-lines cdss-input w-full h-[90px] border-none"
                                        data-field-name="cardiovascular"
                                        placeholder="Type here..">{{ old('cardiovascular', $physicalExam->cardiovascular ?? '') }}</textarea>
                                </td>
                            </tr>

                            {{-- ABDOMEN --}}
                            <tr>
                                <th class="bg-yellow-light text-brown border-b-2 border-line-brown">ABDOMEN</th>
                                <td class="bg-beige border-b-2 border-line-brown">
                                    <textarea name="abdomen_condition" class="notepad-lines cdss-input w-full h-[90px] border-none"
                                        data-field-name="abdomen_condition"
                                        placeholder="Type here..">{{ old('abdomen_condition', $physicalExam->abdomen_condition ?? '') }}</textarea>
                                </td>
                            </tr>

                            {{-- EXTREMITIES --}}
                            <tr>
                                <th class="bg-yellow-light text-brown border-b-2 border-line-brown">EXTREMITIES</th>
                                <td class="bg-beige border-b-2 border-line-brown">
                                    <textarea name="extremities" class="notepad-lines cdss-input w-full h-[90px] border-none"
                                        data-field-name="extremities"
                                        placeholder="Type here..">{{ old('extremities', $physicalExam->extremities ?? '') }}</textarea>
                                </td>
                            </tr>

                            {{-- NEUROLOGICAL --}}
                            <tr class="border-2 border-line-brown">
                                <th class="bg-yellow-light text-brown rounded-bl-lg">NEUROLOGICAL</th>
                                <td class="bg-beige">
                                    <textarea name="neurological" class="notepad-lines cdss-input"
                                        data-field-name="neurological"
                                        placeholder="Type here..">{{ old('neurological', $physicalExam->neurological ?? '') }}</textarea>
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- RIGHT SIDE: ALERTS --}}
                    <div class="w-[25%] rounded-[15px] overflow-hidden">
                        <div class="bg-dark-green text-white font-bold py-2 mb-1 text-center rounded-[15px]">
                            ALERTS
                        </div>
                        <table class="w-full border-collapse">
                            @php
                                $fields = [
                                    'general_appearance' => 'GENERAL APPEARANCE',
                                    'skin_condition' => 'SKIN',
                                    'eye_condition' => 'EYES',
                                    'oral_condition' => 'ORAL CAVITY',
                                    'cardiovascular' => 'CARDIOVASCULAR',
                                    'abdomen_condition' => 'ABDOMEN',
                                    'extremities' => 'EXTREMITIES',
                                    'neurological' => 'NEUROLOGICAL',
                                ];
                            @endphp

                            @foreach ($fields as $fieldKey => $label)
                                <tr>
                                    <td class="align-middle">
                                        <div class="alert-box my-0.5 py-4 px-3 flex justify-center items-center w-full h-[90px]"
                                            data-alert-for="{{ $fieldKey }}">
                                            {{-- Dynamic alert content will load here --}}
                                            <span class="opacity-70 text-white font-semibold">No Alerts</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
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


@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
@endpush