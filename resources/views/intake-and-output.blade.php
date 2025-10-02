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

            <table>
                <tr>
                    <th class="title">ORAL INTAKE (mL)</th>
                    <th class="title">IV FLUIDS (mL)</th>
                    <th class="title">URINE OUTPUT (mL)</th>
                    <th class="title">Alerts</th>
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
        </form>

    <div class="buttons">

        <div class="button-col"></div>

        <div class="button-col">
            <a href="#" class="btn">Calculate fluid balance</a>
        </div>

        <div class="button-col">
            <button class="btn">CDSS</button>
        </div>

        <div class="button-col">
            <button class="btn" type="button" onclick="document.getElementById('io-form').submit()">Submit</button>
        </div>
    </div>


@endsection

@push('styles')
    @vite(['resources/css/intake-and-output-style.css'])
@endpush