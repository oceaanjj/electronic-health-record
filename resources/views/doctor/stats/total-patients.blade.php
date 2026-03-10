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
                    <span class="text-sm font-alte-regular text-gray-500">Total Patients</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-alte text-[#2D6A4F] leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:24px; color:#3B82F6">groups</span>
                    Total Patients
                </h1>
                <p class="text-sm font-alte-regular text-gray-400 mt-0.5">
                    All {{ $patients->count() }} registered patients — sorted alphabetically
                </p>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-6 space-y-5">

        {{-- Summary pill --}}
        <div class="flex items-center gap-3 flex-wrap">
            <span class="inline-flex items-center gap-1.5 bg-blue-50 border border-blue-200 text-blue-700
                         text-sm font-alte px-4 py-2 rounded-full">
                <span class="material-symbols-outlined" style="font-size:15px">groups</span>
                {{ $patients->count() }} Total
            </span>
            <span class="inline-flex items-center gap-1.5 bg-green-50 border border-green-200 text-green-700
                         text-sm font-alte px-4 py-2 rounded-full">
                <span class="material-symbols-outlined" style="font-size:15px">person_check</span>
                {{ $patients->where('is_active', true)->count() }} Active
            </span>
            <span class="inline-flex items-center gap-1.5 bg-gray-50 border border-gray-200 text-gray-600
                         text-sm font-alte px-4 py-2 rounded-full">
                <span class="material-symbols-outlined" style="font-size:15px">person_off</span>
                {{ $patients->where('is_active', false)->count() }} Inactive
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
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($patients as $i => $p)
                        <tr class="cursor-pointer"
                            onclick="window.location.href='{{ route('doctor.form-detail', ['type' => 'vital-signs', 'patient_id' => $p->patient_id]) }}?from=total-patients'">                            <td class="text-gray-400 text-sm">{{ $i + 1 }}</td>
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
                                @if ($p->is_active)
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700
                                                 text-xs font-alte px-2.5 py-1 rounded-full">
                                        <span class="material-symbols-outlined" style="font-size:12px">circle</span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500
                                                 text-xs font-alte px-2.5 py-1 rounded-full">
                                        <span class="material-symbols-outlined" style="font-size:12px">circle</span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-gray-400 font-alte-regular">
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
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 cursor-pointer
                             active:bg-green-50/40 transition-colors"
                     onclick="window.location.href='{{ route('doctor.form-detail', ['type' => 'vital-signs', 'patient_id' => $p->patient_id]) }}?from=total-patients'">                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-alte text-gray-800 text-base leading-tight truncate">{{ $p->name }}</p>
                            <p class="text-sm font-alte-regular text-gray-500 mt-0.5">
                                Age {{ $p->age ?? '—' }} &bull; {{ $p->sex ?? '—' }}
                            </p>
                        </div>
                        @if ($p->is_active)
                            <span class="flex-shrink-0 bg-green-100 text-green-700 text-xs font-alte px-2.5 py-1 rounded-full">Active</span>
                        @else
                            <span class="flex-shrink-0 bg-gray-100 text-gray-500 text-xs font-alte px-2.5 py-1 rounded-full">Inactive</span>
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
                                {{ $p->admission_date->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 font-alte-regular py-12">No patients found.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection
