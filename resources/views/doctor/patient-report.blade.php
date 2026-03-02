@extends('layouts.doctor')
@section('title', 'Generate Patient Report')
@section('content')
    <div class="mx-auto my-12 w-[100%] md:w-[90%] lg:w-[85%]">
        <h2 class="text-dark-green font-alte mb-10 text-center text-[45px] font-black">GENERATE PATIENT REPORT</h2>

        <form id="reportForm" action="{{ route('doctor.generate-report') }}" method="POST" class="space-y-8">
            @csrf

            <div class="flex flex-col items-center justify-center gap-5 sm:flex-row">
                <div class="searchable-dropdown relative w-full sm:w-[350px]"
                    data-select-url="{{ route('doctor.patient-report') }}">
                    <input type="text" id="patient_search_input" placeholder="Search patient..." autocomplete="off"
                        class="focus:ring-dark-green focus:border-dark-green w-full rounded-full border border-gray-300 px-5 py-2 text-gray-700 shadow-sm transition duration-300 ease-in-out outline-none focus:ring-2" />

                    <!-- loading spinner -->
                    <div id="patient-loading" class="absolute top-1/2 right-4 hidden -translate-y-1/2">
                        <svg class="text-dark-green h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l3 3-3 3v-4a8 8 0 01-8-8z"></path>
                        </svg>
                    </div>

                    <div id="patient_options_container"
                        class="absolute z-10 mt-2 hidden max-h-56 w-full overflow-y-auto rounded-[20px] border border-gray-200 bg-white shadow-lg transition-all duration-300 ease-in-out">
                        @foreach ($patients as $patient)
                            <div class="option cursor-pointer px-4 py-2 transition-colors duration-200 hover:bg-green-50"
                                data-value="{{ $patient->patient_id }}">
                                {{ trim($patient->name) }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="patient_id" id="patient_id_hidden" />

                <button type="submit" disabled
                    class="bg-dark-green cursor-not-allowed rounded-full px-8 py-2.5 font-bold text-white opacity-50 shadow-md transition-all duration-300 ease-in-out">
                    GENERATE REPORT
                </button>
            </div>
        </form>
    </div>

    <style>
        .option.active {
            background-color: #dbeafe;
        }
    </style>
@endsection

@push('scripts')
    @vite([
        'resources/js/alert.js',
        'resources/js/patient-loader.js',
        'resources/js/patient-report.js',
        'resources/js/searchable-dropdown.js',
    ])
@endpush