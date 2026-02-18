@extends('layouts.app')
@section('title', 'Patient Vital Signs')
@section('content')
    <style>
        #chart-viewport {
            height: 530px;
            overflow: hidden;
            position: relative;
        }

        #chart-track {
            padding-top: 50px;
            padding-bottom: 40px;
        }

        #chart-track > div {
            margin: 10px 0;
        }

        .bg-dark-green { background-color: #006400; }
        .bg-yellow-light { background-color: #fef08a; }
        .bg-beige { background-color: #f5f5dc; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .btn-hidden { display: none !important; }
        
        :root {
            --color-beige: #f5f5dc;
            --color-dark-red: #8b0000;
        }

        .vital-input::placeholder {
            color: #d1d5db;
            font-weight: bold;
        }
    </style>

    <div id="form-content-container" class="mx-auto max-w-full overflow-x-hidden">
        <div class="mx-auto mt-1 w-full">
            {{-- 1. THE ALERT/ERROR --}}
            @if ($selectedPatient && isset($vitalsData) && $vitalsData->count() > 0)
                <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                    <div id="cdss-alert-content" class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-md">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined animate-pulse text-[#dcb44e]">info</span>
                            <span class="text-sm font-semibold text-[#dcb44e]">Clinical Decision Support System is now available.</span>
                        </div>
                        <button type="button" onclick="closeCdssAlert()" class="group flex items-center justify-center rounded-full p-1 text-amber-700 transition-all duration-300 hover:bg-amber-200/50 active:scale-90">
                            <span class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90">close</span>
                        </button>
                    </div>
                </div>
            @endif

        <div class="mx-auto w-full pt-10">
            {{-- Increased width to accommodate one line --}}
            <div class="mb-5 flex flex-wrap items-center justify-start gap-x-10 gap-y-4 px-4 md:ml-0 lg:ml-33">
                {{-- 1. PATIENT SECTION --}}
                <div class="flex items-center gap-4">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>
                    <div class="w-full md:w-[350px]">
                        {{-- Fixed width so Date/Day don't jump around --}}
                        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                            :selectRoute="route('vital-signs.select')" :inputValue="$selectedPatient?->patient_id ?? ''" />
                    </div>
                </div>

                {{-- 2. DATE & DAY SECTION (Only shows if patient is selected) --}}
                @if ($selectedPatient)
                    <x-date-day-selector :currentDate="$currentDate" :currentDayNo="$currentDayNo"
                        :totalDays="$totalDaysSinceAdmission ?? 30" />
                @endif
            </div>

            {{-- CDSS ALERT MESSAGE (Keep this on its own line below the inputs) --}}
            @if ($selectedPatient && (!isset($vitalsData) || $vitalsData->count() == 0))
                <div class="mt-4 mx-auto flex items-center gap-2 text-xs italic text-gray-500 md:ml-68">
                    <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                    Clinical Decision Support System is not yet available (No data recorded for this date).
                </div>
            @endif
        </div>

        {{-- Hidden form for synchronization of Date/Day No --}}
        <form id="patient-select-form" action="{{ route('vital-signs.select') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
        </form>
        {{-- END OF HEADER --}}

        {{-- MAIN TABLE FOR INPUTS --}}

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <form id="vitals-form" class="cdss-form" method="POST" action="{{ route('vital-signs.store') }}"
                data-analyze-url="{{ route('vital-signs.check') }}"
                data-batch-analyze-url="{{ route('vital-signs.analyze-batch') }}" data-times="{{ json_encode($times) }}"
                data-fetch-url="{{ route('vital-signs.fetch-data') }}" data-alert-height-class="h-[55px]">
                @csrf

                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
                <input type="hidden" id="hidden_date_for_vitals_form" name="date"
                    value="{{ $currentDate ?? now()->format('Y-m-d') }}" />
                <input type="hidden" id="hidden_day_no_for_vitals_form" name="day_no" value="{{ $currentDayNo ?? 1 }}" />

                <div
                    class="mx-auto mt-5 flex w-full max-w-screen-2xl flex-col items-center justify-center gap-5 px-4 md:mt-8 md:w-[98%] md:flex-row lg:items-start md:gap-4">
                    <div class="w-full md:w-2/5">
                        <div class="relative overflow-hidden rounded-[20px]" id="chart-wrapper"></div>

                        <div id="fade-top"
                            class="pointer-events-none absolute top-0 left-0 z-20 hidden h-10 w-full rounded-t-[20px] bg-gradient-to-b from-white/90 to-transparent">
                        </div>

                        <!-- VIEWPORT (SHOWS 3 CHARTS) -->
                        <div id="chart-viewport" class="relative h-auto md:max-h-[530px] overflow-y-auto rounded-[25px]">
                            <div id="chart-track" class="transition-transform duration-700 ease-out">
                                <!-- ✅ REUSABLE CHART CARD -->
                                <div
                                    class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2
                                        class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">
                                        TEMPERATURE CHART
                                    </h2>
                                    <canvas id="tempChart"></canvas>
                                </div>

                                <div
                                    class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2
                                        class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">
                                        HEART RATE CHART
                                    </h2>
                                    <canvas id="hrChart"></canvas>
                                </div>

                                <div
                                    class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2
                                        class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">
                                        RESPIRATORY RATE CHART
                                    </h2>
                                    <canvas id="rrChart"></canvas>
                                </div>

                                <div
                                    class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2
                                        class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">
                                        BLOOD PRESSURE CHART
                                    </h2>
                                    <canvas id="bpChart"></canvas>
                                </div>

                                <div
                                    class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2
                                        class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">
                                        SpO₂ CHART
                                    </h2>
                                    <canvas id="spo2Chart"></canvas>
                                </div>
                            </div>

                            <div id="fade-bottom"
                                class="pointer-events-none absolute bottom-0 left-0 z-20 hidden h-10 w-full rounded-b-[20px] bg-gradient-to-t from-white/90 to-transparent">
                            </div>
                        </div>

                        <!-- DOWN BUTTON -->
                        <button id="chart-up" type="button"
                            class="btn-hidden absolute -top-8 left-1/2 z-30 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full border border-[#e2e8f0] bg-gradient-to-b from-white to-[#f1f5f9] text-[#64748b] shadow-[0_4px_12px_rgba(0,0,0,0.1)] transition-all duration-200 hover:bg-white hover:text-[#334155] hover:shadow-md">
                            <span class="material-symbols-outlined text-[32px]">arrow_drop_up</span>
                        </button>

                        <button id="chart-down" type="button"
                            class="absolute -bottom-8 left-1/2 z-30 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full border border-[#e2e8f0] bg-gradient-to-b from-white to-[#f1f5f9] text-[#64748b] shadow-[0_4px_12px_rgba(0,0,0,0.1)] transition-all duration-200 hover:bg-white hover:text-[#334155] hover:shadow-md">
                            <span class="material-symbols-outlined text-[32px]">arrow_drop_down</span>
                        </button>
                    </div>
                    {{-- COMBINED VITAL SIGNS TABLE WITH ALERTS --}}
                    <div class="w-full overflow-hidden rounded-[15px]">
                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="w-[12%] main-header rounded-tl-[15px]">TIME</th>
                                <th class="w-[15%] main-header">TEMPERATURE</th>
                                <th class="w-[10%] main-header">HR</th>
                                <th class="w-[10%] main-header">RR</th>
                                <th class="w-[10%] main-header">BP</th>
                                <th class="w-[15%] main-header rounded-tr-[15px]">SpO₂</th>
                                <th class="w-[28%] text-center py-2"></th>
                            </tr>

                            @foreach ($times as $index => $time)
                                @php
                                    $vitalsRecord = $vitalsData->get($time);
                                    $isLast = $index === count($times) - 1;
                                @endphp

                                <tr>
                                    {{-- TIME COLUMN --}}
                                    <td class="p-2 font-semibold bg-yellow-light text-brown text-center">
                                        {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                    </td>

                                    {{-- TEMPERATURE --}}
                                    <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                        <input type="text" name="temperature_{{ $time }}" placeholder="temperature"
                                            value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                                            class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center"
                                            data-field-name="temperature" data-time="{{ $time }}" autocomplete="off" />
                                    </td>

                                    {{-- HR --}}
                                    <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                        <input type="text" name="hr_{{ $time }}" placeholder="bpm"
                                            value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                                            class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center"
                                            data-field-name="hr" data-time="{{ $time }}" autocomplete="off" />
                                    </td>

                                    {{-- RR --}}
                                    <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                        <input type="text" name="rr_{{ $time }}" placeholder="bpm"
                                            value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                                            class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center"
                                            data-field-name="rr" data-time="{{ $time }}" autocomplete="off" />
                                    </td>

                                    {{-- BP --}}
                                    <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                        <input type="text" name="bp_{{ $time }}" placeholder="mmHg"
                                            value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                                            class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center"
                                            data-field-name="bp" data-time="{{ $time }}" autocomplete="off" />
                                    </td>

                                    {{-- SpO₂ --}}
                                    <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                        <input type="text" name="spo2_{{ $time }}" placeholder="%"
                                            value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                                            class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center"
                                            data-field-name="spo2" data-time="{{ $time }}" autocomplete="off" />
                                    </td>

                                    {{-- ALERTS COLUMN --}}
                                    <td class="p-2 text-center align-middle border-0">
                                        <div class="h-[60px] flex justify-center items-center text-center px-2"
                                            data-alert-for-time="{{ $time }}">
                                            <div class="alert-icon-btn is-empty">
                                                <span class="material-symbols-outlined">notifications</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>





                <div class="mx-auto mt-5 mb-20 flex w-full justify-center space-x-4 md:w-[90%] md:justify-end">
                    @if (isset($vitalsData) && $vitalsData->count() > 0)
                        <button type="submit" name="action" value="cdss"
                            class="button-default cdss-btn text-center">
                            CDSS
                        </button>
                    @endif

                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </form>

        </fieldset>

        <div id="chart-modal">
            <div class="modal-container">
                <div class="mb-4 flex items-center justify-between">
                    <h3 id="modal-chart-title" class="text-dark-green text-lg font-bold uppercase"></h3>
                    <button type="button" onclick="closeChartModal()"
                        class="cursor-pointer rounded-full p-2 transition-colors hover:bg-gray-100">
                        <span class="material-symbols-outlined text-3xl text-gray-500">close</span>
                    </button>
                </div>

                <div class="relative h-[400px]">
                    <canvas id="modalChartCanvas"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/patient-loader.js', 'resources/js/alert.js', 'resources/js/init.searchable-dropdown.js', 'resources/js/date-day-sync.js', 'resources/js/searchable-dropdown.js', 'resources/js/vital-signs-charts.js', 'resources/js/close-cdss-alert.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const vitalsData = @json($vitalsData);

        document.addEventListener('DOMContentLoaded', function () {
            const timePoints = @json($times);
            if (window.initializeVitalSignsCharts) window.initializeVitalSignsCharts(timePoints, vitalsData);
            if (window.initializeChartScrolling) window.initializeChartScrolling();
            if (window.initSearchableDropdown) window.initSearchableDropdown();

            // -----------------------------------------------------
            // CRITICAL FIX: SYNC MOBILE AND DESKTOP INPUTS
            // -----------------------------------------------------
            // Because we have two sets of inputs, we must mirror them.
            // -----------------------------------------------------
            const allVitalInputs = document.querySelectorAll('.vital-input');
            allVitalInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const field = this.dataset.fieldName;
                    const time = this.dataset.time;
                    const val = this.value;

                    // Find all inputs with same time and field and update them
                    document.querySelectorAll(`.vital-input[data-field-name="${field}"][data-time="${time}"]`).forEach(el => {
                        if (el !== this) {
                            el.value = val;
                            // Trigger colorization for the mirror element
                            if (typeof colorizeInput === 'function') colorizeInput(el);
                        }
                    });
                });
            });

            // Sync Alerts Logic
            function syncAlerts() {
                const desktopAlerts = document.querySelectorAll('.desktop-alert-container');
                desktopAlerts.forEach(desktopAlert => {
                    const time = desktopAlert.getAttribute('data-alert-for-time');
                    if(!time) return;

                    const observer = new MutationObserver(() => {
                        const mobileAlert = document.querySelector(`.mobile-alert-clone[data-alert-for-time="${time}"]`);
                        if (mobileAlert) {
                            const desktopBtn = desktopAlert.querySelector('.alert-icon-btn');
                            const mobileBtn = mobileAlert.querySelector('.alert-icon-btn');
                            if(desktopBtn && mobileBtn) {
                                mobileBtn.className = desktopBtn.className;
                                mobileBtn.innerHTML = desktopBtn.innerHTML;
                            }
                        }
                    });

                    observer.observe(desktopAlert, { attributes: true, childList: true, subtree: true });
                });
            }
            syncAlerts();

            document.addEventListener('click', function(e) {
                const mobileClone = e.target.closest('.mobile-alert-clone');
                if (mobileClone) {
                    const time = mobileClone.getAttribute('data-alert-for-time');
                    const desktopOriginal = document.querySelector(`.desktop-alert-container[data-alert-for-time="${time}"] .alert-icon-btn`);
                    if (desktopOriginal) desktopOriginal.click();
                }
            });
        });

        window.closeChartModal = function () {
            const modal = document.getElementById('chart-modal');
            if (modal) modal.style.display = 'none';
            if (window.modalChartInstance) {
                window.modalChartInstance.destroy();
                window.modalChartInstance = null;
            }
        };

        // Carousel Logic
        document.addEventListener('DOMContentLoaded', function () {
            let currentStep = 0;
            const totalSteps = 4;
            function updateCarousel() {
                const track = document.getElementById('chart-track');
                const upBtn = document.getElementById('chart-up');
                const downBtn = document.getElementById('chart-down');
                const cards = track ? track.querySelectorAll(':scope > div') : [];
                if (!track || !upBtn || !downBtn || !cards.length) return;
                const cardHeight = cards[0].offsetHeight + 10;
                upBtn.classList.toggle('btn-hidden', currentStep === 0);
                downBtn.classList.toggle('btn-hidden', currentStep >= totalSteps);
                track.style.transform = `translateY(-${currentStep * cardHeight}px)`;
            }
            document.addEventListener('click', function (e) {
                if (e.target.closest('#chart-down') && currentStep < totalSteps) { currentStep++; updateCarousel(); }
                if (e.target.closest('#chart-up') && currentStep > 0) { currentStep--; updateCarousel(); }
            });
            updateCarousel();
        });

        // Color Coding Logic
        (function() {
            const vitalRanges = {
                temperature: { ranges: [{ min: 36.3, max: 37, color: 'var(--color-beige)' }, { min: 37.01, max: Infinity, color: 'var(--color-dark-red)' }] },
                hr: { ranges: [{ min: 70, max: 110, color: 'var(--color-beige)' }, { min: 110.01, max: Infinity, color: 'var(--color-dark-red)' }] },
                rr: { ranges: [{ min: 16, max: 22, color: 'var(--color-beige)' }, { min: 22.01, max: Infinity, color: 'var(--color-dark-red)' }] },
                spo2: { ranges: [{ min: 95, max: 100, color: 'var(--color-beige)' }, { min: 0, max: 94.99, color: 'var(--color-dark-red)' }] },
                bp: { normal: 'var(--color-beige)', abnormal: 'var(--color-dark-red)' }
            };

            function getColorForValue(fieldName, value) {
                if (!fieldName || value === "" || value === null) return 'var(--color-beige)';
                if (fieldName === 'bp') {
                    const parts = value.split('/');
                    if (parts.length !== 2) return 'var(--color-beige)';
                    const s = parseFloat(parts[0]), d = parseFloat(parts[1]);
                    return (s > 140 || d > 90 || s < 90 || d < 60) ? vitalRanges.bp.abnormal : vitalRanges.bp.normal;
                }
                const num = parseFloat(value);
                if (isNaN(num)) return 'var(--color-beige)';
                const range = vitalRanges[fieldName].ranges;
                for (let r of range) { if (num >= r.min && num <= r.max) return r.color; }
                return 'var(--color-beige)';
            }

            window.colorizeInput = function(input) {
                const color = getColorForValue(input.dataset.fieldName, input.value.trim());
                input.style.backgroundColor = color;
                input.style.color = (color === 'var(--color-dark-red)') ? '#FFFFFF' : '#000000';
            }

            window.colorizeAllVitals = function() {
                document.querySelectorAll('.vital-input').forEach(input => {
                    colorizeInput(input);
                    input.addEventListener('input', e => colorizeInput(e.target));
                });
            };
            document.addEventListener('DOMContentLoaded', window.colorizeAllVitals);
        })();
    </script>
@endpush