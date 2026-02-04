@extends('layouts.app')
@section('title', 'Patient Vital Signs')
@section('content')

    <div id="form-content-container">

        <form id="medication-administration-form" method="POST" action="{{ route('medication-administration.store') }}"
            class="cdss-form relative">
            @csrf

            <div class="header flex items-center gap-6 my-10 mx-60 w-[100%] relative z-[60]">
                <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
                    PATIENT NAME :
                </label>

                <div class="searchable-dropdown relative w-[280px]"
                    data-select-url="{{ route('medication-administration.select-patient') }}">
                    <input type="text" id="patient_search_input" placeholder="- Select or type to search -"
                        value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off"
                        class="w-full px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">

                    <div id="patient_options_container"
                        class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                        @foreach ($patients as $patient)
                            <div class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150"
                                data-value="{{ $patient->patient_id }}">
                                {{ trim($patient->name) }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Hidden IDs required for scripts --}}
                <input type="hidden" name="patient_id" id="patient_id_for_form"
                    value="{{ $selectedPatient->patient_id ?? '' }}">
                {{-- Alias ID for global 'alert.js' compatibility --}}
                <input type="hidden" id="patient_id_hidden" value="{{ $selectedPatient->patient_id ?? '' }}">

                {{-- 📅 DATE SELECTOR --}}
                <label for="date_selector" class="whitespace-nowrap font-alte font-bold text-dark-green">
                    DATE :
                </label>
                <input type="date" id="date_selector" name="date" value="{{ $selectedDate ?? now()->format('Y-m-d') }}"
                    class="text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-1 focus:ring-gray-400 focus:border-gray-400 outline-none shadow-sm bg-gray-100"
                    disabled>
            </div>


            <fieldset @if(!$selectedPatient) disabled @endif>
                {{-- MAIN CONTAINER --}}
                <div class="w-[100%] mx-auto flex justify-center items-start gap-1">
                    <center>
                        <div class="w-[85%] rounded-[15px] overflow-hidden">
                            <table class="w-full table-fixed border-collapse border-spacing-y-0">
                                <tr>
                                    <th class="w-[20%] main-header rounded-tl-[15px]">MEDICATION</th>
                                    <th class="w-[15%] main-header">DOSE</th>
                                    <th class="w-[15%] main-header">ROUTE</th>
                                    <th class="w-[15%] main-header">FREQUENCY</th>
                                    <th class="w-[20%] main-header">COMMENTS</th>
                                    <th class="w-[15%] main-header rounded-tr-[15px]">TIME</th>
                                </tr>

                                {{-- Row 1 (10:00 AM) --}}
                                <tr class="border-b-2 border-line-brown/70 h-[100px]">
                                    <td class="bg-beige text-center">
                                        <input type="text" name="medication[]" placeholder="Medication"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                        <input type="hidden" name="time[]" value="10:00:00">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="dose[]" placeholder="Dose"
                                            class="w-full h-[45px] focus:outline-none text-center medication-input cdss-input">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="route[]" placeholder="Route"
                                            class="w-full h-[45px] focus:outline-none text-center medication-input cdss-input">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="frequency[]" placeholder="Frequency"
                                            class="w-full h-[45px] focus:outline-none text-center medication-input cdss-input">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="comments[]" placeholder="Comments"
                                            class="w-full h-[45px] focus:outline-none text-center medication-input cdss-input">
                                    </td>
                                    <th class="bg-yellow-light text-brown font-semibold">10:00 AM</th>
                                </tr>

                                {{-- Row 2 (2:00 PM) --}}
                                <tr class="border-b-2 border-line-brown/70 h-[100px]">
                                    <td class="bg-beige text-center">
                                        <input type="text" name="medication[]" placeholder="Medication"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                        <input type="hidden" name="time[]" value="14:00:00">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="dose[]" placeholder="Dose"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="route[]" placeholder="Route"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="frequency[]" placeholder="Frequency"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="comments[]" placeholder="Comments"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                    </td>
                                    <th class="bg-yellow-light text-brown font-semibold">2:00 PM</th>
                                </tr>

                                {{-- Row 3 (6:00 PM) --}}
                                <tr>
                                    <td class="bg-beige text-center h-[100px]">
                                        <input type="text" name="medication[]" placeholder="Medication"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                        <input type="hidden" name="time[]" value="18:00:00">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="dose[]" placeholder="Dose"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="route[]" placeholder="Route"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="frequency[]" placeholder="Frequency"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                    </td>
                                    <td class="bg-beige text-center">
                                        <input type="text" name="comments[]" placeholder="Comments"
                                            class="w-full h-[45px] text-center focus:outline-none medication-input cdss-input">
                                    </td>
                                    <th class=" text-brown font-semibold bg-yellow-light">6:00 PM</th>
                                </tr>
                            </table>
                        </div>

                        <div class="w-[70%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
                            <button class="button-default cdss-btn opacity-50 pointer-events-none" type="submit" id="submit_button">SUBMIT</button>
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
    @vite(
        [
            'resources/js/date-day-loader.js',
            'resources/js/searchable-dropdown.js',
            'resources/js/medication-administration-loader.js',
            'resources/js/medication-form-validation.js',
        ]
    )

@endpush