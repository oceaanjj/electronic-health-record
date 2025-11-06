@extends('layouts.app')
@section('title', 'Search Patient')

@vite(['resources/css/search-style.css', 'resources/js/patient-search.js'])
@section('content')


    <div class="header">
        SEARCH PATIENT
    </div>

    <form action="{{ route('patients.search-results') }}" method="GET">
        <input type="text" name="input" id="patientSearchInput" placeholder="Search by ID or Patient Name"
            value="{{ request('input') }}">
    </form>

    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Age</th>
            </tr>
        </thead>
        <tbody id="patientTableBody">
            <!-- Patient data will be loaded here by JavaScript -->
        </tbody>
    </table>

    </div>

    @endsection