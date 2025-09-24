@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    {{-- Display error message if it exists in the session --}}
    @if(session('error'))
      <div
        style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
        {{ session('error') }}
      </div>
    @endif

    @if (session('success'))
    <div class="alert alert-success" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    {{-- Form for patient selection (submits with GET to reload page) --}}
    <form action="{{ route('medication-reconciliation') }}" method="GET">
      <div class="container">
        <div class="header">
          <label for="patient_id">PATIENT NAME :</label>

          {{-- Patient Name DROPDOWN. 'onchange' event submits the form to fetch data --}}
          <select id="patient_info" name="patient_id" onchange="this.form.submit()">
            <option value="">-- Select Patient --</option>
            @foreach ($patients as $patient)
              <option value="{{ $patient->patient_id }}" {{ (isset($selectedPatient) && $selectedPatient->patient_id == $patient->patient_id) ? 'selected' : '' }}>
                {{ $patient->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </form>
    
    {{-- Form for data submission (submits with POST) --}}
    <form action="{{ route('medreconciliation.store') }}" method="POST">
      @csrf
        {{-- Hidden input to send the selected patient's ID with the POST request --}}
      <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">


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
              {{-- Populate fields with data from the controller --}}
              <td><input type="text" name="current_med" placeholder="Medication" value="{{ $currentMedication->current_med ?? '' }}"></td>
              <td><input type="text" name="current_dose" placeholder="Dose" value="{{ $currentMedication->current_dose ?? '' }}"></td>
              <td><input type="text" name="current_route" placeholder="Route" value="{{ $currentMedication->current_route ?? '' }}"></td>
              <td><input type="text" name="current_frequency" placeholder="Frequency" value="{{ $currentMedication->current_frequency ?? '' }}"></td>
              <td><input type="text" name="current_indication" placeholder="Indication" value="{{ $currentMedication->current_indication ?? '' }}"></td>
              <td><input type="text" name="current_text" value="{{ $currentMedication->current_text ?? '' }}"></td>
            </tr>
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
              {{-- Populate fields with data from the controller --}}
              <td><input type="text" name="home_med" placeholder="Medication" value="{{ $homeMedication->home_med ?? '' }}"></td>
              <td><input type="text" name="home_dose" placeholder="Dose" value="{{ $homeMedication->home_dose ?? '' }}"></td>
              <td><input type="text" name="home_route" placeholder="Route" value="{{ $homeMedication->home_route ?? '' }}"></td>
              <td><input type="text" name="home_frequency" placeholder="Frequency" value="{{ $homeMedication->home_frequency ?? '' }}"></td>
              <td><input type="text" name="home_indication" placeholder="Indication" value="{{ $homeMedication->home_indication ?? '' }}"></td>
              <td><input type="text" name="home_text" value="{{ $homeMedication->home_text ?? '' }}"></td>
            </tr>
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
              {{-- Populate fields with data from the controller --}}
              <td><input type="text" name="change_med" placeholder="Medication" value="{{ $changesInMedication->change_med ?? '' }}"></td>
              <td><input type="text" name="change_dose" placeholder="Dose" value="{{ $changesInMedication->change_dose ?? '' }}"></td>
              <td><input type="text" name="change_route" placeholder="Route" value="{{ $changesInMedication->change_route ?? '' }}"></td>
              <td><input type="text" name="change_frequency" placeholder="Frequency" value="{{ $changesInMedication->change_frequency ?? '' }}"></td>
              <td><input type="text" name="change_indication" placeholder="Indication" value="{{ $changesInMedication->change_indication ?? '' }}"></td>
            </tr>
          </table>
      </div>

      <div class="buttons">
        <button class="btn" type="submit">Submit</button>
      </div>

    </form>
@endsection

@push('styles')
    @vite(['resources/css/medication-reconciliation.css'])
@endpush