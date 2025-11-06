@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    <div class="header">
        <form action="{{ route('vital-signs.select') }}" method="POST" id="patient-select-form"
            class="flex items-center space-x-4">
            @csrf
            <label for="patient_id" style="color: white;">PATIENT NAME :</label>
            <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                <option value="" {{ old('patient_id', session('selected_patient_id')) == '' ? 'selected' : '' }}>-- Select
                    Patient --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->patient_id }}" {{ old('patient_id', session('selected_patient_id')) == $patient->patient_id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
            <label for="date" style="color: white;">DATE :</label>
            <input class="date" type="date" id="date_selector" name="date"
                value="{{ old('date', session('selected_date')) }}" onchange="this.form.submit()">
            <label for="day_no" style="color: white;">DAY NO :</label>
            <select id="day_no" name="day_no" onchange="this.form.submit()">
                <option value="">-- Select number --</option>
                @for ($i = 1; $i <= 30; $i++)
                    <option value="{{ $i }}" {{ old('day_no', session('selected_day_no')) == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </form>
    </div>

    <form id="vitals-form" method="POST" action="{{ route('vital-signs.store') }}">
        @csrf

        <input type="hidden" name="patient_id" value="{{ old('patient_id', session('selected_patient_id')) }}">
        <input type="hidden" name="date" value="{{ old('date', session('selected_date')) }}">
        <input type="hidden" name="day_no" value="{{ old('day_no', session('selected_day_no')) }}">

        <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
            <div class="w-[68%] rounded-[15px] overflow-hidden">
                
                <table class="w-full table-fixed border-collapse border-spacing-y-0">
                    <tr>
                        <th class="w-[15%] bg-dark-green text-white font-bold py-2 text-center rounded-tl-lg">TIME</th>
                        <th class="w-[13%] bg-dark-green text-white">TEMPERATURE</th>
                        <th class="w-[10%] bg-dark-green text-white">HR</th>
                        <th class="w-[10%] bg-dark-green text-white">RR</th>
                        <th class="w-[10%] bg-dark-green text-white">BP</th>
                        <th class="w-[10%] bg-dark-green text-white rounded-tr-lg">SpO₂</th>
                    </tr>

                    {{-- NOTE: paki-explain saakin ito kasi gagawin kong input text ito--}}
                    @php
                        $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
                    @endphp

                    @foreach ($times as $index => $time)
                        @php
                            $vitalsRecord = $vitalsData->get($time);
                            $isLast = $index === count($times) - 1;
                            $borderClass = $isLast ? '' : 'border-b-2 border-line-brown/70';
                        @endphp

                        <tr class="{{ $borderClass }}">
                            {{-- TIME COLUMN --}}
                            <th class="text-center font-semibold py-2 bg-yellow-light text-brown {{ $borderClass }}">
                                {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                            </th>

                            {{-- TEMPERATURE --}}
                            <td class="bg-beige {{ $borderClass }}">
                                <input type="text" name="temperature_{{ $time }}" placeholder="temperature"
                                    value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                                    class="vital-input h-[60px]" data-param="temperature" data-time="{{ $time }}">
                            </td>

                            {{-- HR --}}
                            <td class="bg-beige {{ $borderClass }}">
                                <input type="text" name="hr_{{ $time }}" placeholder="bpm"
                                    value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                                    class="vital-input h-[60px]" data-param="hr" data-time="{{ $time }}">
                            </td>

                            {{-- RR --}}
                            <td class="bg-beige {{ $borderClass }}">
                                <input type="text" name="rr_{{ $time }}" placeholder="bpm"
                                    value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                                    class="vital-input h-[60px]" data-param="rr" data-time="{{ $time }}">
                            </td>

                            {{-- BP --}}
                            <td class="bg-beige {{ $borderClass }}">
                                <input type="text" name="bp_{{ $time }}" placeholder="mmHg"
                                    value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                                    class="vital-input h-[60px]" data-param="bp" data-time="{{ $time }}">
                            </td>

                            {{-- SpO₂ --}}
                            <td class="bg-beige {{ $borderClass }}">
                                <input type="text" name="spo2_{{ $time }}" placeholder="%"
                                    value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                                    class="vital-input h-[60px]" data-param="spo2" data-time="{{ $time }}">
                            </td>
                        </tr>
                    @endforeach

                </table>
            </div>

            <div class="w-[25%] rounded-[15px] overflow-hidden">
                <div class="bg-dark-green text-white font-bold py-2 mb-1 text-center rounded-[15px]">
                    ALERTS
                </div>

                <table class="w-full border-collapse">
                        @foreach ($times as $time)
                            @php
                                $vitalsRecord = $vitalsData->get($time);
                                $severity = optional($vitalsRecord)->news_severity ?? 'NONE';
                                $color = $severity === 'CRITICAL' ? 'text-red-600'
                                        : ($severity === 'WARNING' ? 'text-orange-500'
                                        : ($severity === 'INFO' ? 'text-blue-500'
                                        : ($severity === 'NONE' ? 'text-white' : 'text-black')));
                                $alerts = $vitalsRecord ? explode('; ', $vitalsRecord->alerts) : [];
                            @endphp

                            <tr>
                                <td class="align-middle">
                                    <div class="alert-box my-[3px] h-[53px] flex justify-center items-center">
                                        @if ($vitalsRecord && optional($vitalsRecord)->alerts)
                                            <ul class="list-none text-center {{ $color }}">
                                                @foreach($alerts as $alert)
                                                    <li class="font-semibold">{{ $alert }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="opacity-70 text-white font-semibold">No Alerts</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                </table>
            </div>
        </div>     
    </form>

    
    <div class="w-[70%] mx-auto flex justify-end mt-5 mb-30 space-x-4">
            <button type="button" class="button-default">CDSS</button>
            <button type="submit" class="button-default">SUBMIT</button>       
    </div>
   
@endsection

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
@endpush