{{-- show all patients --}}

@extends('layouts.app')

@section('title', 'Patients List')

@vite(['resources/css/index-style.css'])

@section('content')
    <div class="header">
        <h4>PATIENT LIST</h4>
    </div>

    <div class="actions">
        <a href="{{ route('patients.create') }}" class="btn-add">+ Add New Patient</a>
    </div>

    @if(session('success'))
        <p class="success-msg">{{ session('success') }}</p>
    @endif

    <div class="table-container">
        <table class="ehr-table">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Sex</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                    <tr>
                        <td>{{ $patient->patient_id }}</td>
                        <td>
                            <a href="{{ route('patients.show', $patient->patient_id) }}">
                                {{ $patient->name }}
                            </a>
                        </td>
                        <td>{{ $patient->age }}</td>
                        <td>{{ $patient->sex }}</td>
                        <td>
                            <a href="{{ route('patients.edit', $patient->patient_id) }}" class="btn-edit">Edit</a>
                            <form action="{{ route('patients.destroy', $patient->patient_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
