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
        .tbl tr { cursor: pointer; }
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
                    <span class="text-sm font-alte-regular text-gray-500">Today's Updates</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-alte text-[#2D6A4F] leading-tight flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:24px; color:#D97706">edit_note</span>
                    Today's Updates
                </h1>
                <p class="text-sm font-alte-regular text-gray-400 mt-0.5">
                    {{ $total }} form{{ $total === 1 ? '' : 's' }} updated today &mdash; {{ now()->format('l, F j, Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-6 space-y-5">

        {{-- Summary pill --}}
        <div class="flex items-center gap-3 flex-wrap">
            <span class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-200 text-amber-700
                         text-sm font-alte px-4 py-2 rounded-full">
                <span class="material-symbols-outlined" style="font-size:15px">edit_note</span>
                {{ $total }} Update{{ $total === 1 ? '' : 's' }} Today
            </span>
        </div>

        @if ($items->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-20 text-center">
                <span class="material-symbols-outlined text-gray-300" style="font-size:52px">inbox</span>
                <p class="font-alte text-gray-500 mt-3 text-base">No updates yet today.</p>
                <p class="font-alte-regular text-gray-400 text-sm mt-1">Patient assessment forms updated today will appear here.</p>
            </div>
        @else

            {{-- Desktop table --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hidden sm:block">
                <table class="tbl w-full border-collapse">
                    <thead>
                        <tr class="bg-amber-50/60 border-b border-amber-100 text-left text-gray-600">
                            <th>#</th>
                            <th>Patient</th>
                            <th>Assessment Type</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($items as $i => $item)
                            <tr onclick="window.location.href='{{ $item['patient_id'] ? route('doctor.form-detail', ['type' => $item['type_key'], 'patient_id' => $item['patient_id']]) . '?from=today-updates' : '#' }}'">
                                <td class="text-gray-400 text-sm">{{ $i + 1 }}</td>
                                <td>
                                    <p class="font-alte-regular text-gray-800">{{ $item['patient_name'] }}</p>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 text-white text-xs font-alte
                                                 px-2.5 py-1 rounded-full"
                                          style="background-color: {{ $item['color'] }}">
                                        <span class="material-symbols-outlined" style="font-size:12px">{{ $item['icon'] }}</span>
                                        {{ $item['type'] }}
                                    </span>
                                </td>
                                <td class="text-gray-500 font-alte-regular text-sm">
                                    {{ \Carbon\Carbon::parse($item['time'])->format('h:i A') }}
                                    <span class="text-gray-400 text-xs ml-1">
                                        ({{ \Carbon\Carbon::parse($item['time'])->diffForHumans() }})
                                    </span>
                                </td>
                                <td onclick="event.stopPropagation()">
                                    @if ($item['patient_id'])
                                        <a href="{{ route('doctor.form-detail', ['type' => $item['type_key'], 'patient_id' => $item['patient_id']]) }}?from=today-updates"
                                           class="inline-flex items-center gap-1 text-xs font-alte text-blue-600
                                                  hover:text-blue-800 hover:underline transition-colors">
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
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 cursor-pointer
                                 active:bg-amber-50/40 transition-colors"
                         onclick="window.location.href='{{ $item['patient_id'] ? route('doctor.form-detail', ['type' => $item['type_key'], 'patient_id' => $item['patient_id']]) . '?from=today-updates' : '#' }}'">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-alte text-gray-800 text-base leading-tight truncate">
                                    {{ $item['patient_name'] }}
                                </p>
                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1 text-white text-xs font-alte
                                                 px-2.5 py-0.5 rounded-full"
                                          style="background-color: {{ $item['color'] }}">
                                        {{ $item['type'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <p class="text-xs font-alte-regular text-gray-500">
                                    {{ \Carbon\Carbon::parse($item['time'])->format('h:i A') }}
                                </p>
                                <p class="text-xs font-alte-regular text-gray-400 mt-0.5">
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
