@include('adpie.partials._banner', ['type' => 'evaluation', 'step' => '4'])

<div class="w-full overflow-hidden rounded-[15px] shadow-md">
    <div class="main-header flex items-center justify-between rounded-t-lg px-6 py-3 text-white">
        <span class="font-bold uppercase">Evaluation</span>
        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-sm">STEP 4 of 4</span>
    </div>

    <div class="bg-beige relative">
        <textarea
            id="evaluation"
            name="evaluation"
            class="notepad-lines font-typewriter cdss-input evaluation-textarea w-full rounded-b-lg shadow-sm adpie-textarea"
            placeholder="Enter nursing evaluation here..."
            data-field-name="evaluation"
            maxlength="2000"
        >{{ old('evaluation', $diagnosis->evaluation ?? '') }}</textarea>

        <div class="char-counter" id="char-counter-evaluation">
            <span id="char-count-evaluation">0</span> / 2000
        </div>
    </div>
</div>