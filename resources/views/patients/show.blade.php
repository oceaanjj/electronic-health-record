@extends('layouts.app')

@section('title', 'Patient Details')

{{-- Removed the @push('styles') as styles are now inlined with Tailwind --}}

@section('content')
    <div class="w-[72%] mx-auto my-10">

        {{-- Styled Header for Patient Details --}}
        <div class="bg-dark-green text-white rounded-t-lg font-bold text-lg p-4 w-full">
            PATIENT NAME: {{ $patient->name }}
        </div>

        {{-- Form content area with beige background, padding, and shadow --}}
        <div class="bg-beige p-6 rounded-b-lg shadow-md mb-8">
            
            {{-- A responsive grid for layout. 6 columns on medium screens, 1 on small screens --}}
            <div class="grid grid-cols-6 gap-6">

                {{-- Name --}}
                <div class="col-span-6 md:col-span-3">
                    <label class="block mb-1 font-bold text-dark-green">Name</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->name }}</p>
                </div>

                {{-- Age --}}
                <div class="col-span-6 md:col-span-1">
                    <label class="block mb-1 font-bold text-dark-green">Age</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->age }}</p>
                </div>

                {{-- Sex --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="block mb-1 font-bold text-dark-green">Sex</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->sex }}</p>
                </div>

                {{-- Address --}}
                <div class="col-span-6 md:col-span-4">
                    <label class="block mb-1 font-bold text-dark-green">Address</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->address }}</p>
                </div>

                {{-- Birth Place --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="block mb-1 font-bold text-dark-green">Birth Place</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->birthplace }}</p>
                </div>

                {{-- Religion --}}
                <div class="col-span-6 md:col-span-3">
                    <label class="block mb-1 font-bold text-dark-green">Religion</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->religion }}</p>
                </div>

                {{-- Ethnicity --}}
                <div class="col-span-6 md:col-span-3">
                    <label class="block mb-1 font-bold text-dark-green">Ethnicity</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->ethnicity }}</p>
                </div>

                {{-- Chief Complaints --}}
                <div class="col-span-6">
                    <label class="block mb-1 font-bold text-dark-green">Chief of Complaints</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[80px]">{{ $patient->chief_complaints }}</p>
                </div>

                {{-- Admission Date --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="block mb-1 font-bold text-dark-green">Admission Date</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->admission_date }}</p>
                </div>

                {{-- Room No. --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="block mb-1 font-bold text-dark-green">Room No.</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->room_no ?? 'N/A' }}</p>
                </div>

                {{-- Bed No. --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="block mb-1 font-bold text-dark-green">Bed No.</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->bed_no ?? 'N/A' }}</p>
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
                    <label class="block mb-1 font-bold text-dark-green">Name</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->contact_name ?? 'N/A' }}</p>
                </div>

                {{-- Contact Relationship --}}
                <div class="col-span-1">
                    <label class="block mb-1 font-bold text-dark-green">Relationship</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->contact_relationship ?? 'N/A' }}</p>
                </div>

                {{-- Contact Number --}}
                <div class="col-span-1">
                    <label class="block mb-1 font-bold text-dark-green">Contact Number</label>
                    <p class="w-full text-[15px] px-4 py-2 rounded-lg border border-gray-300 bg-white shadow-sm min-h-[42px]">{{ $patient->contact_number ?? 'N/A' }}</p>
                </div>

            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end items-center mt-8 space-x-4">
            {{-- Styled "Back" Button --}}
            <a href="{{ route('patients.index') }}" 
                    class="button-default text-center">
                Back
            </a>
        </div>

    </div>
@endsection