@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')
    <div id="form-content-container">
        {{-- 1. NEW STRUCTURED HEADER (Layout Fix) --}}
        <div class="mx-auto mt-1 w-full">
            {{-- CDSS ALERT BANNER --}}
            @if ($selectedPatient && isset($adlData))
                {{-- Wrapper matches Physical Exam sizing and transition behavior --}}
                <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                    {{-- Content matches Physical Exam's mt-3, py-3, and px-5 exactly --}}
                    <div
                        id="cdss-alert-content"
                        class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-md"
                    >
                        <div class="flex items-center gap-3">
                            {{-- Pulsing Info Icon --}}
                            <span class="material-symbols-outlined animate-pulse text-[#dcb44e]">info</span>
                            <span class="text-sm font-semibold text-[#dcb44e]">
                                Clinical Decision Support System is now available for this date.
                            </span>
                        </div>

                        {{-- Standardized Close Button with Rotation --}}
                        <button
                            type="button"
                            onclick="closeCdssAlert()"
                            class="group flex items-center justify-center rounded-full p-1 text-amber-700 transition-all duration-300 hover:bg-amber-200/50 active:scale-90"
                        >
                            <span
                                class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90"
                            >
                                close
                            </span>
                        </button>
                    </div>
                </div>
            @endif

            <div class="mx-auto w-full px-4 pt-10">
                <div class="ml-20 flex flex-wrap items-center gap-x-10 gap-y-4">
                    {{-- 1. PATIENT SECTION --}}
                    <div class="flex items-center gap-4">
                        <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                            PATIENT NAME :
                        </label>
                        <div class="w-[320px]">
                            {{-- Standardized width --}}
                            <x-searchable-patient-dropdown
                                :patients="$patients"
                                :selectedPatient="$selectedPatient"
                                :selectRoute="route('adl.select')"
                                :inputValue="$selectedPatient?->name ?? ''"
                            />
                        </div>
                    </div>

                    {{-- 2. DATE & DAY SECTION (Synced with Vital Signs UI) --}}
                    @if ($selectedPatient)
                        <x-date-day-selector
                            :currentDate="$currentDate"
                            :currentDayNo="$currentDayNo"
                            :totalDays="$totalDaysSinceAdmission ?? 30"
                            formId="adl-form"
                        />
                    @endif
                </div>

                {{-- 3. NOT AVAILABLE FOOTER --}}
                @if ($selectedPatient && ! isset($adlData))
                    <div class="mt-4 ml-20 flex items-center gap-2 text-xs text-gray-500 italic">
                        <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                        Clinical Decision Support System is not yet available (No records for this date).
                    </div>
                @endif
            </div>
        </div>

        <form
            id="adl-form"
            method="POST"
            class="cdss-form relative mx-auto w-full mt-5"
            action="{{ route('adl.store') }}"
            data-analyze-url="{{ route('adl.analyze-field') }}"
            data-batch-analyze-url="{{ route('adl.analyze-batch') }}"
            data-alert-height-class="h-[96px]"
        >
            <fieldset @if (!$selectedPatient) disabled @endif>
                @csrf

                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
                <input type="hidden" name="date" value="{{ $currentDate ?? now()->format('Y-m-d') }}" />
                <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}" />

                <div class="w-[90%] mx-auto flex justify-center items-start gap-1 mt-6">
                    {{-- LEFT SIDE TABLE (INPUTS) --}}
                    <div class="w-[70%] overflow-hidden rounded-[15px]">
                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="main-header w-[30%] rounded-tl-lg py-2 text-center">CATEGORY</th>
                                <th class="main-header w-[60%] rounded-tr-lg">ASSESSMENT</th>
                            </tr>

                            @foreach ([
                                    'mobility_assessment' => 'MOBILITY',
                                    'hygiene_assessment' => 'HYGIENE',
                                    'toileting_assessment' => 'TOILETING',
                                    'feeding_assessment' => 'FEEDING',
                                    'hydration_assessment' => 'HYDRATION',
                                    'sleep_pattern_assessment' => 'SLEEP PATTERN',
                                    'pain_level_assessment' => 'PAIN LEVEL'
                                ]
                                as $field => $label)
                                <tr class="border-line-brown/50 border-b-2">
                                    <th class="bg-yellow-light text-brown py-2 text-center font-semibold">
                                        {{ $label }}
                                    </th>
                                    <td class="bg-beige">
                                        <textarea
                                            name="{{ $field }}"
                                            placeholder="Type here..."
                                            class="notepad-lines cdss-input h-[95px] w-full"
                                            data-field-name="{{ $field }}"
                                        >
{{ old($field, $adlData->$field ?? '') }}</textarea
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    {{-- ALERTS TABLE --}}
                    <div class="w-[25%] rounded-[15px]">
                        <div class="main-header rounded-[15px] text-center">ALERTS</div>
                        <table class="w-full border-collapse">
                            @foreach ([
                                    'mobility_assessment',
                                    'hygiene_assessment',
                                    'toileting_assessment',
                                    'feeding_assessment',
                                    'hydration_assessment',
                                    'sleep_pattern_assessment',
                                    'pain_level_assessment'
                                ]
                                as $field)
                                @php
                                    $alertText = 'NO ALERTS';
                                    $alertSeverity = 'none';
                                    if (isset($alerts[$field]) && ! empty($alerts[$field]['alert']) && $alerts[$field]['alert'] !== 'No Findings') {
                                        $alertText = $alerts[$field]['alert'];
                                        $alertSeverity = strtolower($alerts[$field]['severity']);
                                    }
                                @endphp

                                <tr>
                                    <td class="align-middle" data-alert-for="{{ $field }}">
                                        <div
                                            class="alert-box alert-{{ $alertSeverity }} flex h-[96px] items-center justify-center"
                                        >
                                            <span class="font-semibold text-white opacity-70">{{ $alertText }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                <div class="mx-auto mt-5 mb-20 flex w-[80%] justify-end space-x-4">
                    @if (isset($adlData))
                        <a
                            href="{{ route('nursing-diagnosis.start', ['component' => 'adl', 'id' => $adlData->id]) }}"
                            class="button-default cdss-btn text-center"
                        >
                            CDSS
                        </a>
                    @endif

                    <button type="submit" form="adl-form" class="button-default">SUBMIT</button>
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
    ])

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.initSearchableDropdown) {
                window.initSearchableDropdown();
            }
        });
    </script>
@endpush
