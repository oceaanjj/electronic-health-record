@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')

    <div id="form-content-container" class="w-full overflow-x-hidden">
        {{-- CDSS ALERT BANNER --}}
        @if ($selectedPatient && isset($adlData))
            <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                <div id="cdss-alert-content"
                    class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-lg">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined animate-pulse text-[#dcb44e]">info</span>
                        <span class="text-sm font-semibold text-[#dcb44e]">
                            Clinical Decision Support System is now available for this date.
                        </span>
                    </div>

                    <button type="button" onclick="closeCdssAlert()"
                        class="group flex items-center justify-center rounded-full p-1 text-amber-700 transition-all duration-300 hover:bg-amber-200/50 active:scale-90">
                        <span
                            class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90">
                            close
                        </span>
                    </button>
                </div>
            </div>
        @endif

        {{-- HEADER SECTION --}}
        <div class="mx-auto mt-10 mb-5 w-[90%] lg:w-[80%]">
            <div class="flex flex-wrap items-center justify-start gap-x-10 gap-y-4">

                {{-- PATIENT NAME --}}
                <div class="flex items-center gap-4">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                        PATIENT NAME :
                    </label>
                    <div class="w-full lg:w-[350px]">
                        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                            :selectRoute="route('adl.select')" :inputValue="$selectedPatient?->name ?? ''" />
                    </div>
                </div>

                {{-- DATE & DAY SELECTOR --}}
                @if ($selectedPatient)
                    <div class="w-full lg:w-auto">
                        <x-date-day-selector :currentDate="$currentDate" :currentDayNo="$currentDayNo"
                            :totalDays="$totalDaysSinceAdmission ?? 30" formId="adl-form" />
                    </div>
                @endif
            </div>

            {{-- NOT AVAILABLE FOOTER --}}
            @if ($selectedPatient && !isset($adlData))
                <div class="mt-4 flex items-center gap-2 text-xs italic text-gray-500">
                    <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                    Clinical Decision Support System is not yet available (No records for this date).
                </div>
            @endif
        </div>

        {{-- FORM --}}
        <form id="adl-form" method="POST" action="{{ route('adl.store') }}" class="cdss-form"
            data-analyze-url="{{ route('adl.analyze-field') }}" data-batch-analyze-url="{{ route('adl.analyze-batch') }}"
            data-alert-height-class="h-[96px]">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
            <input type="hidden" name="date" value="{{ $currentDate ?? now()->format('Y-m-d') }}" />
            <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}" />

            <fieldset @if (!$selectedPatient) disabled @endif>
                <center>
                    <div class="mt-2 flex w-[90%] items-center gap-1 lg:w-[80%]">
                        <div class="flex-1 text-left">
                            <p class="main-header mb-3 rounded-[15px] lg:mb-1">ACTIVITIES OF DAILY LIVING</p>
                        </div>
                        <div class="hidden lg:block lg:w-[60px]"></div>
                    </div>
                </center>

                <center>
                    @php
                        $adlFields = [
                            'mobility_assessment' => 'MOBILITY',
                            'hygiene_assessment' => 'HYGIENE',
                            'toileting_assessment' => 'TOILETING',
                            'feeding_assessment' => 'FEEDING',
                            'hydration_assessment' => 'HYDRATION',
                            'sleep_pattern_assessment' => 'SLEEP PATTERN',
                            'pain_level_assessment' => 'PAIN LEVEL',
                        ];
                    @endphp

                    @foreach ($adlFields as $key => $label)
                        {{-- Row Wrapper: Card styling for mobile, original flex for desktop --}}
                        <div
                            class="relative mb-6 flex w-[90%] flex-col items-center overflow-hidden rounded-[15px] border border-[#c18b04] bg-beige lg:mb-1.5 lg:w-[80%] lg:flex-row lg:items-center lg:gap-1 lg:overflow-visible lg:rounded-none lg:border-none lg:bg-transparent">

                            {{-- Mobile Card Label --}}
                            <div class="main-header w-full pl-3 p-4 pr-12 text-left text-[15px] lg:hidden">
                                {{ $label }}
                            </div>

                            {{-- Main Table Section --}}
                            <table class="w-full flex-1 border-separate border-spacing-0 lg:bg-beige lg:table lg:border-none">
                                <tbody class="block lg:table-row-group">
                                    <tr class="block lg:table-row">
                                        {{-- System Header (Desktop side-header) --}}
                                        <th rowspan="2"
                                            class="main-header hidden w-full p-2 text-wrap lg:table-cell lg:w-[200px] lg:rounded-l-lg">
                                            {{ $label }}
                                        </th>
                                        {{-- Assessment Header: --}}
                                        <th
                                            class="bg-yellow-light text-brown border-line-brown block p-1 lg:p-0 text-[12px] font-bold lg:table-cell lg:rounded-tr-lg lg:p-1 lg:text-[13px]">
                                            ASSESSMENT
                                        </th>
                                    </tr>

                                    <tr class="block lg:table-row">
                                        <td class="block lg:table-cell lg:rounded-br-lg">
                                            <textarea
                                                class="notepad-lines cdss-input h-[120px] w-full border-none bg-transparent p-4 pr-16 lg:h-[100px] lg:p-4 lg:pr-4"
                                                name="{{ $key }}" placeholder="Type here..."
                                                data-field-name="{{ $key }}">{{ old($key, $adlData->$key ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- ALERT CONTAINER (Pinned to top-right on mobile) --}}
                            <div class="absolute right-4 top-2.5 z-10 flex items-center justify-center lg:static lg:h-[100px] lg:w-[70px] lg:pl-5"
                                data-alert-for="{{ $key }}">
                                <div class="alert-icon-btn is-empty">
                                    <span class="material-symbols-outlined">notifications</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </center>

                {{-- BUTTONS --}}
                <div class="mx-auto mt-5 mb-20 flex w-[90%] flex-row justify-end gap-4 lg:mb-30 lg:w-[80%]">
                    @if (isset($adlData))
                        <a href="{{ route('nursing-diagnosis.start', ['component' => 'adl', 'id' => $adlData->id]) }}"
                            class="button-default cdss-btn inline-block text-center">
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
    @vite(['resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/alert.js', 'resources/js/date-day-sync.js', 'resources/js/close-cdss-alert.js'])
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.initSearchableDropdown) {
                window.initSearchableDropdown();
            }
        });
    </script>
@endpush