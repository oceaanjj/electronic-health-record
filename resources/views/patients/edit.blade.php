@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')

    {{-- ⚠️ Alerts: Using clean, modern styles --}}
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-200 dark:text-green-900" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-200 dark:text-red-900" role="alert">
            <span class="font-medium">Please correct the following errors:</span>
            <ul class="mt-1.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Container: Spacious width --}}
    <div class="w-[90%] md:w-[80%] lg:w-[65%] mx-auto my-12">

        <form action="{{ route('patients.update', $patient->patient_id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Combined Form Card --}}
            <div class="shadow-xl rounded-xl overflow-hidden mb-10 border border-gray-100">

                {{-- CARD HEADER: EDIT PATIENT RECOR (Dark Green Background, White Text, Custom Font) --}}
                <div class="bg-dark-green p-6 sm:p-8">
                    <h1 class="text-2xl font-creato-black font-black text-white tracking-wider">
                        EDIT PATIENT RECORD
                    </h1>
                </div>

                {{-- 1. Patient Information Body (White Background) --}}
                <div class="bg-white p-6 sm:p-8 ">

                    {{-- The old "Patient Details" content starts here, keeping the grid layout --}}
                    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-dark-green pb-3">Patient Details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-6 gap-6 ">

                        {{-- First Name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-1">First Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="{{ $patient->first_name }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="e.g. Juan" required>
                        </div>

                        {{-- Middle Name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="middle_name" class="block text-sm font-semibold text-gray-700 mb-1">Middle
                                Name</label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ $patient->middle_name }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="Optional">
                        </div>

                        {{-- Last Name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ $patient->last_name }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="e.g. Dela Cruz" required>
                        </div>

                        {{-- Birthdate --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthdate" class="block text-sm font-semibold text-gray-700 mb-1">Birthdate <span
                                    class="text-red-500">*</span></label>
                            <input type="date" id="birthdate" name="birthdate"
                                value="{{ $patient->birthdate ? \Carbon\Carbon::parse($patient->birthdate)->format('Y-m-d') : '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                required>
                        </div>

                        {{-- Age --}}
                        <div class="col-span-6 md:col-span-1">
                            <label for="age" class="block text-sm font-semibold text-gray-700 mb-1">Age <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="age" name="age" value="{{ $patient->age }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                placeholder="e.g. 35" required>
                        </div>

                        {{-- Sex --}}
                        <div class="col-span-6 md:col-span-3">
                            <label for="sex" class="block text-sm font-semibold text-gray-700 mb-1">Sex <span
                                    class="text-red-500">*</span></label>
                            <select id="sex" name="sex"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="Male" {{ $patient->sex == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $patient->sex == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ $patient->sex == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        {{-- Address (Full row for better visibility) --}}
                        <div class="col-span-6">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                            <input type="text" id="address" name="address" value="{{ $patient->address }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="Street, City, Province/State, Country">
                        </div>

                        {{-- Birth Place --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthplace" class="block text-sm font-semibold text-gray-700 mb-1">Birth
                                Place</label>
                            <input type="text" id="birthplace" name="birthplace" value="{{ $patient->birthplace }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="City/Municipality">
                        </div>

                        {{-- Religion --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="religion" class="block text-sm font-semibold text-gray-700 mb-1">Religion</label>
                            <input type="text" id="religion" name="religion" value="{{ $patient->religion }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>

                        {{-- Ethnicity --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="ethnicity" class="block text-sm font-semibold text-gray-700 mb-1">Ethnicity</label>
                            <input type="text" id="ethnicity" name="ethnicity" value="{{ $patient->ethnicity }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>

                        {{-- Chief Complaints --}}
                        <div class="col-span-6">
                            <label for="chief_complaints" class="block text-sm font-semibold text-gray-700 mb-1">Chief of
                                Complaints</label>
                            <textarea id="chief_complaints" name="chief_complaints" rows="4"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400 resize-none"
                                placeholder="Describe the patient's primary symptoms or issues.">{{ $patient->chief_complaints }}</textarea>
                        </div>

                        {{-- Admission/Location Details --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="admission_date" class="block text-sm font-semibold text-gray-700 mb-1">Admission
                                Date</label>
                            <input type="date" id="admission_date" name="admission_date"
                                value="{{ $patient->admission_date ? $patient->admission_date->format('Y-m-d') : '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                        </div>

                        <div class="col-span-6 md:col-span-2">
                            <label for="room_no" class="block text-sm font-semibold text-gray-700 mb-1">Room No.</label>
                            <input type="text" id="room_no" name="room_no" placeholder="Enter room number"
                                value="{{ $patient->room_no ?? '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>

                        <div class="col-span-6 md:col-span-2">
                            <label for="bed_no" class="block text-sm font-semibold text-gray-700 mb-1">Bed No.</label>
                            <input type="text" id="bed_no" name="bed_no" placeholder="Enter bed number"
                                value="{{ $patient->bed_no ?? '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>
                    </div>
                </div> {{-- End of Patient Info Body --}}

                {{-- 2. Emergency Contact Section --}}
                <div class="bg-white p-6 sm:p-8 pt-0">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-3  border-dark-green mt-4">Emergency Contact
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- Contact Name --}}
                        <div>
                            <label for="contact_name" class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                            <input type="text" id="contact_name" name="contact_name"
                                placeholder="Contact person's full name" value="{{ $patient->contact_name ?? '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>

                        {{-- Contact Relationship --}}
                        <div>
                            <label for="contact_relationship"
                                class="block text-sm font-semibold text-gray-700 mb-1">Relationship</label>
                            <input type="text" id="contact_relationship" name="contact_relationship"
                                placeholder="e.g. Spouse, Parent" value="{{ $patient->contact_relationship ?? '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>

                        {{-- Contact Number --}}
                        <div>
                            <label for="contact_number" class="block text-sm font-semibold text-gray-700 mb-1">Contact
                                Number</label>
                            <input type="text" id="contact_number" name="contact_number" placeholder="e.g. 0912-345-6789"
                                value="{{ $patient->contact_number ?? '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>
                    </div>
                </div> {{-- End of Emergency Contact Section --}}

            </div> {{-- End of Combined Form Card --}}

            {{-- Action Buttons (Outside the card for clear final action) --}}
            <div class="flex justify-end items-center mt-10 space-x-4">

                {{-- Styled "Back" Button (Original Class) --}}
                <button type="button" onclick="window.history.back()" class="button-default">
                    Back
                </button>

                {{-- Styled "Update" Button (Original Class) --}}
                <button type="submit" class="button-default">Update</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/compute-age.js'])
@endpush