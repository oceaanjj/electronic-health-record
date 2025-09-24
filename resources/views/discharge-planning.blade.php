<head>
    <meta charset="UTF-8">
    <title>Discharge Planning</title>
    @vite(['./resources/css/lab-values.css'])
</head>

@extends('layouts.app')

@section('title', 'Patient Discharge Planning')

@section('content')

    {{-- PATIENT DROP-DOWN FORM --}}

    <div class="container">
        <div class="header">
            <label for="patient_info">PATIENT NAME :</label>
            <form action="{{ route('discharge-planning.select') }}" method="POST" id="patient-select-form">
                @csrf
                <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                    {{-- Check the session for the selected patient ID --}}
                    <option value="" @if(session('selected_patient_id') == '') selected @endif>-- Select Patient --</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->patient_id }}" @if(session('selected_patient_id') == $patient->patient_id)
                        selected @endif>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>


    {{-- Form for data submission (submits with POST) --}}
    <form action="{{ route('discharge-planning.store') }}" method="POST">
        @csrf

        {{-- Hidden input to send the selected patient's ID with the POST request --}}
        <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

        <div class="section">
            <table>
                <tr>
                    <th colspan="2">Discharge Planning</th>
                </tr>
                <tr>
                    <th>Discharge Criteria</th>
                    <th>Required Action</th>
                </tr>
                <tr>
                    <td class="criteria">Fever Resolution</td>
                    <td><textarea name="criteria_feverRes">{{ $dischargePlan->criteria_feverRes ?? '' }}</textarea></td>
                </tr>
                <tr>
                    <td class="criteria">Normalization of Patient Count</td>
                    <td><textarea name="criteria_patientCount">{{ $dischargePlan->criteria_patientCount ?? '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Manage Fever Effectively</td>
                    <td><textarea name="criteria_manageFever">{{ $dischargePlan->criteria_manageFever ?? '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Manage Fever Effectively</td>
                    <td><textarea name="criteria_manageFever2">{{ $dischargePlan->criteria_manageFever2 ?? '' }}</textarea>
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
                    <td><textarea name="instruction_med">{{ $dischargePlan->instruction_med ?? '' }}</textarea></td>
                </tr>
                <tr>
                    <td class="criteria">Follow-Up Appointment</td>
                    <td><textarea
                            name="instruction_appointment">{{ $dischargePlan->instruction_appointment ?? '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Fluid Intake</td>
                    <td><textarea
                            name="instruction_fluidIntake">{{ $dischargePlan->instruction_fluidIntake ?? '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Avoid Mosquito Exposure</td>
                    <td><textarea name="instruction_exposure">{{ $dischargePlan->instruction_exposure ?? '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <td class="criteria">Monitor for Signs of Complications</td>
                    <td><textarea
                            name="instruction_complications">{{ $dischargePlan->instruction_complications ?? '' }}</textarea>
                    </td>
                </tr>
            </table>
        </div>

        <div class="buttons">
            <button class="btn" type="submit">Submit</button>
        </div>

    </form>
@endsection

@push('styles')
    @vite(['resources/css/discharge-planning.css'])
@endpush