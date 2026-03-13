{{--
    Partial: doctor/partials/recent-forms-results.blade.php
    Rendered for both full page loads and AJAX refreshes.
    JS reads data-total / data-last-page from the meta element.
--}}
<template id="rf-meta" data-total="{{ $total }}" data-last-page="{{ $lastPage }}" data-page="{{ $page }}"></template>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

    @if ($items->isEmpty())
        <div class="py-20 text-center">
            <span class="material-symbols-outlined text-gray-300 block mb-3" style="font-size:56px">inbox</span>
            <p class="text-lg font-alte text-gray-500">No records found</p>
            <p class="text-base font-alte-regular text-gray-400 mt-1">Try adjusting your filters or check back later.</p>
        </div>
    @else

        {{-- ── DESKTOP TABLE (md+) ── --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead>
                    <tr class="bg-green-50/60 border-b border-green-100">
                        <th class="w-12 text-center px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">#</th>
                        <th class="text-left px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Form Type</th>
                        <th class="text-left px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="text-left px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Patient ID</th>
                        <th class="text-left px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Last Updated</th>
                        <th class="text-center px-4 py-5 text-sm font-alte text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $i => $form)
                        @php
                            $rowUrl = $form['patient_id']
                                ? route('doctor.form-detail', ['type' => $form['type_key'], 'patient_id' => $form['patient_id']]) . '?from=recent-forms'
                                : null;
                        @endphp
                        <tr class="border-b border-gray-50 transition-colors {{ $rowUrl ? 'cursor-pointer hover:bg-green-50/50' : 'hover:bg-gray-50' }}"
                            {!! $rowUrl ? "onclick=\"window.location.href='{$rowUrl}'\"" : '' !!}>
                            <td class="px-4 py-3.5 text-center">
                                <span class="text-sm text-gray-400 font-mono">{{ ($page - 1) * $perPage + $i + 1 }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                        style="background-color: {{ $form['color'] }}18">
                                        <span class="material-symbols-outlined"
                                            style="font-size:16px; color:{{ $form['color'] }}">{{ $form['icon'] }}</span>
                                    </div>
                                    <span class="font-alte-regular text-gray-800 text-sm leading-tight">{{ $form['type'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-alte-regular text-gray-800 text-base">{{ $form['patient_name'] }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                                    {{ $form['patient_id'] ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <p class="text-sm font-alte-regular text-gray-700">{{ \Carbon\Carbon::parse($form['time'])->format('M d, Y') }}</p>
                                <p class="text-xs font-alte-regular text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($form['time'])->format('g:i A') }} · {{ \Carbon\Carbon::parse($form['time'])->diffForHumans() }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-center" onclick="event.stopPropagation()">
                                @if ($form['patient_id'])
                                    <form method="POST" action="{{ route('doctor.generate-report') }}" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="patient_id" value="{{ $form['patient_id'] }}">
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 text-sm font-alte text-green-700 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                                            <span class="material-symbols-outlined" style="font-size:14px">picture_as_pdf</span>
                                            View Report
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── MOBILE CARDS (< md) ── --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach ($items as $i => $form)
                @php
                    $cardUrl = $form['patient_id']
                        ? route('doctor.form-detail', ['type' => $form['type_key'], 'patient_id' => $form['patient_id']]) . '?from=recent-forms'
                        : null;
                @endphp
                <div class="px-4 py-4 transition-colors {{ $cardUrl ? 'cursor-pointer hover:bg-green-50/50' : 'hover:bg-gray-50' }}"
                     {!! $cardUrl ? "onclick=\"window.location.href='{$cardUrl}'\"" : '' !!}>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                            style="background-color: {{ $form['color'] }}18">
                            <span class="material-symbols-outlined"
                                style="font-size:20px; color:{{ $form['color'] }}">{{ $form['icon'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-base font-alte-regular text-gray-800 truncate">{{ $form['patient_name'] }}</p>
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                <span class="inline-block rounded-full px-2.5 py-0.5 text-white font-alte-regular text-xs"
                                    style="background-color: {{ $form['color'] }}">{{ $form['type'] }}</span>
                                @if ($form['patient_id'])
                                    <span class="font-mono text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $form['patient_id'] }}</span>
                                @endif
                            </div>
                            <p class="text-xs font-alte-regular text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($form['time'])->format('M d, Y · g:i A') }}
                                · {{ \Carbon\Carbon::parse($form['time'])->diffForHumans() }}
                            </p>
                        </div>
                        @if ($form['patient_id'])
                            <form method="POST" action="{{ route('doctor.generate-report') }}" class="flex-shrink-0" onclick="event.stopPropagation()">
                                @csrf
                                <input type="hidden" name="patient_id" value="{{ $form['patient_id'] }}">
                                <button type="submit"
                                    class="w-9 h-9 rounded-xl bg-green-50 hover:bg-green-100 flex items-center justify-center text-green-700 transition-colors">
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
            <div class="px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-gray-50">
                <p class="text-sm font-alte-regular text-gray-500 order-2 sm:order-1">
                    Showing
                    <span class="font-alte text-gray-700">{{ ($page - 1) * $perPage + 1 }}</span>–<span class="font-alte text-gray-700">{{ min($page * $perPage, $total) }}</span>
                    of <span class="font-alte text-gray-700">{{ number_format($total) }}</span> results
                </p>
                <div class="flex items-center gap-1 order-1 sm:order-2">
                    {{-- Prev --}}
                    @if ($page > 1)
                        <a href="#" data-ajax-page="{{ $page - 1 }}"
                            class="rf-page-link inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 hover:border-gray-400 transition-colors">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                        </span>
                    @endif

                    {{-- Page numbers --}}
                    @for ($p = max(1, $page - 2); $p <= min($lastPage, $page + 2); $p++)
                        <a href="#" data-ajax-page="{{ $p }}"
                            class="rf-page-link inline-flex items-center justify-center w-9 h-9 rounded-xl border text-xs font-alte transition-all"
                            style="{{ $p === $page ? 'background-color:#15803d; color:#fff; border-color:#15803d;' : 'background-color:#fff; color:#374151; border-color:#E5E7EB;' }}">
                            {{ $p }}
                        </a>
                    @endfor

                    {{-- Next --}}
                    @if ($page < $lastPage)
                        <a href="#" data-ajax-page="{{ $page + 1 }}"
                            class="rf-page-link inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-300 bg-white text-gray-600 hover:bg-gray-100 hover:border-gray-400 transition-colors">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                        </a>
                    @else
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-gray-200 bg-gray-50 text-gray-300 cursor-not-allowed">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                        </span>
                    @endif
                </div>
            </div>
        @endif

    @endif

</div>{{-- end results card --}}
