@extends('layouts.app')

@section('title', 'Patient Lab Values')

@section('content')

    <x-searchable-dropdown 
        :patients="$patients" 
        :selectedPatient="$selectedPatient ?? null"
        selectUrl="{{ route('lab-values.select') }}" 
    />

    <div id="form-content-container">
        {{-- DISABLED input overlay --}}
        @if (!session('selected_patient_id'))
            <div class="form-overlay" style="margin-left:15rem;">
                <span>Please select a patient to input</span> {{-- message --}}
            </div>
        @endif

    <form action="{{ route('lab-values.store') }}" method="POST" class="cdss-form"
            data-analyze-url="{{ route('lab-values.run-cdss-field') }}">
    @csrf
    <fieldset @if (!session('selected_patient_id')) disabled @endif>
    <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

    <center>
       <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
            <div class="w-[68%] rounded-[15px] overflow-hidden">

                 <table class="w-full table-fixed border-collapse border-spacing-y-0">
                    <tr>
                        <th class="w-[30%] bg-dark-green text-white font-bold py-2 rounded-tl-[15px]">LAB TEST</th>
                        <th class="w-[30%] bg-dark-green text-white font-bold py-2">RESULT</th>
                        <th class="w-[40%] bg-dark-green text-white font-bold py-2 rounded-tr-[15px]">PEDIATRIC NORMAL RANGE</th>
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
                            'Basophils (%)' => 'basophils'
                        ];
                    @endphp

                    @foreach ($labTests as $label => $name)
                        <tr>
                            <td class="p-2 font-semibold">{{ $label }}</td>
                            <td class="p-2">
                                <input type="number" step="any" name="{{ $name }}_result" placeholder="Result"
                                    value="{{ old($name . '_result', optional($labValue)->{$name . '_result'}) }}"
                                    class="w-full h-[20px] cdss-input" data-field-name="{{ $name }}_result">
                            </td>
                            <td class="p-2">
                                <input type="text" name="{{ $name }}_normal_range" placeholder="Normal Range"
                                    value="{{ old($name . '_normal_range', optional($labValue)->{$name . '_normal_range'}) }}"
                                    class="w-full">
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>

            {{-- for alerts, not sure if connected to sa row ng other table for input okei --}}
            <div class="w-[30%] rounded-[15px] overflow-hidden">
                <table class="w-full border-collapse text-center">
                    <tr>
                        <th class="bg-dark-green text-white py-2 rounded-[15px]">ALERTS</th>
                    </tr>

                    @foreach ($labTests as $label => $name)
                        <tr>
                            <td class="align-middle alert-box" data-alert-for="{{ $name }}_result">
                                {{-- Alert content will be dynamically loaded by alert.js --}}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </center>

    {{-- BUTTONS --}}
    <div class="w-[70%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
        <button type="button" class="button-default">CDSS</button>
        <button type="submit" class="button-default">SUBMIT</button>
    </div>
</fieldset>
</form>
</div>

@endsection

@push('styles')
    @vite(['resources/css/lab-values.css'])
@endpush

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
@endpush

