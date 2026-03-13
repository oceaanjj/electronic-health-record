@extends('layouts.doctor')
@section('title', $patient->name . ' — Patient Details')

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .detail-label {
            font-family: 'Alte Haas Grotesk Bold', arial;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #9ca3af;
        }
        .detail-value {
            font-family: 'Alte Haas Grotesk', arial;
            font-size: 15px;
            color: #1f2937;
        }
        .section-title {
            font-family: 'Alte Haas Grotesk Bold', arial;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
        }
        .form-badge {
            font-family: 'Alte Haas Grotesk', arial;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
<div class="-mx-6 min-h-screen bg-[#f0f8f0]">

    {{-- Header / Breadcrumb --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-4 sm:py-5">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <a href="{{ route('doctor-home') }}"
                   class="text-sm font-alte-regular text-gray-400 hover:text-green-700 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size:15px">arrow_back_ios</span>
                    Dashboard
                </a>
                <span class="text-gray-300 text-sm">/</span>
                <a href="{{ $fromCrumb['url'] }}"
                   class="text-sm font-alte-regular text-gray-400 hover:text-green-700 transition-colors">
                    {{ $fromCrumb['label'] }}
                </a>
                <span class="text-gray-300 text-sm">/</span>
                <span class="text-sm font-alte-regular text-gray-500 truncate max-w-[200px]">{{ $patient->name }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-alte text-[#2D6A4F] leading-tight flex items-center gap-2">
                <span class="material-symbols-outlined" style="font-size:24px; color:#2D6A4F">person</span>
                Patient Details
            </h1>
        </div>
    </div>

    <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-6 space-y-5">

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div style="display:flex; flex-direction:row; flex-wrap:nowrap; align-items:stretch; gap:2.5rem;">

                {{-- Avatar --}}
                <div style="flex-shrink:0; width:160px; min-height:180px; border-radius:1rem; background:#e5e7eb;
                            display:flex; align-items:center; justify-content:center; box-shadow:inset 0 2px 6px rgba(0,0,0,0.08);">
                    <span class="font-alte" style="font-size:2rem; color:#9ca3af; line-height:1; user-select:none; letter-spacing:0.1em;">
                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                    </span>
                </div>

                {{-- Name + Status + Quick Pills --}}
                <div style="flex:1; min-width:0; overflow:hidden; padding-top:4px;">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <h2 class="font-alte text-2xl sm:text-3xl text-gray-900 leading-tight">
                            {{ $patient->name }}
                        </h2>
                        @if ($patient->is_active)
                            <span class="inline-flex items-center gap-1 bg-green-100 text-green-700
                                         text-xs font-alte px-2.5 py-1 rounded-full border border-green-200">
                                <span class="material-symbols-outlined" style="font-size:11px">circle</span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500
                                         text-xs font-alte px-2.5 py-1 rounded-full border border-gray-200">
                                <span class="material-symbols-outlined" style="font-size:11px">circle</span>
                                Inactive
                            </span>
                        @endif
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.85rem;" class="text-sm font-alte-regular text-gray-500">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-gray-400" style="font-size:16px">cake</span>
                            Age {{ $patient->age ?? '—' }}
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-gray-400" style="font-size:16px">wc</span>
                            {{ $patient->sex ?? '—' }}
                        </span>
                        @if ($patient->room_no)
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-gray-400" style="font-size:16px">door_front</span>
                            Rm {{ $patient->room_no }}{{ $patient->bed_no ? ' / Bed '.$patient->bed_no : '' }}
                        </span>
                        @endif
                        @if ($patient->admission_date)
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-gray-400" style="font-size:16px">calendar_today</span>
                            Admitted {{ $patient->admission_date->format('M d, Y') }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Personal Information --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <p class="section-title mb-4">Personal Information</p>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="detail-label">First Name</p>
                            <p class="detail-value">{{ $patient->first_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="detail-label">Last Name</p>
                            <p class="detail-value">{{ $patient->last_name ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="detail-label">Middle Name</p>
                            <p class="detail-value">{{ $patient->middle_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="detail-label">Sex</p>
                            <p class="detail-value">{{ $patient->sex ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="detail-label">Age</p>
                            <p class="detail-value">{{ $patient->age ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="detail-label">Birthdate</p>
                            <p class="detail-value">
                                {{ $patient->birthdate ? \Carbon\Carbon::parse($patient->birthdate)->format('M d, Y') : '—' }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <p class="detail-label">Birthplace</p>
                        <p class="detail-value">{{ $patient->birthplace ?? '—' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="detail-label">Religion</p>
                            <p class="detail-value">{{ $patient->religion ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="detail-label">Ethnicity</p>
                            <p class="detail-value">{{ $patient->ethnicity ?? '—' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="detail-label">Address</p>
                        <p class="detail-value">{{ $patient->address ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Admission & Room --}}
            <div class="space-y-5">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <p class="section-title mb-4">Admission Details</p>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="detail-label">Admission Date</p>
                                <p class="detail-value">
                                    {{ $patient->admission_date ? $patient->admission_date->format('M d, Y') : '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="detail-label">Days Admitted</p>
                                @php
                                    $days = $patient->admission_date
                                        ? (int) $patient->admission_date->diffInDays(now())
                                        : null;
                                @endphp
                                <p class="detail-value {{ $days !== null && $days > 7 ? 'text-amber-600' : '' }}">
                                    {{ $days !== null ? $days.' day'.($days === 1 ? '' : 's') : '—' }}
                                    @if ($days !== null && $days > 7)
                                        <span class="material-symbols-outlined text-amber-500 align-middle" style="font-size:14px">warning</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="detail-label">Room No.</p>
                                <p class="detail-value">{{ $patient->room_no ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="detail-label">Bed No.</p>
                                <p class="detail-value">{{ $patient->bed_no ?? '—' }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="detail-label">Status</p>
                            <div class="mt-1">
                                @if ($patient->is_active)
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-700
                                                 text-sm font-alte px-3 py-1 rounded-full border border-green-200">
                                        <span class="material-symbols-outlined" style="font-size:12px">check_circle</span>
                                        Active / Currently Admitted
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-500
                                                 text-sm font-alte px-3 py-1 rounded-full border border-gray-200">
                                        <span class="material-symbols-outlined" style="font-size:12px">cancel</span>
                                        Inactive / Discharged
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Emergency Contact --}}
                @php
                    $contactNames = is_array($patient->contact_name) ? $patient->contact_name : [];
                    $contactRels  = is_array($patient->contact_relationship) ? $patient->contact_relationship : [];
                    $contactNums  = is_array($patient->contact_number) ? $patient->contact_number : [];
                    $hasContacts  = count($contactNames) > 0;
                @endphp
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <p class="section-title mb-4">Emergency Contact</p>
                    @if ($hasContacts)
                        <div class="space-y-4">
                            @foreach ($contactNames as $ci => $cname)
                            <div class="flex flex-col gap-0.5 {{ $ci > 0 ? 'pt-4 border-t border-gray-100' : '' }}">
                                <p class="detail-label">Contact {{ $ci + 1 }}</p>
                                <p class="detail-value">{{ $cname ?? '—' }}</p>
                                <p class="font-alte-regular text-sm text-gray-500">
                                    {{ $contactRels[$ci] ?? '—' }}
                                    @if (!empty($contactNums[$ci]))
                                        &bull; {{ $contactNums[$ci] }}
                                    @endif
                                </p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="font-alte-regular text-sm text-gray-400">No emergency contact on file.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chief Complaints --}}
        @if ($patient->chief_complaints)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <p class="section-title mb-3">Chief Complaints</p>
            <p class="font-alte-regular text-gray-700 text-[15px] leading-relaxed whitespace-pre-line">{{ $patient->chief_complaints }}</p>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <p class="section-title mb-4">Patient Form Records</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @php
                    $formLinks = [
                        ['type' => 'vital-signs',   'label' => 'Vital Signs',              'icon' => 'monitor_heart',    'color' => '#EF4444', 'bg' => 'bg-red-50',    'border' => 'border-red-200',    'text' => 'text-red-700'],
                        ['type' => 'physical-exam', 'label' => 'Physical Exam',            'icon' => 'person_search',    'color' => '#8B5CF6', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-700'],
                        ['type' => 'adl',           'label' => 'Activities of Daily Living','icon' => 'self_improvement', 'color' => '#F97316', 'bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'text' => 'text-orange-700'],
                        ['type' => 'intake-output', 'label' => 'Intake & Output',          'icon' => 'water_drop',       'color' => '#3B82F6', 'bg' => 'bg-blue-50',   'border' => 'border-blue-200',   'text' => 'text-blue-700'],
                        ['type' => 'lab-values',    'label' => 'Lab Values',               'icon' => 'biotech',          'color' => '#0D9488', 'bg' => 'bg-teal-50',   'border' => 'border-teal-200',   'text' => 'text-teal-700'],
                        ['type' => 'medication',    'label' => 'Medication Admin.',        'icon' => 'medication',       'color' => '#10B981', 'bg' => 'bg-emerald-50','border' => 'border-emerald-200','text' => 'text-emerald-700'],
                        ['type' => 'ivs-lines',     'label' => 'IVs & Lines',              'icon' => 'vaccines',         'color' => '#6366F1', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'text' => 'text-indigo-700'],
                    ];
                @endphp
                @foreach ($formLinks as $fl)
                <a href="{{ route('doctor.form-detail', ['type' => $fl['type'], 'patient_id' => $patient->patient_id]) }}?from=patient-details&prev={{ $fromKey }}"
                   class="flex items-center gap-3 p-3.5 rounded-xl border {{ $fl['border'] }} {{ $fl['bg'] }}
                          hover:shadow-md transition-all group">
                    <span class="material-symbols-outlined {{ $fl['text'] }}" style="font-size:20px">{{ $fl['icon'] }}</span>
                    <span class="form-badge {{ $fl['text'] }} leading-tight">{{ $fl['label'] }}</span>
                    <span class="material-symbols-outlined text-gray-300 group-hover:text-gray-500 ml-auto transition-colors" style="font-size:16px">chevron_right</span>
                </a>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
