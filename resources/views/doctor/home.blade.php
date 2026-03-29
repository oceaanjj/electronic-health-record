@extends('layouts.doctor')
@section('title', 'Doctor Dashboard')

@push('styles')
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
        }

        .action-card {
            transition: all 0.2s ease;
        }

        .action-card:hover {
            transform: scale(1.01);
        }

        .forms-feed {
            overflow-y: auto;
            max-height: 570px;
        }

        .forms-feed::-webkit-scrollbar {
            width: 5px;
        }

        .forms-feed::-webkit-scrollbar-track {
            background: transparent;
        }

        .forms-feed::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .dark .forms-feed::-webkit-scrollbar-thumb {
            background: #334155;
        }

        .filter-pill {
            transition: all 0.2s ease;
        }

        .filter-pill.active {
            box-shadow: 0 4px 12px -2px rgba(16, 185, 129, 0.3);
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
                'sub' => 'Lifetime registrations',
                'icon' => 'groups',
                'color' => 'blue',
                'href' => route('doctor.stats.total-patients'),
            ],
            [
                'label' => 'Active Patients',
                'value' => $activePatients,
                'sub' => 'Currently in facility',
                'icon' => 'person_check',
                'color' => 'emerald',
                'href' => route('doctor.stats.active-patients'),
            ],
            [
                'label' => "Today's Activity",
                'value' => $todayForms,
                'sub' => 'Forms updated today',
                'icon' => 'edit_note',
                'color' => 'amber',
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
            ['Medical History', 'history_edu', '#6366F1'],
            ['Diagnostics', 'biotech', '#0D9488'],
            ['Medication Reconciliation', 'rebase_edit', '#10B981'],
        ];
    @endphp

    <div class="-mx-6 min-h-screen bg-slate-50 dark:bg-slate-950 font-rubik transition-colors duration-300">

        {{-- Top Bar Section --}}
        <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
            <div class="max-w-screen-2xl mx-auto px-6 py-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-creato-black font-bold text-slate-900 dark:text-white flex items-center gap-3">
                        <span class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">dashboard</span>
                        </span>
                        Clinical Overview
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 font-alte-regular">
                        Monitoring system status for <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ now()->format('F j, Y') }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('doctor.patient-report') }}"
                        class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-alte text-sm shadow-lg shadow-emerald-200 dark:shadow-none transition-all">
                        <span class="material-symbols-outlined" style="font-size:20px">picture_as_pdf</span>
                        Generate Report
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-screen-2xl mx-auto px-6 py-8 space-y-8">

            {{-- High-Impact Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($statsData as $stat)
                    @php
                        $colorClass = [
                            'blue' => 'from-blue-500 to-indigo-600 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 shadow-blue-100',
                            'emerald' => 'from-emerald-500 to-teal-600 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 shadow-emerald-100',
                            'amber' => 'from-amber-500 to-orange-600 text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 shadow-amber-100',
                        ][$stat['color']];
                        $parts = explode(' ', $colorClass);
                    @endphp
                    <a href="{{ $stat['href'] }}"
                        class="stat-card group bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden">
                        <div class="flex items-start justify-between relative z-10">
                            <div>
                                <p class="text-slate-500 dark:text-slate-400 text-xs font-alte tracking-widest uppercase">{{ $stat['label'] }}</p>
                                <h3 class="text-4xl font-creato-black mt-2 text-slate-900 dark:text-white">{{ $stat['value'] }}</h3>
                                <p class="text-slate-400 dark:text-slate-500 text-xs mt-1 font-alte-regular">{{ $stat['sub'] }}</p>
                            </div>
                            <div class="p-3 rounded-2xl {{ $parts[2] }} {{ $parts[3] }}">
                                <span class="material-symbols-outlined" style="font-size:28px">{{ $stat['icon'] }}</span>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center gap-1 text-xs font-alte {{ $parts[2] }} group-hover:gap-2 transition-all">
                            View Analytics <span class="material-symbols-outlined" style="font-size:14px">arrow_forward</span>
                        </div>

                        {{-- Decorative background glow --}}
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-gradient-to-br {{ $parts[0] }} {{ $parts[1] }} opacity-[0.03] rounded-full blur-2xl group-hover:opacity-[0.08] transition-opacity"></div>
                    </a>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">

                {{-- Quick Controls --}}
                <div class="lg:col-span-4 flex flex-col space-y-6">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-xs font-alte tracking-[0.2em] text-slate-400 uppercase px-1 flex items-center gap-2 mb-6">
                                <span class="w-2 h-px bg-slate-300 dark:bg-slate-700"></span>
                                Quick Controls
                            </h3>

                            <div class="grid grid-cols-1 gap-4">
                                <a href="{{ route('doctor.patient-report') }}"
                                    class="action-card group flex items-center gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm hover:border-emerald-400 dark:hover:border-emerald-500 transition-all">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined" style="font-size:26px">analytics</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-creato-bold text-slate-800 dark:text-slate-200">Patient Report</p>
                                        <p class="text-xs text-slate-400 font-alte-regular mt-0.5 truncate">Export detailed PDF records</p>
                                    </div>
                                    <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 group-hover:text-emerald-500 transition-colors" style="font-size:20px">chevron_right</span>
                                </a>

                                <a href="{{ route('doctor.recent-forms') }}"
                                    class="action-card group flex items-center gap-4 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm hover:border-indigo-400 dark:hover:border-indigo-500 transition-all">
                                    <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined" style="font-size:26px">fact_check</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-creato-bold text-slate-800 dark:text-slate-200">Browse Records</p>
                                        <p class="text-xs text-slate-400 font-alte-regular mt-0.5 truncate">Search all assessment forms</p>
                                    </div>
                                    <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 group-hover:text-indigo-500 transition-colors" style="font-size:20px">chevron_right</span>
                                </a>
                            </div>
                        </div>

                        {{-- Assessment Legend --}}
                        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 shadow-sm">
                            <p class="text-[10px] font-alte tracking-[0.2em] text-slate-400 uppercase mb-5">Clinical Domain Legend</p>
                            <div class="space-y-4">
                                @foreach ($formTypes as [$label, $icon, $color])
                                    @php
                                        $typeSlug = [
                                            'Vital Signs' => 'vital-signs',
                                            'Physical Exam' => 'physical-exam',
                                            'Activities of Daily Living' => 'adl',
                                            'Intake & Output' => 'intake-output',
                                            'Lab Values' => 'lab-values',
                                            'Medication Administration' => 'medication',
                                            'IVs & Lines' => 'ivs-lines',
                                            'Medical History' => 'medical-history',
                                            'Diagnostics' => 'diagnostics',
                                            'Medication Reconciliation' => 'med-reconciliation'
                                        ][$label] ?? 'all';
                                    @endphp
                                    <button onclick="document.querySelector('[data-filter=\'{{ $typeSlug }}\']').click()" 
                                            class="flex items-center gap-3 w-full text-left group hover:bg-slate-50 dark:hover:bg-slate-800/50 p-2 -m-2 rounded-xl transition-all">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-transform group-hover:scale-110" style="background-color: {{ $color }}15">
                                            <span class="material-symbols-outlined" style="font-size:16px; color:{{ $color }}">{{ $icon }}</span>
                                        </div>
                                        <span class="text-sm font-alte-regular text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $label }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Activity Feed --}}
                <div class="lg:col-span-8 flex flex-col space-y-6">
                    <div class="flex items-center justify-between px-1">
                        <h3 class="text-xs font-alte tracking-[0.2em] text-slate-400 uppercase flex items-center gap-2">
                            <span class="w-2 h-px bg-slate-300 dark:bg-slate-700"></span>
                            Recent Clinical Activity
                        </h3>
                        <span id="unread-header-badge" class="{{ $unreadCount > 0 ? '' : 'hidden' }} px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-creato-black">
                            {{ $unreadCount }} NEW
                        </span>
                    </div>

                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col transition-all duration-300">


                        {{-- Filters --}}
                        <div class="flex items-center gap-2 px-6 py-4 border-b border-slate-50 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 overflow-x-auto no-scrollbar">
                            <button class="filter-pill active whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-creato-bold bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 [&.active]:bg-emerald-600 [&.active]:text-white [&.active]:border-transparent" data-filter="all">
                                All Activities <span class="pill-count ml-1 opacity-60 italic">0</span>
                            </button>
                            <button class="filter-pill whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-creato-bold bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 [&.active]:bg-emerald-600 [&.active]:text-white [&.active]:border-transparent" data-filter="unread">
                                Unread <span class="pill-count ml-1 opacity-60 italic">0</span>
                            </button>
                            <button class="filter-pill whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-creato-bold bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 [&.active]:bg-emerald-600 [&.active]:text-white [&.active]:border-transparent" data-filter="today">
                                Today <span class="pill-count ml-1 opacity-60 italic">0</span>
                            </button>
                        </div>

                        {{-- Feed --}}
                        @if ($recentForms->isEmpty())
                            <div class="py-24 text-center flex flex-col items-center gap-4 text-slate-300 dark:text-slate-700 flex-1">
                                <span class="material-symbols-outlined" style="font-size:64px">order_approve</span>
                                <div>
                                    <p class="text-lg font-creato-bold text-slate-400 dark:text-slate-500">All caught up</p>
                                    <p class="text-sm font-alte-regular">New patient records will appear here.</p>
                                </div>
                            </div>
                        @else
                            <div class="relative flex-1 flex flex-col overflow-hidden group/feed">
                                <ul class="forms-feed divide-y divide-slate-50 dark:divide-slate-800 overflow-y-auto no-scrollbar" id="forms-feed-list">
                                    @foreach ($recentForms as $form)
                                        <li class="form-item group"
                                            data-read="{{ $form['is_read'] ? '1' : '0' }}"
                                            data-today="{{ $form['is_today'] ? '1' : '0' }}"
                                            data-type-key="{{ $form['slug'] }}"
                                            data-model-type="{{ $form['model_class'] }}"
                                            data-model-id="{{ $form['record_id'] }}">
                                            <a href="{{ $form['patient_id'] ? route('doctor.form-detail', ['type' => $form['slug'], 'patient_id' => $form['patient_id']]) . '?from=recent-forms' : '#' }}"
                                                class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all block">

                                                {{-- Status Indicator --}}
                                                <div class="flex-shrink-0 relative">
                                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center border border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm"
                                                        style="border-bottom: 2px solid {{ $form['color'] }}">
                                                        <span class="material-symbols-outlined"
                                                            style="font-size:20px; color:{{ $form['color'] }}">{{ $form['icon'] }}</span>
                                                    </div>
                                                    <div class="unread-indicator absolute -top-1 -right-1 w-3 h-3 rounded-full bg-indigo-500 border-2 border-white dark:border-slate-900 {{ $form['is_read'] ? 'hidden' : '' }}"></div>
                                                </div>

                                                {{-- Content --}}
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between mb-0.5">
                                                        <p class="text-sm font-creato-bold truncate {{ $form['is_read'] ? 'text-slate-500' : 'text-slate-900 dark:text-white' }}">
                                                            {{ $form['patient_name'] }}
                                                        </p>
                                                        <span class="text-[10px] font-alte-regular text-slate-400 whitespace-nowrap ml-4 uppercase tracking-tighter">
                                                            {{ \Carbon\Carbon::parse($form['time'])->diffForHumans() }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] font-alte tracking-wider uppercase px-2 py-0.5 rounded-md text-white shadow-sm" style="background-color: {{ $form['color'] }}">
                                                            {{ $form['type'] }}
                                                        </span>
                                                        <span class="text-[10px] font-alte-regular text-slate-400">Submission</span>
                                                    </div>
                                                </div>

                                                <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all" style="font-size:18px">arrow_forward</span>
                                            </a>
                                        </li>
                                    @endforeach

                                    <li id="empty-filtered" class="hidden py-16 text-center">
                                        <span class="material-symbols-outlined text-slate-300 dark:text-slate-700" style="font-size:48px">filter_list_off</span>
                                        <p class="text-sm font-creato-bold text-slate-400 mt-2">No records match this filter</p>
                                    </li>
                                </ul>
                                {{-- Bottom Fade --}}
                                <div class="absolute bottom-0 left-0 right-0 h-12 bg-gradient-to-t from-white dark:from-slate-900 to-transparent pointer-events-none z-10"></div>
                            </div>

                            <div class="mt-auto bg-slate-50/50 dark:bg-slate-900/50 px-6 py-4 flex justify-center border-t border-slate-50 dark:border-slate-800">
                                <a href="{{ route('doctor.recent-forms') }}"
                                    class="text-xs font-creato-black text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 flex items-center gap-1 transition-colors uppercase font-bold pt-2 tracking-widest">
                                    Explore all historical records
                                    <span class="material-symbols-outlined" style="font-size:16px">double_arrow</span>
                                </a>
                            </div>
                        @endif

                    </div>
                </div>

            </div>

        </div>

    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pills   = document.querySelectorAll('.filter-pill');
    const items   = document.querySelectorAll('.form-item');
    const badge   = document.getElementById('unread-header-badge');
    const emptyEl = document.getElementById('empty-filtered');

    function updateCounts() {
        let all = 0, today = 0, unread = 0;
        let typeCounts = {};
        
        items.forEach(function (item) {
            all++;
            if (item.dataset.today === '1') today++;
            if (item.dataset.read  === '0') unread++;
            
            let type = item.dataset.typeKey;
            typeCounts[type] = (typeCounts[type] || 0) + 1;
        });

        document.querySelector('[data-filter="all"]    .pill-count').textContent = all;
        document.querySelector('[data-filter="today"]  .pill-count').textContent = today;
        document.querySelector('[data-filter="unread"] .pill-count').textContent = unread;

        document.querySelectorAll('.filter-pill[data-filter]').forEach(function(pill) {
            let filter = pill.dataset.filter;
            if (filter !== 'all' && filter !== 'today' && filter !== 'unread') {
                pill.querySelector('.pill-count').textContent = typeCounts[filter] || 0;
            }
        });

        if (badge) {
            badge.textContent = unread + ' NEW';
            badge.classList.toggle('hidden', unread === 0);
        }
    }

    function applyFilter(filter) {
        let visible = 0;
        items.forEach(function (item) {
            let show = false;
            if      (filter === 'all')    show = true;
            else if (filter === 'today')  show = item.dataset.today === '1';
            else if (filter === 'unread') show = item.dataset.read  === '0';
            else                          show = item.dataset.typeKey === filter;
            
            item.classList.toggle('hidden', !show);
            if (show) visible++;
        });
        if (emptyEl) emptyEl.classList.toggle('hidden', visible > 0);
    }

    pills.forEach(function (pill) {
        pill.addEventListener('click', function () {
            pills.forEach(function (p) { p.classList.remove('active'); });
            this.classList.add('active');
            applyFilter(this.dataset.filter);
        });
    });

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
                var indicator = item.querySelector('.unread-indicator');
                if (indicator) indicator.classList.add('hidden');
                var name = item.querySelector('p.font-creato-bold');
                if (name) { name.classList.remove('text-slate-900', 'dark:text-white'); name.classList.add('text-slate-500'); }
                updateCounts();
                var active = document.querySelector('.filter-pill.active');
                if (active) applyFilter(active.dataset.filter);
            }
        });
    });

    updateCounts();
    applyFilter('all');
});
</script>
@endpush

@endsection