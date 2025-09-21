<head>
    <meta charset="UTF-8">
    <title>Patient Activities of daily living</title>
    @vite(['resources/css/#.css'])
    <meta charset="UTF-8">
    <title>Patient Ivs and Lines</title>
    @vite(['./resources/css/ivs-and-lines.css'])
</head>
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

    <form action="{{ route('ivs-and-lines.store') }}" method="POST">
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
                <th class="title">IV FLUID</th>
                <th class="title">RATE</th>
                <th class="title">SITE</th>
                <th class="title">STATUS</th>
            </tr>

            <tr>
                
                <td><input type="text" name="iv_fluid" placeholder="iv fluid" ></td>
                <td><input type="text" name="rate" placeholder="rate" ></td>
                <td><input type="text" name="site" placeholder="site" ></td>
                <td><input type="text" name="status" placeholder="status" ></td>
            </tr>


</table>


    <div class="buttons">
        <button class="btn" type="submit">Submit</button>
    </div>
 </div>
</form>
       
    
@endsection

@push('styles')
    @vite(['resources/css/ivs-and-lines.css'])
@endpush