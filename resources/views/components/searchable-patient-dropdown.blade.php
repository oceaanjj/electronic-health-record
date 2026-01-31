@props([
    'patients',
    'selectedPatient',
    'selectRoute',
    'inputPlaceholder' => 'Select or type Patient Name',
    'inputName' => 'patient_id',
    'inputValue' => '',
    'cdssAvailable' => null
])

<div class="header flex items-center gap-4 my-10 mx-auto w-[70%]">
    <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
        PATIENT NAME :
    </label>

    <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ $selectRoute }}">
        <input type="text" id="patient_search_input" placeholder="{{ $inputPlaceholder }}"
            value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off"
            class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">

        {{-- Dropdown options --}}
        <div id="patient_options_container"
            class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
            @foreach ($patients as $patient)
                <div class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150"
                    data-value="{{ $patient->patient_id }}">
                    {{ trim($patient->name) }}
                </div>
            @endforeach
        </div>

        {{-- Hidden input to store selected patient ID --}}
        <input type="hidden" id="patient_id_hidden" name="{{ $inputName ?? 'patient_id' }}"
            value="{{ $inputValue ?? ($selectedPatient->patient_id ?? '') }}">
        
        {{-- CDSS Availability Message --}}
        @if (!is_null($cdssAvailable) && $selectedPatient)
            @if ($cdssAvailable)
                <div class="mt-2 text-xs text-green-600 font-bold ml-4">
                    Clinical Decision Support System is now available
                </div>
            @else
                <div class="mt-2 text-xs text-gray-500 italic ml-4">
                    Clinical Decision Support System is not yet available
                </div>
            @endif
        @endif
    </div>
</div>