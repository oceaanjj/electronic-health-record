

@extends('layouts.app')

@section('title', 'Patient Medical Reconciliation')

@section('content')

  <x-searchable-dropdown 
        :patients="$patients" 
        :selectedPatient="$selectedPatient ?? null"
        selectUrl="{{ route('medreconciliation.select') }}" 
    />


    <div id="form-content-container">
        {{-- DISABLED input overlay --}}
        @if (!session('selected_patient_id'))
            <div class="form-overlay" style="margin-left:15rem;">
                <span>Please select a patient to input</span> {{-- message --}}
            </div>
        @endif

  <form action="{{ route('medreconciliation.store') }}" method="POST">
    @csrf
    <fieldset @if (!session('selected_patient_id')) disabled @endif>
    <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">


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
          <td><input type="text" name="current_med" placeholder="Medication"
              value="{{ old('current_med', $currentMedication->current_med ?? '') }}"></td>
          <td><input type="text" name="current_dose" placeholder="Dose"
              value="{{ old('current_dose', $currentMedication->current_dose ?? '') }}"></td>
          <td><input type="text" name="current_route" placeholder="Route"
              value="{{ old('current_route', $currentMedication->current_route ?? '') }}"></td>
          <td><input type="text" name="current_frequency" placeholder="Frequency"
              value="{{ old('current_frequency', $currentMedication->current_frequency ?? '') }}"></td>
          <td><input type="text" name="current_indication" placeholder="Indication"
              value="{{ old('current_indication', $currentMedication->current_indication ?? '') }}"></td>
          <td><input type="text" name="current_text" value="{{ old('current_text', $currentMedication->current_text ?? '') }}"></td>
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
          <td><input type="text" name="home_med" placeholder="Medication" value="{{ old('home_med', $homeMedication->home_med ?? '') }}">
          </td>
          <td><input type="text" name="home_dose" placeholder="Dose" value="{{ old('home_dose', $homeMedication->home_dose ?? '') }}"></td>
          <td><input type="text" name="home_route" placeholder="Route" value="{{ old('home_route', $homeMedication->home_route ?? '') }}">
          </td>
          <td><input type="text" name="home_frequency" placeholder="Frequency"
              value="{{ old('home_frequency', $homeMedication->home_frequency ?? '') }}"></td>
          <td><input type="text" name="home_indication" placeholder="Indication"
              value="{{ old('home_indication', $homeMedication->home_indication ?? '') }}"></td>
          <td><input type="text" name="home_text" value="{{ old('home_text', $homeMedication->home_text ?? '') }}"></td>
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
          <td><input type="text" name="change_med" placeholder="Medication"
              value="{{ old('change_med', $changesInMedication->change_med ?? '') }}"></td>
          <td><input type="text" name="change_dose" placeholder="Dose"
              value="{{ old('change_dose', $changesInMedication->change_dose ?? '') }}"></td>
          <td><input type="text" name="change_route" placeholder="Route"
              value="{{ old('change_route', $changesInMedication->change_route ?? '') }}"></td>
          <td><input type="text" name="change_frequency" placeholder="Frequency"
              value="{{ old('change_frequency', $changesInMedication->change_frequency ?? '') }}"></td>
          <td><input type="text" name="change_text" placeholder="Indication"
              value="{{ old('change_text', $changesInMedication->change_text ?? '') }}"></td>
        </tr>
      </table>
    </div>

    <div class="buttons">
      <button class="btn" type="submit">Submit</button>
    </div>

  </fieldset>
  </form>
</div>

@endsection

@push('styles')
  @vite(['resources/css/medication-reconciliation.css'])
@endpush

@push('scripts')
  @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
@endpush