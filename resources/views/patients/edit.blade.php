@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')

    {{-- ⚠️ Alerts: Using clean, modern styles --}}
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 ro                                        focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                        placeholder="e.g. 09123456789">
                                    @if($i > 0)
                                    <button type="button" onclick="removeContactRow(this)" class="remove-contact text-red-600 font-bold text-xl pb-2 hover:text-red-800">×</button>
                                    @else
                                    <button type="button" class="remove-contact hidden text-red-600 font-bold text-xl pb-2 hover:text-red-800">×</button>
                                    @endif
                                </div>bg-green-50 dark:bg-green-200 dark:text-green-900" role="alert">
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
    <div class="w-[100%] md:w-[90%] lg:w-[75%] xl:w-[65%] mx-auto my-12">

        <form action="{{ route('patients.update', $patient->patient_id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Main Title --}}
            <h1 class="text-dark-green text-4xl font-extrabold mb-5 pb-1 tracking-tight">
                        EDIT PATIENT RECORD
                </h1>

            {{-- Combined Form Card --}}
            <div class="shadow-2xl rounded-xl overflow-hidden mb-10 border border-gray-100">

                {{-- CARD HEADER: PATIENT DETAILS (Strong Indigo Background) --}}
                <div class="bg-dark-green font-bold py-2 text-white p-4 pl-10 text-xl tracking-wider">
                    <h1>
                        PATIENT DETAILS
                    </h1>
                </div>

                {{-- 1. Patient Information Body (White Background) --}}
                <div class="bg-white p-6 sm:p-8">

                    <div class="grid grid-cols-1 md:grid-cols-6 gap-6 ">

                        {{-- First Name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-1">First Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="{{ $patient->first_name }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="e.g. Juan" required>
                        </div>

                        {{-- Middle Name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="middle_name" class="block text-sm font-semibold text-gray-700 mb-1">Middle
                                Name</label>
                            <input type="text" id="middle_name" name="middle_name" value="{{ $patient->middle_name }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="Optional">
                        </div>

                        {{-- Last Name --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ $patient->last_name }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="e.g. Dela Cruz" required>
                        </div>

                        {{-- Birthdate --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthdate" class="block text-sm font-semibold text-gray-700 mb-1">Birthdate <span
                                    class="text-red-500">*</span></label>
                            <input type="date" id="birthdate" name="birthdate"
                                value="{{ $patient->birthdate ? \Carbon\Carbon::parse($patient->birthdate)->format('Y-m-d') : '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                required>
                        </div>

                        {{-- Age --}}
                        <div class="col-span-6 md:col-span-1">
                            <label for="age" class="block text-sm font-semibold text-gray-700 mb-1">Age <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="age" name="age" value="{{ $patient->age }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                placeholder="e.g. 35" required>
                        </div>

                        {{-- Sex --}}
                        <div class="col-span-6 md:col-span-3">
                            <label for="sex" class="block text-sm font-semibold text-gray-700 mb-1">Sex <span
                                    class="text-red-500">*</span></label>
                            <select id="sex" name="sex"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
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
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="Street, City, Province/State, Country">
                        </div>

                        {{-- Birth Place --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="birthplace" class="block text-sm font-semibold text-gray-700 mb-1">Birth
                                Place</label>
                            <input type="text" id="birthplace" name="birthplace" value="{{ $patient->birthplace }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                placeholder="City/Municipality">
                        </div>

                        {{-- Religion --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="religion" class="block text-sm font-semibold text-gray-700 mb-1">Religion</label>
                            <input type="text" id="religion" name="religion" value="{{ $patient->religion }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>

                        {{-- Ethnicity --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="ethnicity" class="block text-sm font-semibold text-gray-700 mb-1">Ethnicity</label>
                            <input type="text" id="ethnicity" name="ethnicity" value="{{ $patient->ethnicity }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>

                        {{-- Chief Complaints --}}
                        <div class="col-span-6">
                            <label for="chief_complaints" class="block text-sm font-semibold text-gray-700 mb-1">Chief of
                                Complaints</label>
                            <textarea id="chief_complaints" name="chief_complaints" rows="4"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400 resize-none"
                                placeholder="Describe the patient's primary symptoms or issues.">{{ $patient->chief_complaints }}</textarea>
                        </div>

                        {{-- Admission/Location Details --}}
                        <div class="col-span-6 md:col-span-2">
                            <label for="admission_date" class="block text-sm font-semibold text-gray-700 mb-1">Admission
                                Date</label>
                            <input type="date" id="admission_date" name="admission_date"
                                value="{{ $patient->admission_date ? $patient->admission_date->format('Y-m-d') : '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                        </div>

                        <div class="col-span-6 md:col-span-2">
                            <label for="room_no" class="block text-sm font-semibold text-gray-700 mb-1">Room No.</label>
                            <input type="text" id="room_no" name="room_no" placeholder="Enter room number"
                                value="{{ $patient->room_no ?? '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>

                        <div class="col-span-6 md:col-span-2">
                            <label for="bed_no" class="block text-sm font-semibold text-gray-700 mb-1">Bed No.</label>
                            <input type="text" id="bed_no" name="bed_no" placeholder="Enter bed number"
                                value="{{ $patient->bed_no ?? '' }}"
                                class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400">
                        </div>
                    </div>
                </div> {{-- End of Patient Info Body --}}

                </div>

                <div class="shadow-2xl rounded-xl overflow-hidden mb-10 border border-gray-100">
                {{-- 2. Emergency Contact Section --}}

                {{-- CARD HEADER: EMERGENCY CONTACT (Strong Indigo Background) --}}
                <div class="bg-dark-green font-bold py-2 text-white p-4 pl-10 text-xl tracking-wider flex justify-between items-center">
                    <h1>EMERGENCY CONTACTS</h1>
                     <button type="button" onclick="addContactRow()" id="add-contact" class="bg-white text-dark-green px-3 py-1 rounded-md font-bold hover:bg-gray-200">
                        +
                    </button>
                </div>

                {{-- ⚠️ Minor fix: Added missing opening <div> tag here for proper structure and styling --}}
                <div class="bg-white p-6 sm:p-8">
                    <div id="contacts-container">
                        @php
                            $contactNames = is_array($patient->contact_name) ? $patient->contact_name : ($patient->contact_name ? [$patient->contact_name] : []);
                            $contactRelationships = is_array($patient->contact_relationship) ? $patient->contact_relationship : ($patient->contact_relationship ? [$patient->contact_relationship] : []);
                            $contactNumbers = is_array($patient->contact_number) ? $patient->contact_number : ($patient->contact_number ? [$patient->contact_number] : []);
                            $maxContacts = max(count($contactNames), count($contactRelationships), count($contactNumbers), 1);
                        @endphp

                        @for($i = 0; $i < $maxContacts; $i++)
                        <div class="contact-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 pb-4 border-b border-gray-200">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                                <input type="text" name="contact_name[]" value="{{ $contactNames[$i] ?? '' }}"
                                    class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                    placeholder="Contact person's full name">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Relationship</label>
                                <input type="text" name="contact_relationship[]" value="{{ $contactRelationships[$i] ?? '' }}"
                                    class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                    placeholder="e.g. Spouse, Parent">
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Contact Number</label>
                                <div class="flex gap-2">
                                    <input type="text" name="contact_number[]" value="{{ $contactNumbers[$i] ?? '' }}"
                                        class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                                        placeholder="e.g. 09123456789">
                                    @if($i > 0)
                                    <button type="button" onclick="removeContactRow(this)" 
                                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                        ×
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                    
                </div> {{-- End of Emergency Contact Section Body --}}

            </div> 
       {{-- End of Combined Form Card --}}

            {{-- Action Buttons (Outside the card for clear final action) --}}
            <div class="flex justify-end items-center mt-10 space-x-4">

                {{-- Styled "Back" Button (Secondary/Outline Style) --}}
                <button type="button" onclick="window.history.back()" 
                    class="button-default">
                    BACK
                </button>

                {{-- Styled "Update" Button (Primary/Solid Indigo Style) --}}
                <button type="submit" 
                    class="button-default">
                    UPDATE
                </button>
            </div>
            
        </form>
        
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/compute-age.js'])
    <script>
        function addContactRow() {
            const container = document.getElementById('contacts-container');
            
            const newRow = document.createElement('div');
            newRow.className = 'contact-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 pb-4 border-b border-gray-200';
            newRow.innerHTML = `
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                    <input type="text" name="contact_name[]"
                        class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                    focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                        placeholder="Contact person's full name">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Relationship</label>
                    <input type="text" name="contact_relationship[]"
                        class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                    focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                        placeholder="e.g. Spouse, Parent">
                </div>
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Contact Number</label>
                    <div class="flex gap-2">
                        <input type="text" name="contact_number[]"
                            class="w-full text-base px-4 py-2 border border-gray-300 rounded-lg shadow-sm 
                                        focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out placeholder-gray-400"
                            placeholder="e.g. 09123456789">
                        <button type="button" onclick="removeContactRow(this)" class="remove-contact text-red-600 font-bold text-xl pb-2 hover:text-red-800">×</button>
                    </div>
                </div>
            `;
            
            container.appendChild(newRow);
        }
        
        function removeContactRow(button) {
            const container = document.getElementById('contacts-container');
            if (container.children.length > 1) {
                button.closest('.contact-row').remove();
            }
        }
    </script>
@endpush