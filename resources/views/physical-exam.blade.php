@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    {{-- This form handles both saving the data and running the CDSS analysis. --}}
    <form action="{{ route('physical-exam.store') }}" method="POST">
        @csrf

        <div class="container">
            <div class="header">
                <label for="patient_id">PATIENT NAME :</label>

                {{-- Patient Name DROPDOWN --}}
                <select id="patient_info" name="patient_id">
                    <option value="" {{ old('patient_id') == '' ? 'selected' : '' }}>-- Select Patient --</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->patient_id }}" {{ old('patient_id') == $patient->patient_id ? 'selected' : '' }}>
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
                            @if ($alertData['severity'] !== 'NONE')
                                <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span>
                            @endif
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
                            @if ($alertData['severity'] !== 'NONE')
                                <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span>
                            @endif
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
                            @if ($alertData['severity'] !== 'NONE')
                                <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span>
                            @endif
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
                            @if ($alertData['severity'] !== 'NONE')
                                <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span>
                            @endif
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
                            @if ($alertData['severity'] !== 'NONE')
                                <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span>
                            @endif
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
                            @if ($alertData['severity'] !== 'NONE')
                                <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span>
                            @endif
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
                            @if ($alertData['severity'] !== 'NONE')
                                <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span>
                            @endif
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
                            @if ($alertData['severity'] !== 'NONE')
                                <span class="alert-severity"><b>({{ $alertData['severity'] }})</b></span>
                            @endif
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="btn">
            <button type="submit">Submit</button>
            
            <button type="submit" class="btn" formaction="{{ route('physical-exam.runCdssAnalysis') }}">CDSS</button>
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
    
    @if (session('success'))
        <div style="background-color:green; color:white; padding:1rem; text-align:center; margin:1rem;">
            {{ session('success') }}
        </div>
    @endif

@endsection

@push('styles')
    @vite(['resources/css/physical-exam-style.css'])
@endpush
