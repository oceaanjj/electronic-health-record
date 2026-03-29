{{--
    Partial: doctor/partials/recent-forms-results.blade.php
    Rendered for both full page loads and AJAX refreshes.
    JS reads data-total / data-last-page from the meta element.
--}}
<template id="rf-meta" data-total="{{ $total }}" data-last-page="{{ $lastPage }}" data-page="{{ $page }}"></template>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">

    @if ($items->isEmpty())
        <div class="py-20 text-center">
            <span class="material-symbols-outlined text-slate-300 dark:text-slate-700 block mb-3" style="font-size:56px">inbox</span>
            <p class="text-lg font-alte text-slate-500 dark:text-slate-400">No records found</p>
            <p class="text-base font-alte-regular text-slate-400 dark:text-slate-600 mt-1">Try adjusting your filters or check back later.</p>
        </div>
    @else

        {{-- ── DESKTOP TABLE (md+) ── --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="bg-emerald-50/60 dark:bg-emerald-900/20 border-b border-emerald-100 dark:border-emerald-900/30">
                        <th class="w-12 text-center px-4 py-5 text-xs font-alte text-slate-500 dark:text-slate-400 uppercase tracking-wider">#</th>
                        <th class="text-left px-4 py-5 text-xs font-alte text-slate-500 dark:text-slate-400 uppercase tracking-wider">Form Type</th>
                        <th class="text-left px-4 py-5 text-xs font-alte text-slate-500 dark:text-slate-400 uppercase tracking-wider">Patient</th>
                        <th class="text-left px-4 py-5 text-xs font-alte text-slate-500 dark:text-slate-400 uppercase tracking-wider">Patient ID</th>
                        <th class="text-left px-4 py-5 text-xs font-alte text-slate-500 dark:text-slate-400 uppercase tracking-wider">Last Updated</th>
                        <th class="text-center px-4 py-5 text-xs font-alte text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($items as $i => $form)
                        @php
                            $rowUrl = $form['patient_id']
                                ? route('doctor.form-detail', ['type' => $form['type_key'], 'patient_id' => $form['patient_id']]) . '?from=recent-forms'
                                : null;
                        @endphp
                        <tr class="transition-colors {{ $rowUrl ? 'cursor-pointer hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10' : '' }}"
                            {!! $rowUrl ? "onclick=\"window.location.href='{$rowUrl}'\"" : '' !!}>
                            <td class="px-4 py-3.5 text-center">
                                <span class="text-sm text-slate-400 dark:text-slate-600 font-mono">{{ ($page - 1) * $perPage + $i + 1 }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                        style="background-color: {{ $form['color'] }}18">
                                        <span class="material-symbols-outlined"
                                            style="font-size:16px; color:{{ $form['color'] }}">{{ $form['icon'] }}</span>
                                    </div>
                                    <span class="font-alte-regular text-slate-800 dark:text-slate-200 text-sm leading-tight">{{ $form['type'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-alte-regular text-slate-800 dark:text-slate-200 text-base">{{ $form['patient_name'] }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-2 py-1 rounded-lg">
                                    {{ $form['patient_id'] ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="text-sm font-alte-regular text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($form['time'])->format('M d, Y') }}</p>
                                <p class="text-xs font-alte-regular text-slate-400 dark:text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($form['time'])->format('g:i A') }} · {{ \Carbon\Carbon::parse($form['time'])->diffForHumans() }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-center" onclick="event.stopPropagation()">
                                @if ($form['patient_id'])
                                    <form method="POST" action="{{ route('doctor.generate-report') }}" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="patient_id" value="{{ $form['patient_id'] }}">
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 text-xs font-alte text-emerald-700 dark:text-emerald-400 hover:text-emerald-900 dark:hover:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                            <span class="material-symbols-outlined" style="font-size:14px">picture_as_pdf</span>
                                            View Report
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-700">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── MOBILE CARDS (< md) ── --}}
        <div class="md:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @foreach ($items as $i => $form)
                @php
                    $cardUrl = $form['patient_id']
                        ? route('doctor.form-detail', ['type' => $form['type_key'], 'patient_id' => $form['patient_id']]) . '?from=recent-forms'
                        : null;
                @endphp
                <div class="px-4 py-4 transition-colors {{ $cardUrl ? 'cursor-pointer hover:bg-emerald-50/30 dark:hover:bg-emerald-900/10' : '' }}"
                     {!! $cardUrl ? "onclick=\"window.location.href='{$cardUrl}'\"" : '' !!}>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                            style="background-color: {{ $form['color'] }}18">
                            <span class="material-symbols-outlined"
                                style="font-size:20px; color:{{ $form['color'] }}">{{ $form['icon'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-base font-alte-regular text-slate-800 dark:text-slate-200 truncate">{{ $form['patient_name'] }}</p>
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                <span class="inline-block rounded-full px-2.5 py-0.5 text-white font-alte-regular text-[10px] uppercase tracking-wider"
                                    style="background-color: {{ $form['color'] }}">{{ $form['type'] }}</span>
                                @if ($form['patient_id'])
                                    <span class="font-mono text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded">{{ $form['patient_id'] }}</span>
                                @endif
                            </div>
                            <p class="text-xs font-alte-regular text-slate-400 dark:text-slate-500 mt-1">
                                {{ \Carbon\Carbon::parse($form['time'])->format('M d, Y · g:i A') }}
                                · {{ \Carbon\Carbon::parse($form['time'])->diffForHumans() }}
                            </p>
                        </div>
                        @if ($form['patient_id'])
                            <form method="POST" action="{{ route('doctor.generate-report') }}" class="flex-shrink-0" onclick="event.stopPropagation()">
                                @csrf
                                <input type="hidden" name="patient_id" value="{{ $form['patient_id'] }}">
                                <button type="submit"
                                    class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 flex items-center justify-center text-emerald-700 dark:text-emerald-400 transition-colors">
                                    <span class="material-symbols-outlined" style="font-size:18px">picture_as_pdf</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── PAGINATION ── --}}
        @if ($lastPage > 1)
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50 dark:bg-slate-900/50">
                <p class="text-sm font-alte-regular text-slate-500 dark:text-slate-400 order-2 sm:order-1">
                    Showing
                    <span class="font-alte text-slate-700 dark:text-slate-300">{{ ($page - 1) * $perPage + 1 }}</span>–<span class="font-alte text-slate-700 dark:text-slate-300">{{ min($page * $perPage, $total) }}</span>
                    of <span class="font-alte text-slate-700 dark:text-slate-300">{{ number_format($total) }}</span> results
                </p>
                <div class="flex items-center gap-1 order-1 sm:order-2">
                    {{-- Prev --}}
                    @if ($page > 1)
                        <a href="#" data-ajax-page="{{ $page - 1 }}"
                            class="rf-page-link inline-flex items-center justify-center w-9 h-9 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors shadow-sm">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-700 cursor-not-allowed">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                        </span>
                    @endif

                    {{-- Page numbers --}}
                    @for ($p = max(1, $page - 2); $p <= min($lastPage, $page + 2); $p++)
                        <a href="#" data-ajax-page="{{ $p }}"
                            class="rf-page-link inline-flex items-center justify-center w-9 h-9 rounded-xl border text-xs font-alte transition-all shadow-sm"
                            style="{{ $p === $page ? 'background-color:#10b981; color:#fff; border-color:#10b981;' : '' }}"
                            class="{{ $p !== $page ? 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700' : '' }}">
                            {{ $p }}
                        </a>
                    @endfor

                    {{-- Next --}}
                    @if ($page < $lastPage)
                        <a href="#" data-ajax-page="{{ $page + 1 }}"
                            class="rf-page-link inline-flex items-center justify-center w-9 h-9 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors shadow-sm">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-300 dark:text-slate-700 cursor-not-allowed">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                        </span>
                    @endif
                </div>
            </div>
        @endif

    @endif

</div>{{-- end results card --}}
