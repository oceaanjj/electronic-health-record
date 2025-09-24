@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

<head>
    <meta charset="UTF-8">
    <title>Patient Ivs and Lines</title>
    @vite(['./resources/css/ivs-and-lines.css'])
</head>
<body>
    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form for patient selection (submits with GET to reload page) --}}
    <form action="{{ route('ivs-and-lines') }}" method="GET">
        <div class="container">
            <div class="header">
                <label for="patient_id">PATIENT NAME :</label>

                {{-- Patient Name DROPDOWN. The 'onchange' event will submit the form --}}
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
    <form action="{{ route('ivs-and-lines.store') }}" method="POST">
        @csrf

        {{-- Hidden input to send the selected patient's ID with the POST request --}}
        <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

        <table>
            <tr>
                <th class="title">IV FLUID</th>
                <th class="title">RATE</th>
                <th class="title">SITE</th>
                <th class="title">STATUS</th>
            </tr>

            <tr>
                {{-- The 'value' attribute is populated with data from the controller if available --}}
                <td><input type="text" name="iv_fluid" placeholder="iv fluid" value="{{ $ivsAndLineRecord->iv_fluid ?? '' }}"></td>
                <td><input type="text" name="rate" placeholder="rate" value="{{ $ivsAndLineRecord->rate ?? '' }}"></td>
                <td><input type="text" name="site" placeholder="site" value="{{ $ivsAndLineRecord->site ?? '' }}"></td>
                <td><input type="text" name="status" placeholder="status" value="{{ $ivsAndLineRecord->status ?? '' }}"></td>
            </tr>
        </table>

        <div class="buttons">
            <button class="btn" type="submit">Submit</button>
        </div>
    </form>
</body>

@endsection

@push('styles')
    @vite(['resources/css/ivs-and-lines.css'])
@endpush