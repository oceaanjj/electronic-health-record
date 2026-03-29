{{--
    Nursing Diagnosis ADPIE block — shown below each assessment record.
    Usage: @include('doctor.partials.nd-block', ['nd' => $r->nursingDiagnoses])
    $nd can be null — rows will show '—' for missing data.
--}}
<div class="overflow-hidden rounded-xl border border-indigo-200 dark:border-indigo-900/50 mt-1 transition-colors duration-300">
    <table class="w-full border-collapse text-sm">
        <thead>
            <tr class="bg-indigo-100/90 dark:bg-indigo-900/40">
                <th class="px-3 py-2 text-left text-[10px] font-alte text-indigo-700 dark:text-indigo-400 w-28 border-b border-indigo-200 dark:border-indigo-900/50 uppercase tracking-wider">ADPIE</th>
                <th class="px-3 py-2 text-left text-[10px] font-alte text-indigo-700 dark:text-indigo-400 border-b border-indigo-200 dark:border-indigo-900/50 uppercase tracking-wider">Nurse Assessment</th>
                <th class="px-3 py-2 text-left text-[10px] font-alte text-indigo-700 dark:text-indigo-400 w-56 border-b border-indigo-200 dark:border-indigo-900/50 uppercase tracking-wider">CDSS Recommendation</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-slate-900/50">
            @php
                $adpieRows = [
                    ['Diagnosis',    'diagnosis',    'diagnosis_alert'],
                    ['Planning',     'planning',     'planning_alert'],
                    ['Intervention', 'intervention', 'intervention_alert'],
                    ['Evaluation',   'evaluation',   'evaluation_alert'],
                ];
            @endphp
            @foreach($adpieRows as [$title, $field, $alertField])
            <tr class="border-t border-indigo-100 dark:border-indigo-900/30">
                <td class="px-3 py-2.5 font-alte text-xs text-indigo-500 dark:text-indigo-500 align-top whitespace-nowrap">{{ $title }}</td>
                <td class="px-3 py-2.5 font-alte-regular text-sm text-slate-700 dark:text-slate-300 leading-snug align-top">
                    {{ $nd?->{$field} ?? '—' }}
                </td>
                <td class="px-3 py-2.5 align-top">
                    @if(!empty($nd?->{$alertField}))
                        <button type="button" class="alert-chip warn"
                                onclick="openAlertModal(this)"
                                data-title="{{ $title }} Alert"
                                data-items="{{ json_encode([$nd->{$alertField}]) }}">
                            <span class="material-symbols-outlined chip-icon">warning</span>
                            <span class="chip-text">{{ \Illuminate\Support\Str::limit($nd->{$alertField}, 18) }}</span>
                        </button>
                    @else
                        <span class="text-xs text-slate-400 dark:text-slate-600">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
