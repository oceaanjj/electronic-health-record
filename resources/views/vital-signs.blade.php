
@extends('layouts.app')
@section('title', 'Patient Vital Signs')
@section('content')
    <style>
        /* Layout & Scrolling Logic (No Custom Color CSS) */
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

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .btn-hidden { display: none !important; }

        .vital-input::placeholder {
            color: #d1d5db;
            font-weight: bold;
        }

        .vital-input {
            text-align: center;
            font-weight: 500;
        }

        @media (min-width: 768px) {
            .web-button-alignment {
            }
        }
    </style>

    <div id="form-content-container" class="mx-auto max-w-full overflow-x-hidden">

        <div class="mx-auto mt-1 w-full">
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
                <div class="ml-4 md:ml-30 flex flex-wrap items-center gap-x-10 gap-y-4">
                    <div class="flex items-center gap-4">
                        <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>
                        <div class="w-[350px]">
                            <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient" :selectRoute="route('vital-signs.select')" :inputValue="$selectedPatient?->patient_id ?? ''" />
                        </div>
                    </div>

                    @if ($selectedPatient)
                        <x-date-day-selector :currentDate="$currentDate" :currentDayNo="$currentDayNo" :totalDays="$totalDaysSinceAdmission ?? 30" />
                    @endif
                </div>

                @if ($selectedPatient && (!isset($vitalsData) || $vitalsData->count() == 0))
                    <div class="mt-4 ml-2 md:ml-30 flex items-center gap-2 text-xs italic text-gray-500">
                        <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                        Clinical Decision Support System is not yet available (No data recorded for this date).
                    </div>
                @endif
            </div>
        </div>

        <form id="patient-select-form" action="{{ route('vital-signs.select') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
        </form>

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <form id="vitals-form" class="cdss-form" method="POST" action="{{ route('vital-signs.store') }}"
                data-analyze-url="{{ route('vital-signs.check') }}"
                data-batch-analyze-url="{{ route('vital-signs.analyze-batch') }}" data-times="{{ json_encode($times) }}"
                data-fetch-url="{{ route('vital-signs.fetch-data') }}" data-alert-height-class="h-[55px]">
                @csrf

                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
                <input type="hidden" id="hidden_date_for_vitals_form" name="date" value="{{ $currentDate ?? now()->format('Y-m-d') }}" />
                <input type="hidden" id="hidden_day_no_for_vitals_form" name="day_no" value="{{ $currentDayNo ?? 1 }}" />

                <div class="mx-auto mt-5 md:mt-15 flex w-[90%] flex-col md:flex-row items-start justify-between gap-1">
                    
                    {{-- 1. CHARTS COLUMN --}}
                    <div class="relative mr-3 w-full md:w-[30%]">
                        <div class="relative overflow-hidden rounded-[20px]" id="chart-wrapper"></div>
                        <div id="fade-top" class="pointer-events-none absolute top-0 left-0 z-20 hidden h-10 w-full rounded-t-[20px] bg-gradient-to-b from-white/90 to-transparent"></div>

                        <div id="chart-viewport" class="ml-0 md:ml-15 relative h-[530px] overflow-hidden rounded-[25px]">
                            <div id="chart-track" class="transition-transform duration-700 ease-out">
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">TEMPERATURE CHART</h2>
                                    <canvas id="tempChart"></canvas>
                                </div>
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">HEART RATE CHART</h2>
                                    <canvas id="hrChart"></canvas>
                                </div>
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">RESPIRATORY RATE CHART</h2>
                                    <canvas id="rrChart"></canvas>
                                </div>
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">BLOOD PRESSURE CHART</h2>
                                    <canvas id="bpChart"></canvas>
                                </div>
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">SpO₂ CHART</h2>
                                    <canvas id="spo2Chart"></canvas>
                                </div>
                            </div>
                            <div id="fade-bottom" class="pointer-events-none absolute bottom-0 left-0 z-20 hidden h-10 w-full rounded-b-[20px] bg-gradient-to-t from-white/90 to-transparent"></div>
                        </div>

                        <button id="chart-up" type="button" class="btn-hidden absolute -top-8 left-1/2 z-30 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full border border-[#e2e8f0] bg-gradient-to-b from-white to-[#f1f5f9] text-[#64748b] shadow-[0_4px_12px_rgba(0,0,0,0.1)] transition-all duration-200 hover:bg-white hover:text-[#334155] hover:shadow-md">
                            <span class="material-symbols-outlined text-[32px]">arrow_drop_up</span>
                        </button>
                        <button id="chart-down" type="button" class="absolute -bottom-8 left-1/2 z-30 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full border border-[#e2e8f0] bg-gradient-to-b from-white to-[#f1f5f9] text-[#64748b] shadow-[0_4px_12px_rgba(0,0,0,0.1)] transition-all duration-200 hover:bg-white hover:text-[#334155] hover:shadow-md">
                            <span class="material-symbols-outlined text-[32px]">arrow_drop_down</span>
                        </button>
                    </div>

                    {{-- 2. DATA AREA --}}
                    <div class="w-full overflow-hidden rounded-[15px]">
                        
                        {{-- DESKTOP VIEW: Table --}}
                        <div class="hidden md:block w-full">
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
                                    @php $vitalsRecord = $vitalsData->get($time); @endphp
                                    <tr>
                                        {{-- TIME COLUMN (Uses Color Classes from Snippet 1) --}}
                                        <td class="p-2 font-semibold bg-yellow-light text-brown text-center">
                                            {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                        </td>
                                        
                                        {{-- INPUT CELLS (Uses bg-beige from Snippet 1) --}}
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="temperature_{{ $time }}" placeholder="temperature"
                                                value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                                                class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                data-field-name="temperature" data-time="{{ $time }}" autocomplete="off" />
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="hr_{{ $time }}" placeholder="bpm"
                                                value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                                                class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                data-field-name="hr" data-time="{{ $time }}" autocomplete="off" />
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="rr_{{ $time }}" placeholder="bpm"
                                                value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                                                class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                data-field-name="rr" data-time="{{ $time }}" autocomplete="off" />
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="bp_{{ $time }}" placeholder="mmHg"
                                                value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                                                class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                data-field-name="bp" data-time="{{ $time }}" autocomplete="off" />
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="spo2_{{ $time }}" placeholder="%"
                                                value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                                                class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                data-field-name="spo2" data-time="{{ $time }}" autocomplete="off" />
                                        </td>
                                        <td class="p-2 text-center align-middle border-0 desktop-alert-container" data-alert-for-time="{{ $time }}">
                                            <div class="h-[60px] flex justify-center items-center text-center px-2">
                                                <div class="alert-icon-btn is-empty">
                                                    <span class="material-symbols-outlined">notifications</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        {{-- MOBILE VIEW --}}
                        <div class="space-y-4 md:hidden">
                            @foreach ($times as $time)
                                @php 
                                    $vitalsRecord = $vitalsData->get($time); 
                                    $formattedTime = \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
                                @endphp
                                <div class="relative mb-6 flex w-full flex-col rounded-[20px] border border-dark-green bg-beige overflow-hidden shadow-lg">
                                    <div class="main-header w-full bg-dark-green p-2 flex justify-between items-center">
                                        <span class="font-alte font-bold text-white text-lg uppercase">TIME: {{ $formattedTime }}</span>
                                        <div class="mobile-alert-clone" data-alert-for-time="{{ $time }}" data-time="{{ $time }}">
                                            <div class="alert-icon-btn is-empty">
                                                <span class="material-symbols-outlined text-white text-[28px]">notifications</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-2">
                                        @foreach(['temperature' => 'temperature', 'hr' => 'bpm', 'rr' => 'bpm', 'bp' => 'mmHg', 'spo2' => '%'] as $field => $unit)
                                            <div class="mb-2 w-full">
                                                <div class="bg-yellow-light text-brown font-bold text-center py-1.5 text-xs uppercase tracking-widest rounded-t-md border-b border-brown/20">
                                                    ASSESSMENT: {{ strtoupper($field) }}
                                                </div>
                                                <input type="text" name="{{ $field }}_{{ $time }}" placeholder="{{ $unit }}"
                                                    value="{{ old($field . '_' . $time, optional($vitalsRecord)->$field) }}"
                                                    class="cdss-input vital-input w-full p-4 text-center focus:outline-none h-[60px]"
                                                    data-field-name="{{ $field }}" data-time="{{ $time }}" autocomplete="off" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mx-auto mt-5 mb-20 flex w-[90%] justify-end">
                    <div class="flex gap-4 web-button-alignment">
                        <button type="submit" formaction="{{ route('vital-signs.cdss') }}" class="button-default cdss-btn">CDSS</button>
                        <button type="submit" class="button-default">SUBMIT</button>
                    </div>
                </div>
            </form>
        </fieldset>

        {{-- CHART MODAL --}}
        <div id="chart-modal">
            <div class="modal-container">
                <div class="mb-4 flex items-center justify-between">
                    <h3 id="modal-chart-title" class="text-dark-green text-lg font-bold uppercase"></h3>
                    <button type="button" onclick="closeChartModal()" class="cursor-pointer rounded-full p-2 transition-colors hover:bg-gray-100">
                        <span class="material-symbols-outlined text-3xl text-gray-500">close</span>
                    </button>
                </div>
                <div class="relative h-[400px]"><canvas id="modalChartCanvas"></canvas></div>
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

            // SYNC MOBILE AND DESKTOP INPUTS
            const allVitalInputs = document.querySelectorAll('.vital-input');
            allVitalInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const field = this.dataset.fieldName;
                    const time = this.dataset.time;
                    const val = this.value;
                    document.querySelectorAll(`.vital-input[data-field-name="${field}"][data-time="${time}"]`).forEach(el => {
                        if (el !== this) {
                            el.value = val;
                            if (typeof colorizeInput === 'function') colorizeInput(el);
                        }
                    });
                });
            });

            // SYNC ALERTS
            function syncAlerts() {
                const desktopAlerts = document.querySelectorAll('.desktop-alert-container');
                desktopAlerts.forEach(desktopAlert => {
                    const time = desktopAlert.getAttribute('data-alert-for-time');
                    if(!time) return;
                    const observer = new MutationObserver((mutations) => {
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

        // Carousel
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
                upBtn.classList.remove('btn-hidden');
                downBtn.classList.remove('btn-hidden');
                if (currentStep === 0) upBtn.classList.add('btn-hidden');
                if (currentStep >= totalSteps) downBtn.classList.add('btn-hidden');
                track.style.transform = `translateY(-${currentStep * cardHeight}px)`;
            }
            document.addEventListener('click', function (e) {
                if (e.target.closest('#chart-down') && currentStep < totalSteps) { currentStep++; updateCarousel(); }
                if (e.target.closest('#chart-up') && currentStep > 0) { currentStep--; updateCarousel(); }
            });
            updateCarousel();
        });

        // Color Logic (Synchronized with Snippet 1's behavior)
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
                
                // RESTORED Logic from Snippet 1: White text on Red, Black text on Beige
                if (color === 'var(--color-dark-red)') {
                    input.style.color = '#FFFFFF'; 
                } else {
                    input.style.color = '#000000';
                }
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
