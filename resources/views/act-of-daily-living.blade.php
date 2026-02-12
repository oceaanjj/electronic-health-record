@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')
    <div id="form-content-container">
        {{-- CDSS ALERT BANNER --}}
        @if ($selectedPatient && isset($adlData))
            <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                <div id="cdss-alert-content"
                    class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-md">
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
        <div class="m-10 ml-20 flex flex-wrap items-center gap-x-10 gap-y-4">
            {{-- PATIENT NAME --}}
            <div class="flex items-center gap-4">
                <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                    PATIENT NAME :
                </label>
                <div class="w-[350px]">
                    <x-searchable-patient-dropdown :patients="$patients"
                            :selectedPatient="$selectedPatient"
                            :selectRoute="route('adl.select')"
                            :inputValue="$selectedPatient?->name ?? ''"
                        />
                    </div>
                </div>

                {{-- DATE & DAY SELECTOR --}}
                @if ($selectedPatient)
                    <x-date-day-selector
                        :currentDate="$currentDate"
                        :currentDayNo="$currentDayNo"
                        :totalDays="$totalDaysSinceAdmission ?? 30"
                        formId="adl-form"
                    />
                @endif
            </div>

            {{-- NOT AVAILABLE FOOTER --}}
            @if ($selectedPatient && !isset($adlData))
                <div class="mt-4 ml-20 flex items-center gap-2 text-xs text-gray-500 italic">
                    <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                    Clinical Decision Support System is not yet available (No records for this date).
                </div>
            @endif

            {{-- FORM --}}
            <form
                id="adl-form"
                method="POST"
                action="{{ route('adl.store') }}"
                class="cdss-form"
                data-analyze-url="{{ route('adl.analyze-field') }}"
                data-batch-analyze-url="{{ route('adl.analyze-batch') }}"
                data-alert-height-class="h-[96px]"
            >
                @csrf
                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
                <input type="hidden" name="date" value="{{ $currentDate ?? now()->format('Y-m-d') }}" />
                <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}" />

                <fieldset @if (!$selectedPatient) disabled @endif>
                    <center>
                        {{-- Title only spans the table width, not the alerts --}}
                        <div class="flex w-[80%] items-center gap-1 mt-2">
                            <div class="flex-1">
                                <p class="main-header mb-1 rounded-[15px]">ACTIVITIES OF DAILY LIVING</p>
                            </div>
                            <div class="w-[60px]"></div>
                        </div>
                    </center>

                    <center>
                        {{-- MOBILITY --}}
                        <div class="mb-1.5 flex w-[80%] items-center gap-1">
                            <table class="bg-beige flex-1 border-separate border-spacing-0">
                                <tr>
                                    <th rowspan="2" class="main-header w-[200px] rounded-l-lg">MOBILITY</th>
                                    <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                        ASSESSMENT
                                    </th>
                                </tr>
                                <tr>
                                    <td class="rounded-br-lg">
                                        <textarea
                                            class="notepad-lines cdss-input h-[100px]"
                                            name="mobility_assessment"
                                            placeholder="Type here..."
                                            data-field-name="mobility_assessment"
                                        >{{ old('mobility_assessment', $adlData->mobility_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                            <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="mobility_assessment">
                                <div class="alert-icon-btn is-empty">
                                    <span class="material-symbols-outlined">notifications</span>
                                </div>
                            </div>
                        </div>

                        {{-- HYGIENE --}}
                        <div class="mb-1.5 flex w-[80%] items-center gap-1">
                            <table class="bg-beige flex-1 border-separate border-spacing-0">
                                <tr>
                                    <th rowspan="2" class="main-header w-[200px] rounded-l-lg">HYGIENE</th>
                                    <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                        ASSESSMENT
                                    </th>
                                </tr>
                                <tr>
                                    <td class="rounded-br-lg">
                                        <textarea
                                            class="notepad-lines cdss-input h-[100px]"
                                            name="hygiene_assessment"
                                            placeholder="Type here..."
                                            data-field-name="hygiene_assessment"
                                        >{{ old('hygiene_assessment', $adlData->hygiene_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                            <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="hygiene_assessment">
                                <div class="alert-icon-btn is-empty">
                                    <span class="material-symbols-outlined">notifications</span>
                                </div>
                            </div>
                        </div>

                        {{-- TOILETING --}}
                        <div class="mb-1.5 flex w-[80%] items-center gap-1">
                            <table class="bg-beige flex-1 border-separate border-spacing-0">
                                <tr>
                                    <th rowspan="2" class="main-header w-[200px] rounded-l-lg">TOILETING</th>
                                    <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                        ASSESSMENT
                                    </th>
                                </tr>
                                <tr>
                                    <td class="rounded-br-lg">
                                        <textarea
                                            class="notepad-lines cdss-input h-[100px]"
                                            name="toileting_assessment"
                                            placeholder="Type here..."
                                            data-field-name="toileting_assessment"
                                        >{{ old('toileting_assessment', $adlData->toileting_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                            <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="toileting_assessment">
                                <div class="alert-icon-btn is-empty">
                                    <span class="material-symbols-outlined">notifications</span>
                                </div>
                            </div>
                        </div>

                        {{-- FEEDING --}}
                        <div class="mb-1.5 flex w-[80%] items-center gap-1">
                            <table class="bg-beige flex-1 border-separate border-spacing-0">
                                <tr>
                                    <th rowspan="2" class="main-header w-[200px] rounded-l-lg">FEEDING</th>
                                    <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                        ASSESSMENT
                                    </th>
                                </tr>
                                <tr>
                                    <td class="rounded-br-lg">
                                        <textarea
                                            class="notepad-lines cdss-input h-[100px]"
                                            name="feeding_assessment"
                                            placeholder="Type here..."
                                            data-field-name="feeding_assessment"
                                        >{{ old('feeding_assessment', $adlData->feeding_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                            <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="feeding_assessment">
                                <div class="alert-icon-btn is-empty">
                                    <span class="material-symbols-outlined">notifications</span>
                                </div>
                            </div>
                        </div>

                        {{-- HYDRATION --}}
                        <div class="mb-1.5 flex w-[80%] items-center gap-1">
                            <table class="bg-beige flex-1 border-separate border-spacing-0">
                                <tr>
                                    <th rowspan="2" class="main-header w-[200px] rounded-l-lg">HYDRATION</th>
                                    <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                        ASSESSMENT
                                    </th>
                                </tr>
                                <tr>
                                    <td class="rounded-br-lg">
                                        <textarea
                                            class="notepad-lines cdss-input h-[100px]"
                                            name="hydration_assessment"
                                            placeholder="Type here..."
                                            data-field-name="hydration_assessment"
                                        >{{ old('hydration_assessment', $adlData->hydration_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                            <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="hydration_assessment">
                                <div class="alert-icon-btn is-empty">
                                    <span class="material-symbols-outlined">notifications</span>
                                </div>
                            </div>
                        </div>

                        {{-- SLEEP PATTERN --}}
                        <div class="mb-1.5 flex w-[80%] items-center gap-1">
                            <table class="bg-beige flex-1 border-separate border-spacing-0">
                                <tr>
                                    <th rowspan="2" class="main-header w-[200px] rounded-l-lg">SLEEP PATTERN</th>
                                    <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                        ASSESSMENT
                                    </th>
                                </tr>
                                <tr>
                                    <td class="rounded-br-lg">
                                        <textarea
                                            class="notepad-lines cdss-input h-[100px]"
                                            name="sleep_pattern_assessment"
                                            placeholder="Type here..."
                                            data-field-name="sleep_pattern_assessment"
                                        >{{ old('sleep_pattern_assessment', $adlData->sleep_pattern_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                            <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="sleep_pattern_assessment">
                                <div class="alert-icon-btn is-empty">
                                    <span class="material-symbols-outlined">notifications</span>
                                </div>
                            </div>
                        </div>

                        {{-- PAIN LEVEL --}}
                        <div class="mb-1.5 flex w-[80%] items-center gap-1">
                            <table class="bg-beige flex-1 border-separate border-spacing-0">
                                <tr>
                                    <th rowspan="2" class="main-header w-[200px] rounded-l-lg">PAIN LEVEL</th>
                                    <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                        ASSESSMENT
                                    </th>
                                </tr>
                                <tr>
                                    <td class="rounded-br-lg">
                                        <textarea
                                            class="notepad-lines cdss-input h-[100px]"
                                            name="pain_level_assessment"
                                            placeholder="Type here..."
                                            data-field-name="pain_level_assessment"
                                        >{{ old('pain_level_assessment', $adlData->pain_level_assessment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                            <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="pain_level_assessment">
                                <div class="alert-icon-btn is-empty">
                                    <span class="material-symbols-outlined">notifications</span>
                                </div>
                            </div>
                        </div>
                    </center>

                    {{-- BUTTONS - Fixed to be on one line --}}
                    <div class="mx-auto mt-5 mb-30 flex w-[80%] flex-row justify-end gap-4">
                        @if (isset($adlData))
                            <a
                                href="{{ route('nursing-diagnosis.start', ['component' => 'adl', 'id' => $adlData->id]) }}"
                                class="button-default cdss-btn inline-block text-center"
                            >
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
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/alert.js',
        'resources/js/date-day-sync.js',
        'resources/js/close-cdss-alert.js'
    ])
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.initSearchableDropdown) {
                window.initSearchableDropdown();
            }
        });
    </script>
@endpush