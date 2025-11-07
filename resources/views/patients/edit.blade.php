@extends('layouts.app')

@section('title', 'Patient Medical Reconciliation')

@section('content')

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
        <form action="{{ route('patients.update', $patient->patient_id) }}" method="POST">
            @csrf
            @method('PUT')

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
            </div>

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
            </div>

             <div class="form-group">
                <label>Admission Date</label>
                <input type="date" name="admission_date" value="{{ $patient->admission_date ? $patient->admission_date->format('Y-m-d') : '' }}">
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

        </form>
    </div>

@endsection

@push('styles')
    @vite(['resources/css/edit-style.css'])
@endpush

@push('scripts')
    @vite(['resources/js/compute-age.js'])
@endpush
=======
{{-- This container and component are from your second file --}}
<div id="form-content-container">
    <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
            selectRoute="{{ route('medreconciliation.select') }}" inputPlaceholder="-Select or type to search-"
            inputName="patient_id" inputValue="{{ session('selected_patient_id') ?? ($selectedPatient->patient_id ?? '') }}" />

    {{-- =================================================================== --}}
    {{-- 2. MAIN CONTENT FORM (From your first file, wrapped in fieldset) --}}
    {{-- =================================================================== --}}
    <form action="{{ route('medreconciliation.store') }}" method="POST">
        @csrf

        {{-- This fieldset is from your second file, wrapping the tables --}}
        {{-- It will be disabled if no patient is in the session --}}
        <fieldset @if (!session('selected_patient_id') && !$selectedPatient) disabled @endif>

            {{-- Hidden input to send the selected patient's ID with the POST request --}}
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

            {{-- TABLE 1: Patient's Current Medication --}}
            <center>
                <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                    <tr>
                        <th colspan="6" class="bg-dark-green text-white rounded-t-lg">Patient's Current Medication (Upon Admission)</th>
                    </tr>
                    <tr>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Medication</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Dose</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Route</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Frequency</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Indication</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Administered During Stay?</th>
                    </tr>
                    <tr class="bg-beige">
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="current_med" placeholder="Type here...">{{ $currentMedication->current_med ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="current_dose" placeholder="Type here...">{{ $currentMedication->current_dose ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="current_route" placeholder="Type here...">{{ $currentMedication->current_route ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="current_frequency" placeholder="Type here...">{{ $currentMedication->current_frequency ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="current_indication" placeholder="Type here...">{{ $currentMedication->current_indication ?? '' }}</textarea>
                        </td>
                        <td>
                            <textarea class="notepad-lines h-[200px]" name="current_text" placeholder="Type here...">{{ $currentMedication->current_text ?? '' }}</textarea>
                        </td>
                    </tr>
                </table>
            </center>
            
            {{-- TABLE 2: Patient's Home Medication --}}
            <center>
                <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                    <tr>
                        <th colspan="6" class="bg-dark-green text-white rounded-t-lg">Patient's Home Medication (If Any)</th>
                    </tr>
                    <tr>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Medication</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Dose</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Route</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Frequency</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Indication</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Discontinued on Admission?</th>
                    </tr>
                    <tr class="bg-beige">
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="home_med" placeholder="Type here...">{{ $homeMedication->home_med ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="home_dose" placeholder="Type here...">{{ $homeMedication->home_dose ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="home_route" placeholder="Type here...">{{ $homeMedication->home_route ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="home_frequency" placeholder="Type here...">{{ $homeMedication->home_frequency ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="home_indication" placeholder="Type here...">{{ $homeMedication->home_indication ?? '' }}</textarea>
                        </td>
                        <td>
                            <textarea class="notepad-lines h-[200px]" name="home_text" placeholder="Type here...">{{ $homeMedication->home_text ?? '' }}</textarea>
                        </td>
                    </tr>
                </table>
            </center>
            
            {{-- TABLE 3: Changes in Medication --}}
            <center>
                <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                    {{-- Note: colspan is 5 here --}}
                    <tr>
                        <th colspan="5" class="bg-dark-green text-white rounded-t-lg">Changes in Medication During Hospitalization</th>
                    </tr>
                    <tr>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Medication</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Dose</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Route</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">Frequency</th>
                        <th class="bg-yellow-light text-brown text-[13px] border-line-brown">Reason for Change</th>
                    </tr>
                    <tr class="bg-beige">
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="change_med" placeholder="Type here...">{{ $changesInMedication->change_med ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="change_dose" placeholder="Type here...">{{ $changesInMedication->change_dose ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="change_route" placeholder="Type here...">{{ $changesInMedication->change_route ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="change_frequency" placeholder="Type here...">{{ $changesInMedication->change_frequency ?? '' }}</textarea>
                        </td>
                        <td>
                            <textarea class="notepad-lines h-[200px]" name="change_text" placeholder="Type here...">{{ $changesInMedication->change_text ?? '' }}</textarea>
                        </td>
                    </tr>
                </table>
            </center>
            
            {{-- SUBMIT BUTTON --}}
            <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30">
                <button type="submit" class="button-default">Submit</button>
            </div>

        </fieldset>
    </form>

</div> {{-- End of #form-content-container --}}

@endsection

@push('scripts')
    {{-- These scripts are required for the searchable dropdown component --}}
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush
>>>>>>> 0344ddb459dbeb5be996aa258591c4f865de915a
