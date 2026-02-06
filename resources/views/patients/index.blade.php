@extends('layouts.app')

@section('title', 'Patients List')

@section('content')

    <!-- searchable dropdown -->
    <div class="mx-auto w-full px-4 py-10 sm:w-[90%] md:w-[72%]">

        <div class="mx-auto mt-4 max-w-[1382px] px-4 sm:px-6 md:px-6 lg:px-40">

                                    <div class="mb-4 flex flex-col lg:flex-row items-center justify-between space-y-2 lg:space-y-0">

                                        <h2 class="font-creato-black text-dark-green text-3xl font-black mb-2 lg:mb-0">PATIENT LIST</h2>

                                        <div class="flex flex-col lg:flex-row items-stretch lg:items-center space-y-2 lg:space-y-0 lg:space-x-4 w-full lg:w-auto">

                                            <input type="text" id="patient-search" placeholder="Search patients..."

                                                class="bg-white border-dark-green w-full lg:w-64 rounded-full border px-4 py-2 shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-500" />

                                            <a href="{{ route('patients.create') }}" class="button-default w-full lg:w-[200px] text-center">ADD PATIENT</a>

                                        </div>

                                    </div>
            <!-- end searchable dropdown -->


            @if (session('success'))
                <div class="relative mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Table Container --}}
            <div class="bg-beige overflow-hidden rounded-lg shadow-md">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-full border-collapse">
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
                                <tr class="{{ $patient->trashed() ? 'bg-red-100 text-red-700' : 'bg-beige' }} hover:bg-opacity-50 transition-all duration-300 hover:bg-white"
                                    data-id="{{ $patient->patient_id }}">
                                    <td
                                        class="border-line-brown/30 font-creato-black text-brown border-b-2 p-3 text-center text-[13px] font-bold">
                                        {{ $patient->patient_id }}
                                    </td>
                                    <td class="border-line-brown/30 border-b-2 p-3">
                                        <a href="{{ route('patients.edit', $patient->patient_id) }}"
                                            class="font-creato-black text-brown hover:text-brown p-3 text-[13px] font-bold transition-colors duration-150 hover:underline">
                                            {{ $patient->name }}
                                        </a>
                                    </td>
                                    <td
                                        class="border-line-brown/30 font-creato-black text-brown border-b-2 p-3 text-center text-[13px] font-bold">
                                        {{ $patient->age }}
                                    </td>
                                    <td
                                        class="border-line-brown/30 font-creato-black text-brown border-b-2 p-3 text-center text-[13px] font-bold">
                                        {{ $patient->sex }}
                                    </td>
                                    <td class="border-line-brown/30 border-b-2 p-3 text-center whitespace-nowrap">
                                        @if ($patient->trashed())
                                            {{-- This is an INACTIVE patient. Show "Set Active" button. --}}
                                            <button type="button"
                                                class="font-creato-black js-toggle-patient-status inline-flex items-center justify-center rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white shadow-sm transition duration-150 hover:bg-green-600 sm:px-3 sm:py-1"
                                                data-patient-id="{{ $patient->patient_id }}" data-action="activate">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                    class="h-4 w-4 sm:hidden">
                                                    <path fill-rule="evenodd"
                                                        d="M19.916 4.626a.75.75 0 0 1 .208 1.04l-9.5 13.5a.75.75 0 0 1-1.12.02L3.248 11.2a.75.75 0 0 1 1.06-1.06l5.247 5.248 8.902-12.66c.07-.1.154-.174.248-.23a.75.75 0 0 1 .902 0Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span class="hidden sm:inline">SET ACTIVE</span>
                                            </button>
                                        @else
                                            {{-- This is an ACTIVE patient. Show "Edit" and "Set Inactive" buttons. --}}
                                            <a href="{{ route('patients.edit', $patient->patient_id) }}"
                                                class="font-creato-black inline-flex items-center justify-center rounded-full bg-green-500 px-3 py-1 text-xs font-bold text-white shadow-sm transition duration-150 hover:bg-green-600 sm:px-3 sm:py-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" class="h-4 w-4 sm:hidden">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                </svg>
                                                <span class="hidden sm:inline">EDIT</span>
                                            </a>

                                            <button type="button"
                                                class="hover:bg-dark-red font-creato-black js-toggle-patient-status inline-flex items-center justify-center rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white shadow-sm transition duration-150 sm:px-3 sm:py-1"
                                                data-patient-id="{{ $patient->patient_id }}" data-action="deactivate">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                    class="h-4 w-4 sm:hidden">
                                                    <path fill-rule="evenodd"
                                                        d="M16.5 4.478v.227a48.842 48.842 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.841 48.841 0 0 1 3 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a.75.75 0 0 1 .712 0c.055.009.105.01.148.01h.704c1.242 0 2.407.41 3.347 1.144a.75.75 0 0 1-.722 1.164 3.003 3.003 0 0 0-2.292-1.124v-.002zM12 2.25a.75.75 0 0 1 .75.75v.75h3.75a.75.75 0 0 1 0 1.5H7.5a.75.75 0 0 1 0-1.5h3.75V3c0-.414.336-.75.75-.75z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span class="hidden sm:inline">SET INACTIVE</span>
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
        </div>
@endsection

    {{-- Removed the @push('styles') as styles are now inlined with Tailwind --}}

    @push('scripts')
        @vite(['resources/js/soft-delete.js'])
        @vite(['resources/js/patient-search.js'])
    @endpush