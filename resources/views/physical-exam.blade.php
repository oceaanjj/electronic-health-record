@extends('layouts.app')
@section('title', 'Physical Exam')
@section('content')

    <div id="form-content-container">

        {{-- 1. THE ALERT/ERROR FIRST (Only shows if CDSS is available) --}}
        @if ($selectedPatient && isset($physicalExam) && $physicalExam)
            <div class="mt-3w-full px-2">
                <div
                    class="relative flex items-center justify-between py-3 px-5 border border-amber-400/50 rounded-lg shadow-sm bg-amber-100/70 backdrop-blur-md transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#dcb44e]">info</span>
                        <span class="text-sm font-semibold text-[#dcb44e]">
                            Clinical Decision Support System is now available.
                        </span>
                    </div>
                    <button type="button" onclick="this.closest('.relative').remove()"
                        class="flex items-center justify-center text-amber-700 hover:text-amber-950 hover:bg-amber-200/50 rounded-full p-1 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- 2. THE PATIENT SELECTION ROW --}}
        <div class="flex flex-col gap-2 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                    selectRoute="{{ route('physical-exam.select') }}" inputPlaceholder="-Select or type to search-"
                    inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />
            </div>
        </div>



        <form action="{{ route('physical-exam.store') }}" method="POST" class="cdss-form relative w-[85%] mx-auto"
            data-analyze-url="{{ route('physical-exam.analyze-field') }}"
            data-batch-analyze-url="{{ route('physical-exam.analyze-batch') }}" data-alert-height-class="h-[90px]">


            {{-- 3. THE "NOT AVAILABLE" MESSAGE (Stays at the bottom of the dropdown) --}}
            @if ($selectedPatient && (!isset($physicalExam) || !$physicalExam))
                <div class="text-xs text-gray-500 italic flex items-center gap-2">
                    <span class="material-symbols-outlined text-[14px]">pending_actions</span>
                    Clinical Decision Support System is not yet available.
                </div>
            @endif

            @csrf

            {{-- HIDDEN INPUT FOR JS TO CHECK --}}
            <input type="hidden" name="patient_id" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <div class="w-[100%] flex justify-center items-start gap-0 mt-2">

                        <div class="w-full rounded-[15px] overflow-hidden mr-1">

                            <table class="w-full border-separate border-spacing-0">
                                <tr>
                                    <th class="w-[30%] main-header py-2 text-white rounded-tl-lg">SYSTEM</th>
                                    <th class="w-[55%] main-header py-2 text-white rounded-tr-lg">FINDINGS</th>
                                </tr>

                                {{-- GENERAL APPEARANCE --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">
                                        GENERAL<br>APPEARANCE
                                    </th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="general_appearance"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="general_appearance"
                                            placeholder="Type here..">{{ old('general_appearance', $physicalExam->general_appearance ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- SKIN --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">SKIN</th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="skin_condition"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="skin_condition"
                                            placeholder="Type here..">{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- EYES --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">EYES</th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="eye_condition"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="eye_condition"
                                            placeholder="Type here..">{{ old('eye_condition', $physicalExam->eye_condition ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- ORAL CAVITY --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">ORAL CAVITY</th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="oral_condition"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="oral_condition"
                                            placeholder="Type here..">{{ old('oral_condition', $physicalExam->oral_condition ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- CARDIOVASCULAR --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">CARDIOVASCULAR</th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="cardiovascular"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="cardiovascular"
                                            placeholder="Type here..">{{ old('cardiovascular', $physicalExam->cardiovascular ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- ABDOMEN --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">ABDOMEN</th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
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
                        <div class="w-[50%] rounded-[15px] overflow-hidden">
                            <div class="main-header py-2 mb-1 text-center rounded-[15px]">
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
                                            <div class="alert-box my-0.5 py-4 px-3 flex justify-center items-center w-full h-[92px]"
                                                data-alert-for="{{ $fieldKey }}">
                                                {{-- Dynamic alert content will load here --}}
                                                <span class="opacity-70 text-white font-semibold">NO ALERTS</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </center>

                <div class="w-[80%] mx-auto flex justify-end mt-5 mb-20 space-x-4">

                    <!-- cdss button -->
                    @if (isset($physicalExam))
                        <a href="{{ route('nursing-diagnosis.start', ['component' => 'physical-exam', 'id' => $physicalExam->id]) }}"
                            class="button-default cdss-btn text-center">
                            CDSS
                        </a>
                    @endif

                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </fieldset>
        </form>
    </div>

@endsection

@push('scripts')
    @vite([
        'resources/js/alert.js',
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js'
    ])
@endpush