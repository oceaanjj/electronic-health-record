@extends('layouts.app')

@section('title', 'Patient Intake And Output')

@section('content')
    <style>
        /* Global Styles */
        html, body { overflow-x: hidden; margin: 0; padding: 0; }

        /* =========================
            MOBILE (PHONES) <= 768px
        ========================= */
        @media screen and (max-width: 768px) {
            body { margin-top: -40px !important; }

            .mobile-dropdown-container {
                flex-direction: column;
                align-items: stretch;
                width: 90% !important;
                margin: 0 auto 15px auto !important;
            }

            .mobile-card-wrapper {
                width: 95% !important;
                margin: 0 auto 2rem auto;
                border: 1px solid #006400; 
                border-radius: 15px;
                overflow: hidden;
                background-color: #fcf5e5; 
            }

            .mobile-green-header {
                display: flex !important;
                justify-content: space-between;
                align-items: center;
                background-color: #006400;
                color: white;
                padding: 15px 20px;
                font-family: var(--font-creato-bold, sans-serif);
                font-weight: bold;
                font-size: 1.2rem;
            }

            .responsive-table, .responsive-table tbody, .responsive-table tr, .responsive-table td {
                display: block; width: 100%;
            }

            .responsive-table-header-row { display: none !important; }

            .responsive-table-data {
                border-bottom: 1px solid #c18b04;
                margin: 0; padding: 0;
            }
            
            .responsive-table-data:last-child { border-bottom: none; }

            .responsive-table-data::before {
                content: attr(data-label);
                display: block;
                width: 100%;
                background: linear-gradient(180deg, #ffd966, #f4b400); 
                color: #6B4226; 
                font-weight: bold;
                font-size: 13px;
                text-transform: uppercase;
                text-align: center;
                padding: 8px 0;
            }

            .responsive-table-data input {
                width: 100% !important; 
                height: 75px !important; 
                border: none;
                background-color: #fcf5e5 !important; 
                text-align: center;
                font-size: 1.3rem;
                font-weight: 600;
                color: #6B4226;
                outline: none;
            }

            /* Bell Positioning sa Mobile - Nakalutang sa taas */
            .unified-bell-container {
                position: absolute;
                right: 15px;
                top: 15px; /* Inadjust para pantay sa header text */
                z-index: 50;
            }

            .responsive-btns {
                width: 90% !important;
                margin: 1.5rem auto 2.5rem auto;
                display: flex;
                justify-content: flex-end !important;
                gap: 0.75rem;
            }
        }
    </style>

    <div id="form-content-container" class="mx-auto max-w-full">
        
        {{-- CDSS ALERT BANNER (Upper Banner) --}}
        @isset($selectedPatient)
            @if ($ioData)
                <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                    <div id="cdss-alert-content" class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-md">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined animate-pulse text-[#dcb44e]">info</span>
                            <span class="text-sm font-semibold text-[#dcb44e]">Clinical Decision Support System is now available.</span>
                        </div>
                        <button type="button" onclick="closeCdssAlert()" class="group flex items-center justify-center rounded-full p-1 text-amber-700 transition-all duration-300 hover:bg-amber-200/50 active:scale-90">
                            <span class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90">close</span>
                        </button>
                    </div>
                </div>
            @endif
        @endisset

        <div class="mx-auto mt-1 w-full max-w-full">
            <div class="mx-auto w-full pt-10">
                <div class="mobile-dropdown-container mb-5 flex flex-wrap items-center gap-x-10 gap-y-4 md:ml-24">
                    
                    {{-- 1. PATIENT SELECTOR --}}
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>
                        <div class="w-full md:w-[350px]">
                            <x-searchable-patient-dropdown 
                                :patients="$patients" 
                                :selectedPatient="$selectedPatient" 
                                :selectRoute="route('io.select')" 
                                :inputValue="$selectedPatient?->name ?? ''" 
                            />
                        </div>
                    </div>

                    {{-- 2. DAY SELECTOR (Kailangan ito para mag-load ang tamang data) --}}
                    @isset($selectedPatient)
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <label for="day_no_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">
                                DAY NO :
                            </label>
                            <div class="relative">
                                <select
                                    id="day_no_selector"
                                    name="day_no"
                                    form="io-form"
                                    class="font-creato-bold w-[100px] appearance-none rounded-full border border-gray-300 bg-white px-4 py-2 pr-8 text-[15px] shadow-sm transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 focus:outline-none"
                                >
                                    @for ($i = 1; $i <= ($daysSinceAdmission ?? 30); $i++)
                                        <option value="{{ $i }}" @selected(($currentDayNo ?? 1) == $i)>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endisset
                </div>

                {{-- PENDING ACTIONS MESSAGE --}}
                @isset($selectedPatient)
                    @if (! $ioData || $ioData->count() == 0)
                        <div class="mt-4 mb-4 md:ml-24 flex items-center gap-2 text-xs text-gray-500 italic px-4 md:px-0">
                            <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                            Clinical Decision Support System is not yet available (No data for this day).
                        </div>
                    @endif
                @endisset
            </div>
        </div>

        <fieldset @if(!$selectedPatient) disabled @endif>
            <form id="io-form" class="cdss-form" method="POST" action="{{ route('io.store') }}" 
                data-analyze-url="{{ route('io.check') }}" 
                data-batch-analyze-url="{{ route('io.analyze-batch') }}">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />

                <div class="mt-5 flex w-full max-w-screen-2xl flex-col items-center gap-5 md:mt-10 md:ml-24 md:flex-row md:items-center md:justify-start md:gap-0">
                    
                    <div class="relative w-full md:w-[85%] mobile-card-wrapper bg-white md:bg-transparent md:border-none md:rounded-none flex flex-col md:flex-row md:items-center">
                        
                        {{-- Mobile Header --}}
                        <div class="md:hidden mobile-green-header">
                            <span>INTAKE AND OUTPUT</span>
                            <div class="w-[40px]"></div> 
                        </div>

                        {{-- Main Table --}}
                        <div class="w-full overflow-hidden rounded-[15px] md:rounded-[15px] md:border-none">
                            <table class="w-full table-fixed border-collapse border-spacing-y-0 responsive-table">
                                <tr class="responsive-table-header-row hidden md:table-row">
                                    <th class="main-header w-[33%] rounded-tl-lg py-2 text-center">ORAL INTAKE (mL)</th>
                                    <th class="main-header w-[33%] py-2 text-center">IV FLUIDS (mL)</th>
                                    <th class="main-header w-[33%] rounded-tr-lg py-2 text-center">URINE OUTPUT (mL)</th>
                                </tr>

                                <tr class="bg-beige text-brown responsive-table-data-row">
                                    {{-- Oral Intake --}}
                                    <td class="text-center align-middle responsive-table-data" data-label="ORAL INTAKE (mL)">
                                        <input type="text" name="oral_intake" placeholder="Enter Oral Intake"
                                            value="{{ old('oral_intake', $ioData->oral_intake ?? '') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1')"
                                            class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                            data-field-name="oral_intake" data-time="io" />
                                    </td>

                                    {{-- IV Fluids --}}
                                    <td class="text-center align-middle responsive-table-data" data-label="IV FLUIDS (mL)">
                                        <input type="text" name="iv_fluids_volume" placeholder="Enter IV Fluids"
                                            value="{{ old('iv_fluids_volume', $ioData->iv_fluids_volume ?? '') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1')"
                                            class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                            data-field-name="iv_fluids_volume" data-time="io" />
                                    </td>

                                    {{-- Urine Output --}}
                                    <td class="text-center align-middle responsive-table-data" data-label="URINE OUTPUT (mL)">
                                        <input type="text" name="urine_output" placeholder="Enter Urine Output"
                                            value="{{ old('urine_output', $ioData->urine_output ?? '') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1')"
                                            class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                            data-field-name="urine_output" data-time="io" />
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- ALERT BELL --}}
                        <div class="unified-bell-container flex items-center justify-center md:static md:pl-8" 
                             data-alert-for-time="io">
                            <div class="alert-icon-btn is-empty">
                                <span class="material-symbols-outlined text-[40px] md:text-[45px]">notifications</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="mx-auto mt-5 mb-30 flex w-full justify-center space-x-4 responsive-btns md:ml-24 md:w-[85%] md:justify-end">
                    @if ($ioData)
                        <button type="submit" name="action" value="cdss" class="button-default cdss-btn">
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
        'resources/js/searchable-dropdown.js',
        'resources/js/intake-output-data-loader.js',
        'resources/js/close-cdss-alert.js',
    ])
@endpush