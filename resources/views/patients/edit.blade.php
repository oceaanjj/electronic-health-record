{{-- resources/views/patients/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Patient')

@push('styles')
    @vite(['resources/css/edit-style.css'])
@endpush

@section('content')
    <div class="header">
        <h4>Edit Patient</h4>
    </div>

    <div class="form-container">
        <form action="{{ route('patients.update', $patient->patient_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="{{ $patient->name }}" required>
            </div>

            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" value="{{ $patient->age }}" required>
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
                <input type="date" name="admission_date" value="{{ $patient->admission_date }}">
            </div>

            <div class="form-group full-width">
                <label>Chief Complaints</label>
                <textarea name="chief_complaints">{{ $patient->chief_complaints }}</textarea>
            </div>


            <div class="form-group full-width">
                <button type="submit" class="btn-submit">Update</button>
            </div>
        </form>
    </div>
@endsection
