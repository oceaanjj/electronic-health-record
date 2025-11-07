@extends('layouts.app')

@section('title', 'Patient Discharge Planning')

@section('content')

    {{-- =================================================================== --}}
    {{-- 1. SEARCHABLE PATIENT DROPDOWN (From your working design) --}}
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

<<<<<<< HEAD
    <x-searchable-patient-dropdown 
        :patients="$patients" 
        :selectedPatient="$selectedPatient ?? null"
        selectRoute="{{ route('discharge-planning.select') }}" 
        inputPlaceholder="-Select or type to search-"
        inputName="patient_id" 
        inputValue="{{ session('selected_patient_id') }}" />


    <div id="form-content-container">
        {{-- DISABLED input overlay --}}
=======
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


    <div id="form-content-container" class="relative">
        {{-- DISABLED input overlay (From your logic file) --}}
>>>>>>> 0344ddb459dbeb5be996aa258591c4f865de915a
        @if (!session('selected_patient_id'))
            <div class="form-overlay">
                <span>Please select a patient to input</span> {{-- message --}}
            </div>
        @endif

<<<<<<< HEAD
    {{-- =================================================================== --}}
    {{-- 2. MAIN CONTENT FORM (Restyled) --}}
    {{-- =================================================================== --}}
    <form action="{{ route('discharge-planning.store') }}" method="POST">
        @csrf
        <fieldset @if (!session('selected_patient_id')) disabled @endif>

        {{-- Hidden input to send the selected patient's ID with the POST request --}}
        <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

        {{-- TABLE 1: Discharge Planning --}}
        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                <tr>
                    {{-- ===== FIX: Added text-center here ===== --}}
                    <th colspan="2" class="bg-dark-green text-white rounded-t-lg text-center">Discharge Planning</th>
                </tr>
                <tr>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown w-1/3">Discharge Criteria</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Required Action</th>
                </tr>
                <tr>
                    <td class="criteria">Fever Resolution</td>
                    <td><textarea name="criteria_feverRes">{{ old('criteria_feverRes', $dischargePlan->criteria_feverRes ?? '') }}</textarea></td>
                </tr>
                <tr>
                    <td class="criteria">Normalization of Patient Count</td>
                    <td><textarea name="criteria_patientCount">{{ old('criteria_patientCount', $dischargePlan->criteria_patientCount ?? '') }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Manage Fever Effectively</td>
                    <td><textarea name="criteria_manageFever">{{ old('criteria_manageFever', $dischargePlan->criteria_manageFever ?? '') }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Manage Fever Effectively</td>
                    <td><textarea name="criteria_manageFever2">{{ old('criteria_manageFever2', $dischargePlan->criteria_manageFever2 ?? '') }}</textarea>
                    </td>
                </tr>
            </table>
        </center>

        {{-- TABLE 2: Discharge Instruction --}}
        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                <tr>
                    {{-- ===== FIX: Added text-center here ===== --}}
                    <th colspan="2" class="bg-dark-green text-white rounded-t-lg text-center">Discharge Instruction</th>
                </tr>
                <tr>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown w-1/3">Instruction</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Details</th>
                </tr>
                <tr>
                    <td class="criteria">Medications</td>
                    <td><textarea name="instruction_med">{{ old('instruction_med', $dischargePlan->instruction_med ?? '') }}</textarea></td>
                </tr>
                <tr>
                    <td class="criteria">Follow-Up Appointment</td>
                    <td><textarea
                            name="instruction_appointment">{{ old('instruction_appointment', $dischargePlan->instruction_appointment ?? '') }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Fluid Intake</td>
                    <td><textarea
                            name="instruction_fluidIntake">{{ old('instruction_fluidIntake', $dischargePlan->instruction_fluidIntake ?? '') }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Avoid Mosquito Exposure</td>
                    <td><textarea name="instruction_exposure">{{ old('instruction_exposure', $dischargePlan->instruction_exposure ?? '') }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Monitor for Signs of Complications</td>
                    <td><textarea
                            name="instruction_complications">{{ old('instruction_complications', $dischargePlan->instruction_complications ?? '') }}</textarea>
                    </td>
                </tr>
            </table>
        </center>
=======
        {{-- =================================================================== --}}
        {{-- 2. MAIN CONTENT FORM (Combined Design + Logic) --}}
        {{-- =================================================================== --}}
        <form action="{{ route('discharge-planning.store') }}" method="POST">
            @csrf
            
            {{-- Fieldset to disable content (From your logic file) --}}
            <fieldset @if (!session('selected_patient_id')) disabled @endif>

                {{-- Hidden input to send the selected patient's ID with the POST request --}}
                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? session('selected_patient_id') ?? '' }}">

                {{-- TABLE 1: Discharge Planning (With Design + old() logic) --}}
                <center>
                    <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                        <tr>
                            <th colspan="2" class="bg-dark-green text-white rounded-t-lg text-center">Discharge Planning</th>
                        </tr>
                        <tr>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown w-1/3">Discharge Criteria</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Required Action</th>
                        </tr>
                        
                        <tr class="bg-beige">
                            <td class="criteria-cell text-center border-r-2 border-line-brown w-1/3">Fever Resolution</td>
                            <td><textarea class="notepad-lines h-[100px]" name="criteria_feverRes">{{ old('criteria_feverRes', $dischargePlan->criteria_feverRes ?? '') }}</textarea></td>
                        </tr>
                        <tr class="bg-beige">
                            <td class="criteria-cell text-center border-r-2 border-line-brown w-1/3">Normalization of Patient Count</td>
                            <td><textarea class="notepad-lines h-[100px]" name="criteria_patientCount">{{ old('criteria_patientCount', $dischargePlan->criteria_patientCount ?? '') }}</textarea></td>
                        </tr>
                        <tr class="bg-beige">
                            <td class="criteria-cell text-center border-r-2 border-line-brown w-1/3">Manage Fever Effectively</td>
                            <td><textarea class="notepad-lines h-[100px]" name="criteria_manageFever">{{ old('criteria_manageFever', $dischargePlan->criteria_manageFever ?? '') }}</textarea></td>
                        </tr>
                        <tr class="bg-beige">
                            <td class="criteria-cell text-center border-r-2 border-line-brown w-1/3">Manage Fever Effectively</td>
                            <td><textarea class="notepad-lines h-[100px]" name="criteria_manageFever2">{{ old('criteria_manageFever2', $dischargePlan->criteria_manageFever2 ?? '') }}</textarea></td>
                        </tr>
                    </table>
                </center>

                {{-- TABLE 2: Discharge Instruction (With Design + old() logic) --}}
                <center>
                    <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                        <tr>
                            <th colspan="2" class="bg-dark-green text-white rounded-t-lg text-center">Discharge Instruction</th>
                        </tr>
                        <tr>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown w-1/3">Instruction</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Details</th>
                        </tr>
                        <tr class="bg-beige">
                            <td class="criteria-cell text-center border-r-2 border-line-brown w-1/3">Medications</td>
                            <td><textarea class="notepad-lines h-[100px]" name="instruction_med">{{ old('instruction_med', $dischargePlan->instruction_med ?? '') }}</textarea></td>
                        </tr>
                        <tr class="bg-beige text-center">
                            <td class="criteria-cell border-r-2 border-line-brown w-1/3">Follow-Up Appointment</td>
                            <td><textarea class="notepad-lines h-[100px]" name="instruction_appointment">{{ old('instruction_appointment', $dischargePlan->instruction_appointment ?? '') }}</textarea></td>
                        </tr>
                        <tr class="bg-beige">
                            <td class="criteria-cell text-center border-r-2 border-line-brown w-1/3">Fluid Intake</td>
                            <td><textarea class="notepad-lines h-[100px]" name="instruction_fluidIntake">{{ old('instruction_fluidIntake', $dischargePlan->instruction_fluidIntake ?? '') }}</textarea></td>
                        </tr>
                        <tr class="bg-beige">
                            <td class="criteria-cell text-center border-r-2 border-line-brown w-1/3">Avoid Mosquito Exposure</td>
                            <td><textarea class="notepad-lines h-[100px]" name="instruction_exposure">{{ old('instruction_exposure', $dischargePlan->instruction_exposure ?? '') }}</textarea></td>
                        </tr>
                        <tr class="bg-beige">
                            <td class="criteria-cell text-center border-r-2 border-line-brown w-1/3">Monitor for Signs of Complications</td>
                            <td><textarea class="notepad-lines h-[100px]" name="instruction_complications">{{ old('instruction_complications', $dischargePlan->instruction_complications ?? '') }}</textarea></td>
                        </tr>
                    </table>
                </center>
>>>>>>> 0344ddb459dbeb5be996aa258591c4f865de915a

                {{-- SUBMIT BUTTON --}}
                <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30">
                    <button class="button-default" type="submit">Submit</button>
                </div>

            </fieldset>
        </form>
    </div>
@endsection

@push('scripts')
    {{-- These scripts are required for the new searchable dropdown --}}
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush

@push('styles')
<<<<<<< HEAD
    @vite(['resources/css/discharge-planning.css'])
@endpush

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
=======
    {{-- Styles from your design file, plus the .form-overlay style --}}
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

        /* Style for the overlay (from your logic file) */
        .form-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(2px);
            margin-left: 15rem; /* As in your original file */
        }
        .form-overlay span {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            border: 1px solid #ddd;
        }
    </style>
>>>>>>> 0344ddb459dbeb5be996aa258591c4f865de915a
@endpush