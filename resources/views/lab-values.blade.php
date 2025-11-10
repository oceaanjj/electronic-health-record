@extends('layouts.app')

@section('title', 'Patient Lab Values')

@section('content')

<h2 class="text-[45px] font-black mb-10 text-dark-green text-center font-alte mx-auto my-12">
        LAB VALUES
    </h2>

    <div id="form-content-container">
        @if (!session('selected_patient_id'))
            <div
                class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                <span class="text-gray-600 font-creato">Please select a patient to input</span>
            </div>
        @endif

        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
            selectRoute="{{ route('lab-values.select') }}" inputPlaceholder="-Select or type to search-"
            inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />

        <form action="{{ route('lab-values.store') }}" method="POST" class="cdss-form"
            data-analyze-url="{{ route('lab-values.run-cdss-field') }}">
            @csrf
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>

                {{-- MAIN CONTENT - SAME STRUCTURE AS VITAL SIGNS --}}
                <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">

                    {{-- LEFT SIDE: LAB VALUES TABLE --}}
                    <div class="w-[68%] rounded-[15px] overflow-hidden">
                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="w-[30%] main-header rounded-tl-[15px]">LAB TEST</th>
                                <th class="w-[30%] main-header">RESULT</th>
                                <th class="w-[50%] main-header rounded-tr-[15px]">
                                    NORMAL RANGE
                                </th>
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
                                <tr class="border-b-2 border-line-brown/70">
                                    <td class="p-2 font-semibold bg-yellow-light text-brown text-center">
                                        {{ $label }}
                                    </td>
                                    <td class="p-2 bg-beige text-center">
                                        <input type="number" step="any" name="{{ $name }}_result" placeholder="Result"
                                            value="{{ old($name . '_result', optional($labValue)->{$name . '_result'}) }}"
                                            class="w-full h-[40px] focus:outline-none text-center cdss-input" data-field-name="{{ $name }}_result">
                                    </td>
                                    <td class="p-2 bg-beige text-center">
                                        <input type="text" name="{{ $name }}_normal_range" placeholder="Normal Range"
                                            value="{{ old($name . '_normal_range', optional($labValue)->{$name . '_normal_range'}) }}"
                                            class="w-full h-[40px] focus:outline-none text-center">
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    {{-- ALERTS TABLE--}}
                    <div class="w-[25%] rounded-[15px] overflow-hidden">
                        <div class="main-header rounded-[15px]">
                            ALERTS
                        </div>

                        <table class="w-full border-collapse text-center">
                            @foreach ($labTests as $label => $name)
                                <tr>
                                    <td class="align-middle">
                                        <div class="alert-box my-1 h-[53px] flex justify-center items-center flex-col px-2"
                                            data-alert-for="{{ $name }}_result">
                                            <span class="opacity-70 text-white font-semibold">No Alerts</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="w-[66%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
                    @if (isset($labValue))
                        <a href="{{ route('nursing-diagnosis.start', ['component' => 'lab-values', 'id' => $labValue->id]) }}"
                            class="button-default text-center">
                            CDSS
                        </a>
                    @endif
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>

            </fieldset>

        </form>
        </fieldset>

@endsection

    @push('styles')
        @vite(['resources/css/lab-values.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const cdssForm = document.querySelector('.cdss-form');
                if (cdssForm && window.initializeCdssForForm) {
                    window.initializeCdssForForm(cdssForm);
                    window.triggerInitialCdssAnalysis(cdssForm);
                }
            });
        </script>
    @endpush