@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')
    <div id="form-content-container">
        {{-- SEARCHABLE PATIENT DROPDOWN & DATE/DAY SELECTOR --}}
        {{-- Kept OUTSIDE the form so the disabled overlay doesn't block it --}}
        <div class="header mx-auto my-10 flex w-[80%] items-center gap-6">
            <div class="flex w-full items-center gap-6">
                @csrf

                {{-- PATIENT NAME --}}
                <label for="patient_search_input" class="font-alte text-dark-green font-bold whitespace-nowrap">
                    PATIENT NAME :
                </label>

                <div
                    class="searchable-dropdown relative w-[400px]"
                    data-select-url="{{ route('adl.select') }}"
                    data-admission-date="{{ $selectedPatient ? \Carbon\Carbon::parse($selectedPatient->admission_date)->format('Y-m-d') : '' }}"
                    data-sync-mode="html-reload"
                >
                    <input
                        type="text"
                        id="patient_search_input"
                        placeholder="Select or type Patient Name"
                        value="{{ trim($selectedPatient->name ?? '') }}"
                        autocomplete="off"
                        class="font-creato-bold w-full rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
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
                    <input
                        type="hidden"
                        id="patient_id_hidden"
                        name="patient_id"
                        value="{{ $selectedPatient->patient_id ?? '' }}"
                    />
                </div>

                {{-- DATE --}}
                <label for="date_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">DATE :</label>
                <input
                    type="date"
                    id="date_selector"
                    name="date"
                    value="{{ $currentDate ?? now()->format('Y-m-d') }}"
                    @if (!$selectedPatient) disabled @endif
                    class="font-creato-bold rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-gray-400 focus:ring-1 focus:ring-gray-400"
                />

                {{-- DAY NO --}}
                <label for="day_no" class="font-alte text-dark-green font-bold whitespace-nowrap">DAY NO :</label>
                <select
                    id="day_no_selector"
                    name="day_no"
                    @if (!$selectedPatient) disabled @endif
                    class="font-creato-bold w-[120px] rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                >
                    @for ($i = 1; $i <= $totalDaysSinceAdmission; $i++)
                        <option value="{{ $i }}" @if($currentDayNo == $i) selected @endif>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

        {{--
            FIX:
            1. Added 'relative', 'w-[70%]', 'mx-auto' to <form>.
            2. Added 'cdss-form' for JS targeting.
            This creates the correct "box" for the disabled overlay to appear in.
        --}}
        <form
            id="adl-form"
            method="POST"
            class="cdss-form relative mx-auto w-[70%]"
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

                <div class="mx-auto mt-6 flex w-[85%] items-start justify-center gap-1">
                    {{-- LEFT SIDE TABLE (INPUTS) --}}
                    <div class="w-[68%] overflow-hidden rounded-[15px]">
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
                            class="button-default text-center"
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
