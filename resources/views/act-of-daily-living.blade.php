@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')


    <div class="header">

        {{-- PATIENT DROPDOWN AND DATE FORM (TO GET DATA FROM PATIENT & DATE --}}
        <form action="{{ route('adl.select') }}" method="POST" id="patient-select-form" class="flex items-center space-x-4">
            @csrf

            <!-- <label for="patient_id" style="color: white;">PATIENT NAME :</label>
                                                                                    <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                                                                                        <option value="" @if(session('selected_patient_id') == '') selected @endif>-- Select Patient --</option>
                                                                                        @foreach ($patients as $patient)
                                                                                            <option value="{{ $patient->patient_id }}" @if(session('selected_patient_id') == $patient->patient_id)
                                                                                            selected @endif>
                                                                                                {{ $patient->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select> -->

            {{-- This is the new searchable dropdown structure --}}
            <label for="patient_search_input" style="color: white;">PATIENT NAME :</label>

            <div class="searchable-dropdown">
                {{-- This is the text input the user interacts with --}}
                <input type="text" id="patient_search_input" placeholder="-- Select or type to search --"
                    value="{{ $selectedPatient ? $selectedPatient->name : '' }}" autocomplete="off">

                {{-- This container will hold the list of selectable patients --}}
                <div id="patient_options_container">
                    @foreach ($patients as $patient)
                        <div class="option" data-value="{{ $patient->patient_id }}">
                            {{ $patient->name }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- This crucial hidden input stores the selected patient_id for form submission --}}
            <input type="hidden" name="patient_id" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">



            <!-- DATE -->
            <label for="date" style="color: white;">DATE :</label>

            {{-- new date --}}
            <input class="date" type="date" id="date_selector" name="date"
                value="{{ session('selected_date') ?? ($selectedPatient ? $selectedPatient->admission_date : now()->format('Y-m-d')) }}"
                onchange="this.form.submit()">

            <!-- DAY NO -->
            <label for="day_no" style="color: white;">DAY NO :</label>
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

    {{-- MAIN FORM (sumbit) --}}
    <form id="adl-form" method="POST" action="{{ route('adl.store') }}">
        @csrf

        {{-- Hidden PATIENT_ID AND DATE --}}
        <input type="hidden" name="patient_id" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">
        <input type="hidden" name="date" value="{{ session('selected_date') }}">
        <input type="hidden" name="day_no" value="{{ session('selected_day_no') }}">

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


@push('scripts')
    @vite('resources/js/search.js')
@endpush