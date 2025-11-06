@extends('layouts.app')
@section('title', 'Patient Intake And Output')
@section('content')


        {{-- PATIENT DROPDOWN AND DATE/DAY SELECTION FORM (ADL Style) --}}
        <form action="{{ route('io.select') }}" method="POST" id="patient-select-form">
            @csrf

           <div class="header">
                <label for="patient_id" style="color: white;">PATIENT NAME :</label>
                <select id="patient_id" name="patient_id" onchange="this.form.submit()">
                    <option value="" @if(session('selected_patient_id') == '') selected @endif>-- Select Patient --</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->patient_id }}" @if(session('selected_patient_id') == $patient->patient_id)
                        selected @endif>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>

                                <!-- DATE -->
                <label for="date" style="color: white;">DATE :</label>
                <input class="date" type="date" id="date_selector" name="date" value="{{ session('selected_date') }}"
                    onchange="this.form.submit()">
                    
                <!-- DAY NO -->
                <label for="day_no" style="color: white;">DAY NO :</label>
                <select id="day_no" name="day_no" onchange="this.form.submit()">
                    <option value="">-- Select number --</option>
                    @for ($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}" @if(session('selected_day_no') == $i) selected @endif>
                            {{ $i }}
                        </option>
                    @endfor
                </select>

            </div>
        </form>

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
