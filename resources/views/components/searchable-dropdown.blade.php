<div class="header" style="margin-left:15rem;">
    <label for="patient_search_input">PATIENT NAME :</label>

    {{-- The data-select-url attribute is crucial for patient-loader.js --}}
    <div class="searchable-dropdown" data-select-url="{{ $selectUrl }}">

        {{-- This is the text input the user interacts with --}}
        <input type="text" id="patient_search_input" placeholder="-Select or type to search-"
            value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off">

        {{-- This container will hold the list of selectable patients --}}
        <div id="patient_options_container">
            @foreach ($patients as $patient)
                <div class="option" data-value="{{ $patient->patient_id }}">
                    {{ trim($patient->name) }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- This hidden input will hold the selected patient's ID for the main form --}}
    <input type="hidden" name="patient_id_for_form" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">
</div>
