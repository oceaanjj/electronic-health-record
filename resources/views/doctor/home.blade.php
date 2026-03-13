@extends('layouts.doctor')
@section('title', 'Doctor Dashboard')

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .stat-card {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.09);
        }

        .action-card {
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }

        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.09);
        }

        .forms-feed {
            overflow-y: auto;
            max-height: 430px;
        }

        .forms-feed::-webkit-scrollbar {
            width: 4px;
        }

        .forms-feed::-webkit-scrollbar-track {
            background: #f9fafb;
        }

        .forms-feed::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        /* Pill filter styles */
        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 9999px;
            padding: 5px 12px;
            font-size: 0.7rem;
            font-family: inherit;
            cursor: pointer;
            transition: background-color 0.15s, color 0.15s;
            white-space: nowrap;
            border: none;
            outline: none;
        }

        .filter-pill:not(.active) {
            background-color: #f3f4f6;
            color: #4b5563;
        }

        .filter-pill:not(.active):hover {
            background-color: #e5e7eb;
        }

        .filter-pill.active {
            background-color: #15803d;
            color: #ffffff;
        }

        .filter-pill .pill-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 17px;
            height: 15px;
            border-radius: 9999px;
            padding: 0 4px;
            font-size: 0.65rem;
            line-height: 1;
        }

        .filter-pill:not(.active) .pill-count {
            background-color: rgba(209, 213, 219, 0.8);
            color: #374151;
        }

        .filter-pill.active .pill-count {
            background-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }
    </style>
@endpush

@section('content')

    @if (session('sweetalert'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(function () {
                        const opts = @json(session('sweetalert'));
                        if (typeof showSuccess === 'function' && opts.type === 'success') {
                            showSuccess(opts.text || opts.title, opts.title || 'Success!', opts.timer);
                        } else if (typeof showError === 'function' && opts.type === 'error') {
                            showError(opts.text || opts.title, opts.title || 'Error!', opts.timer);
                        } else if (typeof showWarning === 'function' && opts.type === 'warning') {
                            showWarning(opts.text || opts.title, opts.title || 'Warning!', opts.timer);
                        } else if (typeof showInfo === 'function' && opts.type === 'info') {
                            showInfo(opts.text || opts.title, opts.title || 'Info', opts.timer);
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: opts.type || 'info', title: opts.title || '', text: opts.text || '', timer: opts.timer || 2000 });
                        }
                    }, 100);
                });
            </script>
        @endpush
    @endif

    @php
        $statsData = [
            [
                'label' => 'Total Patients',
                'value' => $totalPatients,
                'sub' => 'All registered',
                'icon' => 'groups',
                'accent' => '#3B82F6',
                'bg' => '#EFF6FF',
                'text' => '#1D4ED8',
                'href' => route('doctor.stats.total-patients'),
            ],
            [
                'label' => 'Active Patients',
                'value' => $activePatients,
                'sub' => 'Currently admitted',
                'icon' => 'person_check',
                'accent' => '#16A34A',
                'bg' => '#F0FDF4',
                'text' => '#15803D',
                'href' => route('doctor.stats.active-patients'),
            ],
            [
                'label' => "Today's Updates",
                'value' => $todayForms,
                'sub' => 'Forms updated today',
                'icon' => 'edit_note',
                'accent' => '#D97706',
                'bg' => '#FFFBEB',
                'text' => '#B45309',
                'href' => route('doctor.stats.today-updates'),
            ],
        ];
        $formTypes = [
            ['Vital Signs', 'monitor_heart', '#EF4444'],
            ['Physical Exam', 'person_search', '#8B5CF6'],
            ['Activities of Daily Living', 'self_improvement', '#F97316'],
            ['Intake & Output', 'water_drop', '#3B82F6'],
            ['Lab Values', 'biotech', '#0D9488'],
            ['Medication Administration', 'medication', '#10B981'],
            ['IVs & Lines', 'vaccines', '#6366F1'],
        ];
    @endphp

    {{-- Stretch to fill, cancel the layout's px-6 --}}
    <div class="-mx-6 min-h-screen bg-[#f0f8f0] font-rubik">

        <div class="bg-white border-b border-gray-200">
            <div
                class="max-w-screen-xl mx-auto px-4 sm:px-8 py-4 sm:py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                <div class="min-w-0">
                    <p class="text-xl font-alte text-green-600 uppercase mb-0.5">
                        Doctor's Dashboard
                    </p>

                    <p class="text-sm font-alte text-gray-400 mt-0.5">{{ now()->format('l, F j, Y') }}</p>
                </div>
                <a href="{{ route('doctor.patient-report') }}"
                    class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white text-sm font-alte px-4 sm:px-5 py-2.5 rounded-xlshadow-sm hover:shadow-md transition-all duration-150 self-start sm:self-auto whitespace-nowrap">
                    <span class="material-symbols-outlined" style="font-size:18px">picture_as_pdf</span>
                    Generate PDF Report
                </a>
            </div>
        </div>

        <div class="max-w-screen-xl mx-auto px-4 sm:px-8 py-5 sm:py-7 space-y-6">

            {{-- Stats row: always 3 cards side by side --}}
            <div style="display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:clamp(0.75rem,2vw,1.25rem)">
                @foreach ($statsData as $stat)
                    <a href="{{ $stat['href'] }}"
                        class="stat-card bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden relative block">
                        {{-- Colored top accent stripe --}}
                        <div class="h-1 w-full" style="background-color: {{ $stat['accent'] }}"></div>

                        <div class="p-3 sm:p-5">
                            {{-- Icon row: visible sm+ --}}
                            <div class="hidden sm:flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                    style="background-color: {{ $stat['bg'] }}">
                                    <span class="material-symbols-outlined"
                                        style="font-size:22px; color:{{ $stat['accent'] }}">{{ $stat['icon'] }}</span>
                                </div>
                                <p class="text-sm font-alte text-gray-500 leading-snug">{{ $stat['label'] }}</p>
                            </div>

                            {{-- Number — uniform text-3xl at all sizes --}}
                            <p class="text-4xl font-alte leading-none" style="color: {{ $stat['text'] }}">{{ $stat['value'] }}
                            </p>

                            {{-- Label: mobile only --}}
                            <p class="sm:hidden text-sm font-alte text-gray-500 mt-1 leading-tight">{{ $stat['label'] }}</p>

                            {{-- Sub-text: sm+ --}}
                            <p class="hidden sm:block text-sm font-alte-regular text-gray-400 mt-1">{{ $stat['sub'] }}</p>

                            {{-- Tap indicator --}}
                            <p class="hidden sm:flex items-center gap-0.5 text-xs font-alte-regular mt-2"
                                style="color: {{ $stat['accent'] }}; opacity:0.7">
                                View details
                                <span class="material-symbols-outlined" style="font-size:11px">arrow_forward_ios</span>
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Main content grid: stacked on mobile, 2-col (40/60) on lg+ --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-start">

                {{-- Quick Actions — left col (2/5) --}}
                <div class="lg:col-span-2 flex flex-col gap-4">

                    {{-- Section label --}}
                    <p class="text-sm font-alte tracking-widest text-gray-400 uppercase px-1">Quick Actions</p>

                    {{-- Generate Report --}}
                    <a href="{{ route('doctor.patient-report') }}" class="action-card group flex items-center gap-4 bg-white rounded-2xl
                                                          border border-gray-200 shadow-sm p-4 sm:p-5
                                                          hover:border-green-400">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-green-50 flex items-center justify-center
                                                                flex-shrink-0 group-hover:bg-green-100 transition-colors">
                            <span class="material-symbols-outlined text-green-700" style="font-size:24px">analytics</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-alte text-base text-gray-800">Generate Patient Report</p>
                            <p class="text-sm font-alte-regular text-gray-500 mt-0.5 leading-relaxed hidden sm:block">
                                Export a full PDF of any patient's medical records.
                            </p>
                        </div>
                        <span class="material-symbols-outlined text-gray-300 group-hover:text-green-600
                                                                 group-hover:translate-x-0.5 transition-all flex-shrink-0"
                            style="font-size:20px">arrow_forward_ios</span>
                    </a>

                    {{-- Browse Forms --}}
                    <a href="{{ route('doctor.recent-forms') }}" class="action-card group flex items-center gap-4 bg-white rounded-2xl
                                                          border border-gray-200 shadow-sm p-4 sm:p-5
                                                          hover:border-blue-400">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-50 flex items-center justify-center
                                                                flex-shrink-0 group-hover:bg-blue-100 transition-colors">
                            <span class="material-symbols-outlined text-blue-600" style="font-size:24px">fact_check</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-alte text-base text-gray-800">Browse All Forms</p>
                            <p class="text-sm font-alte-regular text-gray-500 mt-0.5 leading-relaxed hidden sm:block">
                                Filter and search all patient assessment forms.
                            </p>
                        </div>
                        <span class="material-symbols-outlined text-gray-300 group-hover:text-blue-500
                                                                 group-hover:translate-x-0.5 transition-all flex-shrink-0"
                            style="font-size:20px">arrow_forward_ios</span>
                    </a>

                    {{-- Assessment Types legend --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5">
                        <p class="text-sm font-alte tracking-widest text-gray-400 uppercase mb-3">Assessment Types</p>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-y-2.5 gap-x-3">
                            @foreach ($formTypes as [$label, $icon, $color])
                                <li class="flex items-center gap-2.5 min-w-0">
                                    <span class="material-symbols-outlined flex-shrink-0"
                                        style="font-size:16px; color:{{ $color }}">{{ $icon }}</span>
                                    <span class="text-sm font-alte-regular text-gray-600 truncate">{{ $label }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>{{-- /left col --}}

                {{-- Recent Forms — right col (3/5) --}}
                <div class="lg:col-span-3 flex flex-col gap-4">

                    {{-- Section label — matches height of "Quick Actions" on the left --}}
                    <p class="text-sm font-alte tracking-widest text-gray-400 uppercase px-1">Recent Patient Forms</p>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">

                        {{-- Card header --}}
                        <div
                            class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50/60 flex-shrink-0">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="material-symbols-outlined text-green-700 flex-shrink-0"
                                    style="font-size:19px">fact_check</span>
                                <h2 class="font-alte text-gray-800 text-base uppercase tracking-wide truncate">
                                    Recent Patient Forms
                                </h2>
                                {{-- Unread count badge --}}
                                <span id="unread-header-badge"
                                    class="{{ $unreadCount > 0 ? '' : 'hidden' }} flex-shrink-0 inline-flex items-center justify-center min-w-[20px] h-5 rounded-full bg-red-500 text-white text-[10px] font-alte leading-none px-1">
                                    {{ $unreadCount }}
                                </span>
                            </div>
                            <a href="{{ route('doctor.recent-forms') }}"
                                class="flex-shrink-0 ml-4 text-sm font-alte text-green-700 hover:text-green-900
                                                                  flex items-center gap-0.5 hover:underline transition-colors whitespace-nowrap">
                                View All
                                <span class="material-symbols-outlined" style="font-size:14px">chevron_right</span>
                            </a>
                        </div>

                        {{-- Pill filters --}}
                        <div class="flex items-center gap-2 px-4 py-2.5 border-b border-gray-100 bg-white flex-shrink-0 overflow-x-auto">
                            <button class="filter-pill active font-alte" data-filter="all">
                                All <span class="pill-count">0</span>
                            </button>
                            <button class="filter-pill font-alte" data-filter="today">
                                Updates Today <span class="pill-count">0</span>
                            </button>
                            <button class="filter-pill font-alte" data-filter="unread">
                                Unread <span class="pill-count">0</span>
                            </button>
                            <button class="filter-pill font-alte" data-filter="read">
                                Read <span class="pill-count">0</span>
                            </button>
                        </div>

                        {{-- Feed --}}
                        @if ($recentForms->isEmpty())
                            <div class="py-16 text-center flex flex-col items-center gap-2 text-gray-400">
                                <span class="material-symbols-outlined" style="font-size:48px">inbox</span>
                                <p class="text-base font-alte text-gray-500">No submissions yet.</p>
                                <p class="text-sm font-alte-regular">Patient assessment records will appear here.</p>
                            </div>
                        @else
                            <ul class="forms-feed divide-y divide-gray-50" id="forms-feed-list">
                                @foreach ($recentForms as $form)
                                    <li class="form-item"
                                        data-read="{{ $form['is_read'] ? '1' : '0' }}"
                                        data-today="{{ $form['is_today'] ? '1' : '0' }}"
                                        data-model-type="{{ $form['model_class'] }}"
                                        data-model-id="{{ $form['record_id'] }}">
                                        <a href="{{ $form['patient_id'] ? route('doctor.form-detail', ['type' => $form['slug'], 'patient_id' => $form['patient_id']]) . '?from=recent-forms' : '#' }}"
                                            class="group flex items-center gap-3 px-4 sm:px-5 py-3.5 hover:bg-blue-50/40 transition-colors block">

                                            {{-- Unread dot --}}
                                            <div class="flex-shrink-0 w-2 h-2 rounded-full unread-dot transition-opacity {{ $form['is_read'] ? 'opacity-0' : 'bg-blue-500' }}"></div>

                                            {{-- Icon bubble --}}
                                            <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center"
                                                style="background-color: {{ $form['color'] }}1a">
                                                <span class="material-symbols-outlined"
                                                    style="font-size:18px; color:{{ $form['color'] }}">{{ $form['icon'] }}</span>
                                            </div>

                                            {{-- Patient + type --}}
                                            <div class="flex-1 min-w-0">
                                                <p class="text-base font-alte truncate leading-tight transition-colors {{ $form['is_read'] ? 'text-gray-600' : 'text-gray-900' }} group-hover:text-blue-700">
                                                    {{ $form['patient_name'] }}
                                                </p>
                                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                    <span
                                                        class="rounded-full px-2.5 py-0.5 text-white font-alte text-xs leading-none"
                                                        style="background-color: {{ $form['color'] }}">
                                                        {{ $form['type'] }}
                                                    </span>
                                                    <span class="text-xs font-alte-regular text-gray-400">updated</span>
                                                    @if ($form['is_today'])
                                                        <span class="rounded-full px-2 py-0.5 bg-amber-100 text-amber-700 font-alte text-[10px] leading-none">Today</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Time + arrow --}}
                                            <div class="flex-shrink-0 flex flex-col items-end gap-1">
                                                <span class="text-xs font-alte-regular text-gray-400 whitespace-nowrap">
                                                    {{ \Carbon\Carbon::parse($form['time'])->diffForHumans() }}
                                                </span>
                                                <span
                                                    class="material-symbols-outlined text-gray-300 group-hover:text-blue-500 transition-colors"
                                                    style="font-size:15px">arrow_forward_ios</span>
                                            </div>

                                        </a>
                                    </li>
                                @endforeach

                                {{-- Empty state when filter has no matches --}}
                                <li id="empty-filtered" class="hidden py-10 text-center">
                                    <span class="material-symbols-outlined text-gray-300" style="font-size:36px">filter_list_off</span>
                                    <p class="text-sm font-alte text-gray-400 mt-1">No forms match this filter.</p>
                                </li>
                            </ul>

                            {{-- Footer --}}
                            <div class="flex-shrink-0 border-t border-gray-100 bg-gray-50 px-5 py-3">
                                <a href="{{ route('doctor.recent-forms') }}"
                                    class="flex items-center justify-center gap-1 text-sm font-alte
                                                                                                  text-green-700 hover:text-green-900 hover:underline transition-colors">
                                    See all patient form submissions
                                    <span class="material-symbols-outlined" style="font-size:14px">arrow_forward</span>
                                </a>
                            </div>
                        @endif

                    </div>
                </div>{{-- /right col --}}

            </div>{{-- /main grid --}}

        </div>{{-- /page body --}}

    </div>{{-- /full-width wrapper --}}

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pills   = document.querySelectorAll('.filter-pill');
    const items   = document.querySelectorAll('.form-item');
    const badge   = document.getElementById('unread-header-badge');
    const emptyEl = document.getElementById('empty-filtered');

    // ── Count & update pill badges ────────────────────────────────────────
    function updateCounts() {
        let all = 0, today = 0, unread = 0, read = 0;
        items.forEach(function (item) {
            all++;
            if (item.dataset.today === '1') today++;
            if (item.dataset.read  === '0') unread++;
            if (item.dataset.read  === '1') read++;
        });

        document.querySelector('[data-filter="all"]    .pill-count').textContent = all;
        document.querySelector('[data-filter="today"]  .pill-count').textContent = today;
        document.querySelector('[data-filter="unread"] .pill-count').textContent = unread;
        document.querySelector('[data-filter="read"]   .pill-count').textContent = read;

        if (badge) {
            badge.textContent = unread;
            badge.classList.toggle('hidden', unread === 0);
        }
    }

    // ── Apply filter ──────────────────────────────────────────────────────
    function applyFilter(filter) {
        let visible = 0;
        items.forEach(function (item) {
            let show = false;
            if      (filter === 'all')    show = true;
            else if (filter === 'today')  show = item.dataset.today === '1';
            else if (filter === 'unread') show = item.dataset.read  === '0';
            else if (filter === 'read')   show = item.dataset.read  === '1';
            item.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
    }

    // ── Pill click ────────────────────────────────────────────────────────
    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            pills.forEach(function (p) { p.classList.remove('active'); });
            this.classList.add('active');
            applyFilter(this.dataset.filter);
        });
    });

    // ── Mark as read on click (optimistic) ───────────────────────────────
    items.forEach(function (item) {
        var link = item.querySelector('a');
        if (!link) return;
        link.addEventListener('click', function () {
            if (item.dataset.read === '0') {
                fetch('{{ route("doctor.mark-read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        model_type: item.dataset.modelType,
                        model_id:   parseInt(item.dataset.modelId),
                    }),
                });
                item.dataset.read = '1';
                var dot = item.querySelector('.unread-dot');
                if (dot) dot.classList.add('opacity-0');
                var name = item.querySelector('p.font-alte');
                if (name) { name.classList.remove('text-gray-900'); name.classList.add('text-gray-600'); }
                updateCounts();
                // Re-apply current filter so counts stay correct
                var active = document.querySelector('.filter-pill.active');
                if (active) applyFilter(active.dataset.filter);
            }
        });
    });

    // ── Init ──────────────────────────────────────────────────────────────
    updateCounts();
    applyFilter('all');
});
</script>
@endpush

@endsection