@extends('layouts.doctor')
@section('title', 'Recent Forms')

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .pills-scroll::-webkit-scrollbar { height: 3px; }
        .pills-scroll::-webkit-scrollbar-track { background: transparent; }
        .pills-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 9999px; }
    </style>
@endpush

@section('content')
<div class="-mx-6 min-h-screen bg-[#f0f8f0] font-rubik">

    {{-- ── TOP BANNER ── --}}
    <div class="bg-white border-b border-gray-200 px-6 sm:px-10 py-5 mb-6">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <nav class="flex items-center gap-1.5 mb-1.5">
                    <a href="{{ route('doctor-home') }}" class="text-sm font-alte-regular text-gray-400 hover:text-green-700 transition-colors">Dashboard</a>
                    <span class="material-symbols-outlined text-gray-300" style="font-size:13px">chevron_right</span>
                    <span class="text-sm font-alte-regular text-gray-700">Recent Forms</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-alte text-gray-800 flex items-center gap-2 leading-tight">
                    <span class="material-symbols-outlined text-green-700 flex-shrink-0" style="font-size:24px">fact_check</span>
                    Recent Form Submissions
                </h1>
                <p class="text-base font-alte-regular text-gray-400 mt-1">All patient assessment forms submitted across all accounts.</p>
            </div>
            {{-- Total badge — JS updates the inner text on each fetch --}}
            <span id="rf-total-badge" class="inline-flex items-center gap-1.5 bg-green-50 border border-green-200 text-green-800 text-sm font-alte px-5 py-2.5 rounded-full self-start sm:self-auto whitespace-nowrap">
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
                class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    {{-- Form Type --}}
                    <div>
                        <label class="block text-sm font-alte text-gray-500 uppercase tracking-widest mb-1.5">Form Type</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" style="font-size:16px">filter_list</span>
                            <select id="rf-type" name="type"
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl text-sm font-alte-regular text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none cursor-pointer">
                                <option value="all" {{ $filterType === 'all' ? 'selected' : '' }}>All Forms</option>
                                @foreach ($formTypes as $key => $label)
                                    <option value="{{ $key }}" {{ $filterType === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Patient Name --}}
                    <div>
                        <label class="block text-sm font-alte text-gray-500 uppercase tracking-widest mb-1.5">Patient Name</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" style="font-size:16px">search</span>
                            <input id="rf-patient" type="text" name="patient" value="{{ $filterPatient }}" placeholder="Search by name…"
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl text-sm font-alte-regular text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                        </div>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-sm font-alte text-gray-500 uppercase tracking-widest mb-1.5">Date</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" style="font-size:16px">calendar_today</span>
                            <input id="rf-date" type="date" name="date" value="{{ $filterDate }}"
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl text-sm font-alte-regular text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 bg-green-700 hover:bg-green-800 active:bg-green-900 text-white text-base font-alte px-4 py-2.5 rounded-xl shadow-sm transition-all duration-150 whitespace-nowrap">
                            <span class="material-symbols-outlined" style="font-size:16px">search</span>
                            Filter
                        </button>
                        <a id="rf-clear-btn" href="{{ route('doctor.recent-forms') }}"
                            class="flex-shrink-0 inline-flex items-center justify-center w-10 h-[42px] bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-xl transition-colors"
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
                            'all'           => ['All',             '#16A34A'],
                            'vital-signs'   => ['Vital Signs',     '#EF4444'],
                            'physical-exam' => ['Physical Exam',   '#8B5CF6'],
                            'adl'           => ['Daily Living',    '#F97316'],
                            'intake-output' => ['Intake & Output', '#3B82F6'],
                            'lab-values'    => ['Lab Values',      '#0D9488'],
                            'medication'    => ['Medication',      '#10B981'],
                            'ivs-lines'     => ['IVs & Lines',     '#6366F1'],
                        ];
                    @endphp
                    @foreach ($pillMap as $key => $pill)
                        @php [$pillLabel, $pillColor] = $pill; $isActive = $filterType === $key; @endphp
                        <a href="#"
                            class="rf-pill inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full text-sm border whitespace-nowrap transition-all duration-150 hover:opacity-90"
                            data-pill-type="{{ $key }}"
                            data-pill-color="{{ $pillColor }}"
                            style="
                                background-color: {{ $isActive ? $pillColor : '#fff' }};
                                color: {{ $isActive ? '#fff' : '#374151' }};
                                border-color: {{ $isActive ? $pillColor : '#E5E7EB' }};
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
