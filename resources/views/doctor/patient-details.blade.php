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
        .dark .detail-value {
            color: #e2e8f0;
        }
        .section-title {
            font-family: 'Alte Haas Grotesk Bold', arial;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
        }
        .dark .section-title {
            color: #94a3b8;
        }
        .form-badge {
            font-family: 'Alte Haas Grotesk', arial;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
<div class="-mx-6 min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-300">

    {{-- Header / Breadcrumb --}}
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-4 sm:py-5">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <a href="{{ route('doctor-home') }}"
                   class="text-sm font-alte-regular text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size:15px">arrow_back_ios</span>
                    Dashboard
                </a>
                <span class="text-slate-300 dark:text-slate-700 text-sm">/</span>
                <a href="{{ $fromCrumb['url'] }}"
                   class="text-sm font-alte-regular text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                    {{ $fromCrumb['label'] }}
                </a>
                <span class="text-slate-300 dark:text-slate-700 text-sm">/</span>
                <span class="text-sm font-alte-regular text-slate-500 dark:text-slate-400 truncate max-w-[200px]">{{ $patient->name }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-alte text-emerald-700 dark:text-emerald-500 leading-tight flex items-center gap-2">
                <span class="material-symbols-outlined" style="font-size:24px">person</span>
                Patient Details
            </h1>
        </div>
    </div>

    <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-6 space-y-5">

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
            <div class="flex flex-col sm:flex-row items-stretch gap-6 sm:gap-10">

                {{-- Avatar --}}
                <div class="shrink-0 w-32 h-32 sm:w-40 sm:h-48 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shadow-inner transition-colors duration-300">
                    <span class="font-alte text-4xl sm:text-5xl text-slate-300 dark:text-slate-600 select-none tracking-widest">
                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                    </span>
                </div>

                {{-- Name + Status + Quick Pills --}}
                <div class="flex-1 min-w-0 pt-1">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <h2 class="font-alte text-2xl sm:text-3xl text-slate-900 dark:text-white leading-tight">
                            {{ $patient->name }}
                        </h2>
                        @if ($patient->is_active)
                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400
                                         text-xs font-alte px-3 py-1 rounded-full border border-emerald-100 dark:border-emerald-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400
                                         text-xs font-alte px-3 py-1 rounded-full border border-slate-200 dark:border-slate-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Inactive
                            </span>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-sm font-alte-regular text-slate-500 dark:text-slate-400">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 dark:text-slate-600" style="font-size:18px">cake</span>
                            Age {{ $patient->age ?? '—' }}
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 dark:text-slate-600" style="font-size:18px">wc</span>
                            {{ $patient->sex ?? '—' }}
                        </span>
                        @if ($patient->room_no)
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 dark:text-slate-600" style="font-size:18px">door_front</span>
                            Rm {{ $patient->room_no }}{{ $patient->bed_no ? ' / Bed '.$patient->bed_no : '' }}
                        </span>
                        @endif
                        @if ($patient->admission_date)
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 dark:text-slate-600" style="font-size:18px">calendar_today</span>
                            Admitted {{ $patient->admission_date->format('M d, Y') }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Personal Information --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
                <p class="section-title mb-4">Demographic Profile & Personal Information</p>
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
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
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
                                <p class="detail-value {{ $days !== null && $days > 7 ? 'text-amber-600 dark:text-amber-500' : '' }}">
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
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400
                                                 text-sm font-alte px-3.5 py-1.5 rounded-full border border-emerald-100 dark:border-emerald-800/50">
                                        <span class="material-symbols-outlined" style="font-size:16px">check_circle</span>
                                        Active / Currently Admitted
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400
                                                 text-sm font-alte px-3.5 py-1.5 rounded-full border border-slate-200 dark:border-slate-700">
                                        <span class="material-symbols-outlined" style="font-size:16px">cancel</span>
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
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
                    <p class="section-title mb-4">Emergency Contact</p>
                    @if ($hasContacts)
                        <div class="space-y-4">
                            @foreach ($contactNames as $ci => $cname)
                            <div class="flex flex-col gap-0.5 {{ $ci > 0 ? 'pt-4 border-t border-slate-100 dark:border-slate-800' : '' }}">
                                <p class="detail-label">Contact {{ $ci + 1 }}</p>
                                <p class="detail-value">{{ $cname ?? '—' }}</p>
                                <p class="font-alte-regular text-sm text-slate-500 dark:text-slate-400">
                                    {{ $contactRels[$ci] ?? '—' }}
                                    @if (!empty($contactNums[$ci]))
                                        &bull; {{ $contactNums[$ci] }}
                                    @endif
                                </p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="font-alte-regular text-sm text-slate-400 dark:text-slate-600">No emergency contact on file.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chief Complaints --}}
        @if ($patient->chief_complaints)
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
            <p class="section-title mb-3">Chief Complaints</p>
            <p class="font-alte-regular text-slate-700 dark:text-slate-300 text-[15px] leading-relaxed whitespace-pre-line">{{ $patient->chief_complaints }}</p>
        </div>
        @endif

        {{-- Medical History --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
            <p class="section-title mb-4">Medical History</p>
            <div class="space-y-6">
                {{-- Present Illness --}}
                @if($medicalHistory['presentIllness']->isNotEmpty())
                <div>
                    <p class="detail-label mb-2">Present Illness</p>
                    <div class="space-y-3">
                        @foreach($medicalHistory['presentIllness'] as $pi)
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <p class="text-xs text-slate-400 mb-1">{{ \Carbon\Carbon::parse($pi->updated_at)->format('M d, Y') }}</p>
                            <p class="detail-value text-sm">{{ $pi->present_illness }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Past Medical/Surgical --}}
                @if($medicalHistory['pastMedicalSurgical']->isNotEmpty())
                <div>
                    <p class="detail-label mb-2">Past Medical / Surgical History</p>
                    <div class="space-y-3">
                        @foreach($medicalHistory['pastMedicalSurgical'] as $pms)
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <p class="text-xs text-slate-400 mb-1">{{ \Carbon\Carbon::parse($pms->updated_at)->format('M d, Y') }}</p>
                            <p class="detail-value text-sm">{{ $pms->past_medical_surgical }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Allergies --}}
                    <div>
                        <p class="detail-label mb-2">Allergies</p>
                        @if($medicalHistory['allergies']->isNotEmpty())
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($medicalHistory['allergies'] as $allergy)
                            <li class="text-sm text-rose-600 dark:text-rose-400 font-alte-regular">{{ $allergy->allergy }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-sm text-slate-400 dark:text-slate-600 font-alte-regular">No known allergies.</p>
                        @endif
                    </div>
                    {{-- Vaccinations --}}
                    <div>
                        <p class="detail-label mb-2">Vaccinations</p>
                        @if($medicalHistory['vaccinations']->isNotEmpty())
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($medicalHistory['vaccinations'] as $vacc)
                            <li class="text-sm text-slate-600 dark:text-slate-400 font-alte-regular">{{ $vacc->vaccination }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-sm text-slate-400 dark:text-slate-600 font-alte-regular">No vaccination records.</p>
                        @endif
                    </div>
                </div>

                {{-- Developmental History --}}
                @if($medicalHistory['developmental'])
                <div>
                    <p class="detail-label mb-2">Developmental History</p>
                    <div class="p-4 bg-indigo-50/30 dark:bg-indigo-900/10 rounded-xl border border-indigo-100 dark:border-indigo-900/20">
                        <p class="detail-value text-sm leading-relaxed">{{ $medicalHistory['developmental']->developmental_history }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Medical Reconciliation --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
            <p class="section-title mb-4">Medical Reconciliation</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Home Meds --}}
                <div class="space-y-3">
                    <p class="detail-label text-blue-600 dark:text-blue-400 flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:16px">home</span>
                        Home Medications
                    </p>
                    @if($medReconciliation['home']->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($medReconciliation['home'] as $med)
                        <div class="p-3 bg-blue-50/50 dark:bg-blue-900/10 rounded-lg border border-blue-100 dark:border-blue-900/20">
                            <p class="text-sm font-alte-bold text-slate-800 dark:text-slate-200">{{ $med->home_medication }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $med->dosage }} &bull; {{ $med->frequency }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-slate-400 dark:text-slate-600">None on file.</p>
                    @endif
                </div>
                {{-- Current Meds --}}
                <div class="space-y-3">
                    <p class="detail-label text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:16px">medication</span>
                        Current Medications
                    </p>
                    @if($medReconciliation['current']->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($medReconciliation['current'] as $med)
                        <div class="p-3 bg-emerald-50/50 dark:bg-emerald-900/10 rounded-lg border border-emerald-100 dark:border-emerald-900/20">
                            <p class="text-sm font-alte-bold text-slate-800 dark:text-slate-200">{{ $med->current_medication }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $med->dosage }} &bull; {{ $med->frequency }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-slate-400 dark:text-slate-600">None on file.</p>
                    @endif
                </div>
                {{-- Changes --}}
                <div class="space-y-3">
                    <p class="detail-label text-amber-600 dark:text-amber-400 flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:16px">swap_horiz</span>
                        Medication Changes
                    </p>
                    @if($medReconciliation['changes']->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($medReconciliation['changes'] as $change)
                        <div class="p-3 bg-amber-50/50 dark:bg-amber-900/10 rounded-lg border border-amber-100 dark:border-amber-900/20">
                            <p class="text-sm font-alte-bold text-slate-800 dark:text-slate-200">{{ $change->changes_in_medication }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Changed on: {{ \Carbon\Carbon::parse($change->updated_at)->format('M d, Y') }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-slate-400 dark:text-slate-600">No changes recorded.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Diagnostics & Vital Signs --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Diagnostics (Images) --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
                <p class="section-title mb-4">Diagnostics & Imaging</p>
                <div class="space-y-4">
                    @if($diagnosticImages->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($diagnosticImages as $image)
                        <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank" class="group relative aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover transition-transform group-hover:scale-110" alt="Diagnostic Image">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                                <span class="material-symbols-outlined text-white opacity-0 group-hover:opacity-100 transition-opacity" style="font-size:24px">zoom_in</span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-1.5 bg-black/60 backdrop-blur-sm">
                                <p class="text-[10px] text-white truncate text-center">{{ $image->image_type }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-slate-400 dark:text-slate-600 font-alte-regular">No diagnostic images uploaded.</p>
                    @endif

                    @if($diagnostics->isNotEmpty())
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <p class="detail-label mb-2">Diagnostic Reports</p>
                        <ul class="space-y-2">
                            @foreach($diagnostics as $diag)
                            <li class="flex items-center gap-2 text-sm font-alte-regular text-slate-600 dark:text-slate-400">
                                <span class="material-symbols-outlined text-slate-400" style="font-size:16px">description</span>
                                {{ $diag->diagnostic_name }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Vital Signs Summary --}}
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-4">
                    <p class="section-title">Latest Vital Signs</p>
                    <a href="{{ route('doctor.form-detail', ['type' => 'vital-signs', 'patient_id' => $patient->patient_id]) }}?from=patient-details&prev={{ $fromKey }}" 
                       class="text-xs font-alte text-emerald-600 hover:underline">View All</a>
                </div>
                @if($vitalsSummary->isNotEmpty())
                <div class="space-y-3">
                    @foreach($vitalsSummary as $vs)
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ \Carbon\Carbon::parse($vs->date)->format('M d, Y') }} &bull; {{ $vs->time }}</p>
                            @if($vs->alerts)
                                <span class="material-symbols-outlined text-amber-500" style="font-size:14px">warning</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase">Temp</p>
                                <p class="text-sm font-alte-bold text-slate-800 dark:text-slate-200">{{ $vs->temperature }}°C</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase">BP</p>
                                <p class="text-sm font-alte-bold text-slate-800 dark:text-slate-200">{{ $vs->bp }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase">HR</p>
                                <p class="text-sm font-alte-bold text-slate-800 dark:text-slate-200">{{ $vs->hr }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase">SpO2</p>
                                <p class="text-sm font-alte-bold text-slate-800 dark:text-slate-200">{{ $vs->spo2 }}%</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="py-10 text-center">
                    <span class="material-symbols-outlined text-slate-300 dark:text-slate-700" style="font-size:48px">monitor_heart</span>
                    <p class="text-sm text-slate-400 dark:text-slate-600 mt-2">No vital signs recorded.</p>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 transition-colors duration-300">
            <p class="section-title mb-4">Patient Form Records</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @php
                    $formLinks = [
                        ['type' => 'vital-signs',   'label' => 'Vital Signs',              'icon' => 'monitor_heart',    'color_light' => 'bg-red-50 text-red-700 border-red-200', 'color_dark' => 'dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/30'],
                        ['type' => 'physical-exam', 'label' => 'Physical Exam',            'icon' => 'person_search',    'color_light' => 'bg-purple-50 text-purple-700 border-purple-200', 'color_dark' => 'dark:bg-purple-900/20 dark:text-purple-400 dark:border-purple-900/30'],
                        ['type' => 'adl',           'label' => 'Activities of Daily Living','icon' => 'self_improvement', 'color_light' => 'bg-orange-50 text-orange-700 border-orange-200', 'color_dark' => 'dark:bg-orange-900/20 dark:text-orange-400 dark:border-orange-900/30'],
                        ['type' => 'intake-output', 'label' => 'Intake & Output',          'icon' => 'water_drop',       'color_light' => 'bg-blue-50 text-blue-700 border-blue-200', 'color_dark' => 'dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900/30'],
                        ['type' => 'lab-values',    'label' => 'Lab Values',               'icon' => 'biotech',          'color_light' => 'bg-teal-50 text-teal-700 border-teal-200', 'color_dark' => 'dark:bg-teal-900/20 dark:text-teal-400 dark:border-teal-900/30'],
                        ['type' => 'medication',    'label' => 'Medication Admin.',        'icon' => 'medication',       'color_light' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'color_dark' => 'dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/30'],
                        ['type' => 'ivs-lines',     'label' => 'IVs & Lines',              'icon' => 'vaccines',         'color_light' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'color_dark' => 'dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-900/30'],
                        ['type' => 'medical-history','label' => 'Medical History',         'icon' => 'history_edu',      'color_light' => 'bg-slate-50 text-slate-700 border-slate-200', 'color_dark' => 'dark:bg-slate-900/20 dark:text-slate-400 dark:border-slate-900/30'],
                        ['type' => 'diagnostics',    'label' => 'Diagnostics',             'icon' => 'biotech',          'color_light' => 'bg-teal-50 text-teal-700 border-teal-200', 'color_dark' => 'dark:bg-teal-900/20 dark:text-teal-400 dark:border-teal-900/30'],
                        ['type' => 'med-reconciliation','label' => 'Med. Reconciliation', 'icon' => 'rebase_edit',      'color_light' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'color_dark' => 'dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/30'],
                    ];
                @endphp
                @foreach ($formLinks as $fl)
                <a href="{{ route('doctor.form-detail', ['type' => $fl['type'], 'patient_id' => $patient->patient_id]) }}?from=patient-details&prev={{ $fromKey }}"
                   class="flex items-center gap-3 p-3.5 rounded-xl border {{ $fl['color_light'] }} {{ $fl['color_dark'] }}
                          hover:shadow-md dark:hover:shadow-slate-900/50 transition-all group">
                    <span class="material-symbols-outlined" style="font-size:20px">{{ $fl['icon'] }}</span>
                    <span class="form-badge leading-tight">{{ $fl['label'] }}</span>
                    <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 group-hover:text-slate-500 dark:group-hover:text-slate-400 ml-auto transition-colors" style="font-size:16px">chevron_right</span>
                </a>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
