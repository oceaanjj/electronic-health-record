@extends('layouts.app')


@section('title', 'Patient Activities of Daily Living')


@section('content')

    <!-- ALERT MESSAGE -->
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
            <label for="patient_id">PATIENT NAME :</label>

            {{-- PATIENT DROPDOWN AND DATE FORM (TO GET DATA FROM PATIENT & DATE --}}
            <form action="{{ route('adl.show') }}" method="GET" id="patient-select-form">

                <label for="patient_id">PATIENT NAME :</label>
                <select id="patient_info" name="patient_id"
                    onchange="document.getElementById('patient-select-form').submit()">
                    <option value="" @if(request()->query('patient_id') == '') selected @endif>-- Select Patient --</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->patient_id }}" @if(request()->query('patient_id') == $patient->patient_id)
                        selected @endif>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>

                <!-- DATE -->
                <label for="date">DATE :</label>
                <input type="date" id="date_selector" name="date" value="{{ request()->query('date') }}"
                    onchange="document.getElementById('patient-select-form').submit()">
            </form>
        </div>

        {{-- MAIN FORM (sumbit) --}}
        <form id="adl-form" method="POST" action="{{ route('adl.store') }}">
            @csrf

            {{-- Hidden PATIENT_ID AND DATE --}}
            <input type="hidden" name="patient_id" value="{{ request()->query('patient_id') }}">
            <input type="hidden" name="date" value="{{ request()->query('date') }}">

            <!-- DAY -->
            <div class="section-bar">
                <label for="day">DAY NO :</label>
                <select id="day" name="day_no">
                    <option value="">-- Select number --</option>
                    @for ($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}" @if(old('day_no', $adlData->day_no ?? '') == $i) selected @endif>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>


            <table>
                <tr>
                    <th class="title">CATEGORY</th>
                    <th class="title">ASSESSMENT</th>
                    <th class="title">ALERTS</th>
                </tr>

                <tr>
                    <th class="title">MOBILITY</th>
                    <td>
                        <input type="text" name="mobility_assessment" placeholder="mobility"
                            value="{{ old('mobility_assessment', $adlData->mobility_assessment ?? '') }}">
                    </td>
                    <td>
                        @error('mobility_assessment')
                            <div class="alert-box alert-red">
                                <span class="alert-message">{{ $message }}</span>
                            </div>
                        @enderror

                        @if (session('cdss.mobility_assessment'))
                            @php
                                $alertData = session('cdss.mobility_assessment');
                                $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                            @endphp
                            <div class="alert-box {{ $color }}">
                                <span class="alert-message">{{ $alertData['alert'] }}</span>
                            </div>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th class="title">HYGIENE</th>
                    <td>
                        <input type="text" name="hygiene_assessment" placeholder="hygiene"
                            value="{{ old('hygiene_assessment', $adlData->hygiene_assessment ?? '') }}">
                    </td>
                    <td>
                        @error('hygiene_assessment')
                            <div class="alert-box alert-red">
                                <span class="alert-message">{{ $message }}</span>
                            </div>
                        @enderror

                        @if (session('cdss.hygiene_assessment'))
                            @php
                                $alertData = session('cdss.hygiene_assessment');
                                $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                            @endphp
                            <div class="alert-box {{ $color }}">
                                <span class="alert-message">{{ $alertData['alert'] }}</span>
                            </div>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th class="title">TOILETING</th>
                    <td>
                        <input type="text" name="toileting_assessment" placeholder="toileting"
                            value="{{ old('toileting_assessment', $adlData->toileting_assessment ?? '') }}">
                    </td>
                    <td>
                        @error('toileting_assessment')
                            <div class="alert-box alert-red">
                                <span class="alert-message">{{ $message }}</span>
                            </div>
                        @enderror

                        @if (session('cdss.toileting_assessment'))
                            @php
                                $alertData = session('cdss.toileting_assessment');
                                $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                            @endphp
                            <div class="alert-box {{ $color }}">
                                <span class="alert-message">{{ $alertData['alert'] }}</span>
                            </div>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th class="title">FEEDING</th>
                    <td>
                        <input type="text" name="feeding_assessment" placeholder="feeding"
                            value="{{ old('feeding_assessment', $adlData->feeding_assessment ?? '') }}">
                    </td>
                    <td>
                        @error('feeding_assessment')
                            <div class="alert-box alert-red">
                                <span class="alert-message">{{ $message }}</span>
                            </div>
                        @enderror

                        @if (session('cdss.feeding_assessment'))
                            @php
                                $alertData = session('cdss.feeding_assessment');
                                $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                            @endphp
                            <div class="alert-box {{ $color }}">
                                <span class="alert-message">{{ $alertData['alert'] }}</span>
                            </div>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th class="title">HYDRATION</th>
                    <td>
                        <input type="text" name="hydration_assessment" placeholder="hydration"
                            value="{{ old('hydration_assessment', $adlData->hydration_assessment ?? '') }}">
                    </td>
                    <td>
                        @error('hydration_assessment')
                            <div class="alert-box alert-red">
                                <span class="alert-message">{{ $message }}</span>
                            </div>
                        @enderror

                        @if (session('cdss.hydration_assessment'))
                            @php
                                $alertData = session('cdss.hydration_assessment');
                                $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                            @endphp
                            <div class="alert-box {{ $color }}">
                                <span class="alert-message">{{ $alertData['alert'] }}</span>
                            </div>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th class="title">SLEEP PATTERN</th>
                    <td>
                        <input type="text" name="sleep_pattern_assessment" placeholder="sleep pattern"
                            value="{{ old('sleep_pattern_assessment', $adlData->sleep_pattern_assessment ?? '') }}">
                    </td>
                    <td>
                        @error('sleep_pattern_assessment')
                            <div class="alert-box alert-red">
                                <span class="alert-message">{{ $message }}</span>
                            </div>
                        @enderror

                        @if (session('cdss.sleep_pattern_assessment'))
                            @php
                                $alertData = session('cdss.sleep_pattern_assessment');
                                $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                            @endphp
                            <div class="alert-box {{ $color }}">
                                <span class="alert-message">{{ $alertData['alert'] }}</span>
                            </div>
                        @endif
                    </td>
                </tr>

                <tr>
                    <th class="title">PAIN LEVEL</th>
                    <td>
                        <input type="text" name="pain_level_assessment" placeholder="pain level"
                            value="{{ old('pain_level_assessment', $adlData->pain_level_assessment ?? '') }}">
                    </td>
                    <td>
                        @error('pain_level_assessment')
                            <div class="alert-box alert-red">
                                <span class="alert-message">{{ $message }}</span>
                            </div>
                        @enderror
                        @if (session('cdss.pain_level_assessment'))
                            @php
                                $alertData = session('cdss.pain_level_assessment');
                                $color = ($alertData['severity'] === 'CRITICAL') ? 'alert-red' : (($alertData['severity'] === 'WARNING') ? 'alert-orange' : 'alert-green');
                            @endphp
                            <div class="alert-box {{ $color }}">
                                <span class="alert-message">{{ $alertData['alert'] }}</span>
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
    </div>

    <div class="buttons">
        <button class="btn" type="button">CDSS</button>
        <button class="btn" type="submit">Submit</button>
    </div>
    </form>
@endsection

@push('styles')
    @vite('resources/css/act-of-daily-living.css')
@endpush