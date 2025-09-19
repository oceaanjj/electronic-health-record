@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    <form action="{{ route('physical-exam.store') }}" method="POST">
        @csrf

        <div class="container">
            <div class="header">
                <label for="patient_id">PATIENT NAME :</label>

                {{-- Patient Name DROPDOWN --}}
                <select id="patient_info" name="patient_id">
                    <option value="" {{ old('patient_info') == '' ? 'selected' : '' }}>-- Select Patient --</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->patient_id }}" {{ old('patient_info') == $patient->patient_id ? 'selected' : '' }}>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

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
                        placeholder="Enter General Appearance findings">{{ old('general_appearance') }}</textarea>
                </td>
                <td>
                    @if (session('cdss.general_appearance'))
                        <div class="alert-box">
                            <span class="alert-message">{{ session('cdss.general_appearance') }}</span>
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">SKIN</th>
                <td>
                    <textarea name="skin_condition" placeholder="Enter Skin findings">{{ old('skin_condition') }}</textarea>
                </td>
                <td>
                    @if (session('cdss.skin'))
                        <div class="alert-box">
                            <span class="alert-message">{{ session('cdss.skin') }}</span>
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">EYES</th>
                <td>
                    <textarea name="eye_condition" placeholder="Enter Eyes findings">{{ old('eye_condition') }}</textarea>
                </td>
                <td>
                    @if (session('cdss.eyes'))
                        <div class="alert-box">
                            <span class="alert-message">{{ session('cdss.eyes') }}</span>
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">ORAL CAVITY</th>
                <td>
                    <textarea name="oral_condition"
                        placeholder="Enter Oral Cavity findings">{{ old('oral_condition') }}</textarea>
                </td>
                <td>
                    @if (session('cdss.oral'))
                        <div class="alert-box">
                            <span class="alert-message">{{ session('cdss.oral') }}</span>
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">CARDIOVASCULAR</th>
                <td>
                    <textarea name="cardiovascular"
                        placeholder="Enter Cardiovascular findings">{{ old('cardiovascular') }}</textarea>
                </td>
                <td>
                    @if (session('cdss.cardiovascular'))
                        <div class="alert-box">
                            <span class="alert-message">{{ session('cdss.cardiovascular') }}</span>
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">ABDOMEN</th>
                <td>
                    <textarea name="abdomen_condition"
                        placeholder="Enter Abdomen findings">{{ old('abdomen_condition') }}</textarea>
                </td>
                <td>
                    @if (session('cdss.abdomen'))
                        <div class="alert-box">
                            <span class="alert-message">{{ session('cdss.abdomen') }}</span>
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">EXTREMITIES</th>
                <td>
                    <textarea name="extremities"
                        placeholder="Enter Extremities findings">{{ old('extremities') }}</textarea>
                </td>
                <td>
                    @if (session('cdss.extremities'))
                        <div class="alert-box">
                            <span class="alert-message">{{ session('cdss.extremities') }}</span>
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="system">NEUROLOGICAL</th>
                <td>
                    <textarea name="neurological"
                        placeholder="Enter Neurological findings">{{ old('neurological') }}</textarea>
                </td>
                <td>
                    @if (session('cdss.neurological'))
                        <div class="alert-box">
                            <span class="alert-message">{{ session('cdss.neurological') }}</span>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="btn">
            <button type="submit">Submit</button>
        </div>

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
    </form>

    <div class="cdss-btn">
        <a href="#" class="btn">CDSS</a>
    </div>

    @if (session('success'))
        <div style="background-color:green; color:white; padding:1rem; text-align:center; margin:1rem;">
            {{ session('success') }}
        </div>
    @endif

@endsection


@push('styles')
    @vite(['resources/css/physical-exam-style.css'])
@endpush