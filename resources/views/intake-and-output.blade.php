@extends('layouts.app')
@section('title', 'Patient Intake And Output')
@section('content')


       {{-- NEW SEARCHABLE PATIENT DROPDOWN FOR INTAKE AND OUTPUT --}}
    <div class="header flex items-center gap-6 my-10 mx-auto w-[80%]">
        <form action="{{ route('io.select') }}" method="POST" id="patient-select-form" class="flex items-center gap-6 w-full">
            @csrf

            {{-- PATIENT NAME --}}
            <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
                PATIENT NAME :
            </label>

            <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('io.select') }}">
                {{-- Text input for search --}}
                <input
                    type="text"
                    id="patient_search_input"
                    placeholder="Select or type Patient Name"
                    value="{{ trim($selectedPatient->name ?? '') }}"
                    autocomplete="off"
                    class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                >

                {{-- Dropdown list --}}
                <div
                    id="patient_options_container"
                    class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"
                >
                    @foreach ($patients as $patient)
                        <div
                            class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150"
                            data-value="{{ $patient->patient_id }}"
                        >
                            {{ trim($patient->name) }}
                        </div>
                    @endforeach
                </div>

                {{-- Hidden input to store selected patient ID --}}
                <input type="hidden" id="patient_id_hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
            </div>

            {{-- DATE --}}
            <label for="date_selector" class="whitespace-nowrap font-alte font-bold text-dark-green">
                DATE :
            </label>
            <input
                type="date"
                id="date_selector"
                name="date"
                value="{{ old('date', session('selected_date', date('Y-m-d'))) }}"
                onchange="this.form.submit()"
                class="text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
            >

            {{-- DAY NO --}}
            <label for="day_no" class="whitespace-nowrap font-alte font-bold text-dark-green">
                DAY NO :
            </label>
            <select
                id="day_no_selector"
                name="day_no"
                onchange="this.form.submit()"
                class="w-[120px] text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
            >
                <option value="">-- Select number --</option>
                @for ($i = 1; $i <= 30; $i++)
                    <option
                        value="{{ $i }}"
                        {{ old('day_no', session('selected_day_no', 1)) == $i ? 'selected' : '' }}
                    >
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </form>
    </div>
    {{-- END SEARCHABLE PATIENT DROPDOWN FOR INTAKE AND OUTPUT --}}

        {{-- MAIN DATA FORM --}}
    <form id="io-form" method="POST" action="{{ route('io.store') }}">
        @csrf

        {{-- Hidden fields --}}
        <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">
        <input type="hidden" name="date" value="{{ session('selected_date') }}">
        <input type="hidden" name="day_no" value="{{ session('selected_day_no') }}">

        <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
            {{-- INPUT TABLE --}}
            <div class="w-[68%] rounded-[15px] overflow-hidden">
                <table class="w-full table-fixed border-collapse border-spacing-y-0">
                    <tr>
                        <th class="w-[33%] bg-dark-green text-white font-bold py-2 text-center rounded-tl-lg">ORAL INTAKE (mL)</th>
                        <th class="w-[33%] bg-dark-green text-white font-bold py-2 text-center">IV FLUIDS (mL)</th>
                        <th class="w-[33%] bg-dark-green text-white font-bold py-2 text-center rounded-tr-lg">URINE OUTPUT (mL)</th>
                    </tr>

                <tr class="bg-beige text-brown">
                    {{-- ORAL INTAKE --}}
                    <td class="text-center align-middle">
                        <input type="number" 
                            name="oral_intake" 
                            placeholder="Enter Oral Intake"
                            value="{{ old('oral_intake', $ioData->oral_intake ?? '') }}"
                            step="100"
                            min="0"
                            class="w-[80%] h-[100px] rounded-[10px] px-3 text-center bg-beige text-brown font-semibold focus:outline-none" />
                    </td>

                    {{-- IV FLUIDS --}}
                    <td class="text-center align-middle">
                        <input type="number" 
                            name="iv_fluids_volume" 
                            placeholder="Enter IV Fluids"
                            value="{{ old('iv_fluids_volume', $ioData->iv_fluids_volume ?? '') }}"
                            step="100"
                            min="0"
                            class="w-[80%] h-100px] rounded-[10px] px-3 text-center bg-beige text-brown font-semibold focus:outline-none" />
                    </td>

                    {{-- URINE OUTPUT --}}
                    <td class="text-center align-middle">
                        <input type="number" 
                            name="urine_output" 
                            placeholder="Enter Urine Output"
                            value="{{ old('urine_output', $ioData->urine_output ?? '') }}"
                            step="100"
                            min="0"
                            class="w-[80%] h-[100px] rounded-[10px] px-3 text-center bg-beige text-brown font-semibold focus:outline-none" />
                    </td>
                </tr>

                </table>
            </div>

            {{-- ALERTS --}}
            <div class="w-[25%] rounded-[15px] overflow-hidden">
                <div class="bg-dark-green text-white font-bold py-2 mb-1 text-center rounded-[15px]">
                    ALERTS
                </div>

                <table class="w-full border-collapse">
                    <tr>
                        <td class="align-middle">
                            <div class="alert-box my-[3px] h-[90px] flex justify-center items-center">
                                <span class="opacity-70 text-white font-semibold">No Alerts</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>


    <div class="w-[70%] mx-auto flex justify-end mt-20 mb-30 space-x-4">
            <button type="button" class="button-default w-[300px]">CALCULATE FLUID BALANCE</button> 
            <button type="button" class="button-default">CDSS</button>
            <button type="submit" class="button-default" onclick="document.getElementById('io-form').submit()">SUBMIT</button>   
                 
    </div>



@endsection
