@extends('layouts.app')
@section('title', 'Patient Lab Values')
@section('content')

    <div id="form-content-container">

        {{-- 1. STRUCTURED HEADER (Layout & CDSS Banner) --}}
        <div class="mx-auto mt-1 w-full">

            {{-- CDSS ALERT BANNER (Synced with Physical Exam UI/UX) --}}
            @if (session('selected_patient_id') && isset($labValue))
                <div id="cdss-alert-wrapper" class="w-full px-5 overflow-hidden transition-all duration-500">
                    {{-- Content matches Physical Exam's mt-3, py-3, and px-5 exactly --}}
                    <div id="cdss-alert-content" 
                        class="relative flex items-center justify-between mt-3 py-3 px-5 border border-amber-400/50 rounded-lg shadow-sm bg-amber-100/70 backdrop-blur-md 
                                animate-alert-in">
                        
                        <div class="flex items-center gap-3">
                            {{-- Pulsing Info Icon --}}
                            <span class="material-symbols-outlined text-[#dcb44e] animate-pulse">info</span>
                            <span class="text-sm font-semibold text-[#dcb44e]">
                                Clinical Decision Support System is now available for this date.
                            </span>
                        </div>

                        {{-- Standardized Close Button with Rotation --}}
                        <button type="button" onclick="closeCdssAlert()"
                            class="group flex items-center justify-center text-amber-700 hover:bg-amber-200/50 rounded-full p-1 transition-all duration-300 active:scale-90">
                            <span class="material-symbols-outlined text-[20px] group-hover:rotate-90 transition-transform duration-300">
                                close
                            </span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- LAB VALUES PATIENT SELECTION (Synced with Vital Signs UI) --}}
<div class="mx-auto w-full pt-10">
    <div class="mobile-dropdown-container mb-10 flex flex-col items-start gap-2 mx-auto md:w-[85%]">
        
        {{-- PATIENT SECTION --}}
        <div class="flex flex-wrap items-center justify-start gap-4 w-full">
            <label class="font-alte text-dark-green font-bold whitespace-nowrap shrink-0">
                PATIENT NAME :
            </label>
            
            {{-- Fixed width to match Vital Signs perfectly --}}
            <div class="w-full sm:w-[350px]">
                <x-searchable-patient-dropdown 
                    :patients="$patients" 
                    :selectedPatient="$selectedPatient"
                    selectRoute="{{ route('lab-values.select') }}" 
                    inputPlaceholder="Search or type Patient Name..."
                    inputName="patient_id" 
                    inputValue="{{ session('selected_patient_id') }}" 
                />
            </div>
        </div>

    {{-- NOT AVAILABLE FOOTER (Aligned with ml-20) --}}
    @if (session('selected_patient_id') && !isset($labValue))
        <div class="w-full flex items-center justify-start gap-2 text-xs italic text-gray-500 mt-4">
            <span class="material-symbols-outlined text-[16px]">pending_actions</span>
            Clinical Decision Support System is not yet available (No lab records found).
        </div>
    @endif
    </div>

            <form action="{{ route('lab-values.store') }}" method="POST" class="cdss-form"
                data-analyze-url="{{ route('lab-values.run-cdss-field') }}"
                data-batch-analyze-url="{{ route('lab-values.analyze-batch') }}" data-alert-height-class="h-[49.5px]">
                @csrf
                <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

                <fieldset @if (!session('selected_patient_id')) disabled @endif>

                    {{-- MAIN CONTENT - SAME STRUCTURE AS VITAL SIGNS --}}
                    <div class="mx-auto mt-2 flex w-full flex-col items-start justify-center gap-5 md:w-[85%] md:flex-row md:items-start md:gap-0">

                        {{-- LEFT SIDE: LAB VALUES TABLE --}}
                        <div class="w-full md:w-[68%] rounded-[15px] overflow-hidden mobile-table-container">
                            <table class="w-full table-fixed border-collapse border-spacing-y-0 responsive-table">
                                <tr class="responsive-table-header-row">
                                    <th class="min-w-[150px] main-header rounded-tl-[15px]">LAB TEST</th>
                                    <th class="min-w-[100px] main-header">RESULT</th>
                                    <th class="min-w-[150px] main-header rounded-tr-[15px]">
                                        NORMAL RANGE
                                    </th>
                                </tr>
                            
                                @php
                                    $labTests = [
                                        'WBC (×10⁹/L)' => 'wbc',
                                        'RBC (×10¹²/L)' => 'rbc',
                                        'Hgb (g/dL)' => 'hgb',
                                        'Hct (%)' => 'hct',
                                        'Platelets (×10⁹/L)' => 'platelets',
                                        'MCV (fL)' => 'mcv',
                                        'MCH (pg)' => 'mch',
                                        'MCHC (g/dL)' => 'mchc',
                                        'RDW (%)' => 'rdw',
                                        'Neutrophils (%)' => 'neutrophils',
                                        'Lymphocytes (%)' => 'lymphocytes',
                                        'Monocytes (%)' => 'monocytes',
                                        'Eosinophils (%)' => 'eosinophils',
                                        'Basophils (%)' => 'basophils'
                                    ];
                                @endphp
                            
                                @foreach ($labTests as $label => $name)
                                    <tr class="border-b-2 border-line-brown/70 responsive-table-data-row">
                                        <td class="p-2 font-semibold bg-yellow-light text-brown text-center responsive-table-data responsive-table-data-label">
                                            {{ $label }}
                                        </td>
                                                                <td class="p-2 bg-beige text-center responsive-table-data" data-label="RESULT">
                                                                    <input type="text" name="{{ $name }}_result" placeholder="Result"
                                                                        value="{{ old($name . '_result', optional($labValue)->{$name . '_result'}) }}"
                                                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '                                                    ');"
                                                                        class="w-full h-[40px] focus:outline-none text-center cdss-input"
                                                                        data-field-name="{{ $name }}_result">
                                                                </td>
                                                                <td class="p-2 bg-beige text-center responsive-table-data" data-label="NORMAL RANGE">
                                                                    <input type="text" name="{{ $name }}_normal_range" placeholder="Normal Range"
                                                                        value="{{ old($name . '_normal_range', optional($labValue)->{$name . '_normal_range'}) }}"
                                                                        class="w-full h-[40px] focus:outline-none text-center">
                                                                    <div class="alert-box-mobile my-0.5 flex w-full items-center justify-center px-3 py-4"
                                                                        data-alert-for="{{ $name }}_result">
                                                                        <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                                                    </div>
                                                                </td>                                    </tr>
                                @endforeach
                            </table>                        </div>

                        {{-- ALERTS TABLE--}}
                        <div class="w-full md:w-[25%] rounded-[15px] overflow-hidden mobile-table-container">
                            <div class="main-header rounded-[15px]">
                                ALERTS
                            </div>

                            <table class="w-full border-collapse">
                                @foreach ($labTests as $label => $name)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="alert-box my-1 h-[49.5px] flex justify-center items-center text-center px-2"
                                                data-alert-for="{{ $name }}_result">
                                                <span class="opacity-70 text-white font-semibold">NO ALERTS</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="mx-auto mt-5 mb-20 flex w-full justify-end space-x-4 responsive-btns md:w-[85%]">
                        @if (isset($labValue))
                            <a href="{{ route('nursing-diagnosis.start', ['component' => 'lab-values', 'id' => $labValue->id]) }}"
                                class="button-default cdss-btn text-center">
                                CDSS
                            </a>
                        @endif
                        <button type="submit" class="button-default">SUBMIT</button>
                    </div>

                </fieldset>

            </form>
            </fieldset>

@endsection

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

            /* LAB TEST header (td with responsive-table-data-label) */
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

            /* RESULT and NORMAL RANGE cells (td with responsive-table-data) */
            .responsive-table-data {
                padding: 14px;
                border-bottom: none;
                position: relative; /* Needed for data-label pseudo-element */
            }
            
            /* Display data-label for RESULT and NORMAL RANGE */
            .responsive-table-data[data-label]:before {
                content: attr(data-label) ": ";
                font-weight: bold;
                display: block; /* Make the label appear above the input */
                margin-bottom: 5px; /* Space between label and input */
                color: #6B4226;
                font-family: var(--font-creato-bold);
            }

            /* INPUT fields */
            .responsive-table-data input[type="text"] {
                width: 100% !important;
                min-height: 40px; /* Keep consistent with desktop input height */
                box-sizing: border-box;
                border: 1px solid #ccc; /* Add a border for better visual separation */
                border-radius: 5px; /* Slightly rounded corners */
                padding: 8px; /* Inner padding */
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

        @push('scripts')
            @vite([
                'resources/js/alert.js',
                'resources/js/patient-loader.js',
                'resources/js/searchable-dropdown.js'
            ])
        @endpush