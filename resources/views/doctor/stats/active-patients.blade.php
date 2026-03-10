@extends('layouts.doctor')
@section('title', 'Active Patients')

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
    </style>
@endpush

@section('content')
<div class="-mx-6 min-h-screen bg-[#f0f8f0]">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-4 sm:py-5
                    flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('doctor-home') }}"
                       class="text-sm font-alte-regular text-gray-400 hover:text-green-700 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size:15px">arrow_back_ios</span>
                        Dashboard
                    </a>
                    <span class="text-gray-300 text-sm">/</span>
                    <span class="text-sm font-alte-regular text-gray-500">Active Patients</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-alte text-[#2D6A4F] leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:24px; color:#16A34A">person_check</span>
                    Active Patients
                </h1>
                <p class="text-sm font-alte-regular text-gray-400 mt-0.5">
                    {{ $patients->count() }} currently admitted — sorted by most recent admission
                </p>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-6 space-y-5">

        {{-- Summary pill --}}
        <div class="flex items-center gap-3 flex-wrap">
            <span class="inline-flex items-center gap-1.5 bg-green-50 border border-green-200 text-green-700
                         text-sm font-alte px-4 py-2 rounded-full">
                <span class="material-symbols-outlined" style="font-size:15px">person_check</span>
                {{ $patients->count() }} Currently Admitted
            </span>
        </div>

        {{-- Table (desktop) --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hidden sm:block">
            <table class="tbl w-full border-collapse">
                <thead>
                    <tr class="bg-green-50/60 border-b border-green-100 text-left text-gray-600">
                        <th>#</th>
                        <th>Patient Name</th>
                        <th>Age</th>
                        <th>Sex</th>
                        <th>Room / Bed</th>
                        <th>Admission Date</th>
                        <th>Days Admitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($patients as $i => $p)
                        @php
                            $days = $p->admission_date
                                ? (int) $p->admission_date->diffInDays(now())
                                : null;
                        @endphp
                        <tr class="cursor-pointer"
                            onclick="window.location.href='{{ route('doctor.form-detail', ['type' => 'vital-signs', 'patient_id' => $p->patient_id]) }}?from=active-patients'">                            <td class="text-gray-400 text-sm">{{ $i + 1 }}</td>
                            <td>
                                <p class="font-alte-regular text-gray-800">{{ $p->name }}</p>
                            </td>
                            <td class="text-gray-600">{{ $p->age ?? '—' }}</td>
                            <td class="text-gray-600">{{ $p->sex ?? '—' }}</td>
                            <td class="text-gray-600">
                                {{ $p->room_no ? 'Rm '.$p->room_no : '—' }}
                                {{ $p->bed_no ? '/ Bed '.$p->bed_no : '' }}
                            </td>
                            <td class="text-gray-600">
                                {{ $p->admission_date ? $p->admission_date->format('M d, Y') : '—' }}
                            </td>
                            <td>
                                @if ($days !== null)
                                    <span class="inline-flex items-center gap-1 text-sm font-alte-regular
                                                 {{ $days > 7 ? 'text-amber-600' : 'text-gray-600' }}">
                                        {{ $days }} day{{ $days === 1 ? '' : 's' }}
                                        @if ($days > 7)
                                            <span class="material-symbols-outlined" style="font-size:14px">warning</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-gray-400 font-alte-regular">
                                No active patients.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="sm:hidden space-y-3">
            @forelse ($patients as $p)
                @php
                    $days = $p->admission_date ? (int) $p->admission_date->diffInDays(now()) : null;
                @endphp
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 cursor-pointer
                             active:bg-green-50/40 transition-colors"
                     onclick="window.location.href='{{ route('doctor.form-detail', ['type' => 'vital-signs', 'patient_id' => $p->patient_id]) }}?from=active-patients'">                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-alte text-gray-800 text-base leading-tight truncate">{{ $p->name }}</p>
                            <p class="text-sm font-alte-regular text-gray-500 mt-0.5">
                                Age {{ $p->age ?? '—' }} &bull; {{ $p->sex ?? '—' }}
                            </p>
                        </div>
                        @if ($days !== null)
                            <span class="flex-shrink-0 {{ $days > 7 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-green-50 text-green-700 border border-green-200' }}
                                         text-xs font-alte px-2.5 py-1 rounded-full">
                                {{ $days }}d
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2.5 text-sm font-alte-regular text-gray-500">
                        @if ($p->room_no)
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:13px">door_front</span>
                                Rm {{ $p->room_no }} / Bed {{ $p->bed_no ?? '—' }}
                            </span>
                        @endif
                        @if ($p->admission_date)
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:13px">calendar_today</span>
                                Admitted {{ $p->admission_date->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 font-alte-regular py-12">No active patients.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection
