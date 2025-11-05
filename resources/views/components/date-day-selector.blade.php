<div class="date-day-selector-container" data-select-url="{{ route('adl.select') }}" style="display: flex; align-items: center; justify-content: flex-start; gap: 20px;">
    {{-- DATE INPUT --}}
    <label for="date_selector" style=" white-space: nowrap;">DATE :</label>
    <input class="date" type="date" id="date_selector" name="date"
        value="{{ $currentDate ?? now()->format('Y-m-d') }}"
        @if (!$selectedPatient) disabled @endif>

    {{-- DAY NO SELECTOR --}}
    <label for="day_no_selector" style="white-space: nowrap;">DAY NO :</label>
    <select id="day_no_selector" name="day_no" @if (!$selectedPatient) disabled @endif>
        <option value="">-- Day --</option>
        @for ($i = 1; $i <= 30; $i++)
            <option value="{{ $i }}" @if(($currentDayNo ?? 1) == $i) selected @endif>
                {{ $i }}
            </option>
        @endfor
    </select>
</div>
