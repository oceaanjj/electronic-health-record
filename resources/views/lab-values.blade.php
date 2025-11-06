@extends('layouts.app')

@section('title', 'Patient Lab Values')

@section('content')

    <div class="header">
        <label for="patient_info" style="color: white;">PATIENT NAME :</label>
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

    {{-- MAIN CONTENT - SAME STRUCTURE AS VITAL SIGNS --}}
    <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">

        {{-- LEFT SIDE: LAB VALUES TABLE --}}
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
                    <tr class="border-b-2 border-line-brown/70">
                        <td class="p-2 font-semibold bg-yellow-light text-brown text-center">
                            {{ $label }}
                        </td>
                        <td class="p-2 bg-beige text-center">
                            <input type="number" step="any" name="{{ $name }}_result" placeholder="Result"
                                value="{{ old($name . '_result', optional($labValue)->{$name . '_result'}) }}"
                                class="w-full h-[40px] text-center">
                        </td>
                        <td class="p-2 bg-beige text-center">
                            <input type="text" name="{{ $name }}_normal_range" placeholder="Normal Range"
                                value="{{ old($name . '_normal_range', optional($labValue)->{$name . '_normal_range'}) }}"
                                class="w-full h-[40px] text-center">
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        {{-- ALERTS TABLE--}}
        <div class="w-[25%] rounded-[15px] overflow-hidden">
            <div class="bg-dark-green text-white font-bold py-2 mb-1 text-center rounded-[15px]">
                ALERTS
            </div>

            <table class="w-full border-collapse text-center">
                @foreach ($labTests as $label => $name)
                    <tr>
                        <td class="align-middle">
                            <div class="alert-box my-[3px] h-[53px] flex justify-center items-center flex-col px-2">
                                @if (session('alerts') && isset(session('alerts')[$name . '_alerts']))
                                    @foreach (session('alerts')[$name . '_alerts'] as $alertData)
                                        @php
                                            $alertText = $alertData['text'];
                                            $severity = $alertData['severity'];
                                            $color = match($severity) {
                                                \App\Services\LabValuesCdssService::CRITICAL => 'text-red-600 font-bold',
                                                \App\Services\LabValuesCdssService::WARNING => 'text-orange-500',
                                                \App\Services\LabValuesCdssService::INFO => 'text-blue-500',
                                                \App\Services\LabValuesCdssService::NONE => 'text-green-600',
                                                default => 'text-gray-600',
                                            };
                                        @endphp
                                        <p class="{{ $color }} text-sm leading-snug">{{ $alertText }}</p>
                                    @endforeach
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

    {{-- BUTTONS --}}
    <div class="w-[70%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
        <button type="button" class="button-default">CDSS</button>
        <button type="submit" class="button-default">SUBMIT</button>
    </div>

    </form>

@endsection

@push('styles')
    @vite(['resources/css/lab-values.css'])
@endpush
