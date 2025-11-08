@extends('layouts.app')

@section('title', 'Patient Medical Reconciliation')

@section('content')

<div id="form-content-container">

    {{-- =================================================================== --}}
    {{-- 1. SEARCHABLE PATIENT DROPDOWN (Copied from your design) --}}
    {{-- =================================================================== --}}
    <form action="{{ route('medreconciliation.select') }}" method="POST">
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
                <input type="hidden" id="patient_id_hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
            </div>
        </div>
    </form>

    {{-- =================================================================== --}}
    {{-- 2. MAIN CONTENT FORM (Using fieldset logic from your second file) --}}
    {{-- =================================================================== --}}
    <form action="{{ route('medreconciliation.store') }}" method="POST">
        @csrf
        {{-- This fieldset will disable all tables and the submit button if no patient is selected --}}
        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            
            {{-- Hidden input to send the selected patient's ID with the POST request --}}
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

        {{-- TABLE 1: Patient's Current Medication --}}
        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                <tr>
                    <th colspan="6" class="bg-dark-green text-white rounded-t-lg">Patient's Current Medication (Upon Admission)</th>
                </tr>
                <tr>
                    <th class="table-header border-r-2 border-line-brown">Medication</th>
                    <th class="table-header border-r-2 border-line-brown">Dose</th>
                    <th class="table-header border-r-2 border-line-brown">Route</th>
                    <th class="table-header border-r-2 border-line-brown">Frequency</th>
                    <th class="table-header border-r-2 border-line-brown">Indication</th>
                    <th class="table-header border-line-brown">Administered During Stay?</th>
                </tr>
                <tr class="bg-beige">
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="current_med" placeholder="Type here...">{{ $currentMedication->current_med ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="current_dose" placeholder="Type here...">{{ $currentMedication->current_dose ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="current_route" placeholder="Type here...">{{ $currentMedication->current_route ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="current_frequency" placeholder="Type here...">{{ $currentMedication->current_frequency ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="current_indication" placeholder="Type here...">{{ $currentMedication->current_indication ?? '' }}</textarea>
                    </td>
                    <td>
                        <textarea class="notepad-lines h-[200px]" name="current_text" placeholder="Type here...">{{ $currentMedication->current_text ?? '' }}</textarea>
                    </td>
                </tr>
            </table>
        </center>
        
        {{-- TABLE 2: Patient's Home Medication --}}
        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                <tr>
                    <th colspan="6" class="bg-dark-green text-white rounded-t-lg">Patient's Home Medication (If Any)</th>
                </tr>
                <tr>
                    <th class="table-header border-r-2 border-line-brown">Medication</th>
                    <th class="table-header border-r-2 border-line-brown">Dose</th>
                    <th class="table-header border-r-2 border-line-brown">Route</th>
                    <th class="table-header border-r-2 border-line-brown">Frequency</th>
                    <th class="table-header border-r-2 border-line-brown">Indication</th>
                    <th class="table-header border-line-brown">Discontinued on Admission?</th>
                </tr>
                <tr class="bg-beige">
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="home_med" placeholder="Type here...">{{ $homeMedication->home_med ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="home_dose" placeholder="Type here...">{{ $homeMedication->home_dose ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="home_route" placeholder="Type here...">{{ $homeMedication->home_route ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="home_frequency" placeholder="Type here...">{{ $homeMedication->home_frequency ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="home_indication" placeholder="Type here...">{{ $homeMedication->home_indication ?? '' }}</textarea>
                    </td>
                    <td>
                        <textarea class="notepad-lines h-[200px]" name="home_text" placeholder="Type here...">{{ $homeMedication->home_text ?? '' }}</textarea>
                    </td>
                </tr>
            </table>
        </center>
        
        {{-- TABLE 3: Changes in Medication --}}
        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                {{-- Note: colspan is 5 here --}}
                <tr>
                    <th colspan="5" class="bg-dark-green text-white rounded-t-lg">Changes in Medication During Hospitalization</th>
                </tr>
                <tr>
                    <th class="table-header border-r-2 border-line-brown">Medication</th>
                    <th class="table-header border-r-2 border-line-brown">Dose</th>
                    <th class="table-header border-r-2 border-line-brown">Route</th>
                    <th class="table-header border-r-2 border-line-brown">Frequency</th>
                    <th class="table-header border-line-brown">Reason for Change</th>
                </tr>
                <tr class="bg-beige">
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="change_med" placeholder="Type here...">{{ $changesInMedication->change_med ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="change_dose" placeholder="Type here...">{{ $changesInMedication->change_dose ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="change_route" placeholder="Type here...">{{ $changesInMedication->change_route ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="change_frequency" placeholder="Type here...">{{ $changesInMedication->change_frequency ?? '' }}</textarea>
                    </td>
                    <td>
                        <textarea class="notepad-lines h-[200px]" name="change_text" placeholder="Type here...">{{ $changesInMedication->change_text ?? '' }}</textarea>
                    </td>
                </tr>
            </table>
        </center>
        
        {{-- SUBMIT BUTTON --}}
        <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30">
            {{-- Styled just like your "NEXT" button, but it's a submit type --}}
            <button type="submit" class="button-default">SUBMIT</button>
        </div>

        </fieldset>
    </form>

</div> {{-- End of #form-content-container --}}

@endsection

@push('scripts')
  @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush