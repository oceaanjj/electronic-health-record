@extends('layouts.app')
@section('title', 'Patient Lab Values')
@section('content')

    <div id="form-content-container">

        {{-- 1. STRUCTURED HEADER (Layout & CDSS Banner) --}}
        <div class="mx-auto mt-6 w-[80%] space-y-4">

            {{-- CDSS ALERT BANNER --}}
            @if (session('selected_patient_id') && isset($labValue))
                <div
                    class="relative flex items-center justify-between py-3 px-5 border border-amber-400/50 rounded-lg shadow-sm bg-amber-100/70 backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#dcb44e]">info</span>
                        <span class="text-sm font-semibold text-[#dcb44e]">
                            Clinical Decision Support System is now available for this date.
                        </span>
                    </div>
                    <button type="button" onclick="this.closest('.relative').remove()" class="text-amber-700">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
            @endif

            {{-- PATIENT SELECTION ROW --}}
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-6">

                    <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                        selectRoute="{{ route('lab-values.select') }}" inputPlaceholder="-Select or type to search-"
                        inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />
                </div>

                {{-- NOT AVAILABLE FOOTER --}}
                @if (session('selected_patient_id') && !isset($labValue))
                    <div class="text-xs text-gray-500 italic flex items-center gap-2 px-2">
                        <span class="material-symbols-outlined text-[14px]">pending_actions</span>
                        Clinical Decision Support System is not yet available (No lab records found).
                    </div>
                @endif
            </div>

            <form action="{{ route('lab-values.store') }}" method="POST" class="cdss-form"
                data-analyze-url="{{ route('lab-values.run-cdss-field') }}"
                data-batch-analyze-url="{{ route('lab-values.analyze-batch') }}" data-alert-height-class="h-[49.5px]">
                @csrf
                <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

                <fieldset @if (!session('selected_patient_id')) disabled @endif>

                    {{-- MAIN CONTENT - SAME STRUCTURE AS VITAL SIGNS --}}
                    <div class="w-[85%] mx-auto flex justify-center items-start gap-1 mt-6">

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
                                            <input type="text" name="{{ $name }}_result" placeholder="Result"
                                                value="{{ old($name . '_result', optional($labValue)->{$name . '_result'}) }}"
                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                class="w-full h-[40px] focus:outline-none text-center cdss-input"
                                                data-field-name="{{ $name }}_result">
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

                            <table class="w-full border-collapse">
                                @foreach ($labTests as $label => $name)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="alert-box my-1 h-[49.5px] flex justify-center items-center text-center px-2"
                                                data-alert-for="{{ $name }}_result">
                                                <span class="opacity-70 text-white font-semibold">NO ALERTS</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="w-[80%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
                        @if (isset($labValue))
                            <a href="{{ route('nursing-diagnosis.start', ['component' => 'lab-values', 'id' => $labValue->id]) }}"
                                class="button-default cdss-btn text-center">
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
            @vite([
                'resources/js/alert.js',
                'resources/js/patient-loader.js',
                'resources/js/searchable-dropdown.js'
            ])
        @endpush