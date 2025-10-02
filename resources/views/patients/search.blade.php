@extends('layouts.app')
@section('title', 'Search Patient')

@vite(['resources/css/search-style.css'])
@section('content')


    <div class="container">
        <div class="header">
            <h4>SEARCH PATIENT</h4>
        </div>

        <form action="{{ route('patients.search-results') }}" method="GET">
            <input type="text" name="input" placeholder="Search by ID or Patient Name" value="{{ request('input') }}">
            <button type="submit">Search</button>
        </form>

        @if (!empty(request('input')))
            <h2>Search Results for: "{{ request('input') }}"</h2>

            @if ($patients->isNotEmpty())

                {{-- Show a table header for multiple results --}}
                <table>
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Age</th>
                            <!-- <th>Action</th>  -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($patients as $patient)
                            <tr>
                                <td>{{ $patient->patient_id }}</td>
                                <td>{{ $patient->name }}</td>
                                <td>{{ $patient->age }}</td>
                                <!-- actions -->
                                <!-- <td><a href="{{ route('patients.show', $patient->patient_id) }}" class="action-link">View Details</a></td> -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No patient found matching "{{ request('input') }}" in your records.</p>
            @endif
        @else
            <p>Please enter a patient ID or Name.</p>
        @endif
    </div>
@endsection