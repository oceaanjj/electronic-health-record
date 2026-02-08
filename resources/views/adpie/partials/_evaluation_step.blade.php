    @include('adpie.partials._banner', ['type' => 'evaluation', 'step' => '4'])
    <div class="w-full overflow-hidden rounded-[15px] shadow-md">
        <div class="main-header flex items-center justify-between rounded-t-lg px-6 py-3 text-white">
            <span class="font-bold">EVALUATION</span>
            <span class="rounded-full bg-white/20 px-3 py-1 text-xs">STEP 4 of 4</span>
        </div>
        <textarea name="evaluation" id="evaluation" class="notepad-lines font-typewriter adpie-textarea">{{ old('evaluation', $diagnosis->evaluation ?? '') }}</textarea>
    </div>