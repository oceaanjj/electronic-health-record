@extends('layouts.doctor')
@section('title', 'Total Patients')

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
        .tbl tr:hover td { background: #f0fdf4; }
        .dark .tbl tr:hover td { background: rgba(59, 130, 246, 0.05); }
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
                    <span class="text-sm font-alte-regular text-slate-500 dark:text-slate-400">Total Patients</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-alte text-emerald-700 dark:text-emerald-500 leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:24px">groups</span>
                    Total Patients
                </h1>
                <p class="text-sm font-alte-regular text-slate-400 dark:text-slate-500 mt-0.5">
                    All {{ $patients->count() }} registered patients — sorted alphabetically
                </p>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-6 space-y-5 transition-all duration-300">

        {{-- Summary pill --}}
        <div class="flex items-center gap-3 flex-wrap">
            <span class="inline-flex items-center gap-1.5 bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800/50 text-blue-700 dark:text-blue-400
                         text-sm font-alte px-4 py-2 rounded-full shadow-sm">
                <span class="material-symbols-outlined" style="font-size:15px">groups</span>
                {{ $patients->count() }} Total
            </span>
            <span class="inline-flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400
                         text-sm font-alte px-4 py-2 rounded-full shadow-sm">
                <span class="material-symbols-outlined" style="font-size:15px">person_check</span>
                {{ $patients->where('is_active', true)->count() }} Active
            </span>
            <span class="inline-flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400
                         text-sm font-alte px-4 py-2 rounded-full shadow-sm">
                <span class="material-symbols-outlined" style="font-size:15px">person_off</span>
                {{ $patients->where('is_active', false)->count() }} Inactive
            </span>
        </div>

        {{-- Table (desktop) --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hidden sm:block transition-colors duration-300">
            <table class="tbl w-full border-collapse">
                <thead>
                    <tr class="bg-emerald-50/60 dark:bg-emerald-900/20 border-b border-emerald-100 dark:border-emerald-900/30 text-left text-slate-600 dark:text-slate-400">
                        <th>#</th>
                        <th>Patient Name</th>
                        <th>Age</th>
                        <th>Sex</th>
                        <th>Room / Bed</th>
                        <th>Admission Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($patients as $i => $p)
                        <tr class="cursor-pointer transition-colors"
                            onclick="window.location.href='{{ route('doctor.patient-details', ['patient_id' => $p->patient_id]) }}?from=total-patients'">
                            <td class="text-slate-400 dark:text-slate-600 text-sm font-mono">{{ $i + 1 }}</td>
                            <td>
                                <p class="font-alte-regular text-slate-800 dark:text-slate-200">{{ $p->name }}</p>
                            </td>
                            <td class="text-slate-600 dark:text-slate-400">{{ $p->age ?? '—' }}</td>
                            <td class="text-slate-600 dark:text-slate-400">{{ $p->sex ?? '—' }}</td>
                            <td class="text-slate-600 dark:text-slate-400">
                                {{ $p->room_no ? 'Rm '.$p->room_no : '—' }}
                                {{ $p->bed_no ? '/ Bed '.$p->bed_no : '' }}
                            </td>
                            <td class="text-slate-600 dark:text-slate-400">
                                {{ $p->admission_date ? $p->admission_date->format('M d, Y') : '—' }}
                            </td>
                            <td>
                                @if ($p->is_active)
                                    <span class="inline-flex items-center gap-1 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400
                                                 text-xs font-alte px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800/50">
                                        <span class="material-symbols-outlined" style="font-size:12px">circle</span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400
                                                 text-xs font-alte px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700">
                                        <span class="material-symbols-outlined" style="font-size:12px">circle</span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400 dark:text-slate-600 font-alte-regular">
                                No patients found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="sm:hidden space-y-3">
            @forelse ($patients as $p)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 cursor-pointer
                             active:bg-emerald-50/40 dark:active:bg-emerald-900/20 transition-all duration-200"
                     onclick="window.location.href='{{ route('doctor.patient-details', ['patient_id' => $p->patient_id]) }}?from=total-patients'">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-alte text-slate-800 dark:text-slate-200 text-base leading-tight truncate">{{ $p->name }}</p>
                            <p class="text-sm font-alte-regular text-slate-500 dark:text-slate-400 mt-0.5">
                                Age {{ $p->age ?? '—' }} &bull; {{ $p->sex ?? '—' }}
                            </p>
                        </div>
                        @if ($p->is_active)
                            <span class="flex-shrink-0 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 text-xs font-alte px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800/50">Active</span>
                        @else
                            <span class="flex-shrink-0 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs font-alte px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700">Inactive</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2.5 text-sm font-alte-regular text-slate-500 dark:text-slate-400">
                        @if ($p->room_no)
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:13px">door_front</span>
                                Rm {{ $p->room_no }} / Bed {{ $p->bed_no ?? '—' }}
                            </span>
                        @endif
                        @if ($p->admission_date)
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:13px">calendar_today</span>
                                {{ $p->admission_date->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-slate-400 dark:text-slate-600 font-alte-regular py-12">No patients found.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection
