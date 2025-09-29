@extends('layouts.app')

@section('title', 'Patient Lab Values')

@section('content')

<div class="container">
    <div class="header">
        <label for="patient_info">PATIENT NAME :</label>
        <form action="{{ route('lab-values.select') }}" method="POST" id="patient-select-form">
            @csrf
            <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                <option value="" @if(session('selected_patient_id') == '') selected @endif>-- Select Patient --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->patient_id }}" @if(session('selected_patient_id') == $patient->patient_id) selected @endif>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <form action="{{ route('lab-values.store') }}" method="POST">
        @csrf

        <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

        <table>
            <tr>
                <th class="title">LAB TEST</th>
                <th class="title">RESULT</th>
                <th class="title">PEDIATRIC NORMAL RANGE</th>
                <th class="title">ALERTS</th>
            </tr>

            @php
                $labTests = [
                    'WBC (×10⁹/L)' => 'wbc',
                    'RBC (×10¹²/L)' => 'rbc',
                    'Hgb (g/dL)' => 'hgb',
                    'Hct (%)' => 'hct',
                    'Platelets (×10⁹/L)' => 'platelets',
                    'MCV (fL)' => 'mcv',
                    'MCH (pg)' => 'mch',
                    'MCHC (g/dL)' => 'mchc',
                    'RDW (%)' => 'rdw',
                    'Neutrophils (%)' => 'neutrophils',
                    'Lymphocytes (%)' => 'lymphocytes',
                    'Monocytes (%)' => 'monocytes',
                    'Eosinophils (%)' => 'eosinophils',
                    'Basophils (%)' => 'basophils',
                ];
            @endphp

            @foreach ($labTests as $label => $name)
                <tr>
                    <th class="title">{{ $label }}</th>
                    <td>
                        <input type="number" step="any" name="{{ $name }}_result" placeholder="result"
                            value="{{ old($name . '_result', optional($labValue)->{$name . '_result'}) }}">
                    </td>
                    <td>
                        <input type="text" name="{{ $name }}_normal_range" placeholder="normal range"
                            value="{{ old($name . '_normal_range', optional($labValue)->{$name . '_normal_range'}) }}">
                    </td>
                    <td>
                        @if (session('alerts') && isset(session('alerts')[$name . '_alerts']))
                            @foreach (session('alerts')[$name . '_alerts'] as $alert)
                                @php
                                    $color = 'text-gray-600';
                                        if (str_contains(strtolower($alert), 'critical')) {
                                            $color = 'text-red-600 font-bold'; 
                                        } elseif (str_contains(strtolower($alert), 'high') || str_contains(strtolower($alert), 'low')) {
                                            $color = 'text-orange-500'; 
                                        } elseif (str_contains(strtolower($alert), 'normal')) {
                                            $color = 'text-green-600'; 
                                        } else {
                                            $color = 'text-blue-500'; 
                                        }
                                    @endphp
                                <p class="{{ $color }}">{{ $alert }}</p>
                            @endforeach
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
</div>

<div class="buttons">
    <button class="btn" type="button">CDSS</button>
    <button class="btn" type="submit">Submit</button>
</div>

</form>

@endsection

@push('styles')
    @vite(['resources/css/lab-values.css'])
@endpush
