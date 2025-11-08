@extends('layouts.app')

@section('title', 'Register Patient')

@section('content')

    {{-- This container centers the content and matches the width of the medical-history page --}}
    <div class="w-[72%] mx-auto my-10">
        
        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- ERROR VALIDATION --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">Please correct the errors below.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('patients.store') }}" method="POST">
            @csrf

            {{-- Styled Header for Patient Registration --}}
            <div class="bg-dark-green text-white rounded-t-lg font-bold text-lg p-4 w-full">
                PATIENT REGISTRATION
            </div>

            {{-- Form content area with beige background, padding, and shadow --}}
            <div class="bg-beige p-6 rounded-b-lg shadow-md mb-8">
                
                {{-- A responsive grid for layout. 6 columns on medium screens, 1 on small screens --}}
                <div class="grid grid-cols-6 gap-6">

                    {{-- First Name --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="first_name" class="block mb-2 font-bold text-dark-green">First Name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter first name" value="{{ old('first_name') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Middle Name --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="middle_name" class="block mb-2 font-bold text-dark-green">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" placeholder="Enter middle name" value="{{ old('middle_name') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Last Name --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="last_name" class="block mb-2 font-bold text-dark-green">Last Name</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter last name" value="{{ old('last_name') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Birthdate (controls age) --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="birthdate" class="block mb-2 font-bold text-dark-green">Birthdate</label>
                        <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Age (readonly, controlled by birthdate) --}}
                    <div class="col-span-6 md:col-span-1">
                        <label for="age" class="block mb-2 font-bold text-dark-green">Age</label>
                        <input type="number" id="age" name="age" placeholder="Age" value="{{ old('age') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm" readonly>
                    </div>

                    {{-- Sex --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="sex" class="block mb-2 font-bold text-dark-green">Sex</label>
                        <select id="sex" name="sex"
                                class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                            <option>Select sex</option>
                            <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    {{-- Address --}}
                    <div class="col-span-6 md:col-span-4">
                        <label for="address" class="block mb-2 font-bold text-dark-green">Address</label>
                        <input type="text" id="address" name="address" placeholder="Enter complete address" value="{{ old('address') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Birth Place --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="birthplace" class="block mb-2 font-bold text-dark-green">Birth Place</label>
                        <input type="text" id="birthplace" name="birthplace" placeholder="Enter birth place" value="{{ old('birthplace') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Religion --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="religion" class="block mb-2 font-bold text-dark-green">Religion</label>
                        <input type="text" id="religion" name="religion" placeholder="Enter religion" value="{{ old('religion') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Ethnicity --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="ethnicity" class="block mb-2 font-bold text-dark-green">Ethnicity</label>
                        <input type="text" id="ethnicity" name="ethnicity" placeholder="Enter ethnicity" value="{{ old('ethnicity') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Chief Complaints --}}
                    <div class="col-span-6">
                        <label for="chief_complaints" class="block mb-2 font-bold text-dark-green">Chief of Complaints</label>
                        <textarea id="chief_complaints" name="chief_complaints" rows="3" placeholder="Enter chief complaints"
                                  class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm notepad-lines h-24">{{ old('chief_complaints') }}</textarea>
                    </div>

                    {{-- Admission Date --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="admission_date" class="block mb-2 font-bold text-dark-green">Admission Date</label>
                        <input type="date" id="admission_date" name="admission_date" value="{{ old('admission_date') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Room No. --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="room_no" class="block mb-2 font-bold text-dark-green">Room No.</label>
                        <input type="text" id="room_no" name="room_no" placeholder="Enter room number" value="{{ old('room_no') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Bed No. --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="bed_no" class="block mb-2 font-bold text-dark-green">Bed No.</label>
                        <input type="text" id="bed_no" name="bed_no" placeholder="Enter bed number" value="{{ old('bed_no') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Styled Header for Emergency Contact --}}
            <div class="bg-dark-green text-white rounded-t-lg font-bold text-lg p-4 w-full mt-8">
                EMERGENCY CONTACT
            </div>

            {{-- Content area for Emergency Contact --}}
            <div class="bg-beige p-6 rounded-b-lg shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Contact Name --}}
                    <div class="col-span-1">
                        <label for="contact_name" class="block mb-2 font-bold text-dark-green">Name</label>
                        <input type="text" id="contact_name" name="contact_name" placeholder="Enter name" value="{{ old('contact_name') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Contact Relationship --}}
                    <div class="col-span-1">
                        <label for="contact_relationship" class="block mb-2 font-bold text-dark-green">Relationship</label>
                        <input type="text" id="contact_relationship" name="contact_relationship" placeholder="Enter relationship to patient" value="{{ old('contact_relationship') }}"
                               class="w-full text-[1sem 0 0 0] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

       
                    <div class="col-span-1">
                        <label for="contact_number" class="block mb-2 font-bold text-dark-green">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" placeholder="Enter number" value="{{ old('contact_number') }}"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                </div>
            </div>

            {{-- Save Button --}}
            <div class="flex justify-end mt-8">
                <button type="submit" class="button-default">Save</button>
            </div>

        </form>
    </div>

@endsection

        @push('styles')
            @vite(['resources/css/registration-style.css'])
        @endpush

        @push('scripts')
            @vite(['resources/js/compute-age.js'])
        @endpush
