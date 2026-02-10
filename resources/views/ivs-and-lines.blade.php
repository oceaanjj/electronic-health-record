@extends('layouts.app')
@section('title', 'Patient IVs and Lines')
@section('content')

    <div id="form-content-container" class="mx-auto max-w-full">

        {{-- 1. HEADER / PATIENT SELECTION --}}
        <div class="mx-auto w-full pt-10">
            <div class="mobile-dropdown-container mb-5 flex flex-wrap items-center justify-center gap-x-10 gap-y-4 md:ml-20 md:justify-start">
                
                {{-- PATIENT SECTION --}}
                <div class="flex items-center gap-4 w-full sm:w-auto justify-center sm:justify-start">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                        PATIENT NAME :
                    </label>
                    
                    <div class="w-full md:w-[350px]">
                        <x-searchable-patient-dropdown 
                            :patients="$patients" 
                            :selectedPatient="$selectedPatient"
                            selectRoute="{{ route('ivs-and-lines.select') }}" 
                            inputPlaceholder="Search or type Patient Name..."
                            inputName="patient_id" 
                            inputValue="{{ session('selected_patient_id') }}" 
                        />
                    </div>
                </div>

            </div>
        </div>

        {{-- MAIN FORM --}}
        <form action="{{ route('ivs-and-lines.store') }}" method="POST" class="cdss-form" data-analyze-url="">
            @csrf
            <input type="hidden" name="patient_id"
                value="{{ $selectedPatient->patient_id ?? session('selected_patient_id') }}">
            
            <fieldset @if (!session('selected_patient_id')) disabled @endif class="w-full">

                <div class="mx-auto mt-5 flex w-full max-w-screen-2xl flex-col items-center justify-center gap-5 md:mt-10 md:w-[85%] md:flex-row md:items-start">

                    {{-- TABLE CONTAINER --}}
                    <div class="w-full overflow-hidden rounded-[15px] mobile-table-container">
                        
                        <table class="w-full table-fixed border-collapse border-spacing-y-0 responsive-table">
                            {{-- DESKTOP HEADERS --}}
                            <tr class="responsive-table-header-row">
                                <th class="main-header w-[25%] rounded-tl-lg py-2 text-center">IV FLUID</th>
                                <th class="main-header w-[25%] py-2 text-center">RATE</th>
                                <th class="main-header w-[25%] py-2 text-center">SITE</th>
                                <th class="main-header w-[25%] rounded-tr-lg py-2 text-center">STATUS</th>
                            </tr>

                            {{-- DATA ROW --}}
                            <tr class="bg-beige text-brown responsive-table-data-row">

                                {{-- CELL 1: IV FLUID --}}
                                <td class="text-center align-middle responsive-table-data" data-label="IV FLUID">
                                    <input type="text" name="iv_fluid" placeholder="Enter IV Fluid"
                                        value="{{ $ivsAndLineRecord->iv_fluid ?? '' }}"
                                        class="bg-transparent text-brown cdss-input vital-input h-[100px] w-[80%] border-none text-center font-semibold placeholder-brown/50 focus:border-transparent focus:outline-none focus:ring-0"
                                        data-field-name="iv_fluid">
                                    @error('iv_fluid')
                                        <span class="text-red-500 text-xs text-center block mt-1">{{ $message }}</span>
                                    @enderror
                                </td>

                                {{-- CELL 2: RATE --}}
                                <td class="text-center align-middle responsive-table-data" data-label="RATE">
                                    <input type="text" name="rate" placeholder="Enter Rate"
                                        value="{{ $ivsAndLineRecord->rate ?? '' }}"
                                        class="bg-transparent text-brown cdss-input vital-input h-[100px] w-[80%] border-none text-center font-semibold placeholder-brown/50 focus:border-transparent focus:outline-none focus:ring-0"
                                        data-field-name="rate">
                                    @error('rate')
                                        <span class="text-red-500 text-xs text-center block mt-1">{{ $message }}</span>
                                    @enderror
                                </td>

                                {{-- CELL 3: SITE --}}
                                <td class="text-center align-middle responsive-table-data" data-label="SITE">
                                    <input type="text" name="site" placeholder="Enter Site"
                                        value="{{ $ivsAndLineRecord->site ?? '' }}"
                                        class="bg-transparent text-brown cdss-input vital-input h-[100px] w-[80%] border-none text-center font-semibold placeholder-brown/50 focus:border-transparent focus:outline-none focus:ring-0"
                                        data-field-name="site">
                                    @error('site')
                                        <span class="text-red-500 text-xs text-center block mt-1">{{ $message }}</span>
                                    @enderror
                                </td>

                                {{-- CELL 4: STATUS --}}
                                <td class="text-center align-middle responsive-table-data" data-label="STATUS">
                                    <input type="text" name="status" placeholder="Enter Status"
                                        value="{{ $ivsAndLineRecord->status ?? '' }}"
                                        class="bg-transparent text-brown cdss-input vital-input h-[100px] w-[80%] border-none text-center font-semibold placeholder-brown/50 focus:border-transparent focus:outline-none focus:ring-0"
                                        data-field-name="status">
                                    @error('status')
                                        <span class="text-red-500 text-xs text-center block mt-1">{{ $message }}</span>
                                    @enderror
                                </td>

                            </tr>
                        </table>
                    </div>

                </div>

                {{-- BUTTONS --}}
                <div class="mx-auto mt-5 mb-30 flex w-full justify-center space-x-4 responsive-btns md:w-[85%] md:justify-end">
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
                
            </fieldset>
        </form>

    </div>

@endsection

@push('scripts')
    @vite([
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js'
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
    /* Global Styles */
    html, body {
        overflow-x: hidden;
        margin: 0;
        padding: 0;
    }

    /* =========================
       MOBILE (PHONES) <= 640px
       Matched to Physical Exam & Intake Output Design
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

        /* Hide Desktop Headers */
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
            /* CHANGED: Background to white to match the input box */
            background-color: #ffffff; 
            border: 1px solid #c18b04; /* Gold/Brown Border */
            overflow: hidden;
            padding: 0; 
            /* Optional: Add slight shadow for depth */
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
            height: 60px !important; 
            border-radius: 10px;

            /* ENSURED: Background is white */
            background-color: #ffffff !important; 
        }
        
        /* Wrapper inside TD to add padding around input */
        .responsive-table-data input {
             width: calc(100% - 30px) !important;
             margin: 15px auto !important;
             display: block;
        }

        /* 5. BUTTONS */
        .responsive-btns {
            width: 90% !important;
            margin: 1.5rem auto 2.5rem auto;
            display: flex;
            justify-content: flex-end !important;
            gap: 0.75rem;
        }
    }
</style>