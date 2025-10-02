@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    <div class="container">
        <div class="header">

            {{-- PATIENT DROPDOWN AND DATE FORM --}}
            <form action="{{ route('vital-signs.select') }}" method="POST" id="patient-select-form"
                class="flex items-center space-x-4">
                @csrf

                <label for="patient_id">PATIENT NAME :</label>
                <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                    <option value="" @if(session('selected_patient_id') == '') selected @endif>-- Select Patient --</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->patient_id }}" @if(session('selected_patient_id') == $patient->patient_id)
                        selected @endif>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>

                <!-- DATE -->
                <label for="date">DATE :</label>
                <input class="date" type="date" id="date_selector" name="date" value="{{ session('selected_date') }}"
                    onchange="this.form.submit()">

                <!-- DAY NO -->
                <label for="day_no">DAY NO :</label>
                <select id="day_no" name="day_no" onchange="this.form.submit()">
                    <option value="">-- Select number --</option>
                    @for ($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}" @if(session('selected_day_no') == $i) selected @endif>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </form>


        </div>

        {{-- MAIN FORM (submit) --}}
        <form id="vitals-form" method="POST" action="{{ route('vital-signs.store') }}">
            @csrf

            {{-- Hidden PATIENT_ID AND DATE from session --}}
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">
            <input type="hidden" name="date" value="{{ session('selected_date') }}">
            <input type="hidden" name="day_no" value="{{ session('selected_day_no') }}">


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

                        // Kunin ang alerts from session at piliin ang pinaka-severe
                        $alerts = session("cdss.$time") ?? [];
                        $mostSevere = collect($alerts)
                            ->sortBy(fn($a) => $severityOrder[$a['severity']] ?? 4)
                            ->first();
                    @endphp
                    <tr>
                        <th class="time">{{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}</th>
                        <td>
                            <input type="text" name="temperature_{{ $time }}" placeholder="temperature"
                                value="{{ optional($vitalsRecord)->temperature }}">
                        </td>
                        <td>
                            <input type="text" name="hr_{{ $time }}" placeholder="HR" value="{{ optional($vitalsRecord)->hr }}">
                        </td>
                        <td>
                            <input type="text" name="rr_{{ $time }}" placeholder="RR" value="{{ optional($vitalsRecord)->rr }}">
                        </td>
                        <td>
                            <input type="text" name="bp_{{ $time }}" placeholder="BP" value="{{ optional($vitalsRecord)->bp }}">
                        </td>
                        <td>
                            <input type="text" name="spo2_{{ $time }}" placeholder="SpO2"
                                value="{{ optional($vitalsRecord)->spo2 }}">
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