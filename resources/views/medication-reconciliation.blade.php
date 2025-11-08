@extends('layouts.app')

@section('title', 'Patient Medical Reconciliation')

@section('content')

<<<<<<< HEAD
<div id="form-content-container">
  <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
            selectRoute="{{ route('medreconciliation.select') }}" inputPlaceholder="-Select or type to search-"
            inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />

  <form action="{{ route('medreconciliation.store') }}" method="POST">
    @csrf
    <fieldset @if (!session('selected_patient_id')) disabled @endif>
    {{-- Hidden input to send the selected patient's ID with the POST request --}}
    <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
=======
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
    {{-- 2. MAIN CONTENT FORM (Restyled) --}}
    {{-- =================================================================== --}}
    <form action="{{ route('medreconciliation.store') }}" method="POST">
        @csrf
        {{-- Hidden input to send the selected patient's ID with the POST request --}}
        <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
>>>>>>> 5d3b16a7a29d5a9df57baa135128fa9ed225379e

        {{-- TABLE 1: Patient's Current Medication --}}
        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                <tr>
                    <th colspan="6" class="bg-dark-green text-white rounded-t-lg">Patient's Current Medication (Upon Admission)</th>
                </tr>
                <tr>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Medication</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Dose</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Route</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Frequency</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Indication</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Administered During Stay?</th>
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
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Medication</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Dose</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Route</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Frequency</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Indication</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Discontinued on Admission?</th>
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
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Medication</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Dose</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Route</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Frequency</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Reason for Change</th>
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
            <button type="submit" class="button-default">Submit</button>
        </div>

<<<<<<< HEAD
    <div class="section">
      <table>
        <tr>
          <th colspan="6">Patient's Current Medication (Upon Admission)</th>
        </tr>
        <tr>
          <th>Medication</th>
          <th>Dose</th>
          <th>Route</th>
          <th>Frequency</th>
          <th>Indication</th>
          <th>Administered During Stay?</th>
        </tr>
        <tr>
          {{-- Populate fields with data from the controller --}}
          <td><input type="text" name="current_med" placeholder="Medication"
              value="{{ $currentMedication->current_med ?? '' }}"></td>
          <td><input type="text" name="current_dose" placeholder="Dose"
              value="{{ $currentMedication->current_dose ?? '' }}"></td>
          <td><input type="text" name="current_route" placeholder="Route"
              value="{{ $currentMedication->current_route ?? '' }}"></td>
          <td><input type="text" name="current_frequency" placeholder="Frequency"
              value="{{ $currentMedication->current_frequency ?? '' }}"></td>
          <td><input type="text" name="current_indication" placeholder="Indication"
              value="{{ $currentMedication->current_indication ?? '' }}"></td>
          <td><input type="text" name="current_text" value="{{ $currentMedication->current_text ?? '' }}"></td>
        </tr>
      </table>
    </div>
    <br>
    <div class="section">
      <table>
        <tr>
          <th colspan="6">Patient's Home Medication (If Any)</th>
        </tr>
        <tr>
          <th>Medication</th>
          <th>Dose</th>
          <th>Route</th>
          <th>Frequency</th>
          <th>Indication</th>
          <th>Discontinued on Admission?</th>
        </tr>
        <tr>
          {{-- Populate fields with data from the controller --}}
          <td><input type="text" name="home_med" placeholder="Medication" value="{{ $homeMedication->home_med ?? '' }}">
          </td>
          <td><input type="text" name="home_dose" placeholder="Dose" value="{{ $homeMedication->home_dose ?? '' }}"></td>
          <td><input type="text" name="home_route" placeholder="Route" value="{{ $homeMedication->home_route ?? '' }}">
          </td>
          <td><input type="text" name="home_frequency" placeholder="Frequency"
              value="{{ $homeMedication->home_frequency ?? '' }}"></td>
          <td><input type="text" name="home_indication" placeholder="Indication"
              value="{{ $homeMedication->home_indication ?? '' }}"></td>
          <td><input type="text" name="home_text" value="{{ $homeMedication->home_text ?? '' }}"></td>
        </tr>
      </table>
    </div>
    <br>
    <div class="section">
      <table>
        <tr>
          <th colspan="6">Changes in Medication During Hospitalization</th>
        </tr>
        <tr>
          <th>Medication</th>
          <th>Dose</th>
          <th>Route</th>
          <th>Frequency</th>
          <th>Reason for Change</th>
        </tr>
        <tr>
          {{-- Populate fields with data from the controller --}}
          <td><input type="text" name="change_med" placeholder="Medication"
              value="{{ $changesInMedication->change_med ?? '' }}"></td>
          <td><input type="text" name="change_dose" placeholder="Dose"
              value="{{ $changesInMedication->change_dose ?? '' }}"></td>
          <td><input type="text" name="change_route" placeholder="Route"
              value="{{ $changesInMedication->change_route ?? '' }}"></td>
          <td><input type="text" name="change_frequency" placeholder="Frequency"
              value="{{ $changesInMedication->change_frequency ?? '' }}"></td>
          <td><input type="text" name="change_text" placeholder="Indication"
              value="{{ $changesInMedication->change_text ?? '' }}"></td>
        </tr>
      </table>
    </div>

    <div class="buttons">
      <button class="btn" type="submit">Submit</button>
    </div>

    </div>

  </form>
=======
    </form>
>>>>>>> 5d3b16a7a29d5a9df57baa135128fa9ed225379e

@endsection

@push('scripts')
<<<<<<< HEAD
  @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
=======
    {{-- These scripts are required for the new searchable dropdown --}}
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
>>>>>>> 5d3b16a7a29d5a9df57baa135128fa9ed225379e
@endpush