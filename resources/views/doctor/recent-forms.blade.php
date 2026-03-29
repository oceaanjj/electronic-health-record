@extends('layouts.doctor')
@section('title', 'Recent Forms')

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .pills-scroll::-webkit-scrollbar { height: 3px; }
        .pills-scroll::-webkit-scrollbar-track { background: transparent; }
        .pills-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
        .dark .pills-scroll::-webkit-scrollbar-thumb { background: #334155; }
    </style>
@endpush

@section('content')
<div class="-mx-6 min-h-screen bg-slate-50 dark:bg-slate-950 font-rubik transition-colors duration-300">

    {{-- ── TOP BANNER ── --}}
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 sm:px-10 py-5 mb-6 transition-colors duration-300">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <nav class="flex items-center gap-1.5 mb-1.5">
                    <a href="{{ route('doctor-home') }}" class="text-sm font-alte-regular text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
                    <span class="material-symbols-outlined text-slate-300 dark:text-slate-700" style="font-size:13px">chevron_right</span>
                    <span class="text-sm font-alte-regular text-slate-700 dark:text-slate-300">Recent Forms</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-alte text-slate-800 dark:text-white flex items-center gap-2 leading-tight">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-500 flex-shrink-0" style="font-size:24px">fact_check</span>
                    Recent Form Submissions
                </h1>
                <p class="text-base font-alte-regular text-slate-400 dark:text-slate-500 mt-1">All patient assessment forms submitted across all accounts.</p>
            </div>
            {{-- Total badge — JS updates the inner text on each fetch --}}
            <span id="rf-total-badge" class="inline-flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-400 text-sm font-alte px-5 py-2.5 rounded-full self-start sm:self-auto whitespace-nowrap">
                <span class="material-symbols-outlined" style="font-size:14px">inventory_2</span>
                <span id="rf-total-text">{{ number_format($total) }} record{{ $total !== 1 ? 's' : '' }}</span>
            </span>
        </div>
    </div>

    {{-- ── PAGE BODY ── --}}
    <div class="px-4 sm:px-6 md:px-10 pb-12">
        <div class="max-w-7xl mx-auto space-y-4">

            {{-- ── FILTER BAR ── --}}
            <form id="rf-filter-form" method="GET" action="{{ route('doctor.recent-forms') }}"
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-5 transition-colors duration-300">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    {{-- Form Type --}}
                    <div>
                        <label class="block text-xs font-alte text-slate-400 uppercase tracking-widest mb-1.5">Form Type</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" style="font-size:16px">filter_list</span>
                            <select id="rf-type" name="type"
                                class="w-full pl-10 pr-3 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-alte-regular text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent appearance-none cursor-pointer transition-all">
                                <option value="all" {{ $filterType === 'all' ? 'selected' : '' }}>All Forms</option>
                                @foreach ($formTypes as $key => $label)
                                    <option value="{{ $key }}" {{ $filterType === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Patient Name --}}
                    <div>
                        <label class="block text-xs font-alte text-slate-400 uppercase tracking-widest mb-1.5">Patient Name</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" style="font-size:16px">search</span>
                            <input id="rf-patient" type="text" name="patient" value="{{ $filterPatient }}" placeholder="Search by name…"
                                class="w-full pl-10 pr-3 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-alte-regular text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" />
                        </div>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-xs font-alte text-slate-400 uppercase tracking-widest mb-1.5">Date</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" style="font-size:16px">calendar_today</span>
                            <input id="rf-date" type="date" name="date" value="{{ $filterDate }}"
                                class="w-full pl-10 pr-3 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-alte-regular text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" />
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white text-base font-alte px-4 py-2.5 rounded-xl shadow-sm transition-all duration-150 whitespace-nowrap">
                            <span class="material-symbols-outlined" style="font-size:16px">search</span>
                            Filter
                        </button>
                        <a id="rf-clear-btn" href="{{ route('doctor.recent-forms') }}"
                            class="flex-shrink-0 inline-flex items-center justify-center w-10 h-[42px] bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-xl transition-colors"
                            title="Clear filters"
                            style="{{ ($filterType === 'all' && !$filterPatient && !$filterDate) ? 'display:none' : '' }}">
                            <span class="material-symbols-outlined" style="font-size:18px">close</span>
                        </a>
                    </div>

                </div>
            </form>

            {{-- ── TYPE PILL TABS ── --}}
            <div class="overflow-x-auto pills-scroll -mx-1 px-1 pb-1">
                <div class="flex items-center gap-2 w-max">
                    @php
                        $pillMap = [
                            'all'                => ['All',             '#10b981'],
                            'vital-signs'        => ['Vital Signs',     '#EF4444'],
                            'physical-exam'      => ['Physical Exam',   '#8B5CF6'],
                            'adl'                => ['Daily Living',    '#F97316'],
                            'intake-output'      => ['Intake & Output', '#3B82F6'],
                            'lab-values'         => ['Lab Values',      '#0D9488'],
                            'medication'         => ['Medication',      '#10B981'],
                            'ivs-lines'          => ['IVs & Lines',     '#6366F1'],
                            'medical-history'    => ['Med. History',    '#6366F1'],
                            'diagnostics'        => ['Diagnostics',     '#0D9488'],
                            'med-reconciliation' => ['Med. Recon.',    '#10B981'],
                            'discharge-plan'     => ['Discharge',       '#f59e0b'],
                        ];
                    @endphp
                    @foreach ($pillMap as $key => $pill)
                        @php [$pillLabel, $pillColor] = $pill; $isActive = $filterType === $key; @endphp
                        <a href="#"
                            class="rf-pill inline-flex items-center gap-1.5 px-5 py-2 rounded-full text-sm border whitespace-nowrap transition-all duration-150 hover:scale-105"
                            data-pill-type="{{ $key }}"
                            data-pill-color="{{ $pillColor }}"
                            style="
                                background-color: {{ $isActive ? $pillColor : 'transparent' }};
                                color: {{ $isActive ? '#fff' : ($isActive ? '#fff' : '#94a3b8') }};
                                border-color: {{ $isActive ? $pillColor : 'rgba(148, 163, 184, 0.2)' }};
                                font-family: {{ $isActive ? "'Alte Haas Grotesk Bold', arial" : "'Alte Haas Grotesk', arial" }};
                            ">
                            {{ $pillLabel }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- ── RESULTS (swapped by JS on each fetch) ── --}}
            <div id="rf-results-wrap" style="position:relative">
                @include('doctor.partials.recent-forms-results')
            </div>

        </div>{{-- end max-w-7xl --}}
    </div>{{-- end page body --}}

</div>{{-- end full-width wrapper --}}
@endsection

@push('scripts')
    @vite('resources/js/doctor/recent-forms.js')
@endpush
