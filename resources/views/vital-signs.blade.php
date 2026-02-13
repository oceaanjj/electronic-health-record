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

        /* Support for the reference colors */
        .bg-dark-green { background-color: #006400; }
        .bg-yellow-light { background-color: #fef08a; }
        .bg-beige { background-color: #f5f5dc; }

        /* Hide scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Carousel visibility helper */
        .btn-hidden { display: none !important; }
        
        :root {
            --color-beige: #f5f5dc;
            --color-dark-red: #8b0000;
        }

        /* Placeholder styling to match image */
        .vital-input::placeholder {
            color: #d1d5db; /* Light gray to match the image watermark style */
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

            <div class="mx-auto w-full px-4 pt-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-y-4 lg:gap-x-10 lg:ml-20">
                    {{-- PATIENT SECTION --}}
                    <div class="flex items-center md:pl-12 gap-4 w-full md:w-auto justify-start">
                        <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>
                        <div class="w-full md:w-[350px]">
                            <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient" :selectRoute="route('vital-signs.select')" :inputValue="$selectedPatient?->patient_id ?? ''" />
                        </div>
                    </div>

                    {{-- DATE & DAY SECTION --}}
                    @if ($selectedPatient)
                        <div class="flex items-center gap-4 md:pl-12 lg:pl-0">
                            <x-date-day-selector :currentDate="$currentDate" :currentDayNo="$currentDayNo" :totalDays="$totalDaysSinceAdmission ?? 30" />
                        </div>
                    @endif
                </div>

                @if ($selectedPatient && (!isset($vitalsData) || $vitalsData->count() == 0))
                    <div class="mt-4 px-4 lg:ml-32 flex items-center gap-2 text-xs italic text-gray-500">
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

                <div class="mx-auto mt-5 flex w-full max-w-screen-2xl flex-col gap-6 md:gap-4 px-4 md:mt-15 md:w-[95%] md:flex-row md:items-start md:justify-between md:px-0">
                    
                    {{-- 1. CHARTS COLUMN --}}
                    <div class="relative w-full md:w-[30%]">
                        <div id="chart-viewport" class="relative overflow-hidden rounded-[25px]">
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
                        </div>

                        <button id="chart-up" type="button" class="btn-hidden absolute -top-8 left-1/2 z-30 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full border border-[#e2e8f0] bg-gradient-to-b from-white to-[#f1f5f9] text-[#64748b] shadow-[0_4px_12px_rgba(0,0,0,0.1)] transition-all duration-200 hover:bg-white hover:text-[#334155] hover:shadow-md">
                            <span class="material-symbols-outlined text-[32px]">arrow_drop_up</span>
                        </button>
                        <button id="chart-down" type="button" class="absolute -bottom-8 left-1/2 z-30 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full border border-[#e2e8f0] bg-gradient-to-b from-white to-[#f1f5f9] text-[#64748b] shadow-[0_4px_12px_rgba(0,0,0,0.1)] transition-all duration-200 hover:bg-white hover:text-[#334155] hover:shadow-md">
                            <span class="material-symbols-outlined text-[32px]">arrow_drop_down</span>
                        </button>
                    </div>

                    {{-- 2. DATA AREA --}}
                    <div class="w-full md:w-[68%]">
                        
                        {{-- DESKTOP VIEW: Integrated Table --}}
                        <div class="hidden md:block w-full overflow-hidden rounded-[15px]">
                            <table class="w-full table-fixed border-collapse border-spacing-y-0">
                                <thead>
                                    <tr>
                                        <th class="w-[12%] main-header rounded-tl-[15px]">TIME</th>
                                        <th class="w-[15%] main-header">TEMPERATURE</th>
                                        <th class="w-[10%] main-header">HR</th>
                                        <th class="w-[10%] main-header">RR</th>
                                        <th class="w-[10%] main-header">BP</th>
                                        <th class="w-[15%] main-header rounded-tr-[15px]">SpO₂</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($times as $index => $time)
                                        @php $vitalsRecord = $vitalsData->get($time); @endphp
                                        <tr>
                                            <td class="p-2 font-semibold bg-yellow-light text-brown text-center border-b-2 border-line-brown/70">
                                                {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}
                                            </td>
                                            {{-- Data Cells with Placeholders exactly like image --}}
                                            <td class="p-2 bg-beige text-center border-b-2 border-line-brown/70">
                                                <input type="text" name="temperature_{{ $time }}" placeholder="temperature"
                                                    value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                                                    class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                    data-field-name="temperature" data-time="{{ $time }}" autocomplete="off" />
                                            </td>
                                            <td class="p-2 bg-beige text-center border-b-2 border-line-brown/70">
                                                <input type="text" name="hr_{{ $time }}" placeholder="bpm"
                                                    value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                                                    class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                    data-field-name="hr" data-time="{{ $time }}" autocomplete="off" />
                                            </td>
                                            <td class="p-2 bg-beige text-center border-b-2 border-line-brown/70">
                                                <input type="text" name="rr_{{ $time }}" placeholder="bpm"
                                                    value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                                                    class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                    data-field-name="rr" data-time="{{ $time }}" autocomplete="off" />
                                            </td>
                                            <td class="p-2 bg-beige text-center border-b-2 border-line-brown/70">
                                                <input type="text" name="bp_{{ $time }}" placeholder="mmHg"
                                                    value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                                                    class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                    data-field-name="bp" data-time="{{ $time }}" autocomplete="off" />
                                            </td>
                                            <td class="p-2 bg-beige text-center border-b-2 border-line-brown/70">
                                                <input type="text" name="spo2_{{ $time }}" placeholder="%"
                                                    value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                                                    class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" 
                                                    data-field-name="spo2" data-time="{{ $time }}" autocomplete="off" />
                                            </td>
                                            {{-- DESKTOP ALERT BELL (The Master) --}}
                                            <td class="p-2 text-center align-middle bg-white">
                                                <div class="h-[60px] flex justify-center items-center desktop-alert-container" data-alert-for-time="{{ $time }}">
                                                    <div class="alert-icon-btn is-empty">
                                                        <span class="material-symbols-outlined">notifications</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- MOBILE VIEW: ADL Style Cards --}}
                        <div class="space-y-4 md:hidden">
                            @foreach ($times as $time)
                                @php
                                    $vitalsRecord = $vitalsData->get($time);
                                    $formattedTime = \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
                                @endphp

                                <div class="relative mb-6 flex w-full flex-col overflow-hidden rounded-[15px] border border-[#c18b04] bg-beige">
                                    <div class="main-header w-full pl-3 p-4 pr-12 text-left text-[15px]">
                                        TIME: {{ $formattedTime }}
                                    </div>

                                    {{-- MOBILE ALERT BELL (The Clone) --}}
                                    {{-- Note: Added a special class 'mobile-alert-clone' for the JS sync --}}
                                    <div class="absolute right-4 top-2.5 z-10 flex items-center justify-center mobile-alert-clone"
                                        data-alert-for-time="{{ $time }}"
                                        data-time="{{ $time }}">
                                        <div class="alert-icon-btn is-empty">
                                            <span class="material-symbols-outlined">notifications</span>
                                        </div>
                                    </div>

                                    <div class="p-2">
                                        @foreach (['temperature' => 'temperature', 'hr' => 'bpm', 'rr' => 'bpm', 'bp' => 'mmHg', 'spo2' => '%'] as $field => $unit)
                                            <div class="mb-2 w-full">
                                                <div class="bg-yellow-light text-brown font-bold text-center py-1 text-xs uppercase tracking-widest rounded-t-md border-b border-brown/20">
                                                    {{ strtoupper($field) }}
                                                </div>
                                                <input type="text"
                                                    name="{{ $field }}_{{ $time }}"
                                                    placeholder="{{ $unit }}"
                                                    value="{{ old($field . '_' . $time, optional($vitalsRecord)->$field) }}"
                                                    class="cdss-input vital-input w-full p-3 bg-beige text-center focus:outline-none h-[50px] border-b border-[#c18b04]/30"
                                                    data-field-name="{{ $field }}"
                                                    data-time="{{ $time }}" autocomplete="off" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mx-auto mt-5 mb-20 flex w-full justify-center space-x-4 md:w-[95%] md:justify-end">
                    @if (isset($vitalsData) && $vitalsData->count() > 0)
                        <button type="submit" formaction="{{ route('vital-signs.cdss') }}" class="button-default text-center">CDSS</button>
                    @endif
                    <button type="submit" class="button-default">SUBMIT</button>
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

            // -----------------------------------------------------
            // NEW: SYNC DESKTOP ALERTS TO MOBILE ALERTS
            // -----------------------------------------------------
            // Ang script na ito ang magpapagana sa alert bell ng mobile view.
            // Kinokopya nito ang status ng Desktop Table bells papunta sa Mobile Cards.
            // -----------------------------------------------------
            
            function syncAlerts() {
                // Kunin lahat ng Desktop Alert Containers (sa loob ng table)
                const desktopAlerts = document.querySelectorAll('.desktop-alert-container');

                desktopAlerts.forEach(desktopAlert => {
                    const time = desktopAlert.getAttribute('data-alert-for-time');
                    if(!time) return;

                    // Gumawa ng Observer para bantayan ang pagbabago sa Desktop Bell
                    const observer = new MutationObserver((mutations) => {
                        // Hanapin ang katumbas na Mobile Bell
                        const mobileAlert = document.querySelector(`.mobile-alert-clone[data-alert-for-time="${time}"]`);
                        
                        if (mobileAlert) {
                            // Kopyahin ang inner content (yung icon) at class mula sa desktop papuntang mobile
                            // Kuhanin ang button sa loob
                            const desktopBtn = desktopAlert.querySelector('.alert-icon-btn');
                            const mobileBtn = mobileAlert.querySelector('.alert-icon-btn');

                            if(desktopBtn && mobileBtn) {
                                mobileBtn.className = desktopBtn.className; // Copy classes like 'is-empty', 'bg-red-500' etc
                                mobileBtn.innerHTML = desktopBtn.innerHTML; // Copy the icon
                            }
                        }
                    });

                    // Simulan ang pagbabantay sa Desktop element
                    observer.observe(desktopAlert, { 
                        attributes: true, 
                        childList: true, 
                        subtree: true,
                        characterData: true
                    });
                });
            }

            // Patakbuhin ang Sync Logic
            syncAlerts();

            // Handle Mobile Click: Pag pinindot ang mobile bell, pindutin din ang desktop bell
            document.addEventListener('click', function(e) {
                const mobileClone = e.target.closest('.mobile-alert-clone');
                if (mobileClone) {
                    const time = mobileClone.getAttribute('data-alert-for-time');
                    const desktopOriginal = document.querySelector(`.desktop-alert-container[data-alert-for-time="${time}"] .alert-icon-btn`);
                    if (desktopOriginal) {
                        desktopOriginal.click(); // Trigger the original event
                    }
                }
            });
            // -----------------------------------------------------
            // END NEW SYNC SCRIPT
            // -----------------------------------------------------
        });

        window.closeChartModal = function () {
            const modal = document.getElementById('chart-modal');
            if (modal) modal.style.display = 'none';
            if (window.modalChartInstance) {
                window.modalChartInstance.destroy();
                window.modalChartInstance = null;
            }
        };

        // Chart Carousel Logic
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

            function colorizeInput(input) {
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