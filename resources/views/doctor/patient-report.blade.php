@extends('layouts.doctor')
@section('title', 'Generate Patient Report')
@section('content')
    <div class="mx-auto my-12 w-[100%] md:w-[90%] lg:w-[85%] transition-colors duration-300">
        <h2 class="text-emerald-700 dark:text-emerald-500 font-alte mb-10 text-center text-[32px] sm:text-[45px] font-black tracking-tight">GENERATE PATIENT REPORT</h2>

        <form id="reportForm" action="{{ route('doctor.generate-report') }}" method="POST" class="space-y-8">
            @csrf

            <div class="flex flex-col items-center justify-center gap-5 sm:flex-row">
                <div class="searchable-dropdown relative w-full sm:w-[350px]"
                    data-select-url="{{ route('doctor.patient-report') }}">
                    <input type="text" id="patient_search_input" placeholder="Search patient..." autocomplete="off"
                        class="focus:ring-emerald-500 focus:border-emerald-500 w-full rounded-full border border-slate-300 dark:border-slate-700 px-5 py-2.5 text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 shadow-sm transition duration-300 ease-in-out outline-none focus:ring-2" />

                    <!-- loading spinner -->
                    <div id="patient-loading" class="absolute top-1/2 right-4 hidden -translate-y-1/2">
                        <svg class="text-emerald-600 h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l3 3-3 3v-4a8 8 0 01-8-8z"></path>
                        </svg>
                    </div>

                    <div id="patient_options_container"
                        class="absolute z-10 mt-2 hidden max-h-56 w-full overflow-y-auto rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl transition-all duration-300 ease-in-out no-scrollbar">
                        @foreach ($patients as $patient)
                            <div class="option cursor-pointer px-5 py-3 text-slate-700 dark:text-slate-300 transition-colors duration-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 border-b border-slate-50 last:border-0 dark:border-slate-700/50"
                                data-value="{{ $patient->patient_id }}"
                                data-is-active="1">
                                {{ trim($patient->name) }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="patient_id" id="patient_id_hidden" />

                <button type="submit" id="submitBtn" disabled
                    class="bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-700 disabled:bg-slate-300 dark:disabled:bg-slate-800 disabled:cursor-not-allowed rounded-full px-10 py-2.5 font-alte font-bold text-white disabled:text-slate-500 dark:disabled:text-slate-600 shadow-md transition-all duration-300 ease-in-out uppercase tracking-wider text-sm">
                    GENERATE REPORT
                </button>
            </div>
        </form>
    </div>

    <style>
        .option.active {
            background-color: #ecfdf5 !important;
            color: #047857 !important;
        }
        .dark .option.active {
            background-color: rgba(6, 95, 70, 0.4) !important;
            color: #34d399 !important;
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
