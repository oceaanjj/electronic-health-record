@extends('layouts.app')
@section('title', 'Patient Lab Values')
@section('content')
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(251, 191, 36, 0.1);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(251, 191, 36, 0.4);
            border-radius: 10px;
        }
    </style>

    <div id="form-content-container" class="w-full overflow-x-hidden relative">

        {{-- FLOATING CORRELATION ALERTS (Patterns) --}}
        <div id="correlation-overlay" class="fixed bottom-10 right-5 z-[1000] w-[90%] md:w-[320px] pointer-events-none hidden">
            <div class="pointer-events-auto bg-white/95 backdrop-blur-lg border-l-4 border-amber-500 rounded-xl shadow-2xl p-5 overflow-hidden transform transition-all duration-500 hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5 text-amber-700">
                        <div class="bg-amber-100 p-1.5 rounded-lg">
                            <span class="material-symbols-outlined text-[20px] animate-pulse">psychology</span>
                        </div>
                        <h3 class="font-alte font-bold text-[11px] uppercase tracking-widest">CDSS Lab Pattern Correlation</h3>
                    </div>
                    <button type="button" onclick="document.getElementById('correlation-overlay').classList.add('hidden')" class="bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-full p-1 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>

                <div id="correlation-list-container" class="max-h-[250px] overflow-y-auto pr-2 custom-scrollbar">
                    <ul id="correlation-list" class="space-y-3.5">
                        {{-- Populated by JS --}}
                    </ul>
                </div>
                
                <div class="mt-5 pt-4 border-t border-amber-100/50 flex items-center justify-between opacity-70">
                    <div class="flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        <span class="text-[9px] font-bold text-amber-800 uppercase tracking-tighter">Clinical Correlation Analysis</span>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-amber-600">verified_user</span>
                </div>
            </div>
        </div>

        {{-- CDSS ALERT BANNER --}}
        @if (session('selected_patient_id') && isset($labValue))
            <div id="cdss-alert-wrapper" class="w-full px-5 overflow-hidden transition-all duration-500">
                <div id="cdss-alert-content"
                    class="relative flex items-center justify-between mt-3 py-3 px-5 border border-amber-400/50 rounded-lg shadow-sm bg-amber-100/70 backdrop-blur-md animate-alert-in">

                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#dcb44e] animate-pulse">info</span>
                        <span class="text-sm font-semibold text-[#dcb44e]">
                            Clinical Decision Support System is now available for this date.
                        </span>
                    </div>

                    <button type="button" onclick="closeCdssAlert()"
                        class="group flex items-center justify-center text-amber-700 hover:bg-amber-200/50 rounded-full p-1 transition-all duration-300 active:scale-90">
                        <span
                            class="material-symbols-outlined text-[20px] group-hover:rotate-90 transition-transform duration-300">close</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- HEADER SECTION - Aligned to the left side of the table --}}
        <div class="mx-auto mt-10 mb-5 flex w-[90%] flex-col items-start gap-4 md:w-[80%] md:flex-row md:items-center">
            <label class="font-alte text-dark-green font-bold whitespace-nowrap shrink-0">
                PATIENT NAME :
            </label>

            <div class="w-full px-2 md:w-[350px] md:px-0">
                <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                    selectRoute="{{ route('lab-values.select') }}" inputPlaceholder="Search or type Patient Name..."
                    inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />
            </div>
        </div>

        {{-- NOT AVAILABLE MESSAGE - Aligned to the left side of the table --}}
        @if (session('selected_patient_id') && !isset($labValue))
            <div class="mx-auto mt-2 mb-4 flex w-[90%] items-center gap-2 text-xs text-gray-500 italic md:w-[80%]">
                <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                Clinical Decision Support System is not yet available (No lab records found).
            </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('lab-values.store') }}" method="POST" class="cdss-form"
            data-analyze-url="{{ route('lab-values.run-cdss-field') }}"
            data-batch-analyze-url="{{ route('lab-values.analyze-batch') }}" 
            data-correlations-url="{{ route('lab-values.analyze-correlations') }}"
            data-alert-height-class="h-[49.5px]">
            @csrf
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>

                {{-- SECTION TITLE HEADER --}}
                <div class="mx-auto mt-2 flex w-[90%] items-center gap-1 md:w-[80%]">
                    <div class="flex-1 text-left md:text-center">
                        <p class="main-header mb-1 rounded-[15px]">LABORATORY VALUES</p>
                    </div>
                    {{-- Spacer for alert icon--}}
                    <div class="hidden md:block w-[70px]"></div>
                </div>

                <center>
                    {{-- MAIN CONTENT WRAPPER --}}
                    <div class="mb-1.5 flex w-[90%] md:w-[80%] flex-col items-start">
                        <div class="hidden md:flex w-full flex-row items-center gap-1">
                            <table class="w-full table-fixed border-collapse border-spacing-y-0 flex-1">
                                <thead>
                                    <tr>
                                        <th class="w-[20%] main-header rounded-tl-[15px]">LAB TEST</th>
                                        <th class="w-[30%] main-header">RESULT</th>
                                        <th class="w-[50%] main-header rounded-tr-[15px]">NORMAL RANGE</th>
                                    </tr>
                                </thead>
                            </table>
                            {{-- Spacer for alert icon--}}
                            <div class="w-[70px]"></div>
                        </div>

                        {{-- TABLE BODY ROWS --}}
                        <div class="w-full">
                            @php
                                $labTests = [
                                    'WBC (×10⁹/L)' => 'wbc',
                                    'RBC (×10¹²/L)' => 'rbc',
                                    'Hgb (g/dL)' => 'hgb',
                                    'Hct (%)' => 'hct',
                                    'Platelets (×10⁹/L)' => 'platelets',
                                    'MCV (fL)' => 'mcv',
                                    'MCH (pg)' => 'mch',
                                    'MCHC (g/dL)' => 'mchc',
                                    'RDW (%)' => 'rdw',
                                    'Neutrophils (%)' => 'neutrophils',
                                    'Lymphocytes (%)' => 'lymphocytes',
                                    'Monocytes (%)' => 'monocytes',
                                    'Eosinophils (%)' => 'eosinophils',
                                    'Basophils (%)' => 'basophils'
                                ];
                            @endphp

                            @foreach ($labTests as $label => $name)
                                <div
                                    class="relative mb-6 md:mb-0 flex flex-col md:flex-row items-center rounded-[15px] border border-[#c18b04] bg-beige md:rounded-none md:border-none md:bg-transparent md:overflow-visible md:gap-1">

                                    {{-- Mobile Label Header --}}
                                    <div class="main-header w-full p-4 text-left md:hidden pr-12">
                                        {{ $label }}
                                    </div>

                                    {{-- Row Table --}}
                                    <table class="w-full flex-5 table-fixed border-collapse md:pr-5">
                                        <tr class="flex flex-row md:table-row w-full">
                                            {{-- Desktop Label Cell --}}
                                            <td
                                                class="hidden md:table-cell w-[20%] p-2 font-semibold bg-yellow-light text-brown text-center border-b border-line-brown/70">
                                                {{ $label }}
                                            </td>

                                            {{-- Result Field --}}
                                            <td
                                                class="flex-1 md:w-[30%] p-0 md:p-2 bg-beige text-center border-b border-line-brown/70 md:border-r-0">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="bg-yellow-light text-brown font-bold text-[10px] py-1 uppercase md:hidden border-b border-line-brown/30">Result</span>
                                                    <div class="p-2 md:p-0">
                                                        <input type="text" name="{{ $name }}_result" placeholder="Result"
                                                            value="{{ old($name . '_result', optional($labValue)->{$name . '_result'}) }}"
                                                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                            class="w-full h-[40px] focus:outline-none text-center cdss-input bg-white md:bg-transparent rounded md:rounded-none border border-amber-200 md:border-none"
                                                            data-field-name="{{ $name }}_result" />
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Range Field --}}
                                            <td
                                                class="flex-1 md:w-[50%] p-0 md:p-2 bg-beige text-center border-b border-line-brown/70">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="bg-yellow-light text-brown font-bold text-[10px] py-1 uppercase md:hidden border-b border-line-brown/30">Range</span>
                                                    <div class="p-2 md:p-0">
                                                        <input type="text" name="{{ $name }}_normal_range"
                                                            placeholder="Normal Range"
                                                            value="{{ old($name . '_normal_range', optional($labValue)->{$name . '_normal_range'}) }}"
                                                            class="w-full h-[40px] focus:outline-none text-center bg-white md:bg-transparent rounded md:rounded-none border border-amber-200 md:border-none" />
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    {{-- ALERT ICON --}}
                                    <div class="absolute right-4 top-2.5 z-10 flex items-center justify-center md:static md:flex md:w-[70px] md:h-[56px] md:pl-5"
                                        data-alert-for="{{ $name }}_result">
                                        <div class="alert-icon-btn is-empty">
                                            <span class="material-symbols-outlined">notifications</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </center>

                {{-- BUTTONS --}}
                <div class="mx-auto mt-5 mb-20 md:mb-30 flex w-[90%] md:w-[80%] flex-row justify-end gap-4">
                    @if (isset($labValue))
                        <button type="submit" name="action" value="cdss"
                            class="button-default cdss-btn inline-block text-center">
                            CDSS
                        </button>
                    @endif
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </fieldset>
        </form>
    </div>

@endsection

@push('scripts')
    @vite([
        'resources/js/alert.js',
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/close-cdss-alert.js'
    ])

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.cdss-form');
            if (!form) return;

            const overlay = document.getElementById('correlation-overlay');
            const list = document.getElementById('correlation-list');
            const correlationUrl = form.dataset.correlationsUrl;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Function to update the correlation overlay
            async function updateCorrelations() {
                const inputs = form.querySelectorAll('.cdss-input');
                const batchPayload = [];
                
                inputs.forEach(input => {
                    const finding = input.value.trim();
                    if (finding !== '') {
                        batchPayload.push({
                            fieldName: input.dataset.fieldName,
                            finding: finding
                        });
                    }
                });

                if (batchPayload.length === 0) {
                    overlay.classList.add('hidden');
                    return;
                }

                try {
                    const response = await fetch(correlationUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ batch: batchPayload }),
                    });

                    if (!response.ok) return;
                    const data = await response.json();
                    
                    // The updated controller now returns { results: [], correlations: [] }
                    const correlations = data.correlations || [];

                    if (correlations.length > 0) {
                        list.innerHTML = '';
                        correlations.forEach(c => {
                            const li = document.createElement('li');
                            li.className = 'group flex items-start gap-3 p-2 rounded-lg hover:bg-amber-50 transition-colors';
                            li.innerHTML = `
                                <div class="mt-1 w-2 h-2 rounded-full bg-amber-400 shrink-0 shadow-sm shadow-amber-200"></div>
                                <span class="text-[12.5px] font-semibold text-amber-900 leading-snug">${c.text}</span>
                            `;
                            list.appendChild(li);
                        });
                        overlay.classList.remove('hidden');
                        overlay.classList.add('animate-fade-in');
                    } else {
                        overlay.classList.add('hidden');
                    }
                } catch (error) {
                    console.error('Correlation analysis failed:', error);
                }
            }

            // Listen for input changes with a small delay
            let debounceTimer;
            form.addEventListener('input', (e) => {
                if (e.target.classList.contains('cdss-input')) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(updateCorrelations, 600);
                }
            });

            // Initial check
            setTimeout(updateCorrelations, 1000);
        });
    </script>
@endpush