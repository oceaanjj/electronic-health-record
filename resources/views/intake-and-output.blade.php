@extends('layouts.app')

@section('title', 'Patient Intake And Output')

@section('content')
    {{-- FORM OVERLAY (ALERT) --}}
    <div id="form-content-container">
        {{-- 1. STRUCTURED HEADER (Layout & CDSS Banner) --}}
        <div class="mx-auto mt-1 w-full">
            {{-- CDSS ALERT BANNER --}}
            @isset($selectedPatient)
                @if ($ioData)
                    {{-- Exact copy of Physical Exam's wrapper size and padding --}}
                    <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                        {{-- Exact copy of Physical Exam's mt-3, py-3, and px-5 sizing --}}
                        <div
                            id="cdss-alert-content"
                            class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-md"
                        >
                            <div class="flex items-center gap-3">
                                {{-- Pulsing Info Icon matching Physical Exam --}}
                                <span class="material-symbols-outlined animate-pulse text-[#dcb44e]">info</span>
                                <span class="text-sm font-semibold text-[#dcb44e]">
                                    Clinical Decision Support System is now available for this date.
                                </span>
                            </div>

                            {{-- Close Button matching Physical Exam's size and interaction --}}
                            <button
                                type="button"
                                onclick="closeCdssAlert()"
                                class="group flex items-center justify-center rounded-full p-1 text-amber-700 transition-all duration-300 hover:bg-amber-200/50 active:scale-90"
                            >
                                <span
                                    class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90"
                                >
                                    close
                                </span>
                            </button>
                        </div>
                    </div>
                @endif
            @endisset

            <div class="mx-auto w-full pt-10">
                {{-- px-4 matches standard padding for high-res screens --}}
                <div class="ml-25 flex flex-wrap items-center gap-x-10 gap-y-4">
                    {{-- 1. PATIENT SECTION --}}
                    <div class="flex items-center gap-4">
                        <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                            PATIENT NAME :
                        </label>
                        <div class="w-[350px]">
                            {{-- Matches Vital Signs width --}}
                            <x-searchable-patient-dropdown
                                :patients="$patients"
                                :selectedPatient="$selectedPatient"
                                :selectRoute="route('io.select')"
                                :inputValue="$selectedPatient?->name ?? ''"
                            />
                        </div>
                    </div>

                    {{-- 2. DAY NO SECTION --}}
                    @isset($selectedPatient)
                        <div class="flex items-center gap-3">
                            <label for="day_no_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">
                                DAY NO :
                            </label>
                            <div class="relative">
                                <select
                                    id="day_no_selector"
                                    name="day_no"
                                    form="io-form"
                                    class="font-creato-bold w-[100px] appearance-none rounded-full border border-gray-300 bg-white px-4 py-2 pr-8 text-[15px] shadow-sm transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 focus:outline-none"
                                >
                                    @for ($i = 1; $i <= ($daysSinceAdmission ?? 30); $i++)
                                        <option value="{{ $i }}" @selected(($currentDayNo ?? 1) == $i)>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                {{-- Standardized Arrow UI --}}
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endisset
                </div>

                {{-- ALERT MESSAGE (Aligned with ml-20) --}}
                @isset($selectedPatient)
                    @if (! $ioData || $ioData->count() == 0)
                        <div class="mt-4 ml-24 flex items-center gap-2 text-xs text-gray-500 italic">
                            <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                            Clinical Decision Support System is not yet available (No data for this day).
                        </div>
                    @endif
                @endisset
            </div>
        </div>

        <fieldset>
            <form
                id="io-form"
                class="cdss-form"
                method="POST"
                action="{{ route('io.store') }}"
                data-analyze-url="{{ route('io.check') }}"
                data-batch-analyze-url="{{ route('io.analyze-batch') }}"
            >
                @csrf

                <input
                    type="hidden"
                    name="patient_id"
                    value="@isset($selectedPatient){{ $selectedPatient->patient_id }}@endisset "
                />
                <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}" />

                <div class="mx-auto mt-10 flex w-[90%] items-start justify-center gap-1">
                    <div class="w-[68%] overflow-hidden rounded-[15px]">
                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="main-header w-[33%] rounded-tl-lg py-2 text-center">ORAL INTAKE (mL)</th>
                                <th class="main-header w-[33%] py-2 text-center">IV FLUIDS (mL)</th>
                                <th class="main-header w-[33%] rounded-tr-lg py-2 text-center">URINE OUTPUT (mL)</th>
                            </tr>

                            <tr class="bg-beige text-brown">
                                {{-- ORAL INTAKE --}}
                                <td class="text-center align-middle">
                                    <input
                                        type="text"
                                        name="oral_intake"
                                        placeholder="Enter Oral Intake"
                                        value="{{ old('oral_intake', $ioData->oral_intake ?? '') }}"
                                        oninput="
                                            this.value = this.value
                                                .replace(/[^0-9.]/g, '')
                                                .replace(/(\..*?)\..*/g, '$1')
                                        "
                                        class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                        data-field-name="oral_intake"
                                    />
                                </td>

                                {{-- IV FLUIDS --}}
                                <td class="text-center align-middle">
                                    <input
                                        type="text"
                                        name="iv_fluids_volume"
                                        placeholder="Enter IV Fluids"
                                        value="{{ old('iv_fluids_volume', $ioData->iv_fluids_volume ?? '') }}"
                                        oninput="
                                            this.value = this.value
                                                .replace(/[^0-9.]/g, '')
                                                .replace(/(\..*?)\..*/g, '$1')
                                        "
                                        class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                        data-field-name="iv_fluids_volume"
                                    />
                                </td>

                                {{-- URINE OUTPUT --}}
                                <td class="text-center align-middle">
                                    <input
                                        type="text"
                                        name="urine_output"
                                        placeholder="Enter Urine Output"
                                        value="{{ old('urine_output', $ioData->urine_output ?? '') }}"
                                        oninput="
                                            this.value = this.value
                                                .replace(/[^0-9.]/g, '')
                                                .replace(/(\..*?)\..*/g, '$1')
                                        "
                                        class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                        data-field-name="urine_output"
                                    />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="w-[25%] overflow-hidden rounded-[15px]">
                        <div class="main-header mb-1 rounded-[15px] py-2 text-center">ALERTS</div>

                        <table class="w-full border-collapse">
                            <tr>
                                <td class="align-middle">
                                    <div
                                        class="alert-box my-[3px] flex h-[90px] w-full items-center justify-center"
                                        data-alert-for="io_alert"
                                    >
                                        <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="mx-auto mt-5 mb-30 flex w-[80%] justify-end space-x-4">
                    {{-- <button type="button" class="button-default w-[300px]">CALCULATE FLUID BALANCE</button> --}}
                    @if ($ioData)
                        <button type="submit" formaction="{{ route('io.cdss') }}" class="button-default cdss-btn">
                            CDSS
                        </button>
                    @endif

                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </form>
        </fieldset>
    </div>
@endsection

@push('scripts')
    @vite([
        'resources/js/alert.js',
        'resources/js/intake-output-patient-loader.js',
        'resources/js/intake-output-cdss.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/intake-output-data-loader.js',
    ])

    {{-- Define the specific initializers for this page --}}
    <!--
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                window.pageInitializers = [
                    window.initializeSearchableDropdown,
                    window.initializeDateDayLoader,

                    // Assuming intakeOutputCdss has an init method or is directly callable
                    () => {
                        if (typeof window.intakeOutputCdss === 'function') {
                            window.intakeOutputCdss();
                        } else if (
                            typeof window.intakeOutputCdss?.init === 'function'
                        ) {
                            window.intakeOutputCdss.init();
                        }
                    }
                ];
            });
        </script>
        -->
@endpush
