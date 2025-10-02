@extends('layouts.app')

@section('title', 'Patient Details')

@push('styles')
    @vite(['resources/css/show-style.css'])
@endpush

@section('content')
<div class="header">
        PATIENT NAME: {{ $patient->name }}
    </div>

        <div class="details-container">
    <table class="details-table">
        <tr>
            <th>Age:</th>
            <td>{{ $patient->age }}</td>
        </tr>
        <tr>
            <th>Sex:</th>
            <td>{{ $patient->sex }}</td>
        </tr>
        <tr>
            <th>Address:</th>
            <td>{{ $patient->address }}</td>
        </tr>
        <tr>
            <th>Birthplace:</th>
            <td>{{ $patient->birthplace }}</td>
        </tr>
        <tr>
            <th>Religion:</th>
            <td>{{ $patient->religion }}</td>
        </tr>
        <tr>
            <th>Ethnicity:</th>
            <td>{{ $patient->ethnicity }}</td>
        </tr>
        <tr>
            <th>Chief Complaints:</th>
            <td>{{ $patient->chief_complaints }}</td>
        </tr>
        <tr>
            <th>Admission Date:</th>
            <td>{{ $patient->admission_date }}</td>
        </tr>
    </table>

    <a href="{{ route('patients.index') }}">Back</a>
</div>


@endsection
