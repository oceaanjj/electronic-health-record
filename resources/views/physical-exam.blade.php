@extends('layouts.app')

@section('title', 'Physical Exam')

@section('content')

    {{-- PATIENT DROP-DOWN FORM --}}
<body>
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


        <form action="{{ route('physical-exam.store') }}" method="POST">
            @csrf

            {{-- Hidden input for the patient ID to be passed with the form --}}
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">


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
                        <textarea name="general_appearance" class="notepad-lines"
                            placeholder="Enter GENERAL APPEARANCE findings">
                            {{ old('general_appearance', $physicalExam->general_appearance ?? '') }}
                        </textarea>
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
                            </div>
                        @endif
                    </td>
                </tr>


                {{-- SKIN --}}
                <tr>
                    <th class="bg-yellow-light text-brown">SKIN</th>
                    <td>
                        <textarea name="skin_condition" class="notepad-lines"
                            placeholder="Enter SKIN findings">{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}
                        </textarea>
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

                {{-- EYES --}}
                <tr>
                    <th class="bg-yellow-light text-brown">EYES</th>
                    <td>
                        <textarea name="eye_condition" class="notepad-lines"
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

                {{-- ORAL CAVITY --}}
                <tr>
                    <th class="bg-yellow-light text-brown">ORAL CAVITY</th>
                    <td>
                        <textarea name="oral_condition" class="notepad-lines"
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

                {{-- CARDIOVASCULAR --}}
                <tr>
                    <th class="bg-yellow-light text-brown">CARDIOVASCULAR</th>
                    <td>
                        <textarea name="cardiovascular" class="notepad-lines"
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
                    <th class="bg-yellow-light text-brown">ABDOMEN</th>
                    <td>
                        <textarea name="abdomen_condition" class="notepad-lines"
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
                    <th class="bg-yellow-light text-brown">EXTREMITIES</th>
                    <td>
                        <textarea name="extremities" class="notepad-lines"
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
                    <th class="bg-yellow-light text-brown">NEUROLOGICAL</th>
                    <td>
                        <textarea name="neurological" class="notepad-lines"
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
        </center>
    </div>

   
<div class="buttons">
    {{-- These buttons submit the form to save or run analysis --}}
    <button type="submit" name="action" value="submit" class="btn">Submit</button>
    @if ($physicalExam)
    <a href="{{ route('nursing-diagnosis.create-step-1', ['physicalExamId' => $physicalExam->id]) }}" class="btn">CDSS</a>
@endif
</div>

</form>

    </form>
    

@endsection

@push('styles')
    @vite(['resources/css/physical-exam-style.css'])
@endpush