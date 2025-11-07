@extends('layouts.app')
@section('title', 'Physical Exam')
@section('content')

    {{-- NOTE : sa css ko a-add pa ko my-1 py-4 px-3 each alerts tenks wag niyo burahin to makakalimutan ko --}}

    {{-- FORM OVERLAY (ALERT) & DYNAMIC CONTENT --}}
    <div id="form-content-container">
        @if (!session('selected_patient_id'))
            <div
                class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                <span class="text-gray-600 font-creato">Please select a patient to input</span>
            </div>
        @endif

        <!-- DROPDOWN component -->
        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
            selectRoute="{{ route('physical-exam.select') }}" inputPlaceholder="-Select or type to search-"
            inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />

        <form action="{{ route('physical-exam.store') }}" method="POST" class="cdss-form"
            data-analyze-url="{{ route('physical-exam.analyze-field') }}">
            @csrf
            <input type="hidden" name="patient_id" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <div class="w-[70%] mx-auto flex justify-center items-start gap-0 mt-6">

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
                                        <textarea name="general_appearance"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="general_appearance"
                                            placeholder="Type here..">{{ old('general_appearance', $physicalExam->general_appearance ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- SKIN --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">SKIN</th>
                                    <td class="bg-beige border-b-2 border-line-brown">
                                        <textarea name="skin_condition"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="skin_condition"
                                            placeholder="Type here..">{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- EYES --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">EYES</th>
                                    <td class="bg-beige border-b-2 border-line-brown">
                                        <textarea name="eye_condition"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="eye_condition"
                                            placeholder="Type here..">{{ old('eye_condition', $physicalExam->eye_condition ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- ORAL CAVITY --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">ORAL CAVITY</th>
                                    <td class="bg-beige border-b-2 border-line-brown">
                                        <textarea name="oral_condition"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="oral_condition"
                                            placeholder="Type here..">{{ old('oral_condition', $physicalExam->oral_condition ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- CARDIOVASCULAR --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">CARDIOVASCULAR</th>
                                    <td class="bg-beige border-b-2 border-line-brown">
                                        <textarea name="cardiovascular"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="cardiovascular"
                                            placeholder="Type here..">{{ old('cardiovascular', $physicalExam->cardiovascular ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- ABDOMEN --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">ABDOMEN</th>
                                    <td class="bg-beige border-b-2 border-line-brown">
                                        <textarea name="abdomen_condition"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="abdomen_condition"
                                            placeholder="Type here..">{{ old('abdomen_condition', $physicalExam->abdomen_condition ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- EXTREMITIES --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">EXTREMITIES</th>
                                    <td class="bg-beige border-b-2 border-line-brown">
                                        <textarea name="extremities"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
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

                        {{-- ALERTS TABLE--}}
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

                <div class="w-[66%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
                    <button type="button" class="button-default">CDSS</button>
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </fieldset>
        </form>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cdssForm = document.querySelector('.cdss-form');
            if (cdssForm && window.initializeCdssForForm) {
                window.initializeCdssForForm(cdssForm);
                window.triggerInitialCdssAnalysis(cdssForm);
            }
        });
    </script>
@endpush