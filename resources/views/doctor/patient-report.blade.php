@extends('layouts.doctor')
@section('title', 'Generate Patient Report')
@section('content')
    <div class="w-[100%] md:w-[90%] lg:w-[85%] mx-auto my-12">
    <h2 class="text-[45px] font-black mb-10 text-dark-green text-center font-alte">
        GENERATE PATIENT REPORT
    </h2>

    <form id="reportForm" action="{{ route('doctor.generate-report') }}" method="POST" class="space-y-8">
        @csrf

        <div class="flex flex-col sm:flex-row justify-center items-center gap-5">

            <div class="searchable-dropdown relative w-full sm:w-[350px]" data-select-url="{{ route('doctor.patient-report') }}">
                <input
                    type="text"
                    id="patient_search_input"
                    placeholder="Search patient..."
                    autocomplete="off"
                    class="w-full px-5 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none shadow-sm transition duration-300 ease-in-out text-gray-700"
                >

                <!-- loading spinner -->
                <div id="patient-loading" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="w-5 h-5 text-dark-green animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l3 3-3 3v-4a8 8 0 01-8-8z">
                        </path>
                    </svg>
                </div>


                <div id="patient_options_container"
                    class="absolute z-10 w-full bg-white border border-gray-200 rounded-[20px] shadow-lg mt-2 max-h-56 overflow-y-auto hidden transition-all duration-300 ease-in-out">
                    @foreach ($patients as $patient)
                        <div
                            class="option px-4 py-2 hover:bg-green-50 cursor-pointer transition-colors duration-200"
                            data-value="{{ $patient->patient_id }}">
                            {{ trim($patient->name) }}
                        </div>
                    @endforeach
                </div>
            </div>

            <input type="hidden" name="patient_id" id="patient_id_hidden">


            <button
                type="submit"
                disabled
                class="bg-dark-green text-white font-bold px-8 py-2.5 rounded-full shadow-md opacity-50 cursor-not-allowed transition-all duration-300 ease-in-out"
            >
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