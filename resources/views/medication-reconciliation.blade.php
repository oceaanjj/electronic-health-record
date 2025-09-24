@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')



  <form action="{{ route('medreconciliation.store') }}" method="POST">
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


    <div class="section">
      <table>
        <tr>
          <th colspan="6">Patient's Current Medication (Upon Admission)</th>
        </tr>
        <tr>
          <th>Medication</th>
          <th>Dose</th>
          <th>Route</th>
          <th>Frequency</th>
          <th>Indication</th>
          <th>Administered During Stay?</th>
        </tr>
        <tr>
          <td><input type="text" name="current_med" placeholder="Medication"></td>
          <td><input type="text" name="current_dose" placeholder="Dose"></td>
          <td><input type="text" name="current_route" placeholder="Route"></td>
          <td><input type="text" name="current_frequency" placeholder="Frequency"></td>
          <td><input type="text" name="current_indication" placeholder="Indication"></td>
          <td><input type="text" name="current_text"></td>
        </tr>
        <input type="hidden" name="current_medication" value="1">
      </table>
    </div>
    <br>
    <div class="section">
      <table>
        <tr>
          <th colspan="6">Patient's Home Medication (If Any)</th>
        </tr>
        <tr>
          <th>Medication</th>
          <th>Dose</th>
          <th>Route</th>
          <th>Frequency</th>
          <th>Indication</th>
          <th>Discontinued on Admission?</th>
        </tr>
        <tr>
          <td><input type="text" name="home_med" placeholder="Medication"></td>
          <td><input type="text" name="home_dose" placeholder="Dose"></td>
          <td><input type="text" name="home_route" placeholder="Route"></td>
          <td><input type="text" name="home_frequency" placeholder="Frequency"></td>
          <td><input type="text" name="home_indication" placeholder="Indication"></td>
          <td><input type="text" name="home_text"></td>
        </tr>
        <input type="hidden" name="home_medication" value="1">
      </table>
    </div>
    <br>
    <div class="section">
      <table>
        <tr>
          <th colspan="6">Changes in Medication During Hospitalization</th>
        </tr>
        <tr>
          <th>Medication</th>
          <th>Dose</th>
          <th>Route</th>
          <th>Frequency</th>
          <th>Reason for Change</th>
        </tr>
        <tr>
          <td><input type="text" name="change_med" placeholder="Medication"></td>
          <td><input type="text" name="change_dose" placeholder="Dose"></td>
          <td><input type="text" name="change_route" placeholder="Route"></td>
          <td><input type="text" name="change_frequency" placeholder="Frequency"></td>
          <td><input type="text" name="change_indication" placeholder="Indication"></td>
          <td><input type="text" name="change_text"></td>
        </tr>
        <input type="hidden" name="changes_in_medication" value="1">
      </table>
    </div>
    </div>


    <div class="buttons">
      <button class="btn" type="submit">Submit</button>
    </div>

@endsection

  @push('styles')
    {{-- @vite(['resources/css/medication-reconciliation.css']) --}}
  @endpush