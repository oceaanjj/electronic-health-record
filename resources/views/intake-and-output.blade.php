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

        {{-- MAIN DATA FORM (submit) --}}
        <form id="io-form" method="POST" action="{{ route('io.store') }}">
            @csrf

            {{-- Hidden fields to send patient context with the data submission --}}
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">
            <input type="hidden" name="date" value="{{ session('selected_date') }}">
            <input type="hidden" name="day_no" value="{{ session('selected_day_no') }}">

            <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
                <div class="w-[68%] rounded-[15px] overflow-hidden">

                    <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="w-[15%] bg-dark-green text-white font-bold py-2 text-center rounded-tl-lg">ORAL INTAKE (mL)</th>
                                <th class="w-[13%] bg-dark-green text-white">IV FLUIDS (mL)</th>
                                <th class="w-[13%] bg-dark-green text-white rounded-tr-lg">URINE OUTPUT (mL)</th>
                            </tr>

                            <tr>
                                {{-- ORAL INTAKE INPUT --}}
                                <td>
                                    <input type="number" name="oral_intake" placeholder="Oral Intake"
                                        value="{{ old('oral_intake', $ioData->oral_intake ?? '') }}">
                                </td>

                                {{-- IV FLUIDS INPUT --}}
                                <td>
                                    <input type="number" name="iv_fluids_volume" placeholder="IV Fluids"
                                        value="{{ old('iv_fluids_volume', $ioData->iv_fluids_volume ?? '') }}">
                                </td>

                                {{-- URINE OUTPUT INPUT --}}
                                <td>
                                    <input type="number" name="urine_output" placeholder="Urine Output"
                                        value="{{ old('urine_output', $ioData->urine_output ?? '') }}">
                                </td>
                            </tr>
                        </table>
                </div>



             <div class="w-[25%] rounded-[15px] overflow-hidden">
                <div class="bg-dark-green text-white font-bold py-2 mb-1 text-center rounded-[15px]">
                    ALERTS
                </div>

                <table class="w-full border-collapse">
                <tr>

                    {{-- HINDI KO ITO MAGETS --}}
                    {{-- ALERTS --}}
                    <td>
                        <!-- Oral Intake Alerts -->
                        @if ($errors->has('oral_intake') || session('cdss.oral_intake'))
                            <div class="alert-group">
                                @error('oral_intake')
                                    <div class="alert-box alert-red"><span class="alert-message">{{ $message }}</span></div>
                                @enderror
                                @if (session('cdss.oral_intake'))
                                    @php
                                        $alertData = session('cdss.oral_intake');
                                        $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                    @endphp
                                    <div class="alert-box {{ $color }}"><span class="alert-message">Intake:
                                            {{ $alertData['alert'] }}</span></div>
                                @endif
                            </div>
                        @endif

                        <!-- IV Fluids Alerts -->
                        @if ($errors->has('iv_fluids') || session('cdss.iv_fluids'))
                            <div class="alert-group">
                                @error('iv_fluids')
                                    <div class="alert-box alert-red"><span class="alert-message">{{ $message }}</span></div>
                                @enderror
                                @if (session('cdss.iv_fluids'))
                                    @php
                                        $alertData = session('cdss.iv_fluids');
                                        $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                    @endphp
                                    <div class="alert-box {{ $color }}"><span class="alert-message">IV Fluids:
                                            {{ $alertData['alert'] }}</span></div>
                                @endif
                            </div>
                        @endif

                        <!-- Urine Output Alerts -->
                        @if ($errors->has('urine_output') || session('cdss.urine_output'))
                            <div class="alert-group">
                                @error('urine_output')
                                    <div class="alert-box alert-red"><span class="alert-message">{{ $message }}</span></div>
                                @enderror
                                @if (session('cdss.urine_output'))
                                    @php
                                        $alertData = session('cdss.urine_output');
                                        $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                                    @endphp
                                    <div class="alert-box {{ $color }}"><span class="alert-message">Output:
                                            {{ $alertData['alert'] }}</span></div>
                                @endif
                            </div>
                        @endif
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

@push('styles')
    @vite(['resources/css/intake-and-output-style.css'])
@endpush