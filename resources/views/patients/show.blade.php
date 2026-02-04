@extends('layouts.app')

@section('title', 'Patient Details')

{{-- Removed the @push('styles') as styles are now inlined with Tailwind --}}

@section('content')
    <div class="mx-auto my-10 w-[72%]">
        {{-- Styled Header for Patient Details --}}
        <div class="bg-dark-green w-full rounded-t-lg p-4 text-lg font-bold text-white">
            PATIENT NAME: {{ $patient->name }}
        </div>

        {{-- Form content area with beige background, padding, and shadow --}}
        <div class="bg-beige mb-8 rounded-b-lg p-6 shadow-md">
            {{-- A responsive grid for layout. 6 columns on medium screens, 1 on small screens --}}
            <div class="grid grid-cols-6 gap-6">
                {{-- Name --}}
                <div class="col-span-6 md:col-span-3">
                    <label class="text-dark-green mb-1 block font-bold">Name</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->name }}
                    </p>
                </div>

                {{-- Age --}}
                <div class="col-span-6 md:col-span-1">
                    <label class="text-dark-green mb-1 block font-bold">Age</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->age }}
                    </p>
                </div>

                {{-- Sex --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="text-dark-green mb-1 block font-bold">Sex</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->sex }}
                    </p>
                </div>

                {{-- Address --}}
                <div class="col-span-6 md:col-span-4">
                    <label class="text-dark-green mb-1 block font-bold">Address</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->address }}
                    </p>
                </div>

                {{-- Birth Place --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="text-dark-green mb-1 block font-bold">Birth Place</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->birthplace }}
                    </p>
                </div>

                {{-- Religion --}}
                <div class="col-span-6 md:col-span-3">
                    <label class="text-dark-green mb-1 block font-bold">Religion</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->religion }}
                    </p>
                </div>

                {{-- Ethnicity --}}
                <div class="col-span-6 md:col-span-3">
                    <label class="text-dark-green mb-1 block font-bold">Ethnicity</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->ethnicity }}
                    </p>
                </div>

                {{-- Chief Complaints --}}
                <div class="col-span-6">
                    <label class="text-dark-green mb-1 block font-bold">Chief of Complaints</label>
                    <p
                        class="min-h-[80px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->chief_complaints }}
                    </p>
                </div>

                {{-- Admission Date --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="text-dark-green mb-1 block font-bold">Admission Date</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->admission_date }}
                    </p>
                </div>

                {{-- Room No. --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="text-dark-green mb-1 block font-bold">Room No.</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->room_no ?? 'N/A' }}
                    </p>
                </div>

                {{-- Bed No. --}}
                <div class="col-span-6 md:col-span-2">
                    <label class="text-dark-green mb-1 block font-bold">Bed No.</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->bed_no ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Styled Header for Emergency Contact --}}
        <div class="bg-dark-green mt-8 w-full rounded-t-lg p-4 text-lg font-bold text-white">EMERGENCY CONTACT</div>

        {{-- Content area for Emergency Contact --}}
        <div class="bg-beige rounded-b-lg p-6 shadow-md">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                {{-- Contact Name --}}
                <div class="col-span-1">
                    <label class="text-dark-green mb-1 block font-bold">Name</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->contact_name ?? 'N/A' }}
                    </p>
                </div>

                {{-- Contact Relationship --}}
                <div class="col-span-1">
                    <label class="text-dark-green mb-1 block font-bold">Relationship</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->contact_relationship ?? 'N/A' }}
                    </p>
                </div>

                {{-- Contact Number --}}
                <div class="col-span-1">
                    <label class="text-dark-green mb-1 block font-bold">Contact Number</label>
                    <p
                        class="min-h-[42px] w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm"
                    >
                        {{ $patient->contact_number ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex items-center justify-end space-x-4">
            {{-- Styled "Back" Button --}}
            <a href="{{ route('patients.index') }}" class="button-default text-center">Back</a>
        </div>
    </div>
@endsection
