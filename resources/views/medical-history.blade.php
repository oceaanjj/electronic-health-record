<head>
    <meta charset="UTF-8">
    <title>Medical History</title>
    @vite(['./resources/css/lab-values.css'])
</head>

@extends('layouts.app')

@section('title', 'Patient Medical History')

@section('content')

    <body>

        <form action="{{ route('medical-history.select') }}" method="POST">
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


        {{-- FORM for data submission (submits with POST) --}}
        <form action="{{ route('medical.store') }}" method="POST">
            @csrf

            {{-- Hidden input to send the selected patient's ID with the POST request --}}
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

            <table>
                {{-- PRESENT ILLNESS --}}
                <tr>
                    <th rowspan="2" class="title">PRESENT ILLNESS</th>
                    <th>NAME</th>
                    <th>DESCRIPTION</th>
                    <th>MEDICATION</th>
                    <th>DOSAGE</th>
                    <th>SIDE EFFECT</th>
                    <th>COMMENT</th>
                </tr>
                <tr>
                    <td><input type="text" name="present_condition_name"
                            value="{{ $presentIllness->condition_name ?? '' }}"></td>
                    <td><textarea name="present_description">{{ $presentIllness->description ?? '' }}</textarea></td>
                    <td><textarea name="present_medication">{{ $presentIllness->medication ?? '' }}</textarea></td>
                    <td><textarea name="present_dosage">{{ $presentIllness->dosage ?? '' }}</textarea></td>
                    <td><textarea name="present_side_effect">{{ $presentIllness->side_effect ?? '' }}</textarea></td>
                    <td><textarea name="present_comment">{{ $presentIllness->comment ?? '' }}</textarea></td>
                </tr>

                {{-- PAST MEDICAL / SURGICAL --}}
                <tr>
                    <th rowspan="2" class="title">PAST MEDICAL / SURGICAL</th>
                </tr>
                <tr>
                    <td><input type="text" name="past_condition_name"
                            value="{{ $pastMedicalSurgical->condition_name ?? '' }}"></td>
                    <td><textarea name="past_description">{{ $pastMedicalSurgical->description ?? '' }}</textarea></td>
                    <td><textarea name="past_medication">{{ $pastMedicalSurgical->medication ?? '' }}</textarea></td>
                    <td><textarea name="past_dosage">{{ $pastMedicalSurgical->dosage ?? '' }}</textarea></td>
                    <td><textarea name="past_side_effect">{{ $pastMedicalSurgical->side_effect ?? '' }}</textarea></td>
                    <td><textarea name="past_comment">{{ $pastMedicalSurgical->comment ?? '' }}</textarea></td>
                </tr>

                {{-- KNOWN CONDITION OR ALLERGIES --}}
                <tr>
                    <th rowspan="2" class="title">KNOWN CONDITION OR ALLERGIES</th>
                </tr>
                <tr>
                    <td><input type="text" name="allergy_condition_name" value="{{ $allergy->condition_name ?? '' }}">
                    </td>
                    <td><textarea name="allergy_description">{{ $allergy->description ?? '' }}</textarea></td>
                    <td><textarea name="allergy_medication">{{ $allergy->medication ?? '' }}</textarea></td>
                    <td><textarea name="allergy_dosage">{{ $allergy->dosage ?? '' }}</textarea></td>
                    <td><textarea name="allergy_side_effect">{{ $allergy->side_effect ?? '' }}</textarea></td>
                    <td><textarea name="allergy_comment">{{ $allergy->comment ?? '' }}</textarea></td>
                </tr>

                {{-- VACCINATION --}}
                <tr>
                    <th rowspan="2" class="title">VACCINATION & IMMUNIZATION</th>
                </tr>
                <tr>
                    <td><input type="text" name="vaccine_name" value="{{ $vaccination->condition_name ?? '' }}"></td>
                    <td><textarea name="vaccine_description">{{ $vaccination->description ?? '' }}</textarea></td>
                    <td><textarea name="vaccine_medication">{{ $vaccination->medication ?? '' }}</textarea></td>
                    <td><textarea name="vaccine_dosage">{{ $vaccination->dosage ?? '' }}</textarea></td>
                    <td><textarea name="vaccine_side_effect">{{ $vaccination->side_effect ?? '' }}</textarea></td>
                    <td><textarea name="vaccine_comment">{{ $vaccination->comment ?? '' }}</textarea></td>
                </tr>

                {{-- DEVELOPMENTAL HISTORY --}}
                <tr>
                    <th colspan="7" class="title">DEVELOPMENTAL HISTORY</th>
                </tr>
                <tr>
                    <th>GROSS MOTOR</th>
                    <td colspan="6"><textarea name="gross_motor">{{ $developmentalHistory->gross_motor ?? '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <th>FINE MOTOR</th>
                    <td colspan="6"><textarea name="fine_motor">{{ $developmentalHistory->fine_motor ?? '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <th>LANGUAGE</th>
                    <td colspan="6"><textarea name="language">{{ $developmentalHistory->language ?? '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <th>COGNITIVE</th>
                    <td colspan="6"><textarea name="cognitive">{{ $developmentalHistory->cognitive ?? '' }}</textarea>
                    </td>
                </tr>
                <tr>
                    <th>SOCIAL</th>
                    <td colspan="6"><textarea name="social">{{ $developmentalHistory->social ?? '' }}</textarea></td>
                </tr>
            </table>

        </div>

            <div class="buttons">
                <button type="submit" class="btn">Submit</button>
            </div>



        </form>

    </body>

@endsection

@push('styles')
    @vite(['resources/css/medical-history-style.css'])
@endpush