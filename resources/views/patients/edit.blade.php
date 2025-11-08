@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')

<<<<<<< HEAD
<<<<<<< HEAD
<body>
    <div class="header">
        EDIT PATIENT
    </div>

    <div class="form-container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
=======
    {{-- This container centers the content and matches the width of the registration page --}}
    <div class="w-[72%] mx-auto my-10">
        
>>>>>>> 5d3b16a7a29d5a9df57baa135128fa9ed225379e
=======
    {{-- This container centers the content and matches the width of the registration page --}}
    <div class="w-[72%] mx-auto my-10">
        
>>>>>>> 237d7e389833dc81f9f2c113fcef077115a301e6
        <form action="{{ route('patients.update', $patient->patient_id) }}" method="POST">
            @csrf
            @method('PUT')

<<<<<<< HEAD
<<<<<<< HEAD
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="first_name" value="{{ $patient->first_name }}" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="last_name" value="{{ $patient->last_name }}" required>
            </div>

            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" value="{{ $patient->middle_name }}">
            </div>

            <div class="form-group">
                <label>Birthdate</label>
                <input type="date" name="birthdate" id="birthdate" value="{{ $patient->birthdate }}">
            </div>

            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" value="{{ $patient->age }}" id="age" readonly required>
=======
=======
>>>>>>> 237d7e389833dc81f9f2c113fcef077115a301e6
            {{-- Styled Header for Edit Patient --}}
            <div class="bg-dark-green text-white rounded-t-lg font-bold text-lg p-4 w-full">
                EDIT PATIENT
            </div>

            {{-- Form content area with beige background, padding, and shadow --}}
            <div class="bg-beige p-6 rounded-b-lg shadow-md mb-8">
                
                {{-- A responsive grid for layout. 6 columns on medium screens, 1 on small screens --}}
                <div class="grid grid-cols-6 gap-6">

                    {{-- Name --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="name" class="block mb-2 font-bold text-dark-green">Name</label>
                        <input type="text" id="name" name="name" value="{{ $patient->name }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" required>
                    </div>

                    {{-- Age --}}
                    <div class="col-span-6 md:col-span-1">
                        <label for="age" class="block mb-2 font-bold text-dark-green">Age</label>
                        <input type="number" id="age" name="age" value="{{ $patient->age }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" required>
                    </div>

                    {{-- Sex --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="sex" class="block mb-2 font-bold text-dark-green">Sex</label>
                        <select id="sex" name="sex"
                                class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" required>
                            <option value="Male" {{ $patient->sex == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $patient->sex == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ $patient->sex == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    {{-- Address --}}
                    <div class="col-span-6 md:col-span-4">
                        <label for="address" class="block mb-2 font-bold text-dark-green">Address</label>
                        <input type="text" id="address" name="address" value="{{ $patient->address }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Birth Place --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="birthplace" class="block mb-2 font-bold text-dark-green">Birth Place</label>
                        <input type="text" id="birthplace" name="birthplace" value="{{ $patient->birthplace }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Religion --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="religion" class="block mb-2 font-bold text-dark-green">Religion</label>
                        <input type="text" id="religion" name="religion" value="{{ $patient->religion }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Ethnicity --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="ethnicity" class="block mb-2 font-bold text-dark-green">Ethnicity</label>
                        <input type="text" id="ethnicity" name="ethnicity" value="{{ $patient->ethnicity }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Chief Complaints --}}
                    <div class="col-span-6">
                        <label for="chief_complaints" class="block mb-2 font-bold text-dark-green">Chief of Complaints</label>
                        <textarea id="chief_complaints" name="chief_complaints" rows="3"
                                  class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm notepad-lines h-24">{{ $patient->chief_complaints }}</textarea>
                    </div>

                    {{-- Admission Date --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="admission_date" class="block mb-2 font-bold text-dark-green">Admission Date</label>
                        <input type="date" id="admission_date" name="admission_date" value="{{ $patient->admission_date }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Room No. (Added from registration form) --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="room_no" class="block mb-2 font-bold text-dark-green">Room No.</label>
                        <input type="text" id="room_no" name="room_no" placeholder="Enter room number" value="{{ $patient->room_no ?? '' }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Bed No. (Added from registration form) --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="bed_no" class="block mb-2 font-bold text-dark-green">Bed No.</label>
                        <input type="text" id="bed_no" name="bed_no" placeholder="Enter bed number" value="{{ $patient->bed_no ?? '' }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>
                </div>
<<<<<<< HEAD
>>>>>>> 5d3b16a7a29d5a9df57baa135128fa9ed225379e
=======
>>>>>>> 237d7e389833dc81f9f2c113fcef077115a301e6
            </div>

            {{-- Styled Header for Emergency Contact (Added from registration form) --}}
            <div class="bg-dark-green text-white rounded-t-lg font-bold text-lg p-4 w-full mt-8">
                EMERGENCY CONTACT
            </div>

            {{-- Content area for Emergency Contact (Added from registration form) --}}
            <div class="bg-beige p-6 rounded-b-lg shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Contact Name --}}
                    <div class="col-span-1">
                        <label for="contact_name" class="block mb-2 font-bold text-dark-green">Name</label>
                        <input type="text" id="contact_name" name="contact_name" placeholder="Enter name" value="{{ $patient->contact_name ?? '' }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Contact Relationship --}}
                    <div class="col-span-1">
                        <label for="contact_relationship" class="block mb-2 font-bold text-dark-green">Relationship</label>
                        <input type="text" id="contact_relationship" name="contact_relationship" placeholder="Enter relationship to patient" value="{{ $patient->contact_relationship ?? '' }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Contact Number --}}
                    <div class="col-span-1">
                        <label for="contact_number" class="block mb-2 font-bold text-dark-green">Contact Number</label>
                        <input type="text" id="contact__number" name="contact_number" placeholder="Enter number" value="{{ $patient->contact_number ?? '' }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end items-center mt-8 space-x-4">
                {{-- Styled "Back" Button --}}
                <button type="button" onclick="window.history.back()" 
                        class="button-default">
                    Back
                </button>
                
                {{-- Styled "Update" Button --}}
                <button type="submit" class="button-default">Update</button>
            </div>

<<<<<<< HEAD
<<<<<<< HEAD
            <div class="form-group">
                <label>Religion</label>
                <input type="text" name="religion" value="{{ $patient->religion }}">
            </div>

            <div class="form-group">
                <label>Ethnicity</label>
                <input type="text" name="ethnicity" value="{{ $patient->ethnicity }}">
            </div>

             <div class="form-group">
                <label>Admission Date</label>
                <input type="date" name="admission_date" value="{{ $patient->admission_date ? $patient->admission_date->format('Y-m-d') : '' }}">
            </div>

            <div class="form-group full-width">
                <label>Chief Complaints</label>
                <textarea name="chief_complaints">{{ $patient->chief_complaints }}</textarea>
            </div>

             <div class="btnn">
            <button type="button" class="btn-submit" onclick="window.history.back()">Back</button>
             </div>
             
            <div class="btnn">
                <button type="submit" class="btn-submit">Update</button>
            </div>
=======
>>>>>>> 5d3b16a7a29d5a9df57baa135128fa9ed225379e
=======
>>>>>>> 237d7e389833dc81f9f2c113fcef077115a301e6
        </form>
    </div>

@endsection

<<<<<<< HEAD
<<<<<<< HEAD
@push('styles')
    @vite(['resources/css/edit-style.css'])
@endpush

@push('scripts')
    @vite(['resources/js/compute-age.js'])
@endpush
=======
{{-- Removed the push for 'edit-style.css' --}}
>>>>>>> 5d3b16a7a29d5a9df57baa135128fa9ed225379e
=======
{{-- Removed the push for 'edit-style.css' --}}
>>>>>>> 237d7e389833dc81f9f2c113fcef077115a301e6
