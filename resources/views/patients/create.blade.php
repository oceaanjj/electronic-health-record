@extends('layouts.app')

@section('title', 'Register Patient')

@section('content')

    {{-- This container centers the content and matches the width of the medical-history page --}}
    <div class="w-[72%] mx-auto my-10">
        
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

                    {{-- Name --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="name" class="block mb-2 font-bold text-dark-green">Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter patient name" 
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Age --}}
                    <div class="col-span-6 md:col-span-1">
                        <label for="age" class="block mb-2 font-bold text-dark-green">Age</label>
                        <input type="number" id="age" name="age" placeholder="Enter age"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Sex --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="sex" class="block mb-2 font-bold text-dark-green">Sex</label>
                        <select id="sex" name="sex"
                                class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                            <option>Select sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    {{-- Address --}}
                    <div class="col-span-6 md:col-span-4">
                        <label for="address" class="block mb-2 font-bold text-dark-green">Address</label>
                        <input type="text" id="address" name="address" placeholder="Enter complete address"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Birth Place --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="birthplace" class="block mb-2 font-bold text-dark-green">Birth Place</label>
                        <input type="text" id="birthplace" name="birthplace" placeholder="Enter birth place"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Religion --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="religion" class="block mb-2 font-bold text-dark-green">Religion</label>
                        <input type="text" id="religion" name="religion" placeholder="Enter religion"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Ethnicity --}}
                    <div class="col-span-6 md:col-span-3">
                        <label for="ethnicity" class="block mb-2 font-bold text-dark-green">Ethnicity</label>
                        <input type="text" id="ethnicity" name="ethnicity" placeholder="Enter ethnicity"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Chief Complaints --}}
                    <div class="col-span-6">
                        <label for="chief_complaints" class="block mb-2 font-bold text-dark-green">Chief of Complaints</label>
                        <textarea id="chief_complaints" name="chief_complaints" rows="3" placeholder="Enter chief complaints"
                                  class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm notepad-lines h-24"></textarea>
                    </div>

                    {{-- Admission Date --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="admission_date" class="block mb-2 font-bold text-dark-green">Admission Date</label>
                        <input type="date" id="admission_date" name="admission_date"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Room No. --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="room_no" class="block mb-2 font-bold text-dark-green">Room No.</label>
                        <input type="text" id="room_no" name="room_no" placeholder="Enter room number"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Bed No. --}}
                    <div class="col-span-6 md:col-span-2">
                        <label for="bed_no" class="block mb-2 font-bold text-dark-green">Bed No.</label>
                        <input type="text" id="bed_no" name="bed_no" placeholder="Enter bed number"
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
                        <input type="text" id="contact_name" name="contact_name" placeholder="Enter name"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Contact Relationship (Fixed from "Age") --}}
                    <div class="col-span-1">
                        <label for="contact_relationship" class="block mb-2 font-bold text-dark-green">Relationship</label>
                        <input type="text" id="contact_relationship" name="contact_relationship" placeholder="Enter relationship to patient"
                               class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    {{-- Contact Number --}}
                    <div class="col-span-1">
                        <label for="contact_number" class="block mb-2 font-bold text-dark-green">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" placeholder="Enter number"
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

{{-- Removed the push for 'registration-style.css' as styles are now handled by Tailwind --}}
{{-- If 'button-default' or 'notepad-lines' are defined in a global CSS file, they will still be applied --}}