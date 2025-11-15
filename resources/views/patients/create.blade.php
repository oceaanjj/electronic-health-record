@extends('layouts.app')

@section('title', 'Register Patient')

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

    {{-- Main Container --}}
    <div class="w-[100%] md:w-[90%] lg:w-[75%] xl:w-[65%] mx-auto my-12">

        <form action="{{ route('patients.store') }}" method="POST">
            @csrf

            {{-- Main Title --}}
            <h1 class="text-dark-green text-4xl font-extrabold mb-5 pb-1 tracking-tight">
                REGISTER PATIENT
            </h1>

            {{-- Combined Form Card --}}
            <div class="shadow-2xl rounded-xl overflow-hidden mb-10 border border-gray-100">

                {{-- CARD HEADER --}}
                <div class="bg-dark-green font-bold py-2 text-white p-4 pl-10 text-xl tracking-wider">
                    <h1>PATIENT DETAILS</h1>
                </div>

                {{-- Patient Info --}}
                <div class="bg-white p-6 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-6">

                        {{-- First Name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-1">First Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400" placeholder="e.g. Juan" required>
                        </div>

                        {{-- Middle Name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="middle_name" class="block text-sm font-semibold text-gray-700 mb-1">Middle
                                Name</label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400" placeholder="Optional">
                        </div>

                        {{-- Last Name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400" placeholder="e.g. Dela Cruz" required>
                        </div>

                        {{-- Birthdate --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthdate" class="block text-sm font-semibold text-gray-700 mb-1">Birthdate <span
                                    class="text-red-500">*</span></label>
                            <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" required>
                        </div>

                        {{-- Age --}}
                        <div class="col-span-6 md:col-span-1">
                            <label for="age" class="block text-sm font-semibold text-gray-700 mb-1">Age <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="age" name="age" value="{{ old('age') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            bg-gray-100 cursor-not-allowed focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" placeholder="Age" readonly
                                required>
                        </div>

                        {{-- Sex --}}
                        <div class="col-span-6 md:col-span-3">
                            <label for="sex" class="block text-sm font-semibold text-gray-700 mb-1">Sex <span
                                    class="text-red-500">*</span></label>
                            <select id="sex" name="sex" required
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                                <option value="" disabled selected>Select Sex</option>
                                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                            </select>
                        </div>

                        {{-- Address --}}
                        <div class="col-span-6">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="Street, City, Province/State, Country">
                        </div>

                        {{-- Birth Place --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthplace" class="block text-sm font-semibold text-gray-700 mb-1">Birth
                                Place</label>
                            <input type="text" id="birthplace" name="birthplace" value="{{ old('birthplace') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400" placeholder="City/Municipality">
                        </div>

                        {{-- Religion --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="religion" class="block text-sm font-semibold text-gray-700 mb-1">Religion</label>
                            <input type="text" id="religion" name="religion" value="{{ old('religion') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400" placeholder="Enter religion">
                        </div>

                        {{-- Ethnicity --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="ethnicity" class="block text-sm font-semibold text-gray-700 mb-1">Ethnicity</label>
                            <input type="text" id="ethnicity" name="ethnicity" value="{{ old('ethnicity') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400" placeholder="Enter ethnicity">
                        </div>

                        {{-- Chief Complaints --}}
                        <div class="col-span-6">
                            <label for="chief_complaints" class="block text-sm font-semibold text-gray-700 mb-1">Chief of
                                Complaints</label>
                            <textarea id="chief_complaints" name="chief_complaints" rows="4"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400 resize-none"
                                placeholder="Describe the patient's primary symptoms or issues.">{{ old('chief_complaints') }}</textarea>
                        </div>

                        {{-- Admission Info --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="admission_date" class="block text-sm font-semibold text-gray-700 mb-1">Admission
                                Date</label>
                            <input type="date" id="admission_date" name="admission_date" value="{{ $currentDate }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm  bg-gray-100 cursor-not-allowed
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out" readonly>
                        </div>

                        <div class="col-span-6 md:col-span-2">
                            <label for="room_no" class="block text-sm font-semibold text-gray-700 mb-1">Room No.</label>
                            <input type="text" id="room_no" name="room_no" value="{{ old('room_no') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400" placeholder="Enter room number">
                        </div>

                        <div class="col-span-6 md:col-span-2">
                            <label for="bed_no" class="block text-sm font-semibold text-gray-700 mb-1">Bed No.</label>
                            <input type="text" id="bed_no" name="bed_no" value="{{ old('bed_no') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400" placeholder="Enter bed number">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Emergency Contact --}}
            <div class="shadow-2xl rounded-xl overflow-hidden mb-10 border border-gray-100">
                <div class="bg-dark-green font-bold py-2 text-white p-4 pl-10 text-xl tracking-wider">
                    <h1>EMERGENCY CONTACT</h1>
                </div>

                <div class="bg-white p-6 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="contact_name" class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                            <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="Contact person's full name">
                        </div>
                        <div>
                            <label for="contact_relationship"
                                class="block text-sm font-semibold text-gray-700 mb-1">Relationship</label>
                            <input type="text" id="contact_relationship" name="contact_relationship"
                                value="{{ old('contact_relationship') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="e.g. Spouse, Parent">
                        </div>
                        <div>
                            <label for="contact_number" class="block text-sm font-semibold text-gray-700 mb-1">Contact
                                Number</label>
                            <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                            focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="e.g. 0912-345-6789">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end items-center mt-10 space-x-4">
                <button type="button" onclick="window.history.back()" class="button-default">
                    BACK
                </button>
                <button type="submit" class="button-default">
                    SAVE
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/compute-age.js'])
@endpush