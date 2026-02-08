@extends('layouts.app')
@section('title', 'Patient Medical Reconciliation')
@section('content')

    <div id="form-content-container">
        
        {{-- MEDICATION RECONCILIATION PATIENT SELECTION --}}
        <div class="mx-auto w-full pt-10 px-4">
            {{-- 
                RESPONSIVE HEADER:
                Mobile: Flex Column, Centered, No Left Margin.
                Desktop: Flex Row, Left Aligned, Margin-Left 20 (Original).
            --}}
            <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-y-4 md:gap-x-10 md:ml-20">
                
                {{-- PATIENT SECTION --}}
                <div class="flex items-center gap-4 w-full md:w-auto justify-center md:justify-start">
                    <label class="font-alte text-dark-green font-bold whitespace-nowrap shrink-0">
                        PATIENT NAME :
                    </label>
                    
                    {{-- 
                        RESPONSIVE WIDTH:
                        Mobile: w-full
                        Desktop: w-[350px]
                    --}}
                    <div class="w-full md:w-[350px]">
                        <x-searchable-patient-dropdown 
                            :patients="$patients" 
                            :selectedPatient="$selectedPatient"
                            selectRoute="{{ route('medreconciliation.select') }}" 
                            inputPlaceholder="Search or type Patient Name..."
                            inputName="patient_id" 
                            inputValue="{{ session('selected_patient_id') }}" 
                        />
                    </div>
                </div>

            </div>
        </div>

        <form action="{{ route('medreconciliation.store') }}" method="POST">
            @csrf
            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                {{-- Hidden input to send the selected patient's ID with the POST request --}}
                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

                {{-- ..... Patient's Current Medication --}}
                <div class="mx-auto flex w-[100%] items-start justify-center gap-1 mt-10"></div>
                
                {{-- Replaced <center> with flex container for better control --}}
                <div class="flex justify-center w-full px-4 md:px-0">
                    <table class="mb-2 w-full md:w-[85%] border-collapse border-spacing-0 rounded-[15px] overflow-hidden">
                        {{-- MAIN HEADER ROW --}}
                        <thead class="block md:table-header-group">
                            <tr class="block md:table-row">
                                <th colspan="6" class="block md:table-cell main-header rounded-t-lg w-full">
                                    PATIENT'S CURRENT MEDICATION
                                    <div style="margin-top: 0px; font-size: 10px; color: rgb(173, 173, 173);">
                                        ( UPON ADMISSION )
                                    </div>
                                </th>
                            </tr>
                            {{-- SUB HEADERS (Hidden on Mobile) --}}
                            <tr class="hidden md:table-row">
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">ROUTE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">FREQUENCY</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">INDICATION</th>
                                <th class="bg-yellow-light text-brown text-[12px] border-line-brown">ADMINISTERED DURING STAY?</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group">
                            {{-- DATA ROW --}}
                            <tr class="block md:table-row bg-beige border border-line-brown/50 md:border-none rounded-b-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">
                                {{-- COL 1: MEDICATION --}}
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">MEDICATION</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="current_med"
                                        placeholder="Type here...">{{ $currentMedication->current_med ?? '' }}</textarea>
                                </td>
                                {{-- COL 2: DOSE --}}
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">DOSE</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="current_dose"
                                        placeholder="Type here...">{{ $currentMedication->current_dose ?? '' }}</textarea>
                                </td>
                                {{-- COL 3: ROUTE --}}
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">ROUTE</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="current_route"
                                        placeholder="Type here...">{{ $currentMedication->current_route ?? '' }}</textarea>
                                </td>
                                {{-- COL 4: FREQUENCY --}}
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">FREQUENCY</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="current_frequency"
                                        placeholder="Type here...">{{ $currentMedication->current_frequency ?? '' }}</textarea>
                                </td>
                                {{-- COL 5: INDICATION --}}
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">INDICATION</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="current_indication"
                                        placeholder="Type here...">{{ $currentMedication->current_indication ?? '' }}</textarea>
                                </td>
                                {{-- COL 6: TEXT/ADMINISTERED --}}
                                <td class="block md:table-cell p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">ADMINISTERED DURING STAY?</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="current_text"
                                        placeholder="Type here...">{{ $currentMedication->current_text ?? '' }}</textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TABLE 2: Patient's Home Medication --}}
                <div class="flex justify-center w-full px-4 md:px-0 mt-8">
                    <table class="mb-2 w-full md:w-[85%] border-collapse border-spacing-0 rounded-[15px] overflow-hidden">
                        <thead class="block md:table-header-group">
                            <tr class="block md:table-row">
                                <th colspan="6" class="block md:table-cell main-header rounded-t-lg w-full">
                                    PATIENT'S HOME MEDICATION
                                    <div style="margin-top: 0px; font-size: 10px; color: rgb(173, 173, 173);">
                                        ( IF ANY )
                                    </div>
                                </th>
                            </tr>
                            <tr class="hidden md:table-row">
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">ROUTE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">FREQUENCY</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">INDICATION</th>
                                <th class="bg-yellow-light text-brown text-[12px] border-line-brown">DISCONTINUED ON ADMISSION?</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group">
                            <tr class="block md:table-row bg-beige border border-line-brown/50 md:border-none rounded-b-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">MEDICATION</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="home_med"
                                        placeholder="Type here...">{{ $homeMedication->home_med ?? '' }}</textarea>
                                </td>
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">DOSE</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="home_dose"
                                        placeholder="Type here...">{{ $homeMedication->home_dose ?? '' }}</textarea>
                                </td>
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">ROUTE</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="home_route"
                                        placeholder="Type here...">{{ $homeMedication->home_route ?? '' }}</textarea>
                                </td>
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">FREQUENCY</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="home_frequency"
                                        placeholder="Type here...">{{ $homeMedication->home_frequency ?? '' }}</textarea>
                                </td>
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">INDICATION</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="home_indication"
                                        placeholder="Type here...">{{ $homeMedication->home_indication ?? '' }}</textarea>
                                </td>
                                <td class="block md:table-cell p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">DISCONTINUED ON ADMISSION?</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="home_text"
                                        placeholder="Type here...">{{ $homeMedication->home_text ?? '' }}</textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TABLE 3: Changes in Medication --}}
                <div class="flex justify-center w-full px-4 md:px-0 mt-8">
                    <table class="mb-2 w-full md:w-[85%] border-collapse border-spacing-0 rounded-[15px] overflow-hidden">
                        <thead class="block md:table-header-group">
                            <tr class="block md:table-row">
                                <th colspan="5" class="block md:table-cell main-header rounded-t-lg w-full">
                                    CHANGES IN MEDICATION DURING HOSPITALIZATION
                                </th>
                            </tr>
                            <tr class="hidden md:table-row">
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">ROUTE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">FREQUENCY</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-line-brown">REASON FOR CHANGE</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group">
                            <tr class="block md:table-row bg-beige border border-line-brown/50 md:border-none rounded-b-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0">
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">MEDICATION</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="change_med"
                                        placeholder="Type here...">{{ $changesInMedication->change_med ?? '' }}</textarea>
                                </td>
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">DOSE</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="change_dose"
                                        placeholder="Type here...">{{ $changesInMedication->change_dose ?? '' }}</textarea>
                                </td>
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">ROUTE</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="change_route"
                                        placeholder="Type here...">{{ $changesInMedication->change_route ?? '' }}</textarea>
                                </td>
                                <td class="block md:table-cell border-b md:border-b-0 md:border-r-2 border-line-brown/50 p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">FREQUENCY</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="change_frequency"
                                        placeholder="Type here...">{{ $changesInMedication->change_frequency ?? '' }}</textarea>
                                </td>
                                <td class="block md:table-cell p-2 md:p-0">
                                    <div class="md:hidden font-bold text-dark-green text-xs mb-1">REASON FOR CHANGE</div>
                                    <textarea class="notepad-lines h-[120px] md:h-[200px] w-full" name="change_text"
                                        placeholder="Type here...">{{ $changesInMedication->change_text ?? '' }}</textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </fieldset>

            {{-- SUBMIT BUTTON --}}
            {{-- Mobile: Center / Desktop: Right End --}}
            <div class="w-[85%] mx-auto flex justify-center md:justify-end mt-5 mb-30">
                <button type="submit" class="button-default">SUBMIT</button>
            </div>

        </form>

@endsection

    @push('scripts')
        @vite([
            'resources/js/alert.js',
            'resources/js/patient-loader.js',
            'resources/js/searchable-dropdown.js',
            'resources/js/date-day-loader.js'
        ])
    @endpush