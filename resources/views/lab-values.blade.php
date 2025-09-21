<head>
    <meta charset="UTF-8">
    <title>Patient Lab Values</title>
    @vite(['./resources/css/lab-values.css'])
</head>

@extends('layouts.app')

@section('title', 'Patient Lab Values')

@section('content')

    <!-- ALERTS AND MESSAGES -->
    @if ($errors->any())
        <div style="color:red; margin-bottom:5px; padding:5px;">
            <h5 style="margin-bottom: 10px;">Errors:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div style="background-color:green; color:white; padding:1rem; text-align:center; margin:1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
        <div class="header">
            {{-- PATIENT DROPDOWN AND DATE FORM --}}
            <form action="{{ route('lab-values.filter') }}" method="POST" id="patient-select-form">
                @csrf
                <label for="patient_id">PATIENT NAME :</label>
                <select id="patient_info" name="patient_id"
                    onchange="document.getElementById('patient-select-form').submit()">
                    <option value="" @if($patientId == '') selected @endif>-- Select Patient --</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}" @if($patientId == $patient->id) selected @endif>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>
                <label for="record_date">DATE :</label>
                <input type="date" id="record_date" name="record_date" value="{{ $recordDate }}"
                    onchange="document.getElementById('patient-select-form').submit()">
            </form>
        </div>
    </div>

    {{-- MAIN FORM for Lab Values Submission --}}
    <form id="lab-values-form" method="POST" action="{{ route('lab-values.store') }}">
        @csrf

        {{-- Hidden inputs to pass patient and date data --}}
        <input type="hidden" name="patient_id" value="{{ $patientId }}">
        <input type="hidden" name="record_date" value="{{ $recordDate }}">

        <table>
            <tr>
                <th class="title">LAB TEST</th>
                <th class="title">RESULT</th>
                <th class="title">PEDIATRIC NORMAL RANGE</th>
                <th class="title">ALERTS</th>
            </tr>

            {{-- WBC (White Blood Cell Count) --}}
            <tr>
                <th class="title">WBC (×10⁹/L)</th>
                <td><input type="text" name="lab_tests[wbc][result]" placeholder="result"
                        value="{{ old('lab_tests.wbc.result', $labValues->wbc_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[wbc][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.wbc.normal_range', $labValues->wbc_normal_range ?? '') }}"></td>
                <td>
                    @if (session('cdss.wbc'))
                        <div
                            class="alert-box {{ (session('cdss.wbc')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.wbc')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.wbc')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- RBC (Red Blood Cell Count) --}}
            <tr>
                <th class="title">RBC (×10¹²/L)</th>
                <td><input type="text" name="lab_tests[rbc][result]" placeholder="result"
                        value="{{ old('lab_tests.rbc.result', $labValues->rbc_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[rbc][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.rbc.normal_range', $labValues->rbc_normal_range ?? '') }}"></td>
                <td>
                    @if (session('cdss.rbc'))
                        <div
                            class="alert-box {{ (session('cdss.rbc')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.rbc')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.rbc')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- Hgb (Hemoglobin) --}}
            <tr>
                <th class="title">Hgb (g/dL)</th>
                <td><input type="text" name="lab_tests[hgb][result]" placeholder="result"
                        value="{{ old('lab_tests.hgb.result', $labValues->hgb_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[hgb][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.hgb.normal_range', $labValues->hgb_normal_range ?? '') }}"></td>
                <td>
                    @if (session('cdss.hgb'))
                        <div
                            class="alert-box {{ (session('cdss.hgb')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.hgb')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.hgb')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- Hct (Hematocrit) --}}
            <tr>
                <th class="title">Hct (%)</th>
                <td><input type="text" name="lab_tests[hct][result]" placeholder="result"
                        value="{{ old('lab_tests.hct.result', $labValues->hct_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[hct][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.hct.normal_range', $labValues->hct_normal_range ?? '') }}"></td>
                <td>
                    @if (session('cdss.hct'))
                        <div
                            class="alert-box {{ (session('cdss.hct')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.hct')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.hct')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- Platelets --}}
            <tr>
                <th class="title">Platelets (×10⁹/L)</th>
                <td><input type="text" name="lab_tests[platelets][result]" placeholder="result"
                        value="{{ old('lab_tests.platelets.result', $labValues->platelets_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[platelets][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.platelets.normal_range', $labValues->platelets_normal_range ?? '') }}">
                </td>
                <td>
                    @if (session('cdss.platelets'))
                        <div
                            class="alert-box {{ (session('cdss.platelets')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.platelets')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.platelets')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- MCV (Mean Corpuscular Volume) --}}
            <tr>
                <th class="title">MCV (fL)</th>
                <td><input type="text" name="lab_tests[mcv][result]" placeholder="result"
                        value="{{ old('lab_tests.mcv.result', $labValues->mcv_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[mcv][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.mcv.normal_range', $labValues->mcv_normal_range ?? '') }}"></td>
                <td>
                    @if (session('cdss.mcv'))
                        <div
                            class="alert-box {{ (session('cdss.mcv')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.mcv')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.mcv')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- MCH (Mean Corpuscular Hemoglobin) --}}
            <tr>
                <th class="title">MCH (pg)</th>
                <td><input type="text" name="lab_tests[mch][result]" placeholder="result"
                        value="{{ old('lab_tests.mch.result', $labValues->mch_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[mch][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.mch.normal_range', $labValues->mch_normal_range ?? '') }}"></td>
                <td>
                    @if (session('cdss.mch'))
                        <div
                            class="alert-box {{ (session('cdss.mch')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.mch')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.mch')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- MCHC (Mean Corpuscular Hemoglobin Concentration) --}}
            <tr>
                <th class="title">MCHC (g/dL)</th>
                <td><input type="text" name="lab_tests[mchc][result]" placeholder="result"
                        value="{{ old('lab_tests.mchc.result', $labValues->mchc_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[mchc][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.mchc.normal_range', $labValues->mchc_normal_range ?? '') }}"></td>
                <td>
                    @if (session('cdss.mchc'))
                        <div
                            class="alert-box {{ (session('cdss.mchc')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.mchc')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.mchc')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- RDW (Red Cell Distribution Width) --}}
            <tr>
                <th class="title">RDW (%)</th>
                <td><input type="text" name="lab_tests[rdw][result]" placeholder="result"
                        value="{{ old('lab_tests.rdw.result', $labValues->rdw_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[rdw][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.rdw.normal_range', $labValues->rdw_normal_range ?? '') }}"></td>
                <td>
                    @if (session('cdss.rdw'))
                        <div
                            class="alert-box {{ (session('cdss.rdw')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.rdw')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.rdw')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- Neutrophils --}}
            <tr>
                <th class="title">Neutrophils (%)</th>
                <td><input type="text" name="lab_tests[neutrophils][result]" placeholder="result"
                        value="{{ old('lab_tests.neutrophils.result', $labValues->neutrophils_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[neutrophils][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.neutrophils.normal_range', $labValues->neutrophils_normal_range ?? '') }}">
                </td>
                <td>
                    @if (session('cdss.neutrophils'))
                        <div
                            class="alert-box {{ (session('cdss.neutrophils')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.neutrophils')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.neutrophils')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- Lymphocytes --}}
            <tr>
                <th class="title">Lymphocytes (%)</th>
                <td><input type="text" name="lab_tests[lymphocytes][result]" placeholder="result"
                        value="{{ old('lab_tests.lymphocytes.result', $labValues->lymphocytes_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[lymphocytes][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.lymphocytes.normal_range', $labValues->lymphocytes_normal_range ?? '') }}">
                </td>
                <td>
                    @if (session('cdss.lymphocytes'))
                        <div
                            class="alert-box {{ (session('cdss.lymphocytes')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.lymphocytes')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.lymphocytes')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- Monocytes --}}
            <tr>
                <th class="title">Monocytes (%)</th>
                <td><input type="text" name="lab_tests[monocytes][result]" placeholder="result"
                        value="{{ old('lab_tests.monocytes.result', $labValues->monocytes_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[monocytes][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.monocytes.normal_range', $labValues->monocytes_normal_range ?? '') }}">
                </td>
                <td>
                    @if (session('cdss.monocytes'))
                        <div
                            class="alert-box {{ (session('cdss.monocytes')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.monocytes')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.monocytes')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- Eosinophils --}}
            <tr>
                <th class="title">Eosinophils (%)</th>
                <td><input type="text" name="lab_tests[eosinophils][result]" placeholder="result"
                        value="{{ old('lab_tests.eosinophils.result', $labValues->eosinophils_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[eosinophils][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.eosinophils.normal_range', $labValues->eosinophils_normal_range ?? '') }}">
                </td>
                <td>
                    @if (session('cdss.eosinophils'))
                        <div
                            class="alert-box {{ (session('cdss.eosinophils')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.eosinophils')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.eosinophils')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

            {{-- Basophils --}}
            <tr>
                <th class="title">Basophils (%)</th>
                <td><input type="text" name="lab_tests[basophils][result]" placeholder="result"
                        value="{{ old('lab_tests.basophils.result', $labValues->basophils_result ?? '') }}"></td>
                <td><input type="text" name="lab_tests[basophils][normal_range]" placeholder="normal range"
                        value="{{ old('lab_tests.basophils.normal_range', $labValues->basophils_normal_range ?? '') }}">
                </td>
                <td>
                    @if (session('cdss.basophils'))
                        <div
                            class="alert-box {{ (session('cdss.basophils')['severity'] === 'CRITICAL') ? 'alert-red' : ((session('cdss.basophils')['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green') }}">
                            <span class="alert-message">{{ session('cdss.basophils')['alert'] }}</span>
                        </div>
                    @endif
                </td>
            </tr>

        </table>

        <div class="buttons">
            <button class="btn" type="button">CDSS</button>
            <button class="btn" type="submit">Submit</button>
        </div>
    </form>

@endsection

@push('styles')
    @vite(['resources/css/lab-values.css'])
@endpush