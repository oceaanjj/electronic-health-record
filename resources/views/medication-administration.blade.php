@extends('layouts.app')
@section('title', 'Patient Vital Signs')
@section('content')

    <div id="form-content-container">
        {{--
        1. FIX: Added 'cdss-form' and 'relative' to the form.
        This allows the JS to find it and place the overlay.
        --}}
        <form method="POST" action="{{ route('medication-administration.store') }}" class="cdss-form relative">
            @csrf

            {{--
            2. FIX: Added 'relative' and 'z-[60]'.
            The disabled overlay has z-index 50. By setting this to 60,
            we ensure the search bar and date selector float ABOVE the overlay,
            keeping them clickable even when the form is "disabled".
            --}}
            <div class="header flex items-center gap-6 my-10 mx-60 w-[100%] relative z-[60]">
                <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
                    PATIENT NAME :
                </label>

                <div class="searchable-dropdown relative w-[280px]">
                    <input type="text" id="patient_search_input" placeholder="- Select or type to search -"
                        value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off"
                        class="w-full px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">

                    <div id="patient_options_container"
                        class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                        @foreach ($patients as $patient)
                            <div class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150"
                                data-value="{{ $patient->patient_id }}">
                                {{ trim($patient->name) }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Hidden IDs required for scripts --}}
                <input type="hidden" name="patient_id" id="patient_id_for_form"
                    value="{{ $selectedPatient->patient_id ?? '' }}">
                {{-- Alias ID for global 'alert.js' compatibility --}}
                <input type="hidden" id="patient_id_hidden" value="{{ $selectedPatient->patient_id ?? '' }}">

                {{-- 📅 DATE SELECTOR --}}
                <label for="date_selector" class="whitespace-nowrap font-alte font-bold text-dark-green">
                    DATE :
                </label>
                <input type="date" id="date_selector" name="date" value="{{ $selectedDate ?? now()->format('Y-m-d') }}"
                    class="text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">
            </div>

            {{--
            3. FIX: Wrapped the Main Content in <fieldset>.
                This handles the standard input disabling logic.
                The overlay will cover this area.
                --}}
                <fieldset @if(!$selectedPatient) disabled @endif>
                    {{-- MAIN CONTAINER --}}
                    <div class="w-[100%] mx-auto flex justify-center items-start gap-1">
                        <center>
                            <div class="w-[85%] rounded-[15px] overflow-hidden">
                                <table class="w-full table-fixed border-collapse border-spacing-y-0">
                                    <tr>
                                        <th class="w-[20%] main-header rounded-tl-[15px]">MEDICATION</th>
                                        <th class="w-[15%] main-header">DOSE</th>
                                        <th class="w-[15%] main-header">ROUTE</th>
                                        <th class="w-[15%] main-header">FREQUENCY</th>
                                        <th class="w-[20%] main-header">COMMENTS</th>
                                        <th class="w-[15%] main-header rounded-tr-[15px]">TIME</th>
                                    </tr>

                                    {{-- Row 1 (10:00 AM) --}}
                                    <tr class="border-b-2 border-line-brown/70 h-[100px]">
                                        <td class="bg-beige text-center">
                                            <input type="text" name="medication[]" placeholder="Medication"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                            <input type="hidden" name="time[]" value="10:00:00">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="dose[]" placeholder="Dose"
                                                class="w-full h-[45px] focus:outline-none text-center medication-input">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="route[]" placeholder="Route"
                                                class="w-full h-[45px] focus:outline-none text-center medication-input">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="frequency[]" placeholder="Frequency"
                                                class="w-full h-[45px] focus:outline-none text-center medication-input">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="comments[]" placeholder="Comments"
                                                class="w-full h-[45px] focus:outline-none text-center medication-input">
                                        </td>
                                        <th class="bg-yellow-light text-brown font-semibold">10:00 AM</th>
                                    </tr>

                                    {{-- Row 2 (2:00 PM) --}}
                                    <tr class="border-b-2 border-line-brown/70 h-[100px]">
                                        <td class="bg-beige text-center">
                                            <input type="text" name="medication[]" placeholder="Medication"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                            <input type="hidden" name="time[]" value="14:00:00">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="dose[]" placeholder="Dose"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="route[]" placeholder="Route"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="frequency[]" placeholder="Frequency"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="comments[]" placeholder="Comments"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                        </td>
                                        <th class="bg-yellow-light text-brown font-semibold">2:00 PM</th>
                                    </tr>

                                    {{-- Row 3 (6:00 PM) --}}
                                    <tr>
                                        <td class="bg-beige text-center h-[100px]">
                                            <input type="text" name="medication[]" placeholder="Medication"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                            <input type="hidden" name="time[]" value="18:00:00">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="dose[]" placeholder="Dose"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="route[]" placeholder="Route"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="frequency[]" placeholder="Frequency"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                        </td>
                                        <td class="bg-beige text-center">
                                            <input type="text" name="comments[]" placeholder="Comments"
                                                class="w-full h-[45px] text-center focus:outline-none medication-input">
                                        </td>
                                        <th class=" text-brown font-semibold bg-yellow-light">6:00 PM</th>
                                    </tr>
                                </table>
                            </div>

                            <div class="w-[70%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
                                <button class="button-default" type="submit" id="submit_button">SUBMIT</button>
                            </div>
                        </center>
                    </div>
                </fieldset>
        </form>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/medication-administration.css'])
@endpush

@push('scripts')
    {{-- FIX: Included 'searchable-dropdown.js' which contains the global disableForm logic --}}
    @vite(['resources/js/alert.js', 'resources/js/date-day-loader.js', 'resources/js/searchable-dropdown.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize global logic first
            if (window.initSearchableDropdown) window.initSearchableDropdown();

            const searchInput = document.getElementById('patient_search_input');
            const optionsContainer = document.getElementById('patient_options_container');
            const options = optionsContainer.querySelectorAll('.option');

            // UI IDs
            const uiPatientIdInput = document.getElementById('patient_id_for_form');
            const hiddenGlobalIdInput = document.getElementById('patient_id_hidden');
            const dateInput = document.getElementById('date_selector');

            const medicationInputs = document.querySelectorAll('.medication-input');
            const submitButton = document.getElementById('submit_button');
            const form = document.querySelector('form');

            const timeMap = { '10:00:00': 0, '14:00:00': 1, '18:00:00': 2 };

            function toggleFormElements(enable) {
                // Keep manual logic for safety, but global JS handles overlay
                medicationInputs.forEach(input => input.disabled = !enable);
                submitButton.disabled = !enable;
            }

            function clearMedicationInputs() {
                medicationInputs.forEach(input => input.value = '');
            }

            function populateMedicationInputs(records) {
                clearMedicationInputs();
                records.forEach(record => {
                    const timeIndex = timeMap[record.time];
                    if (timeIndex !== undefined) {
                        const baseIndex = timeIndex * 5;
                        if (medicationInputs[baseIndex]) medicationInputs[baseIndex].value = record.medication || '';
                        if (medicationInputs[baseIndex + 1]) medicationInputs[baseIndex + 1].value = record.dose || '';
                        if (medicationInputs[baseIndex + 2]) medicationInputs[baseIndex + 2].value = record.route || '';
                        if (medicationInputs[baseIndex + 3]) medicationInputs[baseIndex + 3].value = record.frequency || '';
                        if (medicationInputs[baseIndex + 4]) medicationInputs[baseIndex + 4].value = record.comments || '';
                    }
                });
            }

            async function fetchAndDisplayRecords() {
                const patientId = uiPatientIdInput.value;
                const date = dateInput.value;

                if (!patientId || !date) {
                    clearMedicationInputs();
                    return;
                }

                try {
                    const response = await fetch(`/medication-administration/records?patient_id=${patientId}&date=${date}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const records = await response.json();
                    populateMedicationInputs(records);
                } catch (error) {
                    console.error('Error fetching medication records:', error);
                }
            }

            // Init State
            if (!uiPatientIdInput.value) {
                toggleFormElements(false);
            } else {
                toggleFormElements(true);
                fetchAndDisplayRecords();
            }

            searchInput.addEventListener('focus', () => {
                optionsContainer.classList.remove('hidden');
            });

            searchInput.addEventListener('input', () => {
                const filter = searchInput.value.toLowerCase();
                options.forEach(option => {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(filter) ? '' : 'none';
                });
                optionsContainer.classList.remove('hidden');

                if (searchInput.value.trim() === '') {
                    uiPatientIdInput.value = '';
                    hiddenGlobalIdInput.value = ''; // Sync global ID
                    toggleFormElements(false);
                    clearMedicationInputs();
                    // Trigger global event for overlay
                    document.dispatchEvent(new CustomEvent("patient:selected", { bubbles: true }));
                }
            });

            options.forEach(option => {
                option.addEventListener('click', () => {
                    const patientId = option.getAttribute('data-value');
                    const patientName = option.textContent.trim();

                    searchInput.value = patientName;
                    uiPatientIdInput.value = patientId;
                    hiddenGlobalIdInput.value = patientId; // Sync global ID

                    optionsContainer.classList.add('hidden');
                    toggleFormElements(true);
                    fetchAndDisplayRecords();

                    // Trigger global event for overlay
                    document.dispatchEvent(new CustomEvent("patient:selected", { bubbles: true }));
                });
            });

            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !optionsContainer.contains(e.target)) {
                    optionsContainer.classList.add('hidden');
                }
            });

            dateInput.addEventListener('change', () => {
                if (uiPatientIdInput.value) {
                    fetchAndDisplayRecords();
                } else {
                    clearMedicationInputs();
                }
            });

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (response.ok) {
                        alert(result.message || 'Medication Administration data saved successfully!');
                        fetchAndDisplayRecords();
                    } else {
                        alert(result.message || 'Error saving data.');
                    }
                } catch (error) {
                    console.error('Network error:', error);
                    alert('An unexpected error occurred. Please try again.');
                }
            });
        });
    </script>
@endpush