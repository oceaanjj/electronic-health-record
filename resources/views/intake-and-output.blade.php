@extends('layouts.app')

@section('title', 'Patient Intake And Output')

@section('content')
    {{-- FORM OVERLAY (ALERT) --}}
    <div id="form-content-container">

        {{-- 1. STRUCTURED HEADER (Layout & CDSS Banner) --}}
        <div class="mx-auto mt-6 w-[80%] space-y-4">

            {{-- CDSS ALERT BANNER --}}
            @isset($selectedPatient)
                @if ($ioData)
                    <div
                        class="relative flex items-center justify-between py-3 px-5 border border-amber-400/50 rounded-lg shadow-sm bg-amber-100/70 backdrop-blur-md">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-[#dcb44e]">info</span>
                            <span class="text-sm font-semibold text-[#dcb44e]">
                                Clinical Decision Support System is now available for this date.
                            </span>
                        </div>
                        <button type="button" onclick="this.closest('.relative').remove()" class="text-amber-700">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                @endif
            @endisset

            {{-- PATIENT SELECTION ROW --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-6">
                    <label for="patient_search_input"
                        class="font-alte text-dark-green font-bold whitespace-nowrap min-w-[120px]">
                        PATIENT NAME :
                    </label>

                    <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('io.select') }}">
                        <input type="text" id="patient_search_input" placeholder="Select or type Patient Name"
                            value="@isset($selectedPatient){{ trim($selectedPatient->name) }}@endisset" autocomplete="off"
                            class="font-creato-bold w-full rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2" />

                        <div id="patient_options_container"
                            class="absolute z-50 mt-2 hidden max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg">
                            @foreach ($patients as $patient)
                                <div class="option cursor-pointer px-4 py-2 transition duration-150 hover:bg-blue-100"
                                    data-value="{{ $patient->patient_id }}">
                                    {{ trim($patient->name) }}
                                </div>
                            @endforeach
                        </div>

                        <input type="hidden" id="patient_id_hidden" name="patient_id"
                            value="@isset($selectedPatient){{ $selectedPatient->patient_id }}@endisset" />
                    </div>
                </div>

                {{-- DAY NO ROW --}}
                @isset($selectedPatient)
                    <div class="flex items-center gap-6">
                        <label for="day_no_selector"
                            class="font-alte text-dark-green font-bold whitespace-nowrap min-w-[120px]">DAY NO :</label>
                        <select id="day_no_selector" name="day_no" form="io-form"
                            class="font-creato-bold w-[120px] rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2">
                            @for ($i = 1; $i <= ($daysSinceAdmission ?? 30); $i++)
                                <option value="{{ $i }}" @if(($currentDayNo ?? 1) == $i) selected @endif>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                @endisset

                {{-- NOT AVAILABLE FOOTER --}}
                @isset($selectedPatient)
                    @if (!$ioData)
                        <div class="text-xs text-gray-500 italic flex items-center gap-2 px-2">
                            <span class="material-symbols-outlined text-[14px]">pending_actions</span>
                            Clinical Decision Support System is not yet available (No data for this day).
                        </div>
                    @endif
                @endisset
            </div>
        </div>

        <fieldset>
            <form id="io-form" class="cdss-form" method="POST" action="{{ route('io.store') }}"
                data-analyze-url="{{ route('io.check') }}" data-batch-analyze-url="{{ route('io.analyze-batch') }}">
                @csrf

                <input type="hidden" name="patient_id"
                    value="@isset($selectedPatient){{ $selectedPatient->patient_id }}@endisset " />
                <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}" />

                <div class="mx-auto mt-6 flex w-[85%] items-start justify-center gap-1">
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
                                    <input type="text" name="oral_intake" placeholder="Enter Oral Intake"
                                        value="{{ old('oral_intake', $ioData->oral_intake ?? '') }}"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                        data-field-name="oral_intake" />
                                </td>

                                {{-- IV FLUIDS --}}
                                <td class="text-center align-middle">
                                    <input type="text" name="iv_fluids_volume" placeholder="Enter IV Fluids"
                                        value="{{ old('iv_fluids_volume', $ioData->iv_fluids_volume ?? '') }}"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                        data-field-name="iv_fluids_volume" />
                                </td>

                                {{-- URINE OUTPUT --}}
                                <td class="text-center align-middle">
                                    <input type="text" name="urine_output" placeholder="Enter Urine Output"
                                        value="{{ old('urine_output', $ioData->urine_output ?? '') }}"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                        class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                        data-field-name="urine_output" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="w-[25%] overflow-hidden rounded-[15px]">
                        <div class="main-header mb-1 rounded-[15px] py-2 text-center">ALERTS</div>

                        <table class="w-full border-collapse">
                            <tr>
                                <td class="align-middle">
                                    <div class="alert-box my-[3px] flex h-[90px] w-full items-center justify-center"
                                        data-alert-for="io_alert">
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
                        <button type="submit" formaction="{{ route('io.cdss') }}" class="button-default cdss-btn">CDSS</button>
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