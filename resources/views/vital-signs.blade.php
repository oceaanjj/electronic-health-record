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

    <div id="form-content-container" class="mx-auto max-w-full">

        <div class="mx-auto mt-1 w-full max-w-full">
            {{-- 1. THE ALERT/ERROR --}}
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
            <div class="mx-auto w-full px-4 pt-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-y-4 lg:gap-x-10 lg:ml-20">
                    
                    {{-- 1. PATIENT SECTION --}}
                    <div class="flex items-center md:pl-12 gap-4 w-full md:w-auto justify-start">
                        <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>
                        <div class="w-full md:w-[350px]">
                            <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                                :selectRoute="route('vital-signs.select')" :inputValue="$selectedPatient?->patient_id ?? ''" />
                        </div>
                    </div>

                    {{-- 2. DATE & DAY SECTION --}}
                    @if ($selectedPatient)
                        <div class="flex items-center gap-4 md:pl-12 lg:pl-0">
                            <x-date-day-selector :currentDate="$currentDate" :currentDayNo="$currentDayNo"
                                :totalDays="$totalDaysSinceAdmission ?? 30" />
                        </div>
                    @endif
                </div>

                {{-- CDSS ALERT MESSAGE --}}
                @if ($selectedPatient && (!isset($vitalsData) || $vitalsData->count() == 0))
                    <div class="mt-4 px-4 lg:ml-32 flex items-center gap-2 text-xs italic text-gray-500">
                        <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                        Clinical Decision Support System is not yet available (No data recorded for this date).
                    </div>
                @endif
            </div>
        </div>

        {{-- Hidden form for synchronization --}}
        <form id="patient-select-form" action="{{ route('vital-signs.select') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />
        </form>

        {{-- MAIN CONTENT --}}
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

                {{-- 
                    LAYOUT CONTAINER:
                    Mobile: Flex Col (Stack)
                    Desktop: Flex Row, Justify Between, Specific Widths to fit 100%
                --}}
                <div class="mx-auto mt-5 flex w-full max-w-screen-2xl flex-col gap-6 md:gap-4 px-4 md:mt-15 md:w-[95%] md:flex-row md:items-start md:justify-between md:px-0">
                    
                    {{-- 1. CHARTS COLUMN (30% on Desktop) --}}
                    <div class="relative w-full md:w-[30%]">
                        <div class="relative overflow-hidden rounded-[20px]" id="chart-wrapper"></div>
                        <div id="fade-top" class="pointer-events-none absolute top-0 left-0 z-20 hidden h-10 w-full rounded-t-[20px] bg-gradient-to-b from-white/90 to-transparent"></div>

                        <div id="chart-viewport" class="relative overflow-hidden rounded-[25px]">
                            <div id="chart-track" class="transition-transform duration-700 ease-out">
                                {{-- TEMP --}}
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">TEMPERATURE CHART</h2>
                                    <canvas id="tempChart"></canvas>
                                </div>
                                {{-- HR --}}
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">HEART RATE CHART</h2>
                                    <canvas id="hrChart"></canvas>
                                </div>
                                {{-- RR --}}
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">RESPIRATORY RATE CHART</h2>
                                    <canvas id="rrChart"></canvas>
                                </div>
                                {{-- BP --}}
                                <div class="h-[220px] rounded-[24px] border-t-2 border-r border-b border-l border-gray-100/50 border-white bg-gradient-to-br from-white via-[#edecec] to-[#f1f5f9] p-4 pb-12 shadow-[0_10px_20px_rgba(0,0,0,0.05),0_6px_6px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-xl">
                                    <h2 class="mb-2 text-center text-sm font-bold tracking-wide text-[#334155] uppercase opacity-80">BLOOD PRESSURE CHART</h2>
                                    <canvas id="bpChart"></canvas>
                                </div>
                                {{-- SpO2 --}}
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

                    {{-- 2. DATA TABLE COLUMN (48% on Desktop) --}}
                    <div class="w-full rounded-[15px] md:w-[48%]">
                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            {{-- HEADERS (Hidden on Mobile) --}}
                            <thead class="hidden md:table-header-group">
                                <tr>
                                    <th class="main-header min-w-[90px] w-[15%] rounded-tl-lg">TIME</th>
                                    <th class="main-header min-w-[110px] w-[18%]">TEMPERATURE</th>
                                    <th class="main-header min-w-[70px] w-[10%]">HR</th>
                                    <th class="main-header min-w-[70px] w-[10%]">RR</th>
                                    <th class="main-header min-w-[90px] w-[10%]">BP</th>
                                    <th class="main-header min-w-[70px] w-[10%]">SpO₂</th>
                                </tr>
                            </thead>

                            <tbody class="block w-full md:table-row-group">
                                @foreach ($times as $index => $time)
                                    @php
                                        $vitalsRecord = $vitalsData->get($time);
                                        $isLast = $index === count($times) - 1;
                                        $borderClass = $isLast ? '' : 'border-line-brown/70 border-b-2';
                                        $formattedTime = \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
                                    @endphp

                                    {{-- CARD ROW (Mobile: Flex Column, Desktop: Table Row) --}}
                                    <tr class="flex flex-col md:table-row border border-line-brown/70 md:border-b-2 md:border-t-0 md:border-x-0 mb-6 md:mb-0 rounded-lg md:rounded-none overflow-hidden shadow-sm md:shadow-none bg-beige {{ $isLast ? '' : 'md:border-b-2' }}">
                                        
                                        {{-- TIME COLUMN --}}
                                        <th class="order-first md:order-none block md:table-cell bg-yellow-light text-brown md:{{ $borderClass }} p-0 md:py-2 text-center font-semibold border-b border-line-brown md:border-b-0">
                                            {{-- Mobile Header --}}
                                            <div class="md:hidden w-full main-header text-[14px] font-bold p-3 text-center">
                                                TIME: {{ $formattedTime }}
                                            </div>
                                            {{-- Desktop Content --}}
                                            <span class="hidden md:block">{{ $formattedTime }}</span>
                                        </th>

                                        {{-- TEMPERATURE --}}
                                        <td class="block md:table-cell bg-beige md:{{ $borderClass }} p-0 border-b border-gray-300/50 md:border-none">
                                            <div class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">TEMPERATURE</div>
                                            <div class="p-2 md:p-0">
                                                <input type="text" name="temperature_{{ $time }}" placeholder="temp"
                                                    value="{{ old('temperature_' . $time, optional($vitalsRecord)->temperature) }}"
                                                    class="cdss-input vital-input h-[50px] md:h-[60px] w-full" data-field-name="temperature"
                                                    data-time="{{ $time }}" />
                                            </div>
                                        </td>

                                        {{-- HR --}}
                                        <td class="block md:table-cell bg-beige md:{{ $borderClass }} p-0 border-b border-gray-300/50 md:border-none">
                                            <div class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">HR</div>
                                            <div class="p-2 md:p-0">
                                                <input type="text" name="hr_{{ $time }}" placeholder="bpm"
                                                    value="{{ old('hr_' . $time, optional($vitalsRecord)->hr) }}"
                                                    class="cdss-input vital-input h-[50px] md:h-[60px] w-full" data-field-name="hr"
                                                    data-time="{{ $time }}" />
                                            </div>
                                        </td>

                                        {{-- RR --}}
                                        <td class="block md:table-cell bg-beige md:{{ $borderClass }} p-0 border-b border-gray-300/50 md:border-none">
                                            <div class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">RR</div>
                                            <div class="p-2 md:p-0">
                                                <input type="text" name="rr_{{ $time }}" placeholder="bpm"
                                                    value="{{ old('rr_' . $time, optional($vitalsRecord)->rr) }}"
                                                    class="cdss-input vital-input h-[50px] md:h-[60px] w-full" data-field-name="rr"
                                                    data-time="{{ $time }}" />
                                            </div>
                                        </td>

                                        {{-- BP --}}
                                        <td class="block md:table-cell bg-beige md:{{ $borderClass }} p-0 border-b border-gray-300/50 md:border-none">
                                            <div class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">BP</div>
                                            <div class="p-2 md:p-0">
                                                <input type="text" name="bp_{{ $time }}" placeholder="mmHg"
                                                    value="{{ old('bp_' . $time, optional($vitalsRecord)->bp) }}"
                                                    class="cdss-input vital-input h-[50px] md:h-[60px] w-full" data-field-name="bp"
                                                    data-time="{{ $time }}" />
                                            </div>
                                        </td>

                                        {{-- SpO₂ --}}
                                        <td class="block md:table-cell bg-beige md:{{ $borderClass }} p-0 md:border-none">
                                            <div class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">SpO₂</div>
                                            <div class="p-2 md:p-0">
                                                <input type="text" name="spo2_{{ $time }}" placeholder="%"
                                                    value="{{ old('spo2_' . $time, optional($vitalsRecord)->spo2) }}"
                                                    class="cdss-input vital-input h-[50px] md:h-[60px] w-full" data-field-name="spo2"
                                                    data-time="{{ $time }}" />
                                            </div>
                                        </td>

                                        {{-- ALERT CELL (VISIBLE ON MOBILE ONLY) --}}
                                        <td class="block md:hidden bg-beige p-2 border-t border-gray-300/50" data-alert-for-time="{{ $time }}">
                                            <div class="alert-box flex h-[50px] w-full items-center justify-center rounded-lg"
                                                data-alert-for-time="{{ $time }}">
                                                <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. ALERTS COLUMN (VISIBLE ON DESKTOP ONLY, 18% width) --}}
                    <div class="hidden md:block w-full rounded-[15px] md:w-[18%]">
                        <div class="main-header rounded-[15px] mb-4 md:mb-0">ALERTS</div>

                        <table class="w-full border-collapse">
                            <tbody class="block md:table-row-group">
                                @foreach ($times as $time)
                                    <tr class="flex flex-col md:table-row mb-6 md:mb-0">
                                        <td class="align-middle block md:table-cell" data-alert-for-time="{{ $time }}">
                                            <div class="alert-box flex h-[50px] md:h-[62px] w-full items-center justify-center rounded-lg md:rounded-none"
                                                data-alert-for-time="{{ $time }}">
                                                <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="mx-auto mt-5 mb-20 flex w-full justify-center space-x-4 md:w-[95%] md:justify-end">
                    @if (isset($vitalsData) && $vitalsData->count() > 0)
                        <button type="submit" formaction="{{ route('vital-signs.cdss') }}" class="button-default text-center">
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
                    <button type="button" onclick="closeChartModal()" class="cursor-pointer rounded-full p-2 transition-colors hover:bg-gray-100">
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
        });

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

                const cardHeight = cards[0].offsetHeight;
                const moveDistance = cardHeight;
                let translateY = 0;

                upBtn.classList.remove('btn-hidden');
                downBtn.classList.remove('btn-hidden');

                if (currentStep === 0) {
                    translateY = 0;
                    upBtn.classList.add('btn-hidden');
                } else if (currentStep === 1) {
                    translateY = moveDistance * 1.3;
                } else if (currentStep === 2) {
                    translateY = moveDistance * 2.3;
                } else if (currentStep === 3) {
                    translateY = moveDistance * 3.3;
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

            window.resetChartCarousel = function () {
                currentStep = 0;
                updateCarousel();
            };
        });
    </script>
@endpush