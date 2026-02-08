@include('adpie.partials._banner', ['type' => 'diagnosis', 'step' => '1'])

<div class="w-full overflow-hidden rounded-[15px] shadow-md">
    <div class="main-header flex items-center justify-between rounded-t-lg px-6 py-3 text-white">
        <span class="font-bold uppercase">Diagnosis</span>
        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-sm">STEP 1 of 4</span>
    </div>

    <div class="bg-beige relative">
        <textarea
            id="diagnosis"
            name="diagnosis"
            class="notepad-lines font-typewriter cdss-input diagnosis-textarea w-full rounded-b-lg shadow-sm adpie-textarea"
            placeholder="Enter nursing diagnosis here..."
            maxlength="2000"
        >{{ old('diagnosis', $diagnosis->diagnosis ?? '') }}</textarea>

        <div class="char-counter" id="char-counter-diagnosis">
            <span id="char-count-diagnosis">0</span> / 2000
        </div>
    </div>
</div>