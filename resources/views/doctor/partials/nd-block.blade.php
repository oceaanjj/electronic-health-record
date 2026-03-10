{{--
    Nursing Diagnosis card block (hasOne — one per assessment record).
    Usage: @include('doctor.partials.nd-block', ['nd' => $r->nursingDiagnoses, 'index' => 0])
--}}
<div class="bg-white rounded-xl border border-indigo-100 px-4 py-3.5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2.5">

        @if(!empty($nd->diagnosis))
        <div class="flex gap-2 items-start">
            <span class="font-alte text-gray-500 text-xs w-24 flex-shrink-0 pt-0.5">1. Diagnosis</span>
            <div class="flex flex-col gap-1 min-w-0">
                <span class="font-alte-regular text-gray-700 text-sm leading-snug">{{ $nd->diagnosis }}</span>
                @if(!empty($nd->diagnosis_alert))
                    <button type="button" class="alert-chip warn self-start"
                            onclick="openAlertModal(this)"
                            data-title="Diagnosis Alert"
                            data-items="{{ json_encode([$nd->diagnosis_alert]) }}">
                        <span class="material-symbols-outlined chip-icon">warning</span>
                        <span class="chip-text">{{ \Illuminate\Support\Str::limit($nd->diagnosis_alert, 14) }}</span>
                    </button>
                @endif
            </div>
        </div>
        @endif

        @if(!empty($nd->planning))
        <div class="flex gap-2 items-start">
            <span class="font-alte text-gray-500 text-xs w-24 flex-shrink-0 pt-0.5">2. Planning</span>
            <div class="flex flex-col gap-1 min-w-0">
                <span class="font-alte-regular text-gray-700 text-sm leading-snug">{{ $nd->planning }}</span>
                @if(!empty($nd->planning_alert))
                    <button type="button" class="alert-chip warn self-start"
                            onclick="openAlertModal(this)"
                            data-title="Planning Alert"
                            data-items="{{ json_encode([$nd->planning_alert]) }}">
                        <span class="material-symbols-outlined chip-icon">warning</span>
                        <span class="chip-text">{{ \Illuminate\Support\Str::limit($nd->planning_alert, 14) }}</span>
                    </button>
                @endif
            </div>
        </div>
        @endif

        @if(!empty($nd->intervention))
        <div class="flex gap-2 items-start">
            <span class="font-alte text-gray-500 text-xs w-24 flex-shrink-0 pt-0.5">3. Intervention</span>
            <div class="flex flex-col gap-1 min-w-0">
                <span class="font-alte-regular text-gray-700 text-sm leading-snug">{{ $nd->intervention }}</span>
                @if(!empty($nd->intervention_alert))
                    <button type="button" class="alert-chip warn self-start"
                            onclick="openAlertModal(this)"
                            data-title="Intervention Alert"
                            data-items="{{ json_encode([$nd->intervention_alert]) }}">
                        <span class="material-symbols-outlined chip-icon">warning</span>
                        <span class="chip-text">{{ \Illuminate\Support\Str::limit($nd->intervention_alert, 14) }}</span>
                    </button>
                @endif
            </div>
        </div>
        @endif

        @if(!empty($nd->evaluation))
        <div class="flex gap-2 items-start">
            <span class="font-alte text-gray-500 text-xs w-24 flex-shrink-0 pt-0.5">4. Evaluation</span>
            <div class="flex flex-col gap-1 min-w-0">
                <span class="font-alte-regular text-gray-700 text-sm leading-snug">{{ $nd->evaluation }}</span>
                @if(!empty($nd->evaluation_alert))
                    <button type="button" class="alert-chip warn self-start"
                            onclick="openAlertModal(this)"
                            data-title="Evaluation Alert"
                            data-items="{{ json_encode([$nd->evaluation_alert]) }}">
                        <span class="material-symbols-outlined chip-icon">warning</span>
                        <span class="chip-text">{{ \Illuminate\Support\Str::limit($nd->evaluation_alert, 14) }}</span>
                    </button>
                @endif
            </div>
        </div>
        @endif

        @if(empty($nd->diagnosis) && empty($nd->planning) && empty($nd->intervention) && empty($nd->evaluation))
            <p class="text-xs font-alte-regular text-gray-400 col-span-2">No details recorded.</p>
        @endif

    </div>
</div>
