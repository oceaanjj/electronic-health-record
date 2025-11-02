@extends('layouts.app')

@section('title', 'Doctor Home')

@section('content')
<div class="container">
    <h1>DOCTOR HOME</h1>

    <div class="card">
        <div class="card-header">Generate Patient Report</div>
        <div class="card-body">
            <form action="{{ route('doctor.generate-report') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="patient_id">Select Patient:</label>
                    <select name="patient_id" id="patient_id" class="form-control">
                        @foreach($patients as $patient)
                            <option value="{{ $patient->patient_id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </form>
        </div>
    </div>
</div>
@endsection