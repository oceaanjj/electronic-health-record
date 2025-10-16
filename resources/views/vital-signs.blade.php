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

        <table>
            <tr>
                <th class="title">TIME</th>
                <th class="title">TEMPERATURE</th>
                <th class="title">HR (bpm)</th>
                <th class="title">RR (bpm)</th>
                <th class="title">BP (mmHg)</th>
                <th class="title">SpO2 (%)</th>
                <th class="title">Alerts</th>
            </tr>

            @php
                $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
                $severityOrder = ['CRITICAL' => 1, 'WARNING' => 2, 'INFO' => 3, 'NONE' => 4];
            @endphp

            @foreach ($times as $time)
                @php
                    $vitalsRecord = $vitalsData->get($time);
                    $alerts = session("cdss.$time") ?? [];
                    $mostSevere = collect($alerts)
                        ->sortBy(fn($a) => $severityOrder[$a['severity']] ?? 4)
                        ->first();
                @endphp
                <tr>
                    <th class="time">{{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}</th>
                    <td>
                        <input type="text" name="temperature_{{ $time }}" placeholder="temperature"
                            value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}">
                    </td>
                    <td>
                        <input type="text" name="hr_{{ $time }}" placeholder="HR"
                            value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}">
                    </td>
                    <td>
                        <input type="text" name="rr_{{ $time }}" placeholder="RR"
                            value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}">
                    </td>
                    <td>
                        <input type="text" name="bp_{{ $time }}" placeholder="BP"
                            value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}">
                    </td>
                    <td>
                        <input type="text" name="spo2_{{ $time }}" placeholder="SpO2"
                            value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}">
                    </td>
                    <td>
                        @if ($mostSevere)
                            @php
                                $color = $mostSevere['severity'] === 'CRITICAL' ? 'red'
                                    : ($mostSevere['severity'] === 'WARNING' ? 'orange'
                                        : ($mostSevere['severity'] === 'INFO' ? 'blue' : 'green'));
                            @endphp
                            <span style="color: {{ $color }}">
                                {{ $mostSevere['alert'] }}
                            </span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </form>
    </div>

    <div class="buttons">
        <button class="btn">CDSS</button>
        <button class="btn" type="button" onclick="document.getElementById('vitals-form').submit();">Submit</button>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/vital-signs-style.css', 'resources/css/act-of-daily-living.css'])
@endpush