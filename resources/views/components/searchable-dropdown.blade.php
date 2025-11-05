<div class="searchable-dropdown" data-select-url="{{ $selectUrl }}" style="min-width: 250px;">
    <label for="patient_search_input" style="white-space: nowrap;">PATIENT NAME :</label>
    <input type="text" id="patient_search_input" placeholder="-Select or type to search-"
        value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off">
    <div id="patient_options_container">
        @foreach ($patients as $patient)
            <div class="option" data-value="{{ $patient->patient_id }}">
                {{ trim($patient->name) }}
            </div>
        @endforeach
    </div>
</div>
{{-- This hidden input will hold the selected patient's ID for the main form and for the Date/Day logic --}}
<input type="hidden" name="patient_id_for_form" id="patient_id_hidden" value="{{ session('selected_patient_id') }}">