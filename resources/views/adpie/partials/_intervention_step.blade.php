@include('adpie.partials._banner', ['type' => 'intervention', 'step' => '3'])

<div class="w-full overflow-hidden rounded-[15px] shadow-md">
    <div class="main-header flex items-center justify-between rounded-t-lg px-6 py-3 text-white">
        <span class="font-bold uppercase">Intervention</span>
        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-sm">STEP 3 of 4</span>
    </div>

    <div class="bg-beige relative">
        <textarea
            id="intervention"
            name="intervention"
            class="notepad-lines font-typewriter cdss-input intervention-textarea w-full rounded-b-lg shadow-sm adpie-textarea"
            placeholder="Enter nursing interventions here..."
            maxlength="2000"
        >{{ old('intervention', $diagnosis->intervention ?? '') }}</textarea>

        <div class="char-counter" id="char-counter-intervention">
            <span id="char-count-intervention">0</span> / 2000
        </div>
    </div>
</div>