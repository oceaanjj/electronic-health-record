@extends('layouts.app')

@section('title', 'Patient Medical Reconciliation')

@section('content')

    <div id="form-content-container">
        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
            selectRoute="{{ route('medreconciliation.select') }}" inputPlaceholder="-Select or type to search-"
            inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />

        <form action="{{ route('medreconciliation.store') }}" method="POST">
            @csrf
            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                {{-- Hidden input to send the selected patient's ID with the POST request --}}
                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

                {{-- TABLE 1: Patient's Current Medication --}}
                <center>
                    <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                        <tr>
                            <th colspan="6" class="bg-dark-green text-white rounded-t-lg">Patient's Current Medication (Upon
                                Admission)</th>
                        </tr>
                        <tr>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Medication</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Dose</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Route</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Frequency</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Indication</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Administered During Stay?
                            </th>
                        </tr>
                        <tr class="bg-beige">
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="current_med"
                                    placeholder="Type here...">{{ $currentMedication->current_med ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="current_dose"
                                    placeholder="Type here...">{{ $currentMedication->current_dose ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="current_route"
                                    placeholder="Type here...">{{ $currentMedication->current_route ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="current_frequency"
                                    placeholder="Type here...">{{ $currentMedication->current_frequency ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="current_indication"
                                    placeholder="Type here...">{{ $currentMedication->current_indication ?? '' }}</textarea>
                            </td>
                            <td>
                                <textarea class="notepad-lines h-[200px]" name="current_text"
                                    placeholder="Type here...">{{ $currentMedication->current_text ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>
                </center>

                {{-- TABLE 2: Patient's Home Medication --}}
                <center>
                    <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                        <tr>
                            <th colspan="6" class="bg-dark-green text-white rounded-t-lg">Patient's Home Medication (If Any)
                            </th>
                        </tr>
                        <tr>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Medication</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Dose</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Route</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Frequency</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Indication</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Discontinued on Admission?
                            </th>
                        </tr>
                        <tr class="bg-beige">
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="home_med"
                                    placeholder="Type here...">{{ $homeMedication->home_med ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="home_dose"
                                    placeholder="Type here...">{{ $homeMedication->home_dose ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="home_route"
                                    placeholder="Type here...">{{ $homeMedication->home_route ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="home_frequency"
                                    placeholder="Type here...">{{ $homeMedication->home_frequency ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="home_indication"
                                    placeholder="Type here...">{{ $homeMedication->home_indication ?? '' }}</textarea>
                            </td>
                            <td>
                                <textarea class="notepad-lines h-[200px]" name="home_text"
                                    placeholder="Type here...">{{ $homeMedication->home_text ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>
                </center>

                {{-- TABLE 3: Changes in Medication --}}
                <center>
                    <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                        {{-- Note: colspan is 5 here --}}
                        <tr>
                            <th colspan="5" class="bg-dark-green text-white rounded-t-lg">Changes in Medication During
                                Hospitalization</th>
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
                                <textarea class="notepad-lines h-[200px]" name="change_med"
                                    placeholder="Type here...">{{ $changesInMedication->change_med ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="change_dose"
                                    placeholder="Type here...">{{ $changesInMedication->change_dose ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="change_route"
                                    placeholder="Type here...">{{ $changesInMedication->change_route ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/70">
                                <textarea class="notepad-lines h-[200px]" name="change_frequency"
                                    placeholder="Type here...">{{ $changesInMedication->change_frequency ?? '' }}</textarea>
                            </td>
                            <td>
                                <textarea class="notepad-lines h-[200px]" name="change_text"
                                    placeholder="Type here...">{{ $changesInMedication->change_text ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>
                </center>

                {{-- SUBMIT BUTTON --}}
                <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30">
                    {{-- Styled just like your "NEXT" button, but it's a submit type --}}
                    <button type="submit" class="button-default">Submit</button>
                </div>

        </form>

@endsection

    @push('scripts')
        {{-- These scripts are required for the new searchable dropdown --}}
        @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
    @endpush