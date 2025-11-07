@extends('layouts.app')

@section('title', 'Patients List')

@section('content')

    <div class="w-[72%] mx-auto my-10">

        {{-- Action Buttons & Success Messages --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-dark-green">PATIENT LIST</h2>
            <a href="{{ route('patients.create') }}" class="button-default">Add Patient</a>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        

        {{-- Table Container --}}
        <div class="bg-beige rounded-lg shadow-md overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="bg-yellow-light text-brown text-[13px] p-3 text-left border-b-2 border-line-brown font-bold">Patient ID</th>
                        <th class="bg-yellow-light text-brown text-[13px] p-3 text-left border-b-2 border-line-brown font-bold">Name</th>
                        <th class="bg-yellow-light text-brown text-[13px] p-3 text-left border-b-2 border-line-brown font-bold">Age</th>
                        <th class="bg-yellow-light text-brown text-[13px] p-3 text-left border-b-2 border-line-brown font-bold">Sex</th>
                        <th class="bg-yellow-light text-brown text-[13px] p-3 text-left border-b-2 border-line-brown font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        {{-- The trashed() check now means "Inactive". The styling is perfect for this. --}}
                        <tr class="{{ $patient->trashed() ? 'bg-red-100 text-red-700' : 'bg-beige' }} hover:bg-gray-100" data-id="{{ $patient->patient_id }}">
                            <td class="p-3 border-b border-line-brown/70">{{ $patient->patient_id }}</td>
                            <td class="p-3 border-b border-line-brown/70">
                                <a href="{{ route('patients.show', $patient->patient_id) }}" class="text-black hover:underline font-semibold">
                                    {{ $patient->name }}
                                </a>
                            </td>
                            <td class="p-3 border-b border-line-brown/70">{{ $patient->age }}</td>
                            <td class="p-3 border-b border-line-brown/70">{{ $patient->sex }}</td>
                            <td class="p-3 border-b border-line-brown/70 whitespace-nowrap">
                                
                                @if($patient->trashed())
                                    {{-- This is an INACTIVE patient. Show "Set Active" button. --}}
                                    <form action="{{ route('patients.activate', $patient->patient_id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150">Set Active</button>
                                    </form>
                                @else
                                    {{-- This is an ACTIVE patient. Show "Edit" and "Set Inactive" buttons. --}}
                                    <a href="{{ route('patients.edit', $patient->patient_id) }}" class="inline-block bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150">Edit</a>
                                    
                                    <form action="{{ route('patients.deactivate', $patient->patient_id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-block bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150">Set Inactive</button>
                                    </form>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">
                                No patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

{{-- Removed the @push('styles') as styles are now inlined with Tailwind --}}

@push('scripts')
    {{-- This JS file might need updates if it was hardcoded to look for 'delete' --}}
    @vite(['resources/js/soft-delete.js'])
@endpush