@include('adpie.partials._banner', ['type' => 'planning', 'step' => '2'])

<div class="w-full overflow-hidden rounded-[15px] shadow-md">
    <div class="main-header flex items-center justify-between rounded-t-lg px-6 py-3 text-white">
        <span class="font-bold uppercase">Planning</span>
        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-sm">STEP 2 of 4</span>
    </div>

    <div class="bg-beige relative">
        <textarea
            id="planning"
            name="planning"
            class="notepad-lines font-typewriter cdss-input planning-textarea w-full rounded-b-lg shadow-sm adpie-textarea"
            placeholder="Enter nursing goals and planning here..."
            data-field-name="planning"
            maxlength="2000"
        >{{ old('planning', $diagnosis->planning ?? '') }}</textarea>

        <div class="char-counter" id="char-counter-planning">
            <span id="char-count-planning">0</span> / 2000
        </div>
    </div>
</div>