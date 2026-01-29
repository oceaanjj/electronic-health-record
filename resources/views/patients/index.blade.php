@extends('layouts.app')

@section('title', 'Patients List')

@section('content')

    <div class="w-[72%] mx-auto my-10">

        {{-- Action Buttons & Success Messages --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-3xl font-creato-black font-black text-dark-green">PATIENT LIST</h2>
            <div class="flex items-center space-x-4">
                                <input type="text" id="patient-search" placeholder="Search patients..." class="w-64 px-4 py-2 rounded-full border border-dark-green focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                <a href="{{ route('patients.create') }}" class="button-default w-[200px] text-center">ADD PATIENT</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif


        {{-- Table Container --}}
        <div class="bg-beige rounded-lg shadow-md overflow-hidden">
            <table class="w-full border-collapse ">
                <thead>
                    <tr>
                        <th
                            class="main-header text-center">
                            PATIENT ID</th>
                        <th
                            class="main-header text-center">
                            NAME</th>
                        <th
                            class="main-header text-center">
                            AGE</th>
                        <th
                            class="main-header text-center">
                            SEX</th>
                        <th
                            class="main-header text-center">
                            ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        {{-- The trashed() check now means "Inactive". The styling is perfect for this. --}}
                        <tr class="{{ $patient->trashed() ? 'bg-red-100 text-red-700' : 'bg-beige' }} hover:bg-white hover:bg-opacity-50 transition-all duration-300"
                            data-id="{{ $patient->patient_id }}">
                            <td
                                class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                                {{ $patient->patient_id }}</td>
                            <td class="p-3 border-b-2 border-line-brown/30">
                                <a href="{{ route('patients.edit', $patient->patient_id) }}"
                                    class="p-3 font-creato-black font-bold text-brown text-[13px] hover:underline hover:text-brown transition-colors duration-150">
                                    {{ $patient->name }}
                                </a>
                            </td>
                            <td
                                class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                                {{ $patient->age }}</td>
                            <td
                                class="p-3 border-b-2 border-line-brown/30 font-creato-black font-bold text-brown text-[13px] text-center">
                                {{ $patient->sex }}</td>
                            <td class="p-3 border-b-2 border-line-brown/30 whitespace-nowrap text-center">

                                @if($patient->trashed())
                                    {{-- This is an INACTIVE patient. Show "Set Active" button. --}}
                                    <button type="button"
                                        class="inline-block bg-green-500 cursor-pointer hover:bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                        data-patient-id="{{ $patient->patient_id }}" data-action="activate">SET ACTIVE</button>
                                @else
                                    {{-- This is an ACTIVE patient. Show "Edit" and "Set Inactive" buttons. --}}
                                    <a href="{{ route('patients.edit', $patient->patient_id) }}"
                                        class="inline-block  bg-green-500 hover:bg-green-600  text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold">EDIT</a>

                                    <button type="button"
                                        class="inline-block bg-red-600 cursor-pointer hover:bg-dark-red text-white text-xs px-3 py-1 rounded-full shadow-sm transition duration-150 font-creato-black font-bold js-toggle-patient-status"
                                        data-patient-id="{{ $patient->patient_id }}" data-action="deactivate">SET INACTIVE</button>
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
    @vite(['resources/js/soft-delete.js'])
    @vite(['resources/js/patient-search.js'])
@endpush