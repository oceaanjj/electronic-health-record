@extends('layouts.app')

@section('title', 'Patient Discharge Planning')

@section('content')

    {{-- PATIENT DROP-DOWN FORM --}}

    <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient ?? null"
        selectRoute="{{ route('discharge-planning.select') }}" inputPlaceholder="-Select or type to search-"
        inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />


    <div id="form-content-container">
        {{-- DISABLED input overlay --}}
        @if (!session('selected_patient_id'))
            <div class="form-overlay" style="margin-left:15rem;">
                <span>Please select a patient to input</span> {{-- message --}}
            </div>
        @endif

        {{-- Form for data submission (submits with POST) --}}
        <form action="{{ route('discharge-planning.store') }}" method="POST">
            @csrf
            <fieldset @if (!session('selected_patient_id')) disabled @endif>

                {{-- Hidden input to send the selected patient's ID with the POST request --}}
                <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

                <div class="section">
                    <table class="w-[72%]">
                        <tr>
                            <th colspan="2">Discharge Planning</th>
                        </tr>
                        <tr>
                            <th>Discharge Criteria</th>
                            <th>Required Action</th>
                        </tr>
                        <tr>
                            <td class="criteria">Fever Resolution</td>
                            <td><textarea
                                    name="criteria_feverRes">{{ old('criteria_feverRes', $dischargePlan->criteria_feverRes ?? '') }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="criteria">Normalization of Patient Count</td>
                            <td><textarea
                                    name="criteria_patientCount">{{ old('criteria_patientCount', $dischargePlan->criteria_patientCount ?? '') }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="criteria">Manage Fever Effectively</td>
                            <td><textarea
                                    name="criteria_manageFever">{{ old('criteria_manageFever', $dischargePlan->criteria_manageFever ?? '') }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="criteria">Manage Fever Effectively</td>
                            <td><textarea
                                    name="criteria_manageFever2">{{ old('criteria_manageFever2', $dischargePlan->criteria_manageFever2 ?? '') }}</textarea>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="section">
                    <table>
                        <tr>
                            <th colspan="2">Discharge Instruction</th>
                        </tr>
                        <tr>
                            <th>Instruction</th>
                            <th>Details</th>
                        </tr>
                        <tr>
                            <td class="criteria">Medications</td>
                            <td><textarea
                                    name="instruction_med">{{ old('instruction_med', $dischargePlan->instruction_med ?? '') }}</textarea>
                            </td>
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
                            <td><textarea
                                    name="instruction_exposure">{{ old('instruction_exposure', $dischargePlan->instruction_exposure ?? '') }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="criteria">Monitor for Signs of Complications</td>
                            <td><textarea
                                    name="instruction_complications">{{ old('instruction_complications', $dischargePlan->instruction_complications ?? '') }}</textarea>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="buttons">
                    <button class="btn" type="submit">Submit</button>
                </div>

            </fieldset>
        </form>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/discharge-planning.css'])
@endpush

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
@endpush