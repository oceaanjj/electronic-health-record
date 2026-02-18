@extends('layouts.app')

@section('title', 'Patients List')

@section('content')

    <div class="mx-auto w-full px-25 py-10 sm:w-[95%] md:w-[95%]">
        <div class="mx-auto mt-4 max-w-[1382px] px-4 sm:px-6 md:px-6 lg:px-20">

            {{-- Header Section --}}
            <div class="mb-6 flex flex-col items-center justify-between gap-3 lg:flex-row">

                <h2 class="font-creato-black text-dark-green text-3xl font-black">
                    PATIENT LIST
                </h2>

                <div class="flex w-full flex-col gap-3 lg:w-auto lg:flex-row lg:items-center">

                    {{-- Search Input --}}
                    <input type="text" id="patient-search" placeholder="Search patients..."
                        class="w-full lg:w-64 rounded-full border border-dark-green bg-white px-4 py-2 text-black shadow-sm  outline-none focus:bg-white focus:border-blue-500  focus:ring-2 focus:ring-blue-500 focus:ring-offset-0" />

                    {{-- Add Button --}}
                    <a href="{{ route('patients.create') }}" class="button-default w-full text-center lg:w-[200px]">
                        ADD PATIENT
                    </a>

                </div>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Table Container --}}
            <div class="overflow-hidden rounded-lg bg-beige shadow-md">
                <div class="overflow-x-auto">

                    <table class="min-w-full w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="main-header text-center">PATIENT ID</th>
                                <th class="main-header text-center">NAME</th>
                                <th class="main-header text-center">AGE</th>
                                <th class="main-header text-center">SEX</th>
                                <th class="main-header text-center">ACTIONS</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($patients as $patient)

                                <tr class="{{ $patient->trashed() ? 'bg-red-100 text-red-700' : 'bg-beige' }}
                                                                                                                                               transition-all duration-300 hover:bg-white hover:bg-opacity-50"
                                    data-id="{{ $patient->patient_id }}">
                                    <td
                                        class="border-line-brown/30 border-b-2 p-3 text-center text-[13px] font-creato-black font-bold text-brown">
                                        {{ $patient->patient_id }}
                                    </td>

                                    <td class="border-line-brown/30 border-b-2 p-3">
                                        <a href="{{ route('patients.edit', $patient->patient_id) }}"
                                            class="font-creato-black text-[13px] font-bold text-brown transition hover:underline">
                                            {{ $patient->name }}
                                        </a>
                                    </td>

                                    <td
                                        class="border-line-brown/30 border-b-2 p-3 text-center text-[13px] font-creato-black font-bold text-brown">
                                        {{ $patient->age }}
                                    </td>

                                    <td
                                        class="border-line-brown/30 border-b-2 p-3 text-center text-[13px] font-creato-black font-bold text-brown">
                                        {{ $patient->sex }}
                                    </td>

                                    <td class="border-line-brown/30 border-b-2 p-3 text-center whitespace-nowrap">

                                        @if ($patient->trashed())
                                            {{-- Inactive --}}
                                            <button type="button"
                                                class="js-toggle-patient-status inline-flex items-center justify-center rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white shadow-sm transition hover:bg-green-600"
                                                data-patient-id="{{ $patient->patient_id }}" data-action="activate">
                                                <span>SET ACTIVE</span>
                                            </button>
                                        @else
                                            {{-- Active --}}
                                            <div class="flex justify-center gap-2">

                                                <a href="{{ route('patients.edit', $patient->patient_id) }}"
                                                    class="inline-flex items-center justify-center rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white shadow-sm transition hover:bg-green-600">
                                                    EDIT
                                                </a>

                                                <button type="button"
                                                    class="js-toggle-patient-status inline-flex items-center justify-center rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white shadow-sm transition hover:bg-dark-red"
                                                    data-patient-id="{{ $patient->patient_id }}" data-action="deactivate">
                                                    SET INACTIVE
                                                </button>

                                            </div>
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

        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/soft-delete.js'])
    @vite(['resources/js/patient-search.js'])
@endpush