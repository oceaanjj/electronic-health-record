{{--
    Nursing Diagnosis ADPIE block — shown below each assessment record.
    Usage: @include('doctor.partials.nd-block', ['nd' => $r->nursingDiagnoses])
    $nd can be null — rows will show '—' for missing data.
--}}
<div class="overflow-hidden rounded-lg border border-indigo-200 mt-1">
    <table class="w-full border-collapse text-sm">
        <thead>
            <tr class="bg-indigo-100/90">
                <th class="px-3 py-2 text-left text-xs font-alte text-indigo-700 w-28 border-b border-indigo-200">ADPIE</th>
                <th class="px-3 py-2 text-left text-xs font-alte text-indigo-700 border-b border-indigo-200">Nurse Assessment</th>
                <th class="px-3 py-2 text-left text-xs font-alte text-indigo-700 w-56 border-b border-indigo-200">CDSS Recommendation</th>
            </tr>
        </thead>
        <tbody class="bg-white">
            @php
                $adpieRows = [
                    ['Diagnosis',    'diagnosis',    'diagnosis_alert'],
                    ['Planning',     'planning',     'planning_alert'],
                    ['Intervention', 'intervention', 'intervention_alert'],
                    ['Evaluation',   'evaluation',   'evaluation_alert'],
                ];
            @endphp
            @foreach($adpieRows as [$title, $field, $alertField])
            <tr class="border-t border-indigo-100">
                <td class="px-3 py-2.5 font-alte text-xs text-indigo-500 align-top whitespace-nowrap">{{ $title }}</td>
                <td class="px-3 py-2.5 font-alte-regular text-sm text-gray-700 leading-snug align-top">
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
                        <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
