@extends('layouts.doctor')
@section('title', $label . ' — ' . $patient->name)

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .alert-badge { display:inline-flex; align-items:center; gap:3px; padding:2px 8px;
                       border-radius:9999px; font-size:10px; font-weight:700; line-height:1.4; white-space:nowrap; }
        .alert-badge.ok    { background:#D1FAE5; color:#065F46; }
        .alert-badge.info  { background:#DBEAFE; color:#1E40AF; }
        /* Compact clickable alert chip */
        .alert-chip {
            display: inline-flex; align-items: center; gap: 3px;
            padding: 3px 9px; border-radius: 9999px; border: none; cursor: pointer;
            font-size: 11px; font-family: 'Alte Haas Grotesk Bold', arial; line-height: 1.4;
            max-width: 130px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            transition: filter 0.1s;
        }
        .alert-chip.warn { background: #FEF3C7; color: #92400E; }
        .alert-chip.warn:hover { filter: brightness(0.94); }
        .alert-chip .chip-icon { flex-shrink: 0; font-size: 11px !important; }
        .alert-chip .chip-text { overflow: hidden; text-overflow: ellipsis; }
        /* Modal */
        #alert-modal { transition: opacity 0.15s; }
        #alert-modal.hidden { display: none; }
        #modal-panel { animation: modalIn 0.15s ease; }
        @keyframes modalIn { from { transform: translate(-50%,-48%) scale(0.96); opacity:0; } to { transform: translate(-50%,-50%) scale(1); opacity:1; } }
        .tbl th { font-size:13px; font-weight:normal; font-family:'Alte Haas Grotesk Bold', arial; text-transform:uppercase;
                  letter-spacing:0.08em; color:#6B7280; padding:16px 18px; white-space:nowrap; }
        .tbl td { font-size:15px; font-family:'Alte Haas Grotesk', arial; color:#1F2937; padding:13px 18px; vertical-align:top; }
        .tbl tr:nth-child(even) td { background:#F9FAFB; }
        .tbl tr:hover td { background:#EFF6FF; transition:background 0.12s; }
        .nd-badge { display:inline-block; padding:3px 10px; border-radius:8px; font-size:11px;
                    font-family:'Alte Haas Grotesk', arial; background:#F3F4F6; color:#374151; margin:2px 2px 2px 0; }
    </style>
@endpush

@section('content')
<div class="-mx-6 min-h-screen bg-[#f0f8f0] font-rubik">

    <div class="bg-white border-b border-gray-200">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-4 sm:py-5">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-sm text-gray-400 mb-3 flex-wrap">
                <a href="{{ route('doctor-home') }}" class="text-sm font-alte-regular hover:text-gray-600 transition-colors">Dashboard</a>
                <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                <a href="{{ $fromCrumb['url'] }}" class="text-sm font-alte-regular hover:text-gray-600 transition-colors">{{ $fromCrumb['label'] }}</a>
                <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                <span class="text-sm font-alte text-gray-600 truncate max-w-[160px]">{{ $patient->name }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Form type icon --}}
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background-color: {{ $color }}1a">
                        <span class="material-symbols-outlined" style="font-size:22px; color:{{ $color }}">{{ $icon }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-alte tracking-widest uppercase mb-0.5" style="color:{{ $color }}">
                            Assessment Record
                        </p>
                        <h1 class="text-xl font-alte text-gray-900 leading-tight truncate">
                            {{ $label }}
                        </h1>
                        <p class="text-sm font-alte-regular text-gray-500 mt-0.5 truncate">
                            Patient: <span class="font-alte text-gray-700">{{ $patient->name }}</span>
                            @if($patient->patient_id)
                                &nbsp;·&nbsp; ID: <span class="font-mono text-gray-600">{{ $patient->patient_id }}</span>
                            @endif
                            @if($patient->admission_date)
                                &nbsp;·&nbsp; Admitted: <span class="font-alte text-gray-700">{{ \Carbon\Carbon::parse($patient->admission_date)->format('M j, Y') }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
                    <a href="{{ $fromCrumb['url'] }}"
                       class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-alte px-4 py-2 rounded-xl transition-colors">
                        <span class="material-symbols-outlined" style="font-size:15px">arrow_back</span>
                        Back
                    </a>
                    <form method="POST" action="{{ route('doctor.generate-report') }}" class="inline">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">
                        <button type="submit"
                           class="inline-flex items-center gap-1.5 bg-green-700 hover:bg-green-800 text-white text-sm font-alte px-4 py-2 rounded-xl shadow-sm transition-colors whitespace-nowrap">
                            <span class="material-symbols-outlined" style="font-size:15px">picture_as_pdf</span>
                            Full Report
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-6">

        @if ($records->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-20 text-center">
                <span class="material-symbols-outlined text-gray-300 block mb-3" style="font-size:56px">{{ $icon }}</span>
                <p class="text-base font-alte text-gray-500">No {{ $label }} records found</p>
                <p class="text-sm font-alte-regular text-gray-400 mt-1">No data has been submitted for this patient yet.</p>
            </div>

        @elseif ($type === 'vital-signs')
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:18px; color:#EF4444">monitor_heart</span>
                    <h2 class="font-alte text-gray-800 text-base">Vital Signs Records</h2>
                    <span class="ml-auto text-sm font-alte-regular text-gray-400">{{ $records->count() }} record(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="tbl w-full border-collapse">
                        <thead class="bg-green-50/60 border-b border-green-100">
                            <tr>
                                <th class="text-left">Day</th>
                                <th class="text-left">Date</th>
                                <th class="text-left">Time</th>
                                <th class="text-left">Temp (°C)</th>
                                <th class="text-left">HR (bpm)</th>
                                <th class="text-left">RR (/min)</th>
                                <th class="text-left">BP (mmHg)</th>
                                <th class="text-left">SpO2 (%)</th>
                                <th class="text-left">Alert</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($records as $r)
                                <tr>
                                    <td class="font-alte-regular text-gray-500">{{ $r->day_no ?? '—' }}</td>
                                    <td>{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('M j, Y') : '—' }}</td>
                                    <td>{{ $r->time ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->temperature ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->hr ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->rr ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->bp ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->spo2 ?? '—' }}</td>
                                    <td>
                                        @if($r->alerts)
                                            <button type="button" class="alert-chip warn"
                                                    onclick="openAlertModal(this)"
                                                    data-title="Vital Signs Alert"
                                                    data-items="{{ json_encode([$r->alerts]) }}">
                                                <span class="material-symbols-outlined chip-icon">warning</span>
                                                <span class="chip-text">{{ \Illuminate\Support\Str::limit($r->alerts, 14) }}</span>
                                            </button>
                                        @else
                                            <span class="alert-badge ok">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="py-0 border-0 bg-indigo-50/30">
                                        <div class="px-5 py-3">
                                            <p class="text-[10px] font-alte text-indigo-400 uppercase tracking-wider mb-2">Nursing Diagnosis (ADPIE)</p>
                                            @include('doctor.partials.nd-block', ['nd' => $r->nursingDiagnoses])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif ($type === 'physical-exam')
            @php
                $systems = [
                    ['General Appearance', 'general_appearance', 'general_appearance_alert'],
                    ['Skin',               'skin_condition',      'skin_alert'],
                    ['Eyes',               'eye_condition',       'eye_alert'],
                    ['Oral',               'oral_condition',      'oral_alert'],
                    ['Cardiovascular',     'cardiovascular',      'cardiovascular_alert'],
                    ['Abdomen',            'abdomen_condition',   'abdomen_alert'],
                    ['Extremities',        'extremities',         'extremities_alert'],
                    ['Neurological',       'neurological',        'neurological_alert'],
                ];
            @endphp
            @foreach ($records as $i => $r)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden {{ $loop->first ? '' : 'mt-5' }}">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:18px; color:#8B5CF6">person_search</span>
                        <h2 class="font-alte text-gray-800 text-base">
                            Physical Exam Record {{ $records->count() > 1 ? '#'.($i+1) : '' }}
                        </h2>
                        <span class="ml-auto text-sm font-alte-regular text-gray-400">{{ \Carbon\Carbon::parse($r->updated_at)->format('M j, Y g:i A') }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="tbl w-full border-collapse">
                            <thead class="bg-green-50/60 border-b border-green-100">
                                <tr>
                                    <th class="text-left w-40">Body System</th>
                                    <th class="text-left">Condition / Findings</th>
                                    <th class="text-left w-32">Alert</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($systems as [$sysLabel, $field, $alertField])
                                    @if ($r->$field)
                                        <tr>
                                            <td class="font-bold text-gray-600">{{ $sysLabel }}</td>
                                            <td>{{ $r->$field }}</td>
                                            <td>
                                                @if($r->$alertField)
                                                    <button type="button" class="alert-chip warn"
                                                            onclick="openAlertModal(this)"
                                                            data-title="{{ $sysLabel }} Alert"
                                                            data-items="{{ json_encode([$r->$alertField]) }}">
                                                        <span class="material-symbols-outlined chip-icon">warning</span>
                                                        <span class="chip-text">{{ \Illuminate\Support\Str::limit($r->$alertField, 14) }}</span>
                                                    </button>
                                                @else
                                                    <span class="alert-badge ok">Normal</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t border-indigo-100 bg-indigo-50/30">
                        <p class="text-[10px] font-alte text-indigo-400 uppercase tracking-wider mb-2">Nursing Diagnosis (ADPIE)</p>
                        @include('doctor.partials.nd-block', ['nd' => $r->nursingDiagnoses])
                    </div>
                </div>
            @endforeach

        @elseif ($type === 'adl')
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:18px; color:#F97316">self_improvement</span>
                    <h2 class="font-alte text-gray-800 text-base">Activities of Daily Living</h2>
                    <span class="ml-auto text-sm font-alte-regular text-gray-400">{{ $records->count() }} record(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="tbl w-full border-collapse">
                        <thead class="bg-green-50/60 border-b border-green-100">
                            <tr>
                                <th class="text-left">Day</th>
                                <th class="text-left">Date</th>
                                <th class="text-left">Mobility</th>
                                <th class="text-left">Hygiene</th>
                                <th class="text-left">Toileting</th>
                                <th class="text-left">Feeding</th>
                                <th class="text-left">Hydration</th>
                                <th class="text-left">Sleep</th>
                                <th class="text-left">Pain Level</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($records as $r)
                                <tr>
                                    <td class="font-alte-regular text-gray-500">{{ $r->day_no ?? '—' }}</td>
                                    <td>{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('M j, Y') : '—' }}</td>
                                    <td>
                                        {{ $r->mobility_assessment ?? '—' }}
                                        @if($r->mobility_alert)
                                            <div><button type="button" class="alert-chip warn mt-1" onclick="openAlertModal(this)" data-title="Mobility Alert" data-items="{{ json_encode([$r->mobility_alert]) }}"><span class="material-symbols-outlined chip-icon">warning</span><span class="chip-text">{{ \Illuminate\Support\Str::limit($r->mobility_alert, 14) }}</span></button></div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $r->hygiene_assessment ?? '—' }}
                                        @if($r->hygiene_alert)
                                            <div><button type="button" class="alert-chip warn mt-1" onclick="openAlertModal(this)" data-title="Hygiene Alert" data-items="{{ json_encode([$r->hygiene_alert]) }}"><span class="material-symbols-outlined chip-icon">warning</span><span class="chip-text">{{ \Illuminate\Support\Str::limit($r->hygiene_alert, 14) }}</span></button></div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $r->toileting_assessment ?? '—' }}
                                        @if($r->toileting_alert)
                                            <div><button type="button" class="alert-chip warn mt-1" onclick="openAlertModal(this)" data-title="Toileting Alert" data-items="{{ json_encode([$r->toileting_alert]) }}"><span class="material-symbols-outlined chip-icon">warning</span><span class="chip-text">{{ \Illuminate\Support\Str::limit($r->toileting_alert, 14) }}</span></button></div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $r->feeding_assessment ?? '—' }}
                                        @if($r->feeding_alert)
                                            <div><button type="button" class="alert-chip warn mt-1" onclick="openAlertModal(this)" data-title="Feeding Alert" data-items="{{ json_encode([$r->feeding_alert]) }}"><span class="material-symbols-outlined chip-icon">warning</span><span class="chip-text">{{ \Illuminate\Support\Str::limit($r->feeding_alert, 14) }}</span></button></div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $r->hydration_assessment ?? '—' }}
                                        @if($r->hydration_alert)
                                            <div><button type="button" class="alert-chip warn mt-1" onclick="openAlertModal(this)" data-title="Hydration Alert" data-items="{{ json_encode([$r->hydration_alert]) }}"><span class="material-symbols-outlined chip-icon">warning</span><span class="chip-text">{{ \Illuminate\Support\Str::limit($r->hydration_alert, 14) }}</span></button></div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $r->sleep_pattern_assessment ?? '—' }}
                                        @if($r->sleep_pattern_alert)
                                            <div><button type="button" class="alert-chip warn mt-1" onclick="openAlertModal(this)" data-title="Sleep Pattern Alert" data-items="{{ json_encode([$r->sleep_pattern_alert]) }}"><span class="material-symbols-outlined chip-icon">warning</span><span class="chip-text">{{ \Illuminate\Support\Str::limit($r->sleep_pattern_alert, 14) }}</span></button></div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $r->pain_level_assessment ?? '—' }}
                                        @if($r->pain_level_alert)
                                            <div><button type="button" class="alert-chip warn mt-1" onclick="openAlertModal(this)" data-title="Pain Level Alert" data-items="{{ json_encode([$r->pain_level_alert]) }}"><span class="material-symbols-outlined chip-icon">warning</span><span class="chip-text">{{ \Illuminate\Support\Str::limit($r->pain_level_alert, 14) }}</span></button></div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="py-0 border-0 bg-indigo-50/30">
                                        <div class="px-5 py-3">
                                            <p class="text-[10px] font-alte text-indigo-400 uppercase tracking-wider mb-2">Nursing Diagnosis (ADPIE)</p>
                                            @include('doctor.partials.nd-block', ['nd' => $r->nursingDiagnoses])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif ($type === 'intake-output')
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:18px; color:#3B82F6">water_drop</span>
                    <h2 class="font-alte text-gray-800 text-base">Intake & Output Records</h2>
                    <span class="ml-auto text-sm font-alte-regular text-gray-400">{{ $records->count() }} record(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="tbl w-full border-collapse">
                        <thead class="bg-green-50/60 border-b border-green-100">
                            <tr>
                                <th class="text-left">Day</th>
                                <th class="text-left">Oral Intake (mL)</th>
                                <th class="text-left">IV Fluids Vol (mL)</th>
                                <th class="text-left">IV Fluids Type</th>
                                <th class="text-left">Urine Output (mL)</th>
                                <th class="text-left">Alert</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($records as $r)
                                <tr>
                                    <td class="font-alte-regular text-gray-500">{{ $r->day_no ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->oral_intake ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->iv_fluids_volume ?? '—' }}</td>
                                    <td>{{ $r->iv_fluids_type ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->urine_output ?? '—' }}</td>
                                    <td>
                                        @if($r->alert)
                                            <button type="button" class="alert-chip warn"
                                                    onclick="openAlertModal(this)"
                                                    data-title="Intake & Output Alert"
                                                    data-items="{{ json_encode([$r->alert]) }}">
                                                <span class="material-symbols-outlined chip-icon">warning</span>
                                                <span class="chip-text">{{ \Illuminate\Support\Str::limit($r->alert, 14) }}</span>
                                            </button>
                                        @else
                                            <span class="alert-badge ok">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="py-0 border-0 bg-indigo-50/30">
                                        <div class="px-5 py-3">
                                            <p class="text-[10px] font-alte text-indigo-400 uppercase tracking-wider mb-2">Nursing Diagnosis (ADPIE)</p>
                                            @include('doctor.partials.nd-block', ['nd' => $r->nursingDiagnoses])
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif ($type === 'lab-values')
            @php
                $labTests = [
                    ['WBC',         'wbc'],
                    ['RBC',         'rbc'],
                    ['Hemoglobin',  'hgb'],
                    ['Hematocrit',  'hct'],
                    ['Platelets',   'platelets'],
                    ['MCV',         'mcv'],
                    ['MCH',         'mch'],
                    ['MCHC',        'mchc'],
                    ['RDW',         'rdw'],
                    ['Neutrophils', 'neutrophils'],
                    ['Lymphocytes', 'lymphocytes'],
                    ['Monocytes',   'monocytes'],
                    ['Eosinophils', 'eosinophils'],
                    ['Basophils',   'basophils'],
                ];
            @endphp
            @foreach ($records as $i => $r)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden {{ $loop->first ? '' : 'mt-5' }}">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center gap-2">
                        <span class="material-symbols-outlined" style="font-size:18px; color:#0D9488">biotech</span>
                        <h2 class="font-alte text-gray-800 text-base">
                            Lab Values {{ $records->count() > 1 ? '#'.($i+1) : '' }}
                        </h2>
                        <span class="ml-auto text-sm font-alte-regular text-gray-400">{{ \Carbon\Carbon::parse($r->updated_at)->format('M j, Y g:i A') }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="tbl w-full border-collapse">
                            <thead class="bg-green-50/60 border-b border-green-100">
                                <tr>
                                    <th class="text-left w-36">Test</th>
                                    <th class="text-left">Result</th>
                                    <th class="text-left">Normal Range</th>
                                    <th class="text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($labTests as [$testName, $key])
                                    @if ($r->{$key.'_result'} !== null && $r->{$key.'_result'} !== '')
                                        <tr>
                                            <td class="font-bold text-gray-700">{{ $testName }}</td>
                                            <td class="font-mono font-bold text-gray-900">{{ $r->{$key.'_result'} }}</td>
                                            <td class="text-gray-500 font-mono text-xs">{{ $r->{$key.'_normal_range'} ?? '—' }}</td>
                                            <td>
                                                @php $alert = $r->{$key.'_alert'} ?? null; @endphp
                                                @if($alert)
                                                    <button type="button" class="alert-chip warn"
                                                            onclick="openAlertModal(this)"
                                                            data-title="{{ $testName }} Alert"
                                                            data-items="{{ json_encode([$alert]) }}">
                                                        <span class="material-symbols-outlined chip-icon">warning</span>
                                                        <span class="chip-text">{{ \Illuminate\Support\Str::limit($alert, 14) }}</span>
                                                    </button>
                                                @else
                                                    <span class="alert-badge ok">Normal</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t border-indigo-100 bg-indigo-50/30">
                        <p class="text-[10px] font-alte text-indigo-400 uppercase tracking-wider mb-2">Nursing Diagnosis (ADPIE)</p>
                        @include('doctor.partials.nd-block', ['nd' => $r->nursingDiagnoses])
                    </div>
                </div>
            @endforeach

        @elseif ($type === 'medication')
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:18px; color:#10B981">medication</span>
                    <h2 class="font-alte text-gray-800 text-base">Medication Administration Records</h2>
                    <span class="ml-auto text-sm font-alte-regular text-gray-400">{{ $records->count() }} record(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="tbl w-full border-collapse">
                        <thead class="bg-green-50/60 border-b border-green-100">
                            <tr>
                                <th class="text-left">Date</th>
                                <th class="text-left">Time</th>
                                <th class="text-left">Medication</th>
                                <th class="text-left">Dose</th>
                                <th class="text-left">Route</th>
                                <th class="text-left">Frequency</th>
                                <th class="text-left">Comments</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($records as $r)
                                <tr>
                                    <td>{{ $r->date ? \Carbon\Carbon::parse($r->date)->format('M j, Y') : '—' }}</td>
                                    <td>{{ $r->time ?? '—' }}</td>
                                    <td class="font-bold text-gray-900">{{ $r->medication ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->dose ?? '—' }}</td>
                                    <td>
                                        @if($r->route)
                                            <span class="alert-badge info">{{ $r->route }}</span>
                                        @else —
                                        @endif
                                    </td>
                                    <td>{{ $r->frequency ?? '—' }}</td>
                                    <td class="text-gray-500 italic text-xs">{{ $r->comments ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif ($type === 'ivs-lines')
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/60 flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:18px; color:#6366F1">vaccines</span>
                    <h2 class="font-alte text-gray-800 text-base">IVs & Lines Records</h2>
                    <span class="ml-auto text-sm font-alte-regular text-gray-400">{{ $records->count() }} record(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="tbl w-full border-collapse">
                        <thead class="bg-green-50/60 border-b border-green-100">
                            <tr>
                                <th class="text-left">IV Fluid</th>
                                <th class="text-left">Rate</th>
                                <th class="text-left">Site</th>
                                <th class="text-left">Status</th>
                                <th class="text-left">Last Updated</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($records as $r)
                                <tr>
                                    <td class="font-bold text-gray-900">{{ $r->iv_fluid ?? '—' }}</td>
                                    <td class="font-alte-regular">{{ $r->rate ?? '—' }}</td>
                                    <td>{{ $r->site ?? '—' }}</td>
                                    <td>
                                        @php $st = strtolower($r->status ?? ''); @endphp
                                        @if(str_contains($st, 'active') || str_contains($st, 'running'))
                                            <span class="alert-badge ok">{{ $r->status }}</span>
                                        @elseif($st)
                                            <span class="alert-badge info">{{ $r->status }}</span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($r->updated_at)->format('M j, Y g:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @endif

        {{-- ─ Bottom action bar ─ --}}
        <div class="mt-6 flex items-center justify-between flex-wrap gap-3">
            <a href="{{ $fromCrumb['url'] }}"
               class="inline-flex items-center gap-1.5 text-sm font-alte text-gray-500 hover:text-gray-800 transition-colors">
                <span class="material-symbols-outlined" style="font-size:17px">arrow_back</span>
                Back to {{ $fromCrumb['label'] }}
            </a>
            <form method="POST" action="{{ route('doctor.generate-report') }}">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->patient_id }}">
                <button type="submit"
                   class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white
                          text-sm font-alte px-5 py-2.5 rounded-xl shadow-sm transition-colors">
                    <span class="material-symbols-outlined" style="font-size:17px">picture_as_pdf</span>
                    Generate Full Report
                </button>
            </form>
        </div>

    </div>{{-- /content --}}
</div>{{-- /wrapper --}}

<div id="alert-modal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-[1px]" onclick="closeAlertModal()"></div>

    {{-- Panel --}}
    <div id="modal-panel"
         class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 z-10 overflow-hidden">

        {{-- Modal header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-amber-50/60">
            <div class="flex items-center gap-2 min-w-0">
                <span class="material-symbols-outlined text-amber-500 flex-shrink-0" style="font-size:20px">warning</span>
                <h3 id="modal-title" class="font-alte text-gray-800 text-base leading-tight truncate">Alert Details</h3>
            </div>
            <button onclick="closeAlertModal()"
                    class="flex-shrink-0 ml-3 text-gray-400 hover:text-gray-700 transition-colors rounded-lg p-1 hover:bg-gray-100">
                <span class="material-symbols-outlined" style="font-size:20px">close</span>
            </button>
        </div>

        {{-- Modal body --}}
        <div class="px-5 py-4 max-h-80 overflow-y-auto">
            <ul id="modal-list" class="space-y-2.5"></ul>
        </div>

        {{-- Modal footer --}}
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 text-right">
            <button onclick="closeAlertModal()"
                    class="inline-flex items-center gap-1.5 text-sm font-alte text-gray-600
                           hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openAlertModal(el) {
        const title = el.dataset.title || 'Alert Details';
        let items = [];
        try { items = JSON.parse(el.dataset.items || '[]'); } catch(e) {}

        document.getElementById('modal-title').textContent = title;

        const list = document.getElementById('modal-list');
        list.innerHTML = items
            .filter(i => i && String(i).trim())
            .map(item => `
                <li class="flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-amber-500 flex-shrink-0 mt-0.5" style="font-size:15px">error</span>
                    <span style="font-family:'Alte Haas Grotesk',arial; font-size:14px; color:#374151; line-height:1.5;">${item}</span>
                </li>
            `).join('');

        if (list.innerHTML === '') {
            list.innerHTML = '<li style="font-family:\'Alte Haas Grotesk\',arial; font-size:14px; color:#6B7280;">No alert detail available.</li>';
        }

        document.getElementById('alert-modal').classList.remove('hidden');
    }

    function closeAlertModal() {
        document.getElementById('alert-modal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAlertModal();
    });
</script>
@endpush

@endsection


