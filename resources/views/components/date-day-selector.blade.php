@props([
    'currentDate' => null,
    'currentDayNo' => 1,
    'totalDays' => 30,
    'disabled' => false,
    'formId' => 'patient-select-form',
])

<div {{ $attributes->merge(['class' => 'flex flex-col md:flex-row items-start md:items-center gap-3 md:gap-10']) }}>

    {{-- DATE PICKER --}}
    <div class="flex items-center gap-3">
        <label for="date_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">DATE :</label>
        
        <div class="relative cursor-not-allowed">
            <input
                type="date"
                id="date_selector"
                name="date"
                form="{{ $formId }}"
                value="{{ $currentDate ?? now()->format('Y-m-d') }}"
                disabled
                class="pointer-events-none font-creato-bold w-[150px] rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-[15px] shadow-sm opacity-80 outline-none"
            />
        </div>
    </div>

    {{-- DAY NO SELECTOR --}}
    <div class="flex items-center gap-3">
        <label for="day_no_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">DAY NO :</label>

        <div class="relative cursor-not-allowed">
            <select
                id="day_no_selector"
                name="day_no"
                form="{{ $formId }}"
                disabled
                class="pointer-events-none font-creato-bold w-[150px] appearance-none rounded-full border border-gray-300 bg-gray-100 px-4 py-2 pr-8 text-[15px] shadow-sm opacity-80 outline-none"
            >
                @for ($i = 1; $i <= $totalDays; $i++)
                    <option value="{{ $i }}" @selected($currentDayNo == $i)>{{ $i }}</option>
                @endfor
            </select>

            {{-- Custom Arrow --}}
            <div class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>
    </div>

</div>