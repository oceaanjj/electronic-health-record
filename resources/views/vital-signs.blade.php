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

        #chart-track>div {
            margin: 10px 0;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>

    <div id="form-content-container" class="mx-auto max-w-full overflow-x-hidden">

        {{-- CDSS ALERT BANNER --}}
        @if ($selectedPatient && isset($vitalsData) && $vitalsData->count() > 0)
            <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                <div id="cdss-alert-content"
                    class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined animate-pulse text-[#dcb44e]">info</span>
                        <span class="text-sm font-semibold text-[#dcb44e]">
                            Clinical Decision Support System is now available.
                        </span>
                    </div>
                    <button type="button" onclick="closeCdssAlert()"
                        class="group flex items-center justify-center rounded-full p-1 text-amber-700 transition-all duration-300 hover:bg-amber-200/50 active:scale-90">
                        <span class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90">
                            close
                        </span>
                    </button>
                </div>
            </div>
        @endif

        {{-- HEADER SECTION --}}
        <div class="mx-auto mt-10 mb-5 flex w-[90%] flex-col items-start gap-4 md:w-[98%] md:flex-row md:items-center lg:ml-10">
            <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

            <div class="w-full px-2 md:w-[350px] md:px-0">
                <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                    :selectRoute="route('vital-signs.select')" :inputValue="$selectedPatient?->patient_id ?? ''" />
            </div>

            @if ($selectedPatient)
                <div class="w-full md:w-auto">
                    <x-date-day-selector :currentDate="$currentDate" :currentDayNo="$currentDayNo"
                        :totalDays="$totalDaysSinceAdmission ?? 30" />
                </div>
            @endif
        </div>

        {{-- NOT AVAILABLE MESSAGE --}}
        @if ($selectedPatient && (!isset($vitalsData) || $vitalsData->count() == 0))
            <div class="mx-auto mt-2 mb-4 flex w-[90%] items-center gap-2 text-xs text-gray-500 italic md:w-[98%] lg:ml-10">
                <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                Clinical Decision Support System is not yet available (No data recorded for this date).
            </div>
        @endif

        {{-- Hidden form for synchronization of Date/Day No --}}
        <form id="patient-select-form" action="{{ route('vital-signs.select') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
        </form>

        {{-- MAIN FORM --}}
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

                <div class="mx-auto mt-5 flex w-full max-w-screen-2xl flex-col items-center justify-center gap-5 px-4 md:mt-8 md:w-[98%] md:flex-row lg:items-start md:gap-4">
                    
                    {{-- 1. CHARTS COLUMN --}}
                    <div class="w-full md:w-2/5">
                        <div class="relative overflow-hidden rounded-[20px]" id="chart-wrapper"></div>
                        <div id="fade-top" class="pointer-events-none absolute top-0 left-0 z-20 hidden h-10 w-full rounded-t-[20px] bg-gradient-to-b from-white/90 to-transparent"></div>

                        <div id="chart-viewport" class="relative h-auto md:max-h-[530px] overflow-y-auto rounded-[25px] no-scrollbar">
                            <div id="chart-track" class="transition-transform duration-700 ease-out">
                                @foreach(['tempChart' => 'TEMPERATURE', 'hrChart' => 'HEART RATE', 'rrChart' => 'RESPIRATORY RATE', 'bpChart' => 'BLOOD PRESSURE', 'spo2Chart' => 'SpO₂'] as $id => $title)
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl mb-4">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">{{ $title }} CHART</h2>
                                    <canvas id="{{ $id }}"></canvas>
                                </div>
                                @endforeach
                            </div>
                            <div id="fade-bottom" class="pointer-events-none absolute bottom-0 left-0 z-20 hidden h-10 w-full rounded-b-[20px] bg-gradient-to-t from-white/90 to-transparent"></div>
                        </div>

                        <div class="relative w-full h-0">
                            <button id="chart-up" type="button" class="btn-hidden absolute -top-[550px] left-1/2 z-30 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full border border-[#e2e8f0] bg-gradient-to-b from-white to-[#f1f5f9] text-[#64748b] shadow-md hover:bg-white hover:text-[#334155]">
                                <span class="material-symbols-outlined text-[32px]">arrow_drop_up</span>
                            </button>
                            <button id="chart-down" type="button" class="absolute -bottom-8 left-1/2 z-30 flex h-10 w-10 -translate-x-1/2 items-center justify-center rounded-full border border-[#e2e8f0] bg-gradient-to-b from-white to-[#f1f5f9] text-[#64748b] shadow-md hover:bg-white hover:text-[#334155]">
                                <span class="material-symbols-outlined text-[32px]">arrow_drop_down</span>
                            </button>
                        </div>
                    </div>

                    {{-- 2. VITAL SIGNS INPUTS COLUMN --}}
                    <div class="w-full md:w-3/5">
                        
                        {{-- A. WEB VIEW: Original Table (Hidden on Mobile) --}}
                        <div id="desktop-view" class="hidden md:block w-full overflow-hidden rounded-[15px]">
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
                                @foreach ($times as $time)
                                    @php $vitalsRecord = $vitalsData->get($time); @endphp
                                    <tr>
                                        <td class="p-2 font-semibold bg-yellow-light text-brown text-center">{{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}</td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="temperature_{{ $time }}" placeholder="°C" value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}" class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" data-field-name="temperature" data-time="{{ $time }}" autocomplete="off">
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="hr_{{ $time }}" placeholder="bpm" value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}" class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" data-field-name="hr" data-time="{{ $time }}" autocomplete="off">
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="rr_{{ $time }}" placeholder="bpm" value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}" class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" data-field-name="rr" data-time="{{ $time }}" autocomplete="off">
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="bp_{{ $time }}" placeholder="mmHg" value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}" class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" data-field-name="bp" data-time="{{ $time }}" autocomplete="off">
                                        </td>
                                        <td class="p-2 bg-beige text-center border-b-1 border-line-brown/70">
                                            <input type="text" name="spo2_{{ $time }}" placeholder="%" value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}" class="cdss-input vital-input h-[60px] w-full focus:outline-none text-center" data-field-name="spo2" data-time="{{ $time }}" autocomplete="off">
                                        </td>
                                        <td class="p-2 text-center align-middle border-0">
                                            <div class="h-[60px] flex justify-center items-center text-center px-2" data-alert-for-time="{{ $time }}">
                                                <div class="alert-icon-btn is-empty"><span class="material-symbols-outlined">notifications</span></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                        {{-- B. MOBILE VIEW: Card Style (Exactly matching Physical Exam) --}}
                        <div id="mobile-view" class="block md:hidden w-full space-y-4">
                            @foreach ($times as $time)
                                @php $vitalsRecord = $vitalsData->get($time); @endphp
                                
                                {{-- Card Container: Beige with Brown/Gold Border --}}
                                <div class="relative overflow-hidden rounded-[15px] border border-[#c18b04] bg-beige shadow-sm">
                                    
                                    {{-- Card Header: Uses 'main-header' class like Physical Exam --}}
                                    <div class="main-header w-full flex justify-between items-center pl-4 pr-2 py-2 text-[13px]">
                                        <span class="font-bold">TIME: {{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}</span>
                                        <div data-alert-for-time="{{ $time }}">
                                            <div class="alert-icon-btn is-empty scale-90">
                                                <span class="material-symbols-outlined">notifications</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Card Body: Beige background (from container) with inputs --}}
                                    <div class="grid grid-cols-2 gap-x-4 gap-y-3 p-4">
                                        {{-- Temp --}}
                                        <div class="flex flex-col">
                                            <label class="text-[11px] font-bold text-brown uppercase mb-1">Temperature</label>
                                            <input type="text" name="temperature_{{ $time }}" placeholder="°C" value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}" class="cdss-input vital-input w-full border-b border-[#c18b04]/50 bg-transparent p-1 text-sm focus:outline-none text-center" data-field-name="temperature" data-time="{{ $time }}" autocomplete="off">
                                        </div>
                                        {{-- HR --}}
                                        <div class="flex flex-col">
                                            <label class="text-[11px] font-bold text-brown uppercase mb-1">Heart Rate</label>
                                            <input type="text" name="hr_{{ $time }}" placeholder="bpm" value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}" class="cdss-input vital-input w-full border-b border-[#c18b04]/50 bg-transparent p-1 text-sm focus:outline-none text-center" data-field-name="hr" data-time="{{ $time }}" autocomplete="off">
                                        </div>
                                        {{-- RR --}}
                                        <div class="flex flex-col">
                                            <label class="text-[11px] font-bold text-brown uppercase mb-1">Resp Rate</label>
                                            <input type="text" name="rr_{{ $time }}" placeholder="bpm" value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}" class="cdss-input vital-input w-full border-b border-[#c18b04]/50 bg-transparent p-1 text-sm focus:outline-none text-center" data-field-name="rr" data-time="{{ $time }}" autocomplete="off">
                                        </div>
                                        {{-- BP --}}
                                        <div class="flex flex-col">
                                            <label class="text-[11px] font-bold text-brown uppercase mb-1">Blood Pressure</label>
                                            <input type="text" name="bp_{{ $time }}" placeholder="mmHg" value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}" class="cdss-input vital-input w-full border-b border-[#c18b04]/50 bg-transparent p-1 text-sm focus:outline-none text-center" data-field-name="bp" data-time="{{ $time }}" autocomplete="off">
                                        </div>
                                        {{-- SpO2 (Full Width) --}}
                                        <div class="flex flex-col col-span-2">
                                            <label class="text-[11px] font-bold text-brown uppercase mb-1 text-center">SpO₂</label>
                                            <input type="text" name="spo2_{{ $time }}" placeholder="%" value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}" class="cdss-input vital-input w-full border-b border-[#c18b04]/50 bg-transparent p-1 text-sm focus:outline-none text-center" data-field-name="spo2" data-time="{{ $time }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="mx-auto mt-5 mb-20 flex w-[90%] justify-end space-x-4 md:w-[98%]">
                    @if (isset($vitalsData) && $vitalsData->count() > 0)
                        <button type="submit" formaction="{{ route('vital-signs.cdss') }}"
                            class="button-default cdss-btn text-center">
                            CDSS
                        </button>
                    @endif
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </form>
        </fieldset>

        {{-- CHART MODAL (Unchanged) --}}
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
    @vite([
        'resources/js/patient-loader.js',
        'resources/js/alert.js',
        'resources/js/init.searchable-dropdown.js',
        'resources/js/date-day-sync.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/vital-signs-charts.js',
    ])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const vitalsData = @json($vitalsData);

        document.addEventListener('DOMContentLoaded', function () {
            // ... existing chart logic ...
            const timePoints = @json($times);
            if (window.initializeVitalSignsCharts) {
                window.initializeVitalSignsCharts(timePoints, vitalsData);
            }
            if (window.initializeChartScrolling) {
                window.initializeChartScrolling();
            }
            if (window.initSearchableDropdown) {
                window.initSearchableDropdown();
            }

            // --- DATA SYNC SCRIPT (Crucial for Mobile/Desktop toggle) ---
            // This prevents duplicate input submission issues
            const form = document.getElementById('vitals-form');
            if(form) {
                form.addEventListener('submit', function() {
                    const isMobile = window.innerWidth < 768; // Tailwind 'md' breakpoint
                    const desktopView = document.getElementById('desktop-view');
                    const mobileView = document.getElementById('mobile-view');
                    
                    // Disable inputs in the hidden view so they are not submitted
                    if(isMobile) {
                        const desktopInputs = desktopView.querySelectorAll('input');
                        desktopInputs.forEach(input => input.disabled = true);
                    } else {
                        const mobileInputs = mobileView.querySelectorAll('input');
                        mobileInputs.forEach(input => input.disabled = true);
                    }
                });
            }
        });

        // ... existing closeChartModal and Carousel logic ...
        window.closeChartModal = function () {
            const modal = document.getElementById('chart-modal');
            if (modal) modal.style.display = 'none';
            if (window.modalChartInstance) {
                window.modalChartInstance.destroy();
                window.modalChartInstance = null;
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            const modalWrapper = document.getElementById('chart-modal');
            if (modalWrapper) {
                modalWrapper.addEventListener('click', function (event) {
                    if (event.target === modalWrapper) {
                        closeChartModal();
                    }
                });
            }

            const sidebar = document.getElementById('mySidenav');
            if (sidebar) {
                const observer = new MutationObserver((mutations) => {
                    mutations.forEach((mutation) => {
                        if (mutation.attributeName === 'class') {
                            const isSidebarOpen = !sidebar.classList.contains('-translate-x-full');
                            if (isSidebarOpen) closeChartModal();
                        }
                    });
                });
                observer.observe(sidebar, { attributes: true });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            let currentStep = 0;
            const totalSteps = 3;

            function updateCarousel() {
                const track = document.getElementById('chart-track');
                const upBtn = document.getElementById('chart-up');
                const downBtn = document.getElementById('chart-down');
                const cards = track ? track.querySelectorAll(':scope > div') : [];

                if (!track || !upBtn || !downBtn || !cards.length) return;

                const cardHeight = cards[0].offsetHeight + 16; // Height + margin
                const moveDistance = cardHeight;
                let translateY = 0;

                upBtn.classList.remove('btn-hidden');
                downBtn.classList.remove('btn-hidden');

                if (currentStep === 0) {
                    translateY = 0;
                    upBtn.classList.add('btn-hidden');
                } else {
                    translateY = moveDistance * currentStep;
                }
                
                if (currentStep >= totalSteps) {
                    downBtn.classList.add('btn-hidden');
                }

                track.style.transform = `translateY(-${translateY}px)`;
            }

            document.addEventListener('click', function (e) {
                if (e.target.closest('#chart-down')) {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        updateCarousel();
                    }
                }
                if (e.target.closest('#chart-up')) {
                    if (currentStep > 0) {
                        currentStep--;
                        updateCarousel();
                    }
                }
            });

            updateCarousel();
        });
    </script>
@endpush