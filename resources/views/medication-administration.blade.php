@extends('layouts.app')
@section('title', 'Patient Vital Signs')
@section('content')
    <div id="form-content-container">
        <form
            id="medication-administration-form"
            method="POST"
            action="{{ route('medication-administration.store') }}"
            class="cdss-form relative"
        >
            @csrf

            {{-- MEDICATION ADMINISTRATION PATIENT SELECTION (Synced with Vital Signs UI) --}}
            <div class="mx-auto w-full px-4 pt-10">
                <div class="ml-20 flex flex-wrap items-center gap-x-10 gap-y-4">
                    {{-- 1. PATIENT SECTION --}}
                    <div class="flex items-center gap-4">
                        <label
                            for="patient_search_input"
                            class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap"
                        >
                            PATIENT NAME :
                        </label>

                        <div
                            class="searchable-dropdown relative w-[350px]"
                            data-select-url="{{ route('medication-administration.select-patient') }}"
                        >
                            <input
                                type="text"
                                id="patient_search_input"
                                placeholder="Select or type Patient Name..."
                                value="{{ trim($selectedPatient->name ?? '') }}"
                                autocomplete="off"
                                {{-- Updated classes for UI consistency --}}
                                class="font-creato-bold w-full rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 focus:outline-none"
                            />

                            <div
                                id="patient_options_container"
                                class="absolute z-50 mt-2 hidden max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
                            >
                                @foreach ($patients as $patient)
                                    <div
                                        class="option cursor-pointer px-4 py-2 transition duration-150 hover:bg-blue-100"
                                        data-value="{{ $patient->patient_id }}"
                                    >
                                        {{ trim($patient->name) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Hidden IDs required for scripts --}}
                        <input
                            type="hidden"
                            name="patient_id"
                            id="patient_id_for_form"
                            value="{{ $selectedPatient->patient_id ?? '' }}"
                        />
                        <input type="hidden" id="patient_id_hidden" value="{{ $selectedPatient->patient_id ?? '' }}" />
                    </div>

                    {{-- 2. DATE SELECTOR (Synced with x-date-day-selector style) --}}
                    <div class="flex items-center gap-4">
                        <label for="date_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">
                            DATE :
                        </label>
                        <input
                            type="date"
                            id="date_selector"
                            name="date"
                            value="{{ $selectedDate ?? now()->format('Y-m-d') }}"
                            {{-- Match the "Vital Signs" pill style --}}
                            class="font-creato-bold rounded-full border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 focus:outline-none disabled:cursor-not-allowed disabled:bg-gray-100 disabled:opacity-70"
                            @if(!$selectedPatient) disabled @endif
                        />
                    </div>
                </div>
            </div>

            <fieldset @if(!$selectedPatient) disabled @endif>
                {{-- MAIN CONTAINER --}}
                <div class="mx-auto flex w-[100%] items-start justify-center gap-1 mt-10">
                    <center>
                        <div class="w-[85%] overflow-hidden rounded-[15px]">
                            <table class="w-full table-fixed border-collapse border-spacing-y-0">
                                <tr>
                                    <th class="main-header w-[20%] rounded-tl-[15px]">MEDICATION</th>
                                    <th class="main-header w-[15%]">DOSE</th>
                                    <th class="main-header w-[15%]">ROUTE</th>
                                    <th class="main-header w-[15%]">FREQUENCY</th>
                                    <th class="main-header w-[20%]">COMMENTS</th>
                                    <th class="main-header w-[15%] rounded-tr-[15px]">TIME</th>
                                </tr>

                                {{-- Row 1 (10:00 AM) --}}
                                <tr class="border-line-brown/70 h-[100px] border-b-2">
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="medication[]"
                                            placeholder="Medication"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                        <input type="hidden" name="time[]" value="10:00:00" />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="dose[]"
                                            placeholder="Dose"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="route[]"
                                            placeholder="Route"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="frequency[]"
                                            placeholder="Frequency"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="comments[]"
                                            placeholder="Comments"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <th class="bg-yellow-light text-brown font-semibold">10:00 AM</th>
                                </tr>

                                {{-- Row 2 (2:00 PM) --}}
                                <tr class="border-line-brown/70 h-[100px] border-b-2">
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="medication[]"
                                            placeholder="Medication"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                        <input type="hidden" name="time[]" value="14:00:00" />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="dose[]"
                                            placeholder="Dose"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="route[]"
                                            placeholder="Route"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="frequency[]"
                                            placeholder="Frequency"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="comments[]"
                                            placeholder="Comments"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <th class="bg-yellow-light text-brown font-semibold">2:00 PM</th>
                                </tr>

                                {{-- Row 3 (6:00 PM) --}}
                                <tr>
                                    <td class="bg-beige h-[100px] text-center">
                                        <input
                                            type="text"
                                            name="medication[]"
                                            placeholder="Medication"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                        <input type="hidden" name="time[]" value="18:00:00" />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="dose[]"
                                            placeholder="Dose"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="route[]"
                                            placeholder="Route"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="frequency[]"
                                            placeholder="Frequency"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input
                                            type="text"
                                            name="comments[]"
                                            placeholder="Comments"
                                            class="medication-input h-[45px] w-full text-center focus:outline-none"
                                        />
                                    </td>
                                    <th class="text-brown bg-yellow-light font-semibold">6:00 PM</th>
                                </tr>
                            </table>
                        </div>

                        <div class="mx-auto mt-5 mb-20 flex w-[70%] justify-end space-x-4">
                            <button class="button-default" type="submit" id="submit_button">SUBMIT</button>
                        </div>
                    </center>
                </div>
            </fieldset>
        </form>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/medication-administration.css'])
@endpush

@push('scripts')
    @vite([
        'resources/js/date-day-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/medication-administration-loader.js',
    ])
@endpush
