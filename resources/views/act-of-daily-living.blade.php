@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')
    <div id="form-content-container">
        
        {{-- 1. NEW STRUCTURED HEADER (Layout Fix) --}}
        <div class="mx-auto mt-1 w-full">
            
            {{-- CDSS ALERT BANNER --}}
            @if ($selectedPatient && isset($adlData))
                <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                    <div
                        id="cdss-alert-content"
                        class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-md"
                    >
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined animate-pulse text-[#dcb44e]">info</span>
                            <span class="text-sm font-semibold text-[#dcb44e]">
                                Clinical Decision Support System is now available for this date.
                            </span>
                        </div>
                        <button
                            type="button"
                            onclick="closeCdssAlert()"
                            class="group flex items-center justify-center rounded-full p-1 text-amber-700 transition-all duration-300 hover:bg-amber-200/50 active:scale-90"
                        >
                            <span class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90">
                                close
                            </span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- 
                FIXED CONTAINER ALIGNMENT 
                1. Removed 'ml-33' and 'lg:ml-20'.
                2. Added 'md:w-[90%]' to match the table container below.
            --}}
            <div class="mobile-dropdown-container mx-auto w-full md:w-[90%] px-4 pt-10">
                <div class="flex flex-wrap items-center gap-x-10 gap-y-4">
                    
                    {{-- 1. PATIENT SECTION --}}
                    <div class="flex items-center gap-4">
                        <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                            PATIENT NAME :
                        </label>
                        <div class="w-full md:w-[320px]">
                            <x-searchable-patient-dropdown
                                :patients="$patients"
                                :selectedPatient="$selectedPatient"
                                :selectRoute="route('adl.select')"
                                :inputValue="$selectedPatient?->name ?? ''"
                            />
                        </div>
                    </div>

                    {{-- 2. DATE & DAY SECTION --}}
                    {{-- Removed 'lg:ml-20' so it flows naturally next to patient name --}}
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
                {{-- Removed margins, aligned with parent container --}}
                @if ($selectedPatient && (!isset($adlData) || !$adlData))
                    <div class="w-full flex items-center justify-start gap-2 text-xs italic text-gray-500 mt-4">
                        <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                        Clinical Decision Support System is not yet available.
                    </div>
                @endif
            </div>
        </div>

        <form
            id="adl-form"
            method="POST"
            class="cdss-form relative mx-auto mt-5 w-full"
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

                {{-- 
                    FORM TABLE CONTAINER
                    Matches the width of the header container above (md:w-[90%])
                --}}
                <div class="mx-auto mt-6 flex flex-col md:flex-row w-full md:w-[90%] items-center md:items-start justify-center gap-y-4 md:gap-1 px-4">
                    
                    {{-- LEFT SIDE TABLE (INPUTS) --}}
                    <div class="mobile-table-container w-full md:w-[70%] overflow-hidden rounded-[15px] overflow-x-auto">
                        <table class="responsive-table w-full table-fixed border-collapse border-spacing-y-0">
                            <tr class="responsive-table-header-row">
                                <th class="main-header w-[30%] rounded-tl-lg py-2 text-white">CATEGORY</th>
                                <th class="main-header w-[55%] rounded-tr-lg py-2 text-white">FINDINGS</th>
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
                                <tr class="responsive-table-data-row border-line-brown border-b-2">
                                    <th
                                        class="bg-yellow-light text-brown @if ($loop->last) rounded-bl-lg @endif responsive-table-data-label">
                                        {{ $label }}
                                    </th>
                                        <td class="bg-beige @if (!$loop->last) border-line-brown/50 border-b-2 @endif responsive-table-data"
                                            data-label="{{ $label }}">
                                            <textarea name="{{ $field }}"
                                                class="notepad-lines cdss-input h-[100px] w-full border-none"
                                                data-field-name="{{ $field }}"
                                                placeholder="Type here..">{{ old($field, $adlData->$field ?? '') }}</textarea>

                                            <div class="alert-box-mobile my-0.5 flex w-full items-center justify-center px-3 py-4"
                                                data-alert-for="{{ $field }}">
                                                <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                            </div>
                                        </td>
                                    </tr>
                            @endforeach
                        </table>
                    </div>

                    {{-- ALERTS TABLE --}}
                    <div class="mobile-table-container w-full md:w-[25%] rounded-[15px] overflow-x-auto">
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

                {{-- BUTTONS --}}
                <div class="mx-auto mt-5 mb-20 flex w-full justify-end space-x-4 responsive-btns md:w-[85%]">
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

<style>
    html,
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    * {
        box-sizing: border-box;
    }

    /* =========================
   MOBILE ALERT
========================= */
    .alert-box-mobile {
        display: none;
        border-radius: 0 0 15px 15px;
        justify-content: center;
        align-items: center;
        text-align: center;
        min-height: 40px;
        padding-bottom: 0px;
    }

    /* =========================
   MOBILE (PHONES)
   <= 640px
========================= */
    @media screen and (max-width: 640px) {

        body {
            margin-top: -40px !important;
        }

        /* Adjusted dropdown container to be responsive */
        .mobile-dropdown-container {
            display: flex !important;
            flex-wrap: wrap;
            width: 90% !important;
            margin: 0 auto 15px auto !important;
            box-sizing: border-box;
        }

        /* Show mobile alerts */
        .alert-box-mobile {
            display: flex !important;
            padding: 0px !important;
        }

        /* Hide right alerts table */
        .mobile-table-container:last-of-type {
            display: none !important;
        }

        /* Force table container width */
        .mobile-table-container {
            width: 90% !important;
            margin: 0 auto !important;
        }

        /* BREAK TABLE LAYOUT */
        .responsive-table,
        .responsive-table tbody,
        .responsive-table-data-row,
        .responsive-table-data-label,
        .responsive-table-data {
            display: block;
            width: 100%;
        }

        /* Remove desktop header row */
        .responsive-table-header-row {
            display: none;
        }

        /* Card container */
        .responsive-table-data-row {
            margin: 0 auto 1.5rem auto;
            border-radius: 15px;
            background-color: #F5F5DC;
            overflow: hidden;
            width: 100%;
            border: 1px solid #c18b04;

        }

        /* SYSTEM header (th) */
        .responsive-table-data-label {
            justify-content: left;
            padding: 10px 14px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            color: #6B4226;
            background: linear-gradient(180deg, #ffd966, #f4b400);
            font-family: var(--font-creato-bold);
        }

        /* FINDINGS cell (td) */
        .responsive-table-data {
            padding: 14px;
            border-bottom: none;
        }

        /* TEXTAREA */
        .responsive-table-data textarea {
            width: 100% !important;
            min-height: 80px;
            box-sizing: border-box;
        }

        /* Buttons aligned right like desktop */
        .responsive-btns {
            width: 90% !important;
            margin: 1.5rem auto 2.5rem auto;
            display: flex;
            justify-content: flex-end !important;
            gap: 0.75rem;
        }

        .responsive-btns .button-default,
        .responsive-btns .cdss-btn {
            min-width: 100px;
            text-align: center;
        }
    }
</style>