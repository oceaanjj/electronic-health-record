@extends('layouts.doctor')
@section('title', "Today's Updates")

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .tbl th {
            font-family: 'Alte Haas Grotesk Bold', arial;
            font-size: 13px;
            padding: 16px 18px;
            white-space: nowrap;
        }
        .tbl td {
            font-family: 'Alte Haas Grotesk', arial;
            font-size: 14px;
            padding: 13px 18px;
        }
        .tbl tr:hover td { background: #fffbeb; }
        .dark .tbl tr:hover td { background: rgba(217, 119, 6, 0.05); }
        .tbl tr { cursor: pointer; }
    </style>
@endpush

@section('content')
<div class="-mx-6 min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-300">

    {{-- Header --}}
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-4 sm:py-5
                    flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('doctor-home') }}"
                       class="text-sm font-alte-regular text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size:15px">arrow_back_ios</span>
                        Dashboard
                    </a>
                    <span class="text-slate-300 dark:text-slate-700 text-sm">/</span>
                    <span class="text-sm font-alte-regular text-slate-500 dark:text-slate-400">Today's Updates</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-alte text-emerald-700 dark:text-emerald-500 leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:24px">edit_note</span>
                    Today's Updates
                </h1>
                <p class="text-sm font-alte-regular text-slate-400 dark:text-slate-500 mt-0.5">
                    {{ $total }} form{{ $total === 1 ? '' : 's' }} updated today &mdash; {{ now()->format('l, F j, Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-6 space-y-5 transition-all duration-300">

        {{-- Summary pill --}}
        <div class="flex items-center gap-3 flex-wrap">
            <span class="inline-flex items-center gap-1.5 bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800/50 text-amber-700 dark:text-amber-400
                         text-sm font-alte px-4 py-2 rounded-full shadow-sm">
                <span class="material-symbols-outlined" style="font-size:15px">edit_note</span>
                {{ $total }} Update{{ $total === 1 ? '' : 's' }} Today
            </span>
        </div>

        @if ($items->isEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm py-20 text-center transition-colors duration-300">
                <span class="material-symbols-outlined text-slate-300 dark:text-slate-700" style="font-size:52px">inbox</span>
                <p class="font-alte text-slate-500 dark:text-slate-400 mt-3 text-base">No updates yet today.</p>
                <p class="font-alte-regular text-slate-400 dark:text-slate-600 text-sm mt-1">Patient assessment forms updated today will appear here.</p>
            </div>
        @else

            {{-- Desktop table --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hidden sm:block transition-colors duration-300">
                <table class="tbl w-full border-collapse">
                    <thead>
                        <tr class="bg-amber-50/60 dark:bg-amber-900/20 border-b border-amber-100 dark:border-amber-900/30 text-left text-slate-600 dark:text-slate-400">
                            <th>#</th>
                            <th>Patient</th>
                            <th>Assessment Type</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($items as $i => $item)
                            <tr onclick="window.location.href='{{ $item['patient_id'] ? route('doctor.form-detail', ['type' => $item['type_key'], 'patient_id' => $item['patient_id']]) . '?from=today-updates' : '#' }}'" class="transition-colors">
                                <td class="text-slate-400 dark:text-slate-600 text-sm font-mono">{{ $i + 1 }}</td>
                                <td>
                                    <p class="font-alte-regular text-slate-800 dark:text-slate-200">{{ $item['patient_name'] }}</p>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 text-white text-[10px] font-alte
                                                 px-2.5 py-1 rounded-full uppercase tracking-wider"
                                          style="background-color: {{ $item['color'] }}">
                                        <span class="material-symbols-outlined" style="font-size:12px">{{ $item['icon'] }}</span>
                                        {{ $item['type'] }}
                                    </span>
                                </td>
                                <td class="text-slate-500 dark:text-slate-400 font-alte-regular text-sm">
                                    {{ \Carbon\Carbon::parse($item['time'])->format('h:i A') }}
                                    <span class="text-slate-400 dark:text-slate-600 text-xs ml-1">
                                        ({{ \Carbon\Carbon::parse($item['time'])->diffForHumans() }})
                                    </span>
                                </td>
                                <td onclick="event.stopPropagation()">
                                    @if ($item['patient_id'])
                                        <a href="{{ route('doctor.form-detail', ['type' => $item['type_key'], 'patient_id' => $item['patient_id']]) }}?from=today-updates"
                                           class="inline-flex items-center gap-1 text-xs font-alte text-emerald-600 dark:text-emerald-400
                                                  hover:text-emerald-800 dark:hover:text-emerald-300 transition-colors">
                                            View
                                            <span class="material-symbols-outlined" style="font-size:12px">open_in_new</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="sm:hidden space-y-3">
                @foreach ($items as $item)
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 cursor-pointer
                                 active:bg-amber-50/40 dark:active:bg-amber-900/20 transition-all duration-200"
                         onclick="window.location.href='{{ $item['patient_id'] ? route('doctor.form-detail', ['type' => $item['type_key'], 'patient_id' => $item['patient_id']]) . '?from=today-updates' : '#' }}'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-alte text-slate-800 dark:text-slate-200 text-base leading-tight truncate">
                                    {{ $item['patient_name'] }}
                                </p>
                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1 text-white text-[10px] font-alte
                                                 px-2.5 py-0.5 rounded-full uppercase tracking-wider"
                                          style="background-color: {{ $item['color'] }}">
                                        {{ $item['type'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <p class="text-xs font-alte-regular text-slate-500 dark:text-slate-400">
                                    {{ \Carbon\Carbon::parse($item['time'])->format('h:i A') }}
                                </p>
                                <p class="text-xs font-alte-regular text-slate-400 dark:text-slate-600 mt-0.5">
                                    {{ \Carbon\Carbon::parse($item['time'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @endif
    </div>
</div>
@endsection
