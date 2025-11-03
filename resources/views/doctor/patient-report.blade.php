@extends('layouts.app')

@section('title', 'Generate Patient Report')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4">Generate Patient Report</h1>

        <form id="reportForm" action="{{ route('doctor.generate-report') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="patient_search_input" class="block text-gray-700 font-bold mb-2">Search Patient:</label>
                <div class="searchable-dropdown" data-select-url="{{ route('doctor.patient-report') }}">
                    <input type="text" id="patient_search_input" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="-Select or type to search-" autocomplete="off">
                    <div id="patient_options_container" class="absolute z-10 w-full bg-white border border-gray-300 rounded-b-lg shadow-lg" style="display: none;">
                        @foreach ($patients as $patient)
                            <div class="option p-2 hover:bg-gray-200 cursor-pointer" data-value="{{ $patient->patient_id }}">
                                {{ trim($patient->name) }}
                            </div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="patient_id" id="patient_id_hidden">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Generate Report</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
@endpush
