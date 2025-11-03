@extends('layouts.doctor')
@section('title', 'Doctor Home')
@section('content')

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">DOCTOR HOME</h1>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Generate Patient Report</h2>
                <a href="{{ route('doctor.patient-report') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Go</a>
            </div>
        </div>
    </div>
@endsection