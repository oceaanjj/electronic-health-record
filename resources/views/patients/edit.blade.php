{{-- resources/views/patients/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')

<body>
    <div class="header">
        EDIT PATIENT
    </div>

    <div class="form-container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('patients.update', $patient->patient_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ $patient->first_name }}" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ $patient->last_name }}" required>
            </div>

            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" value="{{ $patient->middle_name }}">
            </div>

            <div class="form-group">
                <label>Birthdate</label>
                <input type="date" name="birthdate" id="birthdate" value="{{ $patient->birthdate }}">
            </div>

            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" value="{{ $patient->age }}" id="age" readonly required>
            </div>

            <div class="form-group">
                <label>Sex</label>
                <select name="sex" required>
                    <option value="Male" {{ $patient->sex == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $patient->sex == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ $patient->sex == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="{{ $patient->address }}">
            </div>

            <div class="form-group">
                <label>Birthplace</label>
                <input type="text" name="birthplace" value="{{ $patient->birthplace }}">
            </div>

            <div class="form-group">
                <label>Religion</label>
                <input type="text" name="religion" value="{{ $patient->religion }}">
            </div>

            <div class="form-group">
                <label>Ethnicity</label>
                <input type="text" name="ethnicity" value="{{ $patient->ethnicity }}">
            </div>

             <div class="form-group">
                <label>Admission Date</label>
                <input type="date" name="admission_date" value="{{ $patient->admission_date ? $patient->admission_date->format('Y-m-d') : '' }}">
            </div>

            <div class="form-group full-width">
                <label>Chief Complaints</label>
                <textarea name="chief_complaints">{{ $patient->chief_complaints }}</textarea>
            </div>

             <div class="btnn">
            <button type="button" class="btn-submit" onclick="window.history.back()">Back</button>
             </div>
             
            <div class="btnn">
                <button type="submit" class="btn-submit">Update</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/edit-style.css'])
@endpush

@push('scripts')
    @vite(['resources/js/compute-age.js'])
@endpush
