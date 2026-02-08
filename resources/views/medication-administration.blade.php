@extends('layouts.app')
@section('title', 'Patient Vital Signs')
@section('content')
    <div id="form-content-container">
        <form
            id="medication-administration-form"
            method="POST"
            action="{{ route('medication-administration.store') }}"
            class="cdss-form relative"
        >
            @csrf

            {{-- MEDICATION ADMINISTRATION PATIENT SELECTION --}}
            <div class="mx-auto w-full px-4 pt-10">
                {{-- 
                    RESPONSIVE CONTAINER:
                    Mobile: Flex Column, Centered, No Left Margin.
                    Desktop (md): Flex Row, Left Aligned, Margin-Left 20 (Original Web View).
                --}}
                <div class="flex flex-col md:flex-row items-center gap-y-4 md:gap-x-10 md:ml-20">
                    
                    {{-- 1. PATIENT SECTION --}}
                    <div class="flex items-center gap-4 w-full md:w-auto justify-center md:justify-start">
                        <label
                            for="patient_search_input"
                            class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap"
                        >
                            PATIENT NAME :
                        </label>

                        {{-- 
                            RESPONSIVE WIDTH:
                            Mobile: w-full
                            Desktop: w-[350px] (Original Fixed Width)
                        --}}
                        <div
                            class="searchable-dropdown relative w-full md:w-[350px]"
                            data-select-url="{{ route('medication-administration.select-patient') }}"
                        >
                            <input
                                type="text"
                                id="patient_search_input"
                                placeholder="Select or type Patient Name..."
                                value="{{ trim($selectedPatient->name ?? '') }}"
                                autocomplete="off"
                                class="font-creato-bold w-full rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 focus:outline-none"
                            />

                            <div
                                id="patient_options_container"
                                class="absolute z-50 mt-2 hidden max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg"
                            >
                                @foreach ($patients as $patient)
                                    <div
                                        class="option cursor-pointer px-4 py-2 transition duration-150 hover:bg-blue-100"
                                        data-value="{{ $patient->patient_id }}"
                                    >
                                        {{ trim($patient->name) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <input
                            type="hidden"
                            name="patient_id"
                            id="patient_id_for_form"
                            value="{{ $selectedPatient->patient_id ?? '' }}"
                        />
                        <input type="hidden" id="patient_id_hidden" value="{{ $selectedPatient->patient_id ?? '' }}" />
                    </div>

                    {{-- 2. DATE SELECTOR --}}
                    <div class="flex items-center gap-4">
                        <label for="date_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">
                            DATE :
                        </label>
                        <input
                            type="date"
                            id="date_selector"
                            name="date"
                            value="{{ $selectedDate ?? now()->format('Y-m-d') }}"
                            class="font-creato-bold rounded-full border border-gray-300 bg-white px-4 py-2 text-[15px] shadow-sm transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 focus:outline-none disabled:cursor-not-allowed disabled:bg-gray-100 disabled:opacity-70"
                            @if(!$selectedPatient) disabled @endif
                        />
                    </div>
                </div>
            </div>

            <fieldset @if(!$selectedPatient) disabled @endif>
                {{-- MAIN CONTAINER --}}
                <div class="mx-auto mt-10 flex w-full md:w-[100%] items-start justify-center gap-1 px-4 md:px-0">
                    <center class="w-full">
                        <div class="w-full md:w-[85%] overflow-hidden rounded-[15px]">
                            
                            {{-- 
                                RESPONSIVE TABLE STRUCTURE:
                                Mobile: 'block' display to allow stacking.
                                Desktop: 'table' display to preserve original layout.
                            --}}
                            <table class="w-full md:table-fixed border-collapse border-spacing-y-0">
                                
                                {{-- 
                                    DESKTOP HEADERS 
                                    Hidden on Mobile (hidden). Visible on Desktop (md:table-header-group).
                                --}}
                                <thead class="hidden md:table-header-group">
                                    <tr>
                                        <th class="main-header w-[20%] rounded-tl-[15px]">MEDICATION</th>
                                        <th class="main-header w-[15%]">DOSE</th>
                                        <th class="main-header w-[15%]">ROUTE</th>
                                        <th class="main-header w-[15%]">FREQUENCY</th>
                                        <th class="main-header w-[20%]">COMMENTS</th>
                                        <th class="main-header w-[15%] rounded-tr-[15px]">TIME</th>
                                    </tr>
                                </thead>

                                <tbody class="block md:table-row-group">
                                    {{-- 
                                        ROW 1 (10:00 AM)
                                        Mobile: Block (Card style with border/margin).
                                        Desktop: Table Row (Original height/border).
                                    --}}
                                    <tr class="block md:table-row border border-line-brown/70 md:border-b-2 md:border-t-0 md:border-x-0 md:h-[100px] mb-6 md:mb-0 rounded-lg md:rounded-none overflow-hidden shadow-sm md:shadow-none bg-beige">
                                        
                                        {{-- MEDICATION --}}
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            {{-- Mobile Label --}}
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">MEDICATION</div>
                                            <input type="text" name="medication[]" placeholder="Medication"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                            <input type="hidden" name="time[]" value="10:00:00" />
                                        </td>

                                        {{-- DOSE --}}
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">DOSE</div>
                                            <input type="text" name="dose[]" placeholder="Dose"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>

                                        {{-- ROUTE --}}
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">ROUTE</div>
                                            <input type="text" name="route[]" placeholder="Route"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>

                                        {{-- FREQUENCY --}}
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">FREQUENCY</div>
                                            <input type="text" name="frequency[]" placeholder="Frequency"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>

                                        {{-- COMMENTS --}}
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">COMMENTS</div>
                                            <input type="text" name="comments[]" placeholder="Comments"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>

                                        {{-- TIME --}}
                                        <th class="block md:table-cell bg-yellow-light text-brown font-semibold py-2 md:py-0">
                                            <span class="md:hidden font-bold text-dark-green text-xs mr-2">TIME:</span>
                                            10:00 AM
                                        </th>
                                    </tr>

                                    {{-- ROW 2 (2:00 PM) --}}
                                    <tr class="block md:table-row border border-line-brown/70 md:border-b-2 md:border-t-0 md:border-x-0 md:h-[100px] mb-6 md:mb-0 rounded-lg md:rounded-none overflow-hidden shadow-sm md:shadow-none bg-beige">
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">MEDICATION</div>
                                            <input type="text" name="medication[]" placeholder="Medication"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                            <input type="hidden" name="time[]" value="14:00:00" />
                                        </td>
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">DOSE</div>
                                            <input type="text" name="dose[]" placeholder="Dose"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">ROUTE</div>
                                            <input type="text" name="route[]" placeholder="Route"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">FREQUENCY</div>
                                            <input type="text" name="frequency[]" placeholder="Frequency"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">COMMENTS</div>
                                            <input type="text" name="comments[]" placeholder="Comments"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>
                                        <th class="block md:table-cell bg-yellow-light text-brown font-semibold py-2 md:py-0">
                                            <span class="md:hidden font-bold text-dark-green text-xs mr-2">TIME:</span>
                                            2:00 PM
                                        </th>
                                    </tr>

                                    {{-- ROW 3 (6:00 PM) --}}
                                    <tr class="block md:table-row border border-line-brown/70 md:border-b-2 md:border-t-0 md:border-x-0 md:h-[100px] mb-6 md:mb-0 rounded-lg md:rounded-none overflow-hidden shadow-sm md:shadow-none bg-beige">
                                        <td class="block md:table-cell bg-beige h-auto md:h-[100px] text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">MEDICATION</div>
                                            <input type="text" name="medication[]" placeholder="Medication"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                            <input type="hidden" name="time[]" value="18:00:00" />
                                        </td>
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">DOSE</div>
                                            <input type="text" name="dose[]" placeholder="Dose"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">ROUTE</div>
                                            <input type="text" name="route[]" placeholder="Route"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">FREQUENCY</div>
                                            <input type="text" name="frequency[]" placeholder="Frequency"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>
                                        <td class="block md:table-cell bg-beige text-center p-2 md:p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div class="md:hidden font-bold text-dark-green text-xs mb-1 text-left pl-2">COMMENTS</div>
                                            <input type="text" name="comments[]" placeholder="Comments"
                                                class="medication-input h-[50px] md:h-[45px] w-full text-center focus:outline-none bg-beige" />
                                        </td>
                                        <th class="block md:table-cell text-brown bg-yellow-light font-semibold py-2 md:py-0">
                                            <span class="md:hidden font-bold text-dark-green text-xs mr-2">TIME:</span>
                                            6:00 PM
                                        </th>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                <div class="w-[85%] mx-auto flex justify-center md:justify-end mt-5 mb-20 space-x-4">

                            <button class="button-default " type="submit" id="submit_button">SUBMIT</button>
                        </div>
                    </center>
                </div>
            </fieldset>
        </form>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/medication-administration.css'])
@endpush

@push('scripts')
    @vite([
        'resources/js/date-day-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/medication-administration-loader.js',
        'resources/js/medication-form-validation.js',
    ])
@endpush