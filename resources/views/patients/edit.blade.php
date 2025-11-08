@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')



    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- This container centers the content and matches the width of the registration page --}}
    <div class="w-[72%] mx-auto my-10">

        <form action="{{ route('patients.update', $patient->patient_id) }}" method="POST">
            @csrf
            @method('PUT')


            <div class="flex justify-between items-center mb-4">
                <h2 class="text-3xl font-creato-black font-black text-dark-green">EDIT PATIENT</h2>
            </div>

            {{-- Form content area with beige background, padding, and shadow --}}
            <div class="bg-beige p-6 rounded-lg shadow-md mb-8">

                {{-- A responsive grid for layout. 6 columns on medium screens, 1 on small screens --}}
                <div class="grid grid-cols-6 gap-6">

                    {{-- First Name --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="first_name" class="block mb-2 font-creato-black text-black font-bold">First Name</label> <input type="text"
                            id="first_name" name="first_name" value="{{ $patient->first_name }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                            required>
                    </div>

                    {{-- Middle Name --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="middle_name" class="block mb-2 font-creato-black text-black font-bold">Middle Name</label> <input
                            type="text" id="middle_name" name="middle_name" value="{{ $patient->middle_name }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Last Name --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="last_name" class="block mb-2 font-creato-black text-black font-bold">Last Name</label> <input
                            type="text" id="last_name" name="last_name" value="{{ $patient->last_name }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                            required>
                    </div>

                    {{-- Birthdate --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="birthdate" class="block mb-2 font-creato-black text-black font-bold">Birthdate</label> <input
                            type="date" id="birthdate" name="birthdate"
                            value="{{ $patient->birthdate ? \Carbon\Carbon::parse($patient->birthdate)->format('Y-m-d') : '' }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                            required>
                    </div>

                    {{-- Age --}}
                    <div class="col-span-6 md:col-span-1">
                        <label for="age" class="block mb-2 font-creato-black text-black font-bold">Age</label> <input type="number"
                            id="age" name="age" value="{{ $patient->age }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                            required>
                    </div>

                    {{-- Sex --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="sex" class="block mb-2 font-creato-black text-black font-bold">Sex</label> <select id="sex"
                            name="sex"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                            required>
                            <option value="Male" {{ $patient->sex == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $patient->sex == 'Female' ? 'selected' : '' }}>Female
                            </option>
                            <option value="Other" {{ $patient->sex == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    {{-- Address --}}
                    <div class="col-span-6 md:col-span-4">
                        <label for="address" class="block mb-2 font-creato-black text-black font-bold">Address</label> <input
                            type="text" id="address" name="address" value="{{ $patient->address }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Birth Place --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="birthplace" class="block mb-2 font-creato-black text-black font-bold">Birth Place</label> <input
                            type="text" id="birthplace" name="birthplace" value="{{ $patient->birthplace }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Religion --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="religion" class="block mb-2 font-creato-black text-black font-bold">Religion</label> <input
                            type="text" id="religion" name="religion" value="{{ $patient->religion }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Ethnicity --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="ethnicity" class="block mb-2 font-creato-black text-black font-bold">Ethnicity</label> <input
                            type="text" id="ethnicity" name="ethnicity" value="{{ $patient->ethnicity }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Chief Complaints --}}
                    <div class="col-span-6">
                        <label for="chief_complaints" class="block mb-2 font-creato-black text-black font-bold">Chief of
                            Complaints</label>
                        <textarea id="chief_complaints" name="chief_complaints" rows="3"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm notepad-lines h-24">{{ $patient->chief_complaints }}</textarea>
                    </div>

                    {{-- Admission Date --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="admission_date" class="block mb-2 font-creato-black text-black font-bold">Admission
                            Date</label>
                        <input type="date" id="admission_date" name="admission_date"
                            value="{{ $patient->admission_date ? $patient->admission_date->format('Y-m-d') : '' }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Room No. (Added from registration form) --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="room_no" class="block mb-2 font-creato-black text-black font-bold">Room No.</label>
                        <input type="text" id="room_no" name="room_no" placeholder="Enter room number"
                            value="{{ $patient->room_no ?? '' }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Bed No. (Added from registration form) --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="bed_no" class="block mb-2 font-creato-black text-black font-bold">Bed No.</label>
                        <input type="text" id="bed_no" name="bed_no" placeholder="Enter bed number"
                            value="{{ $patient->bed_no ?? '' }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>
                </div>

            </div>

            <div class="flex justify-between items-center mb-4 mt-8">
                <h2 class="text-3xl font-creato-black font-black text-dark-green">EMERGENCY CONTACT</h2>
            </div>

            {{-- Content area for Emergency Contact (Added from registration form) --}}
            <div class="bg-beige p-6 rounded-b-lg shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Contact Name --}}
                    <div class="col-span-1">
                        <label for="contact_name" class="block mb-2 font-creato-black text-black font-bold">Name</label>
                        <input type="text" id="contact_name" name="contact_name" placeholder="Enter name"
                            value="{{ $patient->contact_name ?? '' }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Contact Relationship --}}
                    <div class="col-span-1">
                        <label for="contact_relationship"
                            class="block mb-2 font-creato-black text-black font-bold">Relationship</label>
                        <input type="text" id="contact_relationship" name="contact_relationship"
                            placeholder="Enter relationship to patient" value="{{ $patient->contact_relationship ?? '' }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Contact Number --}}
                    <div class="col-span-1">
                        <label for="contact_number" class="block mb-2 font-creato-black text-black font-bold">Contact
                            Number</label>
                        <input type="text" id="contact__number" name="contact_number" placeholder="Enter number"
                            value="{{ $patient->contact_number ?? '' }}"
                            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-lg border border-line-brown focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end items-center mt-8 space-x-4">
                {{-- Styled "Back" Button --}}
                <button type="button" onclick="window.history.back()" class="button-default">
                    Back
                </button>

                {{-- Styled "Update" Button --}}
                <button type="submit" class="button-default">Update</button>
            </div>


        </form>
@endsection



    @push('scripts')
        @vite(['resources/js/compute-age.js'])
    @endpush