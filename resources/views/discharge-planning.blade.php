@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    <div class="container">
        <div class="header">
            <label for="patient">PATIENT NAME :</label>
            <select id="patient" name="patient">
                <option value="">-- Select Patient --</option>
                <option value="Althea Pascua">Jovilyn Esquerra</option>
            </select>
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
@endsection

            @push('styles')
                    @vite(['resources/css/discharge-planning.css'])
            @endpush
