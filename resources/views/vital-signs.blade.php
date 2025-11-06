@extends('layouts.app')

@section('title', 'Patient Vital Signs')

@section('content')

    {{-- FORM OVERLAY (ALERT) --}}
    <div id="form-content-container">
        @if (!session('selected_patient_id'))
            <div
                class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                <span class="text-gray-600 font-creato">Please select a patient to input</span>
            </div>
        @endif

   {{-- NEW SEARCHABLE PATIENT DROPDOWN FOR VITAL SIGNS --}}
<div class="header flex items-center gap-6 my-10 mx-auto w-[80%]">
    <form action="{{ route('vital-signs.select') }}" method="POST" id="patient-select-form" class="flex items-center gap-6 w-full">
        @csrf

        {{-- PATIENT NAME --}}
        <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
            PATIENT NAME :
        </label>

        <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('vital-signs.select') }}">
            {{-- Text input for search --}}
            <input
                type="text"
                id="patient_search_input"
                placeholder="Select or type Patient Name"
                value="{{ trim($selectedPatient->name ?? '') }}"
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
            <input type="hidden" id="patient_id_hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
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
            @if (!$selectedPatient) disabled @endif
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
            @if (!$selectedPatient) disabled @endif
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
{{-- END SEARCHABLE PATIENT DROPDOWN FOR VITAL SIGNS --}}

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <form id="vitals-form" class="cdss-form" method="POST" action="{{ route('vital-signs.store') }}" data-analyze-url="{{ route('vital-signs.check') }}">
                @csrf

                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
                <input type="hidden" name="date" value="{{ $currentDate ?? now()->format('Y-m-d') }}">
                <input type="hidden" name="day_no" value="{{ $currentDayNo ?? 1 }}">

                <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">
                    <div class="w-[68%] rounded-[15px] overflow-hidden">

                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="w-[15%] bg-dark-green text-white font-bold py-2 text-center rounded-tl-lg">TIME
                                </th>
                                <th class="w-[13%] bg-dark-green text-white">TEMPERATURE</th>
                                <th class="w-[10%] bg-dark-green text-white">HR</th>
                                <th class="w-[10%] bg-dark-green text-white">RR</th>
                                <th class="w-[10%] bg-dark-green text-white">BP</th>
                                <th class="w-[10%] bg-dark-green text-white rounded-tr-lg">SpO₂</th>
                            </tr>

                            {{-- NOTE: paki-explain saakin ito kasi gagawin kong input text ito--}}
                            @php
                                $times = ['06:00', '08:00', '12:00', '14:00', '18:00', '20:00', '00:00', '02:00'];
                            @endphp

                            @foreach ($times as $index => $time)
                                @php
                                    $vitalsRecord = $vitalsData->get($time);
                                    $isLast = $index === count($times) - 1;
                                    $borderClass = $isLast ? '' : 'border-b-2 border-line-brown/70';
                                @endphp

                                <tr class="{{ $borderClass }}">
                                    {{-- TIME COLUMN --}}
                                    <th class="text-center font-semibold py-2 bg-yellow-light text-brown {{ $borderClass }}">
                                        {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                    </th>

                                    {{-- TEMPERATURE --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="number" step="0.1" name="temperature_{{ $time }}" placeholder="temperature"
                                            value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="temperature" data-time="{{ $time }}">
                                    </td>

                                    {{-- HR --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="number" name="hr_{{ $time }}" placeholder="bpm"
                                            value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="hr" data-time="{{ $time }}">
                                    </td>

                                    {{-- RR --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="number" name="rr_{{ $time }}" placeholder="bpm"
                                            value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="rr" data-time="{{ $time }}">
                                    </td>

                                    {{-- BP --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="text" name="bp_{{ $time }}" placeholder="mmHg"
                                            value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="bp" data-time="{{ $time }}">
                                    </td>

                                    {{-- SpO₂ --}}
                                    <td class="bg-beige {{ $borderClass }}">
                                        <input type="number" name="spo2_{{ $time }}" placeholder="%"
                                            value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                                            class="cdss-input vital-input h-[60px]" data-field-name="spo2" data-time="{{ $time }}">
                                    </td>
                                </tr>
                            @endforeach

                        </table>
                    </div>

                    <div class="w-[25%] rounded-[15px] overflow-hidden">
                        <div class="bg-dark-green text-white font-bold py-2 mb-1 text-center rounded-[15px]">
                            ALERTS
                        </div>

                        <table class="w-full border-collapse">
                            @foreach ($times as $time)
                                @php
                                    $vitalsRecord = $vitalsData->get($time);
                                    $severity = optional($vitalsRecord)->news_severity ?? 'NONE';
                                    $color = $severity === 'CRITICAL' ? 'text-red-600'
                                        : ($severity === 'WARNING' ? 'text-orange-500'
                                            : ($severity === 'INFO' ? 'text-blue-500'
                                                : ($severity === 'NONE' ? 'text-white' : 'text-black')));
                                    $alerts = $vitalsRecord ? explode('; ', $vitalsRecord->alerts) : [];
                                @endphp

                                <tr>
                                    <td class="align-middle" data-alert-for-time="{{ $time }}">
                                        <div class="alert-box my-[3px] h-[53px] flex justify-center items-center">
                                            @if ($vitalsRecord && optional($vitalsRecord)->alerts)
                                                <ul class="list-none text-center {{ $color }}">
                                                    @foreach($alerts as $alert)
                                                        <li class="font-semibold">{{ $alert }}</li>
                                                    @endforeach
                                                </ul>
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
                <div class="w-[70%] mx-auto flex justify-end mt-5 mb-30 space-x-4">
                    <button type="button" class="button-default">CDSS</button>
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </form>
        </fieldset>
    </div>

@endsection
@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/date-day-loader.js', 'resources/js/vital-signs-alerts.js'])
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
            if (window.initializeVitalSignsAlerts) {
                window.initializeVitalSignsAlerts();
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