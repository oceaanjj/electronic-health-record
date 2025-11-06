@extends('layouts.app')
@section('title', 'Patient Activities of Daily Living')
@section('content')

<div class="header flex items-center gap-4">
    <label for="patient_search_input" class="text-black whitespace-nowrap">
        PATIENT NAME :
    </label>

    <div class="searchable-dropdown relative w-[280px]" data-select-url="{{ route('adl.select') }}">
        <input 
            type="text" 
                    id="patient_search_input"
                    placeholder="- Select or type to search -" 
                    value="{{ trim($selectedPatient->name ?? '') }}"
                    autocomplete="off"
                    class="w-full px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
        >

        {{-- dito yung dropdown options --}}
        <div id="patient_options_container" 
            class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
            @foreach ($patients as $patient)
                <div 
                    class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150" 
                    data-value="{{ $patient->patient_id }}">
                    {{ trim($patient->name) }}
                </div>
            @endforeach
        </div>
        <input type="hidden" id="patient_id_hidden" name="patient_id">
    </div>
    

    {{-- Hidden input for patient ID --}}
    <input type="hidden" name="patient_id_for_form" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">

    {{-- 📅 DATE SELECTOR --}}
    <label for="date_selector" class="text-white whitespace-nowrap font-medium">DATE :</label>
    <input 
        class="date border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 focus:outline-none transition duration-200" 
        type="date" 
        id="date_selector" 
        name="date"
        value="{{ $currentDate ?? now()->format('Y-m-d') }}"
        @if (!$selectedPatient) disabled @endif
    >

    {{-- 🔢 DAY SELECTOR --}}
    <label for="day_no_selector" class="text-white whitespace-nowrap font-medium">DAY NO :</label>
    <select 
        id="day_no_selector" 
        name="day_no" 
        class="border border-gray-300 rounded-lg px-3 py-2 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-300 focus:outline-none transition duration-200"
        @if (!$selectedPatient) disabled @endif
    >
        <option value="">-- Day --</option>
        @for ($i = 1; $i <= 30; $i++)
            <option value="{{ $i }}" @if(($currentDayNo ?? 1) == $i) selected @endif>
                {{ $i }}
            </option>
        @endfor
    </select>
</div>
{{-- 🌼 END HEADER --}}



 <div id="form-content-container">
        {{-- DISABLED input overlay --}}
        @if (!session('selected_patient_id'))
            <div class="form-overlay">
                <span>Please select a patient first to input</span> {{-- message --}}
            </div>
        @endif


        <form id="adl-form" method="POST" action="{{ route('adl.store') }}" class="cdss-form"
            data-analyze-url="{{ route('adl.analyze-field') }}">
            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                @csrf

                {{-- Hidden PATIENT_ID AND DATE/DAY from header elements --}}
                <input type="hidden" name="patient_id" class="patient-id-input" value="{{ session('selected_patient_id') }}">
                {{-- Use the explicit variables for consistency in form submission --}}
                <input type="hidden" name="date" class="date-input" value="{{ $currentDate ?? session('selected_date') }}">
                <input type="hidden" name="day_no" class="day-no-input" value="{{ $currentDayNo ?? session('selected_day_no') }}">

                {{-- START COPY OF VITAL SIGNS LAYOUT STYLE --}}
                <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
                    {{-- LEFT SIDE TABLE (INPUTS) --}}
                    <div class="w-[68%] rounded-[15px] overflow-hidden">
                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="w-[40%] bg-dark-green text-white font-bold py-2 text-center rounded-tl-lg">CATEGORY</th>
                                <th class="w-[60%] bg-dark-green text-white rounded-tr-lg">ASSESSMENT</th>
                            </tr>

                            @php
                                // Helper to get alert data and color
                                function getAlertData($field, $session)
                                {
                                    $alertData = $session->get("cdss.$field");
                                    if (!$alertData)
                                        return ['alert' => null, 'color' => null];

                                    $color = 'text-white';
                                    if ($alertData['severity'] === 'CRITICAL') $color = 'text-red-600';
                                    elseif ($alertData['severity'] === 'WARNING') $color = 'text-orange-500';
                                    elseif ($alertData['severity'] === 'INFO') $color = 'text-blue-500';

                                    return ['alert' => $alertData['alert'], 'color' => $color];
                                }
                            @endphp

                            @foreach ([
                                'mobility_assessment' => 'MOBILITY',
                                'hygiene_assessment' => 'HYGIENE',
                                'toileting_assessment' => 'TOILETING',
                                'feeding_assessment' => 'FEEDING',
                                'hydration_assessment' => 'HYDRATION',
                                'sleep_pattern_assessment' => 'SLEEP PATTERN',
                                'pain_level_assessment' => 'PAIN LEVEL',
                            ] as $field => $label)
                                @php
                                    $alert = getAlertData($field, session());
                                @endphp
                                <tr class="border-b-2 border-line-brown/70">
                                    {{-- CATEGORY --}}
                                    <th class="text-center font-semibold py-2 bg-yellow-light text-brown">
                                        {{ $label }}
                                    </th>

                                    {{-- ASSESSMENT INPUT --}}
                                    <td class="bg-beige">
                                        <input type="text" name="{{ $field }}" placeholder="{{ strtolower($label) }}"
                                            class="cdss-input vital-input h-[60px]"
                                            data-field-name="{{ $field }}"
                                            value="{{ old($field, $adlData->$field ?? '') }}">
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    {{-- ALERTS TABLE --}}
                    <div class="w-[25%] rounded-[15px] overflow-hidden">
                        <div class="bg-dark-green text-white font-bold py-2 mb-1 text-center rounded-[15px]">
                            ALERTS
                        </div>

                        <table class="w-full border-collapse">
                            @foreach ([
                                'mobility_assessment',
                                'hygiene_assessment',
                                'toileting_assessment',
                                'feeding_assessment',
                                'hydration_assessment',
                                'sleep_pattern_assessment',
                                'pain_level_assessment',
                            ] as $field)
                                @php
                                    $alert = getAlertData($field, session());
                                @endphp
                                <tr>
                                    <td class="align-middle">
                                        <div class="alert-box my-[3px] h-[53px] flex justify-center items-center">
                                            @if ($alert['alert'])
                                                <span class="font-semibold {{ $alert['color'] }}">{{ $alert['alert'] }}</span>
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


       
                <div class="w-[65%] mx-auto flex justify-end mt-5 mb-30 space-x-4">
                    <button type="button" class="button-default">CDSS</button>
                    <button type="submit" class="button-default">SUBMIT</button>       
                </div>
            </fieldset>
        </form>
    </div>
@endsection


@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush


