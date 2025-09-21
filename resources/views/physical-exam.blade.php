@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    <!-- ALERT MESSAGE -->

    @if ($errors->any())
        <div style="color:red; margin-bottom:5px padding:5px;">
            <h5 style="margin-bottom: 10px;">Errors:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div style="background-color:green; color:white; padding:1rem; text-align:center; margin:1rem;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Updated PATIENT DROP-DOWN FORM --}}

    <div class="container">
        <div class="header">
            <label for="patient_info">PATIENT NAME :</label>
            <form action="{{ route('physical-exam.index') }}" method="GET" id="patient-select-form">
                <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                    <option value="" @if(request()->query('patient_id') == '') selected @endif>-- Select Patient --
                    </option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->patient_id }}" @if(request()->query('patient_id') == $patient->patient_id)
                        selected @endif>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- This form handles both saving/updating the data and running the CDSS analysis. --}}

    <form action="{{ route('physical-exam.store') }}" method="POST">
        @csrf

        {{-- Hidden input for the patient ID to be passed with the form --}}
        <input type="hidden" name="patient_id" value="{{ request()->query('patient_id') }}">

        <table>
            <tr>
                <th class="title">SYSTEM</th>
                <th class="title">FINDINGS</th>
                <th class="title">ALERTS</th>
            </tr>
            <tr>
                <th class="system">GENERAL APPEARANCE</th>
                <td>
                    <textarea name="general_appearance"
                        placeholder="Enter GENERAL APPEARANCE findings">{{ old('general_appearance', $physicalExam->general_appearance ?? '') }}</textarea>
                </td>
                <td class="alert-box">
                    @error('general_appearance')
                        <div class="alert-box alert-red">
                            <span class="alert-message">{{ $message }}</span>
                        </div>
                    @enderror

                    @if (session('cdss.general_appearance'))
                        @php
                            $alertData = session('cdss.general_appearance');
                            $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                        @endphp
                        <div class="alert-box {{ $color }}">
                            <span class="alert-message">{{ $alertData['alert'] }}</span>
                            <!-- @if ($alertData['severity'] !== 'NONE') <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span> @endif -->
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">SKIN</th>
                <td>
                    <textarea name="skin_condition"
                        placeholder="Enter SKIN findings">{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}</textarea>
                </td>
                <td class="alert-box">
                    @error('skin_condition')
                        <div class="alert-box alert-red">
                            <span class="alert-message">{{ $message }}</span>
                        </div>
                    @enderror

                    @if (session('cdss.skin'))
                        @php
                            $alertData = session('cdss.skin');
                            $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                        @endphp
                        <div class="alert-box {{ $color }}">
                            <span class="alert-message">{{ $alertData['alert'] }}</span>
                            <!-- @if ($alertData['severity'] !== 'NONE') <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span> @endif -->

                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">EYES</th>
                <td>
                    <textarea name="eye_condition"
                        placeholder="Enter EYES findings">{{ old('eye_condition', $physicalExam->eye_condition ?? '') }}</textarea>
                </td>
                <td class="alert-box">
                    @error('eye_condition')
                        <div class="alert-box alert-red">
                            <span class="alert-message">{{ $message }}</span>
                        </div>
                    @enderror

                    @if (session('cdss.eyes'))
                        @php
                            $alertData = session('cdss.eyes');
                            $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                        @endphp
                        <div class="alert-box {{ $color }}">
                            <span class="alert-message">{{ $alertData['alert'] }}</span>
                            <!-- @if ($alertData['severity'] !== 'NONE') <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span> @endif -->

                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">ORAL CAVITY</th>
                <td>
                    <textarea name="oral_condition"
                        placeholder="Enter ORAL CAVITY findings">{{ old('oral_condition', $physicalExam->oral_condition ?? '') }}</textarea>
                </td>
                <td class="alert-box">
                    @error('oral_condition')
                        <div class="alert-box alert-red">
                            <span class="alert-message">{{ $message }}</span>
                        </div>
                    @enderror

                    @if (session('cdss.oral'))
                        @php
                            $alertData = session('cdss.oral');
                            $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                        @endphp
                        <div class="alert-box {{ $color }}">
                            <span class="alert-message">{{ $alertData['alert'] }}</span>
                            <!-- @if ($alertData['severity'] !== 'NONE') <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span> @endif -->

                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">CARDIOVASCULAR</th>
                <td>
                    <textarea name="cardiovascular"
                        placeholder="Enter CARDIOVASCULAR findings">{{ old('cardiovascular', $physicalExam->cardiovascular ?? '') }}</textarea>
                </td>
                <td class="alert-box">
                    @error('cardiovascular')
                        <div class="alert-box alert-red">
                            <span class="alert-message">{{ $message }}</span>
                        </div>
                    @enderror

                    @if (session('cdss.cardiovascular'))
                        @php
                            $alertData = session('cdss.cardiovascular');
                            $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                        @endphp
                        <div class="alert-box {{ $color }}">
                            <span class="alert-message">{{ $alertData['alert'] }}</span>
                            <!-- @if ($alertData['severity'] !== 'NONE') <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span> @endif -->

                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">ABDOMEN</th>
                <td>
                    <textarea name="abdomen_condition"
                        placeholder="Enter ABDOMEN findings">{{ old('abdomen_condition', $physicalExam->abdomen_condition ?? '') }}</textarea>
                </td>
                <td class="alert-box">
                    @error('abdomen_condition')
                        <div class="alert-box alert-red">
                            <span class="alert-message">{{ $message }}</span>
                        </div>
                    @enderror

                    @if (session('cdss.abdomen'))
                        @php
                            $alertData = session('cdss.abdomen');
                            $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                        @endphp
                        <div class="alert-box {{ $color }}">
                            <span class="alert-message">{{ $alertData['alert'] }}</span>
                            <!-- @if ($alertData['severity'] !== 'NONE') <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span> @endif -->

                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">EXTREMITIES</th>
                <td>
                    <textarea name="extremities"
                        placeholder="Enter EXTREMITIES findings">{{ old('extremities', $physicalExam->extremities ?? '') }}</textarea>
                </td>
                <td class="alert-box">
                    @error('extremities')
                        <div class="alert-box alert-red">
                            <span class="alert-message">{{ $message }}</span>
                        </div>
                    @enderror

                    @if (session('cdss.extremities'))
                        @php
                            $alertData = session('cdss.extremities');
                            $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                        @endphp
                        <div class="alert-box {{ $color }}">
                            <span class="alert-message">{{ $alertData['alert'] }}</span>
                            <!-- @if ($alertData['severity'] !== 'NONE') <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span> @endif -->

                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">NEUROLOGICAL</th>
                <td>
                    <textarea name="neurological"
                        placeholder="Enter NEUROLOGICAL findings">{{ old('neurological', $physicalExam->neurological ?? '') }}</textarea>
                </td>
                <td class="alert-box">
                    @error('neurological')
                        <div class="alert-box alert-red">
                            <span class="alert-message">{{ $message }}</span>
                        </div>
                    @enderror

                    @if (session('cdss.neurological'))
                        @php
                            $alertData = session('cdss.neurological');
                            $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                        @endphp
                        <div class="alert-box {{ $color }}">
                            <span class="alert-message">{{ $alertData['alert'] }}</span>
                            <!-- @if ($alertData['severity'] !== 'NONE') <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span> @endif -->

                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="btn">
            <button type="submit" class="btn">Submit</button>
            <button type="button" class="btn">CDSS</button>
        </div>

    </form>

@endsection

@push('styles')
    @vite(['resources/css/physical-exam-style.css'])
@endpush