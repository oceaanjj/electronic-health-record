<div class="header mx-auto my-10 flex w-[70%] items-center gap-4">
    <label for="patient_search_input" class="font-alte text-dark-green font-bold whitespace-nowrap">
        PATIENT NAME :
    </label>

    <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ $selectRoute }}">
        <input
            type="text"
            id="patient_search_input"
            placeholder="{{ $inputPlaceholder }}"
            value="{{ trim($selectedPatient->name ?? '') }}"
            autocomplete="off"
            class="font-creato-bold w-full rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
        />

        {{-- Dropdown options --}}
        <div
            id="patient_options_container"
            class="absolute z-50 mt-2 hidden max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
        >
            @foreach ($patients as $patient)
                <div
                    class="option cursor-pointer px-4 py-2 transition duration-150 hover:bg-blue-100"
                    data-value="{{ $patient->patient_id }}"
                >
                    {{ trim($patient->name) }}
                </div>
            @endforeach
        </div>

        {{-- Hidden input to store selected patient ID --}}
        <input
            type="hidden"
            id="patient_id_hidden"
            name="{{ $inputName ?? 'patient_id' }}"
            value="{{ $inputValue ?? ($selectedPatient->patient_id ?? '') }}"
        />
    </div>
</div>
