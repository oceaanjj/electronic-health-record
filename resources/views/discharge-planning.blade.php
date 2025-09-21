@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

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

    <form action="{{ route('discharge-planning.store') }}" method="POST">
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
          <th colspan="2">Discharge Planning</th>
        </tr>
        <tr>
          <th>Discharge Criteria</th>
          <th>Required Action</th>
        </tr>
        <tr>
          <td class="criteria">Fever Resolution</td>
          <td><textarea name="criteria_feverRes"></textarea></td>
        </tr>
        <tr>
          <td class="criteria">Normalization of Patient Count</td>
          <td><textarea name="criteria_patientCount"></textarea></td>
        </tr>
        <tr>
          <td class="criteria">Manage Fever Effectively</td>
          <td><textarea name="criteria_manageFever"></textarea></td>
        </tr>
        <tr>
          <td class="criteria">Manage Fever Effectively</td>
          <td><textarea name="criteria_manageFever"></textarea></td>
        </tr>
      </table>
    </div>

    <div class="section">
      <table>
        <tr>
          <th colspan="2">Discharge Instruction</th>
        </tr>
        <tr>
          <th>Instruction</th>
          <th>Details</th>
        </tr>
        <tr>
          <td class="criteria">Medications</td>
          <td><textarea name="instruction_med"></textarea></td>
        </tr>
        <tr>
          <td class="criteria">Follow-Up Appointment</td>
          <td><textarea name="instruction_appointment"></textarea></td>
        </tr>
        <tr>
          <td class="criteria">Fluid Intake</td>
          <td><textarea name="instruction_fluidIntake"></textarea></td>
        </tr>
        <tr>
          <td class="criteria">Avoid Mosquito Exposure</td>
          <td><textarea name="instruction_exposure"></textarea></td>
        </tr>
        <tr>
          <td class="criteria">Monitor for Signs of Complications</td>
            <td><textarea name="instruction_complications"></textarea></td>
        </tr>
      </table>
    </div>

  </div>
      <div class="buttons">
        <button class="btn"type="submit">Submit</button>
    </div>

    </form>
@endsection

            @push('styles')
                    @vite(['resources/css/discharge-planning.css'])
            @endpush