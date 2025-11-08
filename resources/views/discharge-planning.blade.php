@extends('layouts.app')

@section('title', 'Patient Discharge Planning')

@section('content')

    {{-- =================================================================== --}}
    {{-- 1. SEARCHABLE PATIENT DROPDOWN --}}
    {{-- =================================================================== --}}
    <form action="{{ route('discharge-planning.select') }}" method="POST" id="patient-select-form">
        @csrf
        <div class="header flex items-center gap-4 my-10 mx-auto w-[70%]">
            <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
                PATIENT NAME :
            </label>

            <div class="searchable-dropdown relative w-[400px]">
                <input 
                    type="text" 
                    id="patient_search_input"
                    placeholder="Select or type Patient Name" 
                    value="{{ trim($selectedPatient->name ?? '') }}"
                    autocomplete="off"
                    class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                >

                {{-- Dropdown options --}}
                <div id="patient_options_container" 
                    class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                    @foreach ($patients as $patient)
                        <div 
                            class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150" 
                            data-value="{{ $patient->patient_id }}">
                            {{ trim($patient->name) }}
                        </div>
                    @endforeach
                </div>

                {{-- Hidden input to store selected patient ID for the form --}}
                <input type="hidden" id="patient_id_hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? session('selected_patient_id') ?? '' }}">
            </div>
        </div>
    </form>


    {{-- =================================================================== --}}
    {{-- 2. MAIN CONTENT FORM (Restyled) --}}
    {{-- =================================================================== --}}
    <form action="{{ route('discharge-planning.store') }}" method="POST">
        @csrf

        {{-- Hidden input to send the selected patient's ID with the POST request --}}
        <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? session('selected_patient_id') ?? '' }}">

        {{-- TABLE 1: Discharge Planning --}}
        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                <tr>
                    {{-- ===== FIX: Added text-center here ===== --}}
                    <th colspan="2" class="main-header rounded-t-lg text-center">DISCHARGE PLANNING</th>
                </tr>
                <tr>
                    <th class="table-header border-r-2 border-line-brown w-1/3">DISCHARGE CRITERIA</th>
                    <th class="table-header border-line-brown">REQUIRED ACTION</th>
                </tr>
                
                <tr class="bg-beige text-center border-b-2 border-line-brown">
                    <td class="text-center table-header border-r-2 border-line-brown w-1/3">FEVER RESOLUTION</td>
                    <td><textarea class="notepad-lines h-[100px]" placeholder="Type here..." name="criteria_feverRes">{{ $dischargePlan->criteria_feverRes ?? '' }}</textarea></td>
                </tr>
                <tr class="bg-beige text-center border-b-2 border-line-brown">
                    <td class="text-center table-header border-r-2 border-line-brown w-1/3">NORMALIZATION OF PATIENT COUNT</td>
                    <td><textarea class="notepad-lines h-[100px]" placeholder="Type here..." name="criteria_patientCount">{{ $dischargePlan->criteria_patientCount ?? '' }}</textarea></td>
                </tr>
                <tr class="bg-beige text-center border-b-2 border-line-brown">
                    <td class="text-center table-header border-r-2 border-line-brown w-1/3">MANAGE FEVER EFFECTIVELY</td>
                    <td><textarea class="notepad-lines h-[100px]" placeholder="Type here..." name="criteria_manageFever">{{ $dischargePlan->criteria_manageFever ?? '' }}</textarea></td>
                </tr>
                <tr class="bg-beige text-center border-b-2 border-line-brown">
                    <td class="text-center table-header border-r-2 border-line-brown w-1/3">MANAGE FEVER EFFECTIVELY</td>
                    <td><textarea class="notepad-lines h-[100px]" placeholder="Type here..." name="criteria_manageFever2">{{ $dischargePlan->criteria_manageFever2 ?? '' }}</textarea></td>
                </tr>
            </table>
        </center>

        {{-- TABLE 2: Discharge Instruction --}}
        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                <tr>
                    {{-- ===== FIX: Added text-center here ===== --}}
                    <th colspan="2" class="main-header rounded-t-lg text-center">DISCHARGE INSTRUCTION</th>
                </tr>
                <tr class="bg-beige text-center border-b-2 border-line-brown">
                    <th class="table-header border-r-2 border-line-brown w-1/3">INSTRUCTION</th>
                    <th class="table-header border-line-brown">DETAILS</th>
                </tr>
                <tr class="bg-beige text-center border-b-2 border-line-brown">
                    <td class="text-center table-header border-r-2  border-line-brown w-1/3">MEDICATIONS</td>
                    <td><textarea class="notepad-lines h-[100px]" placeholder="Type here..." name="instruction_med">{{ $dischargePlan->instruction_med ?? '' }}</textarea></td>
                </tr>
                <tr class="bg-beige text-center border-b-2 border-line-brown">
                    <td class="table-header border-r-2 border-line-brown w-1/3">FOLLOW-UP APPOINTMENT</td>
                    <td><textarea class="notepad-lines h-[100px]" placeholder="Type here..." name="instruction_appointment">{{ $dischargePlan->instruction_appointment ?? '' }}</textarea></td>
                </tr>
                <tr class="bg-beige text-center border-b-2 border-line-brown">
                    <td class="table-header text-center border-r-2 border-line-brown w-1/3">FLUID INTAKE</td>
                    <td><textarea class="notepad-lines h-[100px]" placeholder="Type here..." name="instruction_fluidIntake">{{ $dischargePlan->instruction_fluidIntake ?? '' }}</textarea></td>
                </tr>
                <tr class="bg-beige text-center border-b-2 border-line-brown">
                    <td class="table-header text-center border-r-2 border-line-brown w-1/3">AVOID MOSQUITO EXPOSURE</td>
                    <td><textarea class="notepad-lines h-[100px]" placeholder="Type here..." name="instruction_exposure">{{ $dischargePlan->instruction_exposure ?? '' }}</textarea></td>
                </tr>
                <tr class="bg-beige">
                    <td class="table-header text-center border-r-2 border-line-brown w-1/3">MONITOR FOR SIGNS OF COMPLICATIONS</td>
                    <td><textarea class="notepad-lines h-[100px]" placeholder="Type here..." name="instruction_complications">{{ $dischargePlan->instruction_complications ?? '' }}</textarea></td>
                </tr>
            </table>
        </center>

        {{-- SUBMIT BUTTON --}}
        <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30">
            <button class="button-default" type="submit">SUBMIT</button>
        </div>

    </form>
@endsection

@push('scripts')
    {{-- These scripts are required for the new searchable dropdown --}}
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush

@push('styles')
    <style>
        .criteria-cell {
            background-color: #FDF5E6; /* bg-beige */
            border-right: 2px solid rgba(139, 69, 19, 0.4); /* border-line-brown/70 */
            border-bottom: 1px solid rgba(139, 69, 19, 0.4); /* Custom for row separation */
            padding: 12px;
            font-weight: 600;
            color: #6B4F4F; /* A brown color, adjust as needed */
            text-align: center; /* Centered text */
            vertical-align: top; /* Aligned to the top */
            font-size: 13px;
        }
        /* Remove bottom border from the last row's cells */
        table tr:last-child .criteria-cell,
        table tr:last-child td {
            border-bottom: 0;
        }
    </style>
@endpush