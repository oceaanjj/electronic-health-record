@extends('layouts.app')

@section('title', 'Physical Exam')

@section('content')

    {{-- PATIENT DROP-DOWN FORM --}}
    <form action="{{ route('physical-exam.select') }}" method="POST">
        @csrf
        <div class="header">
            <label for="patient_id" style="color: white;">PATIENT NAME :</label>
            <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                <option value="">-- Select Patient --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->patient_id }}" {{ (isset($selectedPatient) && $selectedPatient->patient_id == $patient->patient_id) ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <form action="{{ route('physical-exam.store') }}" method="POST" class="cdss-form"
        data-analyze-url="{{ route('physical-exam.analyze-field') }}">
        @csrf

        {{-- Hidden input for the patient ID --}}
        <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">


        <!-- DISABLED input, need to select a patient first -->
        @if (!session('selected_patient_id'))
            <div class="form-overlay">
                <span>Please select a patient firs to input</span> {{-- message --}}
            </div>
        @endif
        {{-- Wrap the form content in a fieldset and disable it if no patient is selected --}}
        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <center>
                <table>
                    <tr>
                        <th class="bg-dark-green text-white rounded-tl-lg">SYSTEM</th>
                        <th class="bg-dark-green text-white">FINDINGS</th>
                        <th class="bg-dark-green text-white rounded-tr-lg">ALERTS</th>
                    </tr>
                    {{-- GENERAL APPEARANCE --}}
                    <tr>
                        <th class="bg-yellow-light text-brown">GENERAL APPEARANCE</th>
                        <td>
                            <textarea name="general_appearance" class="notepad-lines cdss-input"
                                data-field-name="general_appearance"
                                placeholder="Enter GENERAL APPEARANCE findings">{{ old('general_appearance', $physicalExam->general_appearance ?? '') }}</textarea>
                        </td>
                        <td class="alert-box" data-alert-for="general_appearance">
                            @if (session('cdss.general'))
                                @php
                                    $alertData = session('cdss.general');
                                    $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                @endphp
                                <div class="alert-box {{ $color }}">
                                    <span class="alert-message">{{ $alertData['alert'] }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- SKIN --}}
                    <tr>
                        <th class="bg-yellow-light text-brown">SKIN</th>
                        <td>
                            <textarea name="skin_condition" class="notepad-lines cdss-input"
                                data-field-name="skin_condition"
                                placeholder="Enter SKIN findings">{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}</textarea>
                        </td>
                        <td class="alert-box" data-alert-for="skin_condition">
                            @if (session('cdss.skin'))
                                @php
                                    $alertData = session('cdss.skin');
                                    $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                @endphp
                                <div class="alert-box {{ $color }}">
                                    <span class="alert-message">{{ $alertData['alert'] }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- EYES --}}
                    <tr>
                        <th class="bg-yellow-light text-brown">EYES</th>
                        <td>
                            <textarea name="eye_condition" class="notepad-lines cdss-input" data-field-name="eye_condition"
                                placeholder="Enter EYES findings">{{ old('eye_condition', $physicalExam->eye_condition ?? '') }}</textarea>
                        </td>
                        <td class="alert-box" data-alert-for="eye_condition">
                            @if (session('cdss.eye'))
                                @php
                                    $alertData = session('cdss.eye');
                                    $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                @endphp
                                <div class="alert-box {{ $color }}">
                                    <span class="alert-message">{{ $alertData['alert'] }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- ORAL CAVITY --}}
                    <tr>
                        <th class="bg-yellow-light text-brown">ORAL CAVITY</th>
                        <td>
                            <textarea name="oral_condition" class="notepad-lines cdss-input"
                                data-field-name="oral_condition"
                                placeholder="Enter ORAL CAVITY findings">{{ old('oral_condition', $physicalExam->oral_condition ?? '') }}</textarea>
                        </td>
                        <td class="alert-box" data-alert-for="oral_condition">
                            @if (session('cdss.oral'))
                                @php
                                    $alertData = session('cdss.oral');
                                    $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                @endphp
                                <div class="alert-box {{ $color }}">
                                    <span class="alert-message">{{ $alertData['alert'] }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- CARDIOVASCULAR --}}
                    <tr>
                        <th class="bg-yellow-light text-brown">CARDIOVASCULAR</th>
                        <td>
                            <textarea name="cardiovascular" class="notepad-lines cdss-input"
                                data-field-name="cardiovascular"
                                placeholder="Enter CARDIOVASCULAR findings">{{ old('cardiovascular', $physicalExam->cardiovascular ?? '') }}</textarea>
                        </td>
                        <td class="alert-box" data-alert-for="cardiovascular">
                            @if (session('cdss.cardiovascular'))
                                @php
                                    $alertData = session('cdss.cardiovascular');
                                    $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                @endphp
                                <div class="alert-box {{ $color }}">
                                    <span class="alert-message">{{ $alertData['alert'] }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- ABDOMEN --}}
                    <tr>
                        <th class="bg-yellow-light text-brown">ABDOMEN</th>
                        <td>
                            <textarea name="abdomen_condition" class="notepad-lines cdss-input"
                                data-field-name="abdomen_condition"
                                placeholder="Enter ABDOMEN findings">{{ old('abdomen_condition', $physicalExam->abdomen_condition ?? '') }}</textarea>
                        </td>
                        <td class="alert-box" data-alert-for="abdomen_condition">
                            @if (session('cdss.abdomen'))
                                @php
                                    $alertData = session('cdss.abdomen');
                                    $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                @endphp
                                <div class="alert-box {{ $color }}">
                                    <span class="alert-message">{{ $alertData['alert'] }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- EXTREMITIES --}}
                    <tr>
                        <th class="bg-yellow-light text-brown">EXTREMITIES</th>
                        <td>
                            <textarea name="extremities" class="notepad-lines cdss-input" data-field-name="extremities"
                                placeholder="Enter EXTREMITIES findings">{{ old('extremities', $physicalExam->extremities ?? '') }}</textarea>
                        </td>
                        <td class="alert-box" data-alert-for="extremities">
                            @if (session('cdss.extremities'))
                                @php
                                    $alertData = session('cdss.extremities');
                                    $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                @endphp
                                <div class="alert-box {{ $color }}">
                                    <span class="alert-message">{{ $alertData['alert'] }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>

                    {{-- NEUROLOGICAL --}}
                    <tr>
                        <th class="bg-yellow-light text-brown">NEUROLOGICAL</th>
                        <td>
                            <textarea name="neurological" class="notepad-lines cdss-input" data-field-name="neurological"
                                placeholder="Enter NEUROLOGICAL findings">{{ old('neurological', $physicalExam->neurological ?? '') }}</textarea>
                        </td>
                        <td class="alert-box" data-alert-for="neurological">
                            @if (session('cdss.neurological'))
                                @php
                                    $alertData = session('cdss.neurological');
                                    $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                @endphp
                                <div class="alert-box {{ $color }}">
                                    <span class="alert-message">{{ $alertData['alert'] }}</span>
                                </div>
                            @endif
                        </td>
                    </tr>

                </table>
            </center>

            <div class="buttons">
                <button type="submit" class="btn">Submit</button>
                <button type="button" class="btn">CDSS</button>
            </div>
        </fieldset>
    </form>
@endsection

@push('styles')
    @vite(['resources/css/physical-exam-style.css'])
@endpush

@push('scripts')
    @vite('resources/js/alert.js')
@endpush