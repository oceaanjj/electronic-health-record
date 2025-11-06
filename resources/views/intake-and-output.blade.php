@extends('layouts.app')

@section('title', 'Patient Intake And Output')

@section('content')

    {{-- FORM OVERLAY (ALERT) --}}
    <div id="form-content-container">
        @if (!session('selected_patient_id'))
            <div
                class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                <span class="text-gray-600 font-creato">Please select a patient to input</span>
            </div>
        @endif

   {{-- NEW SEARCHABLE PATIENT DROPDOWN FOR INTAKE AND OUTPUT --}}
<div class="header flex items-center gap-6 my-10 mx-auto w-[80%]">
    <form action="{{ route('io.select') }}" method="POST" id="patient-select-form" class="flex items-center gap-6 w-full">
        @csrf

        {{-- PATIENT NAME --}}
        <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
            PATIENT NAME :
        </label>

        <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('io.select') }}">
            {{-- Text input for search --}}
            <input
                type="text"
                id="patient_search_input"
                placeholder="Select or type Patient Name"
                value="@isset($selectedPatient){{ trim($selectedPatient->name) }}@endisset"
                autocomplete="off"
                class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
            >

            {{-- Dropdown list --}}
            <div
                id="patient_options_container"
                class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"
            >
                @foreach ($patients as $patient)
                    <div
                        class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150"
                        data-value="{{ $patient->patient_id }}"
                    >
                        {{ trim($patient->name) }}
                    </div>
                @endforeach
            </div>

            {{-- Hidden input to store selected patient ID --}}
            <input type="hidden" id="patient_id_hidden" name="patient_id" value="@isset($selectedPatient){{ $selectedPatient->patient_id }}@endisset">
        </div>

        {{-- DATE --}}
        <label for="date_selector" class="whitespace-nowrap font-alte font-bold text-dark-green">
            DATE :
        </label>
        <input
            type="date"
            id="date_selector"
            name="date"
            value="{{ $currentDate ?? now()->format('Y-m-d') }}"
            @if (!isset($selectedPatient)) disabled @endif
            class="text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
        >

        {{-- DAY NO --}}
        <label for="day_no" class="whitespace-nowrap font-alte font-bold text-dark-green">
            DAY NO :
        </label>
        <select
            id="day_no_selector"
            name="day_no"
            @if (!isset($selectedPatient)) disabled @endif
            class="w-[120px] text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300
                   focus:ring-2 focus->ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
        >
            <option value="">-- Select number --</option>
            @for ($i = 1; $i <= 30; $i++)
                <option
                    value="{{ $i }}"
                    @if(($currentDayNo ?? 1) == $i) selected @endif
                >
                    {{ $i }}
                </option>
            @endfor
        </select>
    </form>
</div>
{{-- END SEARCHABLE PATIENT DROPDOWN FOR INTAKE AND OUTPUT --}}

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <form id="io-form" class="cdss-form" method="POST" action="{{ route('io.store') }}" data-analyze-url="{{ route('io.check') }}">
                @csrf

                <input type="hidden" name="patient_id" value="@isset($selectedPatient){{ $selectedPatient->patient_id }}@endisset">
                <input type="hidden" name="date" value="{{ $currentDate ?? now()->format('Y-m-d') }}">
                <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}">

                <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
                    <div class="w-[68%] rounded-[15px] overflow-hidden">

                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="w-[33%] bg-dark-green text-white font-bold py-2 text-center rounded-tl-lg">ORAL INTAKE (mL)</th>
                                <th class="w-[33%] bg-dark-green text-white font-bold py-2 text-center">IV FLUIDS (mL)</th>
                                <th class="w-[33%] bg-dark-green text-white font-bold py-2 text-center rounded-tr-lg">URINE OUTPUT (mL)</th>
                            </tr>

                            <tr class="bg-beige text-brown">
                                {{-- ORAL INTAKE --}}
                                <td class="text-center align-middle">
                                    <input type="number"
                                        name="oral_intake"
                                        placeholder="Enter Oral Intake"
                                        value="{{ old('oral_intake', $ioData->oral_intake ?? '') }}"
                                        min="0"
                                        class="w-[80%] h-[100px] rounded-[10px] px-3 text-center bg-beige text-brown font-semibold focus:outline-none cdss-input"
                                        data-field-name="oral_intake" />
                                </td>

                                {{-- IV FLUIDS --}}
                                <td class="text-center align-middle">
                                    <input type="number"
                                        name="iv_fluids_volume"
                                        placeholder="Enter IV Fluids"
                                        value="{{ old('iv_fluids_volume', $ioData->iv_fluids_volume ?? '') }}"
                                        min="0"
                                        class="w-[80%] h-[100px] rounded-[10px] px-3 text-center bg-beige text-brown font-semibold focus:outline-none cdss-input"
                                        data-field-name="iv_fluids_volume" />
                                </td>

                                {{-- URINE OUTPUT --}}
                                <td class="text-center align-middle">
                                    <input type="number"
                                        name="urine_output"
                                        placeholder="Enter Urine Output"
                                        value="{{ old('urine_output', $ioData->urine_output ?? '') }}"
                                        min="0"
                                        class="w-[80%] h-[100px] rounded-[10px] px-3 text-center bg-beige text-brown font-semibold focus:outline-none cdss-input"
                                        data-field-name="urine_output" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="w-[25%] rounded-[15px] overflow-hidden">
                        <div class="bg-dark-green text-white font-bold py-2 mb-1 text-center rounded-[15px]">
                            ALERTS
                        </div>

                        <table class="w-full border-collapse">
                            <tr>
                                <td class="align-middle" data-alert-for-field="io_alert">
                                    <div class="alert-box my-[3px] h-[90px] flex justify-center items-center">
                                        <span class="opacity-70 text-white font-semibold">No Alerts</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="w-[70%] mx-auto flex justify-end mt-20 mb-30 space-x-4">
                    <button type="button" class="button-default w-[300px]">CALCULATE FLUID BALANCE</button>
                    <button type="button" class="button-default">CDSS</button>
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </form>
        </fieldset>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/date-day-loader.js', 'resources/js/io-alerts.js'])
    <script>
        function initializeSearchableDropdown() {
            const dropdownContainer = document.querySelector(".searchable-dropdown");
            if (!dropdownContainer) return;

            const searchInput = document.getElementById("patient_search_input");
            const hiddenInput = document.getElementById("patient_id_hidden");
            const optionsContainer = document.getElementById("patient_options_container");
            if (!optionsContainer) return;
            const options = optionsContainer.querySelectorAll(".option");
            const selectUrl = dropdownContainer.dataset.selectUrl;

            let currentFocus = -1;
            optionsContainer.style.display = "none";

            const removeActive = () => {
                options.forEach((option) => {
                    option.classList.remove("active");
                });
            };

            const addActive = (n) => {
                removeActive();
                if (n >= options.length) n = 0;
                if (n < 0) n = options.length - 1;
                currentFocus = n;
                const visibleOptions = Array.from(options).filter(
                    (opt) => opt.style.display !== "none"
                );
                if (visibleOptions.length > 0) {
                    const focusedOption = visibleOptions[currentFocus % visibleOptions.length];
                    if (focusedOption) {
                        focusedOption.classList.add("active");
                        focusedOption.scrollIntoView({
                            block: "nearest",
                            behavior: "smooth",
                        });
                    }
                }
            };

            const filterAndShowOptions = () => {
                const filter = searchInput.value.toLowerCase();
                let visibleCount = 0;
                options.forEach((option) => {
                    const text = (option.textContent || option.innerText).toLowerCase();
                    const shouldShow = text.includes(filter);
                    option.style.display = shouldShow ? "block" : "none";
                    if (shouldShow) {
                        visibleCount++;
                    }
                });
                currentFocus = -1;
                removeActive();
                if (visibleCount > 0) {
                    optionsContainer.style.display = "block";
                } else {
                    optionsContainer.style.display = "none";
                }
            };

            searchInput.addEventListener("focus", () => {
                filterAndShowOptions();
            });

            searchInput.addEventListener("keyup", (event) => {
                if (
                    event.key !== "ArrowUp" &&
                    event.key !== "ArrowDown" &&
                    event.key !== "Enter"
                ) {
                    filterAndShowOptions();
                }
            });

            const selectOption = (option) => {
                const patientId = option.getAttribute("data-value");
                const patientName = (option.textContent || option.innerText).trim();
                searchInput.value = patientName;
                hiddenInput.value = patientId;
                optionsContainer.style.display = "none";
                currentFocus = -1;
                removeActive();
                const event = new CustomEvent("patient:selected", {
                    bubbles: true,
                    detail: {
                        patientId: patientId,
                        selectUrl: selectUrl,
                    },
                });
                document.dispatchEvent(event);
            };

            searchInput.addEventListener("keydown", (event) => {
                const visibleOptions = Array.from(options).filter(
                    (opt) => opt.style.display !== "none"
                );
                if (event.key === "ArrowDown" || event.key === "ArrowUp") {
                    event.preventDefault();
                    if (visibleOptions.length > 0) {
                        const direction = event.key === "ArrowDown" ? 1 : -1;
                        let nextFocus = currentFocus + direction;
                        if (nextFocus >= visibleOptions.length) {
                            nextFocus = 0;
                        } else if (nextFocus < 0) {
                            nextFocus = visibleOptions.length - 1;
                        }
                        addActive(nextFocus);
                    }
                } else if (event.key === "Enter") {
                    event.preventDefault();
                    const activeOption = optionsContainer.querySelector(".option.active");
                    if (activeOption) {
                        selectOption(activeOption);
                    } else {
                        const firstVisibleOption = visibleOptions[0];
                        if (firstVisibleOption) {
                            selectOption(firstVisibleOption);
                        }
                    }
                }
            });

            options.forEach((option) => {
                option.addEventListener("click", () => {
                    selectOption(option);
                });
            });

            document.addEventListener("click", (event) => {
                setTimeout(() => {
                    if (!event.target.closest(".searchable-dropdown")) {
                        if (document.activeElement !== searchInput) {
                            optionsContainer.style.display = "none";
                        }
                    }
                }, 100);
            });
        }

        function initializePageScripts() {
            initializeSearchableDropdown();
            if (window.initializeDateDayLoader) {
                window.initializeDateDayLoader();
            }
            if (window.initializeIntakeOutputAlerts) {
                window.initializeIntakeOutputAlerts();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializePageScripts();

            const container = document.getElementById('form-content-container');
            if (container) {
                const observer = new MutationObserver(function(mutations) {
                    for (let mutation of mutations) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            initializePageScripts();
                            break; 
                        }
                    }
                });
                observer.observe(container, { childList: true, subtree: true });
            }
        });
    </script>
@endpush