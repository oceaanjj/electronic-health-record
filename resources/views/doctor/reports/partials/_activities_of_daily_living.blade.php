@extends('layouts.app')
@section('title', 'Activities of Daily Living')
@section('content')

    {{-- FORM OVERLAY (ALERT) & DYNAMIC CONTENT --}}
    <div id="form-content-container">

        @if (!session('selected_patient_id'))
            <div
                class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                <span class="text-gray-600 font-creato">Please select a patient to input</span>
            </div>
        @endif

        <!-- DROPDOWN component -->
        <x-searchable-patient-dropdown
            :patients="$patients"
            :selectedPatient="$selectedPatient"
            selectRoute="{{ route('activities-daily-living.select') }}"
            inputPlaceholder="-Select or type to search-"
            inputName="patient_id"
            inputValue="{{ session('selected_patient_id') }}"
        />

        <form action="{{ route('activities-daily-living.store') }}" method="POST"
              class="cdss-form"
              data-analyze-url="{{ route('activities-daily-living.analyze-field') }}"
              data-batch-analyze-url="{{ route('activities-daily-living.analyze-batch') }}"
              data-alert-height-class="h-[90px]">

            @csrf

            <input type="hidden" name="patient_id" id="patient_id_hidden"
                   value="{{ session('selected_patient_id') }}">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>

                <center>
                    <div class="w-[70%] mx-auto flex justify-center items-start gap-0 mt-6">

                        {{-- ================= LEFT TABLE (ASSESSMENT) ================= --}}
                        <div class="w-[68%] rounded-[15px] overflow-hidden mr-1">

                            <table class="w-full border-separate border-spacing-0">
                                <tr>
                                    <th class="w-[20%] main-header py-2 text-white rounded-tl-lg">
                                        CATEGORY
                                    </th>
                                    <th class="w-[45%] main-header py-2 text-white rounded-tr-lg">
                                        ASSESSMENT
                                    </th>
                                </tr>

                                {{-- MOBILITY --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">
                                        MOBILITY
                                    </th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="mobility_assessment"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="mobility_assessment"
                                            placeholder="Type here..">{{ old('mobility_assessment', $adl->mobility_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- HYGIENE --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">
                                        HYGIENE
                                    </th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="hygiene_assessment"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="hygiene_assessment"
                                            placeholder="Type here..">{{ old('hygiene_assessment', $adl->hygiene_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- TOILETING --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">
                                        TOILETING
                                    </th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="toileting_assessment"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="toileting_assessment"
                                            placeholder="Type here..">{{ old('toileting_assessment', $adl->toileting_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- FEEDING --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">
                                        FEEDING
                                    </th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="feeding_assessment"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="feeding_assessment"
                                            placeholder="Type here..">{{ old('feeding_assessment', $adl->feeding_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- HYDRATION --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">
                                        HYDRATION
                                    </th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="hydration_assessment"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="hydration_assessment"
                                            placeholder="Type here..">{{ old('hydration_assessment', $adl->hydration_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- SLEEP PATTERN --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-b-2 border-line-brown">
                                        SLEEP PATTERN
                                    </th>
                                    <td class="bg-beige border-b-2 border-line-brown/50">
                                        <textarea name="sleep_pattern_assessment"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="sleep_pattern_assessment"
                                            placeholder="Type here..">{{ old('sleep_pattern_assessment', $adl->sleep_pattern_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>

                                {{-- PAIN LEVEL --}}
                                <tr class="border-2 border-line-brown">
                                    <th class="bg-yellow-light text-brown rounded-bl-lg">
                                        PAIN LEVEL
                                    </th>
                                    <td class="bg-beige">
                                        <textarea name="pain_level_assessment"
                                            class="notepad-lines cdss-input w-full h-[90px] border-none"
                                            data-field-name="pain_level_assessment"
                                            placeholder="Type here..">{{ old('pain_level_assessment', $adl->pain_level_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- ================= RIGHT TABLE (ALERTS) ================= --}}
                        <div class="w-[25%] rounded-[15px] overflow-hidden">

                            <div class="main-header text-white py-2 mb-1 text-center rounded-[15px]">
                                ALERTS
                            </div>

                            <table class="w-full border-collapse">
                                @php
                                    $fields = [
                                        'mobility_assessment' => 'MOBILITY',
                                        'hygiene_assessment' => 'HYGIENE',
                                        'toileting_assessment' => 'TOILETING',
                                        'feeding_assessment' => 'FEEDING',
                                        'hydration_assessment' => 'HYDRATION',
                                        'sleep_pattern_assessment' => 'SLEEP PATTERN',
                                        'pain_level_assessment' => 'PAIN LEVEL',
                                    ];
                                @endphp

                                @foreach ($fields as $fieldKey => $label)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="alert-box my-0.5 py-4 px-3 flex justify-center items-center w-full h-[90px]"
                                                 data-alert-for="{{ $fieldKey }}">
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
                <div class="w-[66%] mx-auto flex justify-end mt-5 mb-20 space-x-4">

                    @if (isset($adl))
                        <a href="{{ route('nursing-diagnosis.start', ['component' => 'activities-daily-living', 'id' => $adl->id]) }}"
                           class="button-default text-center">
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
