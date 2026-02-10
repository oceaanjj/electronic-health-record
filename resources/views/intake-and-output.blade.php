@extends('layouts.app')

@section('title', 'Patient Intake And Output')

@section('content')
    {{-- FORM OVERLAY (ALERT) --}}
    <div id="form-content-container" class="mx-auto max-w-full">
        {{-- 1. STRUCTURED HEADER (Layout & CDSS Banner) --}}
        <div class="mx-auto mt-1 w-full max-w-full">
            {{-- CDSS ALERT BANNER --}}
            @isset($selectedPatient)
                @if ($ioData)
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
            @endisset

            <div class="mx-auto w-full pt-10">
                {{-- UPDATED: Removed justify-center/items-center, added items-start for mobile left alignment --}}
                <div class="mobile-dropdown-container mb-5 flex flex-wrap items-start justify-start gap-x-10 gap-y-4 md:ml-25 md:items-center">
                    
                    {{-- 1. PATIENT SECTION --}}
                    {{-- UPDATED: Changed justify-center to justify-start --}}
                    <div class="flex items-center gap-4 w-full sm:w-auto justify-start sm:justify-start">
                        <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                            PATIENT NAME :
                        </label>
                        <div class="w-full md:w-[350px]">
                            <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                                :selectRoute="route('io.select')" :inputValue="$selectedPatient?->name ?? ''" />
                        </div>
                    </div>

                    {{-- 2. DAY NO SECTION --}}
                    @isset($selectedPatient)
                        {{-- UPDATED: Changed justify-center to justify-start to align with Patient Name --}}
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-start sm:justify-start">
                            <label for="day_no_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">
                                DAY NO :
                            </label>
                            <div class="relative">
                                <select id="day_no_selector" name="day_no" form="io-form"
                                    class="font-creato-bold w-[100px] appearance-none rounded-full border border-gray-300 bg-white px-4 py-2 pr-8 text-[15px] shadow-sm transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 focus:outline-none">
                                    @for ($i = 1; $i <= ($daysSinceAdmission ?? 30); $i++)
                                        <option value="{{ $i }}" @selected(($currentDayNo ?? 1) == $i)>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endisset
                </div>

                {{-- ALERT MESSAGE --}}
                @isset($selectedPatient)
                    @if (!$ioData || $ioData->count() == 0)
                        {{-- UPDATED: Added w-[90%] to match container width, changed justify-center to justify-start --}}
                        <div class="mt-4 mx-auto w-[90%] md:w-auto flex items-center justify-start gap-2 text-xs italic text-gray-500 md:ml-24">
                            <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                            Clinical Decision Support System is not yet available (No data for this day).
                        </div>
                    @endif
                @endisset
            </div>
        </div>

        <fieldset>
            <form id="io-form" class="cdss-form" method="POST" action="{{ route('io.store') }}"
                data-analyze-url="{{ route('io.check') }}" data-batch-analyze-url="{{ route('io.analyze-batch') }}">
                @csrf

                <input type="hidden" name="patient_id"
                    value="@isset($selectedPatient){{ $selectedPatient->patient_id }}@endisset " />
                <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}" />

                <div
                    class="mx-auto mt-5 flex w-full max-w-screen-2xl flex-col items-center justify-center gap-5 md:mt-10 md:w-[98%] md:flex-row md:items-start md:gap-4">
                    
                    {{-- MAIN DATA TABLE --}}
                    <div class="w-full overflow-hidden rounded-[15px] md:w-3/4 mobile-table-container">
                        <table class="w-full table-fixed border-collapse border-spacing-y-0 responsive-table">
                            <tr class="responsive-table-header-row">
                                <th class="main-header w-[33%] rounded-tl-lg py-2 text-center">ORAL INTAKE (mL)</th>
                                <th class="main-header w-[33%] py-2 text-center">IV FLUIDS (mL)</th>
                                <th class="main-header w-[33%] rounded-tr-lg py-2 text-center">URINE OUTPUT (mL)</th>
                            </tr>

                            <tr class="bg-beige text-brown responsive-table-data-row">
                                {{-- ORAL INTAKE --}}
                                <td class="text-center align-middle responsive-table-data" data-label="ORAL INTAKE (mL)">
                                    <input type="text" name="oral_intake" placeholder="Enter Oral Intake"
                                        value="{{ old('oral_intake', $ioData->oral_intake ?? '') }}" oninput="
                                                this.value = this.value
                                                    .replace(/[^0-9.]/g, '')
                                                    .replace(/(\..*?)\..*/g, '$1')
                                            "
                                        class="bg-transparent text-brown cdss-input vital-input h-[100px] w-[80%] border-none text-center font-semibold placeholder-brown/50 focus:border-transparent focus:outline-none focus:ring-0"
                                        data-field-name="oral_intake" />
                                </td>

                                {{-- IV FLUIDS --}}
                                <td class="text-center align-middle responsive-table-data" data-label="IV FLUIDS (mL)">
                                    <input type="text" name="iv_fluids_volume" placeholder="Enter IV Fluids"
                                        value="{{ old('iv_fluids_volume', $ioData->iv_fluids_volume ?? '') }}" oninput="
                                                this.value = this.value
                                                    .replace(/[^0-9.]/g, '')
                                                    .replace(/(\..*?)\..*/g, '$1')
                                            "
                                        class="bg-transparent text-brown cdss-input vital-input h-[100px] w-[80%] border-none text-center font-semibold placeholder-brown/50 focus:border-transparent focus:outline-none focus:ring-0"
                                        data-field-name="iv_fluids_volume" />
                                </td>

                                {{-- URINE OUTPUT --}}
                                <td class="text-center align-middle responsive-table-data" data-label="URINE OUTPUT (mL)">
                                    <input type="text" name="urine_output" placeholder="Enter Urine Output"
                                        value="{{ old('urine_output', $ioData->urine_output ?? '') }}" oninput="
                                                this.value = this.value
                                                    .replace(/[^0-9.]/g, '')
                                                    .replace(/(\..*?)\..*/g, '$1')
                                            "
                                        class="bg-transparent text-brown cdss-input vital-input h-[100px] w-[80%] border-none text-center font-semibold placeholder-brown/50 focus:border-transparent focus:outline-none focus:ring-0"
                                        data-field-name="urine_output" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- ALERTS TABLE --}}
                    <div class="w-full overflow-hidden rounded-[15px] md:w-1/4 mobile-table-container">
                        <div class="main-header mb-1 rounded-[15px] py-2 text-center">ALERTS</div>

                        <table class="w-full border-collapse">
                            <tr>
                                <td class="align-middle">
                                    <div class="alert-box my-[3px] flex h-[90px] w-full items-center justify-center"
                                        data-alert-for="io_alert">
                                        <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="mx-auto mt-5 mb-30 flex w-full justify-center space-x-4 responsive-btns md:w-[83%] md:justify-end">
                    @if ($ioData)
                        <button type="submit" formaction="{{ route('io.cdss') }}" class="button-default cdss-btn">
                            CDSS
                        </button>
                    @endif

                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </form>
        </fieldset>
    </div>
@endsection

@push('scripts')
    @vite([
        'resources/js/alert.js',
        'resources/js/intake-output-patient-loader.js',
        'resources/js/intake-output-cdss.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/intake-output-data-loader.js',
        'resources/js/close-cdss-alert.js',
    ])
@endpush

<style>
    /* Global Styles */
    html, body {
        overflow-x: hidden;
        margin: 0;
        padding: 0;
    }

    /* =========================
       MOBILE (PHONES) <= 640px
       Matched to Physical Exam Design
    ========================= */
    @media screen and (max-width: 640px) {

        /* General Layout Adjustments */
        body {
            margin-top: -40px !important;
        }

        .mobile-dropdown-container {
            display: flex !important;
            flex-direction: column;
            width: 90% !important;
            margin: 0 auto 15px auto !important;
        }

        /* Container for Table cards */
        .mobile-table-container {
            display: block !important;
            width: 90% !important;
            margin: 0 auto 1.5rem auto !important;
            max-width: none;
            box-sizing: border-box;
        }

        /* 1. Reset Table Structure */
        .responsive-table,
        .responsive-table tbody,
        .responsive-table-header-row,
        .responsive-table-data-row,
        .responsive-table-data {
            display: block;
            width: 100%;
        }

        /* Hide Desktop Headers/Structure */
        .responsive-table .responsive-table-header-row {
            display: none;
        }

        /* Remove the row border from desktop and make it transparent */
        .responsive-table-data-row {
            background-color: transparent !important; 
            border: none !important;
            margin: 0;
            padding: 0;
        }

        /* 2. TRANSFORM CELLS INTO CARDS */
        .responsive-table .responsive-table-data {
            position: relative;
            margin-bottom: 1.5rem;
            border-radius: 15px;
            background-color: #ffffff; 
            border: 1px solid #c18b04; /* Gold/Brown Border */
            overflow: hidden;
            padding: 0; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        /* 3. PSEUDO HEADER (The Gradient Bar) */
        .responsive-table .responsive-table-data::before {
            content: attr(data-label);
            display: block;
            width: 100%;
            background: linear-gradient(180deg, #ffd966, #f4b400); 
            color: #6B4226; 
            font-family: var(--font-creato-bold, sans-serif);
            font-weight: bold;
            font-size: 13px;
            text-transform: uppercase;
            padding: 10px 14px;
            box-sizing: border-box;
            text-align: left;
        }

        /* 4. INPUT FIELD STYLING */
        .responsive-table-data input {
            width: 100% !important;
            box-sizing: border-box;
            margin: 15px 0; 
            height: 80px !important; 
            border-radius: 10px;
            /* border: 1px solid #ddd; */
            background-color: #ffffff !important; 
        }
        
        /* Wrapper inside TD to add padding around input */
        .responsive-table-data input {
             width: calc(100% - 30px) !important; 
             margin: 15px auto !important;
             display: block;
        }

        /* 5. ALERT BOXES (Mobile) */
        .alert-box {
            border-radius: 15px;
            margin-bottom: 20px;
        }

        /* 6. BUTTONS */
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