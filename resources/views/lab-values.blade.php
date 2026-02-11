@extends('layouts.app')
@section('title', 'Patient Lab Values')
@section('content')

    <div id="form-content-container">
        {{-- CDSS ALERT BANNER --}}
        @if (session('selected_patient_id') && isset($labValue))
            <div id="cdss-alert-wrapper" class="w-full px-5 overflow-hidden transition-all duration-500">
                <div id="cdss-alert-content" 
                    class="relative flex items-center justify-between mt-3 py-3 px-5 border border-amber-400/50 rounded-lg shadow-sm bg-amber-100/70 backdrop-blur-md animate-alert-in">
                    
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#dcb44e] animate-pulse">info</span>
                        <span class="text-sm font-semibold text-[#dcb44e]">
                            Clinical Decision Support System is now available for this date.
                        </span>
                    </div>

                    <button type="button" onclick="closeCdssAlert()"
                        class="group flex items-center justify-center text-amber-700 hover:bg-amber-200/50 rounded-full p-1 transition-all duration-300 active:scale-90">
                        <span class="material-symbols-outlined text-[20px] group-hover:rotate-90 transition-transform duration-300">
                            close
                        </span>
                    </button>
                </div>
            </div>
        @endif

        {{-- HEADER SECTION --}}
        <div class="m-10 ml-20 flex items-center gap-4">
            <label class="font-alte text-dark-green font-bold whitespace-nowrap shrink-0">
                PATIENT NAME :
            </label>
            
            <div class="w-[350px]">
                <x-searchable-patient-dropdown 
                    :patients="$patients" 
                    :selectedPatient="$selectedPatient"
                    selectRoute="{{ route('lab-values.select') }}" 
                    inputPlaceholder="Search or type Patient Name..."
                    inputName="patient_id" 
                    inputValue="{{ session('selected_patient_id') }}" 
                />
            </div>
        </div>

        {{-- NOT AVAILABLE MESSAGE --}}
        @if (session('selected_patient_id') && !isset($labValue))
            <div class="text-xs text-gray-500 italic flex items-center gap-2 mt-4 ml-20">
                <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                Clinical Decision Support System is not yet available (No lab records found).
            </div>
        @endif

        {{-- FORM --}}
        <form 
            action="{{ route('lab-values.store') }}" 
            method="POST" 
            class="cdss-form"
            data-analyze-url="{{ route('lab-values.run-cdss-field') }}"
            data-batch-analyze-url="{{ route('lab-values.analyze-batch') }}" 
            data-alert-height-class="h-[49.5px]"
        >
            @csrf
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    {{-- Title only spans the table width, not the alerts --}}
                    <div class="flex w-[80%] items-center gap-1 mt-2">
                        <div class="flex-1">
                            <p class="main-header mb-1 rounded-[15px]">LABORATORY VALUES</p>
                        </div>
                        <div class="w-[60px]"></div>
                    </div>
                </center>

                <center>
                    {{-- LAB VALUES TABLE WITH ALERTS --}}
                    <div class="mb-1.5 flex w-[80%] items-start gap-1">
                        <div class="w-full rounded-[15px] overflow-hidden flex-1">
                            <table class="w-full table-fixed border-collapse border-spacing-y-0">
                                <tr>
                                    <th class="w-[20%] main-header rounded-tl-[15px]">LAB TEST</th>
                                    <th class="w-[30%] main-header">RESULT</th>
                                    <th class="w-[50%] main-header rounded-tr-[15px]">NORMAL RANGE</th>
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
                                        <td class="p-2 font-semibold bg-yellow-light text-brown text-center">
                                            {{ $label }}
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input 
                                                type="text" 
                                                name="{{ $name }}_result" 
                                                placeholder="Result"
                                                value="{{ old($name . '_result', optional($labValue)->{$name . '_result'}) }}"
                                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                class="w-full h-[40px] focus:outline-none text-center cdss-input"
                                                data-field-name="{{ $name }}_result"
                                            />
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input 
                                                type="text" 
                                                name="{{ $name }}_normal_range" 
                                                placeholder="Normal Range"
                                                value="{{ old($name . '_normal_range', optional($labValue)->{$name . '_normal_range'}) }}"
                                                class="w-full h-[40px] focus:outline-none text-center"
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        {{-- ALERTS COLUMN --}}
                        <div class="w-[60px] flex flex-col">
                            {{-- Empty header space to align with table header --}}
                            <div class="h-[42px]"></div>
                            
                            @foreach ($labTests as $label => $name)
                                <div class="flex h-[56px] w-[70px] pl-5 items-center justify-center" data-alert-for="{{ $name }}_result">
                                    <div class="alert-icon-btn is-empty">
                                        <span class="material-symbols-outlined">notifications</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </center>

                {{-- BUTTONS --}}
                <div class="mx-auto mt-5 mb-30 flex w-[80%] flex-row justify-end gap-4">
                    @if (isset($labValue))
                        <a 
                            href="{{ route('nursing-diagnosis.start', ['component' => 'lab-values', 'id' => $labValue->id]) }}"
                            class="button-default cdss-btn inline-block text-center"
                        >
                            CDSS
                        </a>
                    @endif
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
    @vite([
        'resources/js/alert.js',
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/close-cdss-alert.js'
    ])
@endpush