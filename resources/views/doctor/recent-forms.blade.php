@extends('layouts.doctor')
@section('title', 'Recent Forms')

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        /* Custom scrollbar for pills row */
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
                {{-- Breadcrumb --}}
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
            <span class="inline-flex items-center gap-1.5 bg-green-50 border border-green-200 text-green-800 text-sm font-alte px-5 py-2.5 rounded-full self-start sm:self-auto whitespace-nowrap">
                <span class="material-symbols-outlined" style="font-size:14px">inventory_2</span>
                {{ number_format($total) }} record{{ $total !== 1 ? 's' : '' }}
            </span>
        </div>
    </div>

    {{-- ── PAGE BODY ── --}}
    <div class="px-4 sm:px-6 md:px-10 pb-12">
        <div class="max-w-7xl mx-auto space-y-4">

            {{-- ── FILTER BAR ── --}}
            <form method="GET" action="{{ route('doctor.recent-forms') }}"
                class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    {{-- Form Type --}}
                    <div>
                        <label class="block text-sm font-alte text-gray-500 uppercase tracking-widest mb-1.5">Form Type</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" style="font-size:16px">filter_list</span>
                            <select name="type"
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
                            <input type="text" name="patient" value="{{ $filterPatient }}" placeholder="Search by name…"
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl text-sm font-alte-regular text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                        </div>
                    </div>

                    {{-- Date --}}
                    <div>
                        <label class="block text-sm font-alte text-gray-500 uppercase tracking-widest mb-1.5">Date</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" style="font-size:16px">calendar_today</span>
                            <input type="date" name="date" value="{{ $filterDate }}"
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
                        @if ($filterType !== 'all' || $filterPatient || $filterDate)
                            <a href="{{ route('doctor.recent-forms') }}"
                                class="flex-shrink-0 inline-flex items-center justify-center w-10 h-[42px] bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-xl transition-colors"
                                title="Clear filters">
                                <span class="material-symbols-outlined" style="font-size:18px">close</span>
                            </a>
                        @endif
                    </div>

                </div>
            </form>

            {{-- ── TYPE PILL TABS (horizontally scrollable on mobile) ── --}}
            <div class="overflow-x-auto pills-scroll -mx-1 px-1 pb-1">
                <div class="flex items-center gap-2 w-max">
                    @php
                        $pillMap = [
                            'all'           => ['All',                          '#16A34A'],
                            'vital-signs'   => ['Vital Signs',                  '#EF4444'],
                            'physical-exam' => ['Physical Exam',                '#8B5CF6'],
                            'adl'           => ['Daily Living',                 '#F97316'],
                            'intake-output' => ['Intake & Output',              '#3B82F6'],
                            'lab-values'    => ['Lab Values',                   '#0D9488'],
                            'medication'    => ['Medication',                   '#10B981'],
                            'ivs-lines'     => ['IVs & Lines',                  '#6366F1'],
                        ];
                    @endphp
                    @foreach ($pillMap as $key => $pill)
                        @php [$pillLabel, $pillColor] = $pill; $isActive = $filterType === $key; @endphp
                        <a href="{{ route('doctor.recent-forms', array_merge(request()->except('type','page'), ['type' => $key])) }}"
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-full text-sm border whitespace-nowrap transition-all duration-150 hover:opacity-90"
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

            {{-- ── RESULTS ── --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                @if ($items->isEmpty())
                    <div class="py-20 text-center">
                        <span class="material-symbols-outlined text-gray-300 block mb-3" style="font-size:56px">inbox</span>
                        <p class="text-lg font-alte text-gray-500">No records found</p>
                        <p class="text-base font-alte-regular text-gray-400 mt-1">Try adjusting your filters or check back later.</p>
                        @if ($filterType !== 'all' || $filterPatient || $filterDate)
                            <a href="{{ route('doctor.recent-forms') }}"
                                class="inline-flex items-center gap-1.5 mt-4 text-sm font-alte text-green-700 hover:underline">
                                <span class="material-symbols-outlined" style="font-size:16px">refresh</span>
                                Clear all filters
                            </a>
                        @endif
                    </div>
                @else

                    {{-- ── DESKTOP TABLE (md+) ── --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full min-w-[640px] text-sm">
                            <thead>
                                <tr class="bg-green-50/60 border-b border-green-100">
                                    <th class="w-12 text-center px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="text-left px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Form Type</th>
                                    <th class="text-left px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="text-left px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Patient ID</th>
                                    <th class="text-left px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Last Updated</th>
                                    <th class="text-center px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $i => $form)
                                    @php $rowUrl = $form['patient_id'] ? route('doctor.form-detail', ['type' => $form['type_key'], 'patient_id' => $form['patient_id']]) . '?from=recent-forms' : null; @endphp
                                    <tr class="border-b border-gray-50 transition-colors {{ $rowUrl ? 'cursor-pointer hover:bg-green-50/50' : 'hover:bg-gray-50' }}"
                                        {!! $rowUrl ? "onclick=\"window.location.href='{$rowUrl}'\"" : '' !!}>
                                        {{-- # --}}
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="text-sm text-gray-400 font-mono">{{ ($page - 1) * $perPage + $i + 1 }}</span>
                                        </td>
                                        {{-- Form Type --}}
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                                    style="background-color: {{ $form['color'] }}18">
                                                    <span class="material-symbols-outlined"
                                                        style="font-size:16px; color:{{ $form['color'] }}">{{ $form['icon'] }}</span>
                                                </div>
                                                <span class="font-alte-regular text-gray-800 text-sm leading-tight">{{ $form['type'] }}</span>
                                            </div>
                                        </td>
                                        {{-- Patient --}}
                                        <td class="px-4 py-3.5">
                                            <span class="font-alte-regular text-gray-800 text-base">{{ $form['patient_name'] }}</span>
                                        </td>
                                        {{-- Patient ID --}}
                                        <td class="px-4 py-3.5">
                                            <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                                                {{ $form['patient_id'] ?? '—' }}
                                            </span>
                                        </td>
                                        {{-- Date --}}
                                        <td class="px-4 py-3.5">
                                            <p class="text-sm font-alte-regular text-gray-700">{{ \Carbon\Carbon::parse($form['time'])->format('M d, Y') }}</p>
                                            <p class="text-xs font-alte-regular text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($form['time'])->format('g:i A') }} · {{ \Carbon\Carbon::parse($form['time'])->diffForHumans() }}</p>
                                        </td>
                                        {{-- Action --}}
                                        <td class="px-4 py-3.5 text-center" onclick="event.stopPropagation()">
                                            @if ($form['patient_id'])
                                                <form method="POST" action="{{ route('doctor.generate-report') }}" style="display:inline">
                                                    @csrf
                                                    <input type="hidden" name="patient_id" value="{{ $form['patient_id'] }}">
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 text-sm font-alte text-green-700 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                                        <span class="material-symbols-outlined" style="font-size:14px">picture_as_pdf</span>
                                                        View Report
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-300">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ── MOBILE CARDS (< md) ── --}}
                    <div class="md:hidden divide-y divide-gray-100">
                        @foreach ($items as $i => $form)
                            @php $cardUrl = $form['patient_id'] ? route('doctor.form-detail', ['type' => $form['type_key'], 'patient_id' => $form['patient_id']]) . '?from=recent-forms' : null; @endphp
                            <div class="px-4 py-4 transition-colors {{ $cardUrl ? 'cursor-pointer hover:bg-green-50/50' : 'hover:bg-gray-50' }}"
                                 {!! $cardUrl ? "onclick=\"window.location.href='{$cardUrl}'\"" : '' !!}>
                                <div class="flex items-start gap-3">
                                    {{-- Icon --}}
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                                        style="background-color: {{ $form['color'] }}18">
                                        <span class="material-symbols-outlined"
                                            style="font-size:20px; color:{{ $form['color'] }}">{{ $form['icon'] }}</span>
                                    </div>
                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-base font-alte-regular text-gray-800 truncate">{{ $form['patient_name'] }}</p>
                                        <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                            <span class="inline-block rounded-full px-2.5 py-0.5 text-white font-alte-regular text-xs"
                                                style="background-color: {{ $form['color'] }}">{{ $form['type'] }}</span>
                                            @if ($form['patient_id'])
                                                <span class="font-mono text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $form['patient_id'] }}</span>
                                            @endif
                                        </div>
                                        <p class="text-xs font-alte-regular text-gray-400 mt-1">
                                            {{ \Carbon\Carbon::parse($form['time'])->format('M d, Y · g:i A') }}
                                            · {{ \Carbon\Carbon::parse($form['time'])->diffForHumans() }}
                                        </p>
                                    </div>
                                    {{-- PDF button (stops row click propagation) --}}
                                    @if ($form['patient_id'])
                                        <form method="POST" action="{{ route('doctor.generate-report') }}" class="flex-shrink-0" onclick="event.stopPropagation()">
                                            @csrf
                                            <input type="hidden" name="patient_id" value="{{ $form['patient_id'] }}">
                                            <button type="submit"
                                                class="w-9 h-9 rounded-xl bg-green-50 hover:bg-green-100 flex items-center justify-center text-green-700 transition-colors">
                                                <span class="material-symbols-outlined" style="font-size:18px">picture_as_pdf</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ── PAGINATION ── --}}
                    @if ($lastPage > 1)
                        <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-gray-50">
                            <p class="text-sm font-alte-regular text-gray-500 order-2 sm:order-1">
                                Showing
                                <span class="font-alte text-gray-700">{{ ($page - 1) * $perPage + 1 }}</span>–<span class="font-alte text-gray-700">{{ min($page * $perPage, $total) }}</span>
                                of <span class="font-alte text-gray-700">{{ number_format($total) }}</span> results
                            </p>
                            <div class="flex items-center gap-1 order-1 sm:order-2">
                                {{-- Prev --}}
                                @if ($page > 1)
                                    <a href="{{ route('doctor.recent-forms', array_merge(request()->except('page'), ['page' => $page - 1])) }}"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 hover:border-gray-400 transition-colors">
                                        <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                                    </a>
                                @else
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                                        <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                                    </span>
                                @endif

                                {{-- Page numbers --}}
                                @for ($p = max(1, $page - 2); $p <= min($lastPage, $page + 2); $p++)
                                    <a href="{{ route('doctor.recent-forms', array_merge(request()->except('page'), ['page' => $p])) }}"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl border text-xs font-alte transition-all"
                                        style="{{ $p === $page ? 'background-color:#15803d; color:#fff; border-color:#15803d;' : 'background-color:#fff; color:#374151; border-color:#E5E7EB;' }}">
                                        {{ $p }}
                                    </a>
                                @endfor

                                {{-- Next --}}
                                @if ($page < $lastPage)
                                    <a href="{{ route('doctor.recent-forms', array_merge(request()->except('page'), ['page' => $page + 1])) }}"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 hover:border-gray-400 transition-colors">
                                        <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                                    </a>
                                @else
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                                        <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                @endif
            </div>{{-- end results card --}}

        </div>{{-- end max-w-7xl --}}
    </div>{{-- end page body --}}

</div>{{-- end full-width wrapper --}}
@endsection
