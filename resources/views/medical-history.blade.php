

@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')
    <body>

        {{-- FORM --}}
        <form action="{{ route('medical.store') }}" method="POST">
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
              <td><input type="text" name="present_condition_name"></td>
              <td><textarea name="present_description"></textarea></td>
              <td><textarea name="present_medication"></textarea></td>
              <td><textarea name="present_dosage"></textarea></td>
              <td><textarea name="present_side_effect"></textarea></td>
              <td><textarea name="present_comment"></textarea></td>
            </tr>

            {{-- PAST MEDICAL / SURGICAL --}}
            <tr>
              <th rowspan="2" class="title">PAST MEDICAL / SURGICAL</th>
            </tr>
            <tr>
              <td><input type="text" name="past_condition_name"></td>
              <td><textarea name="past_description"></textarea></td>
              <td><textarea name="past_medication"></textarea></td>
              <td><textarea name="past_dosage"></textarea></td>
              <td><textarea name="past_side_effect"></textarea></td>
              <td><textarea name="past_comment"></textarea></td>
            </tr>

            {{-- KNOWN CONDITION OR ALLERGIES --}}
            <tr>
              <th rowspan="2" class="title">KNOWN CONDITION OR ALLERGIES</th>
            </tr>
            <tr>
              <td><input type="text" name="allergy_condition_name"></td>
              <td><textarea name="allergy_description"></textarea></td>
              <td><textarea name="allergy_medication"></textarea></td>
              <td><textarea name="allergy_dosage"></textarea></td>
              <td><textarea name="allergy_side_effect"></textarea></td>
              <td><textarea name="allergy_comment"></textarea></td>
            </tr>

            {{-- VACCINATION --}}
            <tr>
              <th rowspan="2" class="title">VACCINATION & IMMUNIZATION</th>
            </tr>
            <tr>
              <td><input type="text" name="vaccine_name"></td>
              <td><textarea name="vaccine_description"></textarea></td>
              <td><textarea name="vaccine_medication"></textarea></td>
              <td><textarea name="vaccine_dosage"></textarea></td>
              <td><textarea name="vaccine_side_effect"></textarea></td>
              <td><textarea name="vaccine_comment"></textarea></td>
            </tr>

            {{-- DEVELOPMENTAL HISTORY --}}
            <tr>
              <th colspan="7" class="title">DEVELOPMENTAL HISTORY</th>
            </tr>
            <tr>
              <th>GROSS MOTOR</th>
              <td colspan="6"><textarea name="gross_motor"></textarea></td>
            </tr>
            <tr>
              <th>FINE MOTOR</th>
              <td colspan="6"><textarea name="fine_motor"></textarea></td>
            </tr>
            <tr>
              <th>LANGUAGE</th>
              <td colspan="6"><textarea name="language"></textarea></td>
            </tr>
            <tr>
              <th>COGNITIVE</th>
              <td colspan="6"><textarea name="cognitive"></textarea></td>
            </tr>
            <tr>
              <th>SOCIAL</th>
              <td colspan="6"><textarea name="social"></textarea></td>
            </tr>
          </table>

          <div class="btn">
            <button type="submit">Submit</button>
          </div>
        </form>
      </div>

@endsection

            @push('styles')
                    @vite(['resources/css/medical-history-style.css'])
            @endpush

