@extends('layouts.app')

@section('title', 'Patient IVs and Lines')

@section('content')

    <head>
        <meta charset="UTF-8">
        <title>Patient Ivs and Lines</title>
        @vite(['./resources/css/ivs-and-lines.css'])
    </head>

    <body>

        <form action="{{ route('ivs-and-lines.select') }}" method="POST">
            @csrf
            <div class="container">
                <div class="header">
                    <label for="patient_id">PATIENT NAME :</label>
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

        <form action="{{ route('ivs-and-lines.store') }}" method="POST">
            @csrf
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

            <table>
                <tr>
                    <th class="title">IV FLUID</th>
                    <th class="title">RATE</th>
                    <th class="title">SITE</th>
                    <th class="title">STATUS</th>
                </tr>

                <tr>
                    <td><input type="text" name="iv_fluid" placeholder="iv fluid"
                            value="{{ $ivsAndLineRecord->iv_fluid ?? '' }}"></td>
                    <td><input type="text" name="rate" placeholder="rate" value="{{ $ivsAndLineRecord->rate ?? '' }}"></td>
                    <td><input type="text" name="site" placeholder="site" value="{{ $ivsAndLineRecord->site ?? '' }}"></td>
                    <td><input type="text" name="status" placeholder="status" value="{{ $ivsAndLineRecord->status ?? '' }}">
                    </td>
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