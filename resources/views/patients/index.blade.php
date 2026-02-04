@extends('layouts.app')

@section('title', 'Patients List')

@section('content')
    <div class="mx-auto my-10 w-[72%]">
        {{-- Action Buttons & Success Messages --}}
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-creato-black text-dark-green text-3xl font-black">PATIENT LIST</h2>
            <div class="flex items-center space-x-4">
                <input
                    type="text"
                    id="patient-search"
                    placeholder="Search patients..."
                    class="border-dark-green w-64 rounded-full border px-4 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
                />
                <a href="{{ route('patients.create') }}" class="button-default w-[200px] text-center">ADD PATIENT</a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="relative mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700"
                role="alert"
            >
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Table Container --}}
        <div class="bg-beige overflow-hidden rounded-lg shadow-md">
            <table class="w-full border-collapse">
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
                        {{-- The trashed() check now means "Inactive". The styling is perfect for this. --}}
                        <tr
                            class="{{ $patient->trashed() ? 'bg-red-100 text-red-700' : 'bg-beige' }} hover:bg-opacity-50 transition-all duration-300 hover:bg-white"
                            data-id="{{ $patient->patient_id }}"
                        >
                            <td
                                class="border-line-brown/30 font-creato-black text-brown border-b-2 p-3 text-center text-[13px] font-bold"
                            >
                                {{ $patient->patient_id }}
                            </td>
                            <td class="border-line-brown/30 border-b-2 p-3">
                                <a
                                    href="{{ route('patients.edit', $patient->patient_id) }}"
                                    class="font-creato-black text-brown hover:text-brown p-3 text-[13px] font-bold transition-colors duration-150 hover:underline"
                                >
                                    {{ $patient->name }}
                                </a>
                            </td>
                            <td
                                class="border-line-brown/30 font-creato-black text-brown border-b-2 p-3 text-center text-[13px] font-bold"
                            >
                                {{ $patient->age }}
                            </td>
                            <td
                                class="border-line-brown/30 font-creato-black text-brown border-b-2 p-3 text-center text-[13px] font-bold"
                            >
                                {{ $patient->sex }}
                            </td>
                            <td class="border-line-brown/30 border-b-2 p-3 text-center whitespace-nowrap">
                                @if ($patient->trashed())
                                    {{-- This is an INACTIVE patient. Show "Set Active" button. --}}
                                    <button
                                        type="button"
                                        class="font-creato-black js-toggle-patient-status inline-block cursor-pointer rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white shadow-sm transition duration-150 hover:bg-green-600"
                                        data-patient-id="{{ $patient->patient_id }}"
                                        data-action="activate"
                                    >
                                        SET ACTIVE
                                    </button>
                                @else
                                    {{-- This is an ACTIVE patient. Show "Edit" and "Set Inactive" buttons. --}}
                                    <a
                                        href="{{ route('patients.edit', $patient->patient_id) }}"
                                        class="font-creato-black inline-block rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white shadow-sm transition duration-150 hover:bg-green-600"
                                    >
                                        EDIT
                                    </a>

                                    <button
                                        type="button"
                                        class="hover:bg-dark-red font-creato-black js-toggle-patient-status inline-block cursor-pointer rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white shadow-sm transition duration-150"
                                        data-patient-id="{{ $patient->patient_id }}"
                                        data-action="deactivate"
                                    >
                                        SET INACTIVE
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500">No patients found.</td>
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
