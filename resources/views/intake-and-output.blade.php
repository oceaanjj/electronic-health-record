@extends('layouts.app')

@section('title', 'Patient Intake And Output')

@section('content')
    {{-- FORM OVERLAY (ALERT) --}}
    <div id="form-content-container">

        {{-- NEW SEARCHABLE PATIENT DROPDOWN FOR INTAKE AND OUTPUT --}}
        <div class="header mx-auto my-10 flex w-[80%] items-center gap-6">
            <form id="patient-select-form" class="flex w-full items-center gap-6">
                @csrf

                {{-- PATIENT NAME --}}
                <label for="patient_search_input" class="font-alte text-dark-green font-bold whitespace-nowrap">
                    PATIENT NAME :
                </label>

                <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('io.select') }}">
                    {{-- Text input for search --}}
                    <input type="text" id="patient_search_input" placeholder="Select or type Patient Name"
                        value="@isset($selectedPatient){{ trim($selectedPatient->name) }}@endisset" autocomplete="off"
                        class="font-creato-bold w-full rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500" />

                    {{-- Dropdown list --}}
                    <div id="patient_options_container"
                        class="absolute z-50 mt-2 hidden max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg">
                        @foreach ($patients as $patient)
                            <div class="option cursor-pointer px-4 py-2 transition duration-150 hover:bg-blue-100"
                                data-value="{{ $patient->patient_id }}">
                                {{ trim($patient->name) }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Hidden input to store selected patient ID --}}
                    <input type="hidden" id="patient_id_hidden" name="patient_id"
                        value="@isset($selectedPatient){{ $selectedPatient->patient_id }}@endisset" />
                </div>

                {{-- DAY NO --}}
                <label for="day_no" class="font-alte text-dark-green font-bold whitespace-nowrap">DAY NO :</label>
                <select id="day_no_selector" name="day_no"
                    class="font-creato-bold focus->ring-blue-500 w-[120px] rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2">
                    @for ($i = 1; $i <= ($daysSinceAdmission ?? 30); $i++)
                        <option value="{{ $i }}" @if(($currentDayNo ?? 1) == $i) selected @endif>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </form>
        </div>
        {{-- END SEARCHABLE PATIENT DROPDOWN FOR INTAKE AND OUTPUT --}}

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
                                    <input type="number" name="oral_intake" placeholder="Enter Oral Intake"
                                        value="{{ old('oral_intake', $ioData->oral_intake ?? '') }}" min="0"
                                        class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                        data-field-name="oral_intake" />
                                </td>

                                {{-- IV FLUIDS --}}
                                <td class="text-center align-middle">
                                    <input type="number" name="iv_fluids_volume" placeholder="Enter IV Fluids"
                                        value="{{ old('iv_fluids_volume', $ioData->iv_fluids_volume ?? '') }}" min="0"
                                        class="bg-beige text-brown cdss-input vital-input h-[100px] w-[80%] rounded-[10px] px-3 text-center font-semibold focus:outline-none"
                                        data-field-name="iv_fluids_volume" />
                                </td>

                                {{-- URINE OUTPUT --}}
                                <td class="text-center align-middle">
                                    <input type="number" name="urine_output" placeholder="Enter Urine Output"
                                        value="{{ old('urine_output', $ioData->urine_output ?? '') }}" min="0"
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
                                        <span class="font-semibold text-white opacity-70">No Alerts</span>
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