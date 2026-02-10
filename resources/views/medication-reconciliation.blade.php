@extends('layouts.app')
@section('title', 'Patient Medical Reconciliation')
@section('content')

    <div id="form-content-container">

        {{-- MEDICATION RECONCILIATION PATIENT SELECTION --}}
        <div class="mx-auto w-full pt-10">
            {{--
            UPDATED CONTAINER:
            1. mx-auto md:w-[85%] -> Matches the width of the tables below.
            2. flex-col items-start -> Aligns content to the left.
            3. px-4 md:px-0 -> Adds padding on mobile, removes it on desktop to align flush with tables.
            --}}
            <div class="mx-auto mb-5 flex flex-col items-start gap-y-4 px-4 md:w-[85%] md:px-0">

                {{-- PATIENT SECTION --}}
                <div class="flex w-full flex-wrap items-center justify-start gap-4">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                        PATIENT NAME :
                    </label>

                    <div class="w-full md:w-[350px]">
                        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                            selectRoute="{{ route('medreconciliation.select') }}"
                            inputPlaceholder="Search or type Patient Name..." inputName="patient_id"
                            inputValue="{{ session('selected_patient_id') }}" />
                    </div>
                </div>

            </div>
        </div>

        <form action="{{ route('medreconciliation.store') }}" method="POST">
            @csrf
            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

                {{-- Patient's Current Medication --}}
                <div class="mx-auto mt-5 flex w-[100%] items-start justify-center gap-1"></div>

                <div class="flex w-full justify-center px-4 md:px-0">
                    <table class="mb-2 w-full border-collapse border-spacing-0 overflow-hidden rounded-[15px] md:w-[85%]">
                        {{-- MAIN HEADER ROW --}}
                        <thead class="block md:table-header-group">
                            <tr class="block md:table-row">
                                <th colspan="6" class="main-header block w-full rounded-t-lg md:table-cell">
                                    PATIENT'S CURRENT MEDICATION
                                    <div style="margin-top: 0px; font-size: 10px; color: rgb(173, 173, 173);">
                                        ( UPON ADMISSION )
                                    </div>
                                </th>
                            </tr>
                            {{-- SUB HEADERS (Hidden on Mobile) --}}
                            <tr class="hidden md:table-row">
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">MEDICATION
                                </th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">DOSE</th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">ROUTE</th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">FREQUENCY
                                </th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">INDICATION
                                </th>
                                <th class="border-line-brown bg-yellow-light text-[12px] text-brown">ADMINISTERED DURING
                                    STAY?</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group ">
                            {{-- DATA ROW --}}
                            <tr
                                class="block overflow-hidden rounded-b-lg border border-line-brown/50 bg-beige text-center shadow-sm md:table-row md:border-none md:rounded-none md:shadow-none">

                                {{-- COL 1: MEDICATION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    {{-- Mobile Header --}}

                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        MEDICATION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="current_med"
                                            placeholder="Type here...">{{ $currentMedication->current_med ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 2: DOSE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DOSE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="current_dose"
                                            placeholder="Type here...">{{ $currentMedication->current_dose ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 3: ROUTE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        ROUTE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="current_route"
                                            placeholder="Type here...">{{ $currentMedication->current_route ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 4: FREQUENCY --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        FREQUENCY
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="current_frequency"
                                            placeholder="Type here...">{{ $currentMedication->current_frequency ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 5: INDICATION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        INDICATION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="current_indication"
                                            placeholder="Type here...">{{ $currentMedication->current_indication ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 6: TEXT/ADMINISTERED --}}
                                <td class="block md:table-cell md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        ADMINISTERED DURING STAY?
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="current_text"
                                            placeholder="Type here...">{{ $currentMedication->current_text ?? '' }}</textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TABLE 2: Patient's Home Medication --}}
                <div class="mt-8 flex w-full justify-center px-4 md:px-0">
                    <table
                        class="text-center mb-2 w-full border-collapse border-spacing-0 overflow-hidden rounded-[15px] md:w-[85%]">
                        <thead class="block md:table-header-group">
                            <tr class="block md:table-row">
                                <th colspan="6" class="main-header block w-full rounded-t-lg md:table-cell">
                                    PATIENT'S HOME MEDICATION
                                    <div style="margin-top: 0px; font-size: 10px; color: rgb(173, 173, 173);">
                                        ( IF ANY )
                                    </div>
                                </th>
                            </tr>
                            <tr class="hidden md:table-row">
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">MEDICATION
                                </th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">DOSE</th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">ROUTE</th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">FREQUENCY
                                </th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">INDICATION
                                </th>
                                <th class="border-line-brown bg-yellow-light text-[12px] text-brown">DISCONTINUED ON
                                    ADMISSION?</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group">
                            <tr
                                class="block overflow-hidden rounded-b-lg border border-line-brown/50 bg-beige shadow-sm md:table-row md:border-none md:rounded-none md:shadow-none">

                                {{-- COL 1: MEDICATION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        MEDICATION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="home_med"
                                            placeholder="Type here...">{{ $homeMedication->home_med ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 2: DOSE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DOSE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="home_dose"
                                            placeholder="Type here...">{{ $homeMedication->home_dose ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 3: ROUTE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        ROUTE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="home_route"
                                            placeholder="Type here...">{{ $homeMedication->home_route ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 4: FREQUENCY --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        FREQUENCY
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="home_frequency"
                                            placeholder="Type here...">{{ $homeMedication->home_frequency ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 5: INDICATION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        INDICATION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="home_indication"
                                            placeholder="Type here...">{{ $homeMedication->home_indication ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 6: TEXT/DISCONTINUED --}}
                                <td class="block md:table-cell md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DISCONTINUED ON ADMISSION?
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="home_text"
                                            placeholder="Type here...">{{ $homeMedication->home_text ?? '' }}</textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TABLE 3: Changes in Medication --}}
                <div class="text-center mt-8 flex w-full justify-center px-4 md:px-0">
                    <table class="mb-2 w-full border-collapse border-spacing-0 overflow-hidden rounded-[15px] md:w-[85%]">
                        <thead class="block md:table-header-group">
                            <tr class="block md:table-row">
                                <th colspan="5" class="main-header block w-full rounded-t-lg md:table-cell">
                                    CHANGES IN MEDICATION DURING HOSPITALIZATION
                                </th>
                            </tr>
                            <tr class="hidden md:table-row">
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">MEDICATION
                                </th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">DOSE</th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">ROUTE</th>
                                <th class="border-r-2 border-line-brown bg-yellow-light text-[13px] text-brown">FREQUENCY
                                </th>
                                <th class="border-line-brown bg-yellow-light text-[13px] text-brown">REASON FOR CHANGE</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group">
                            <tr
                                class="block overflow-hidden rounded-b-lg border border-line-brown/50 bg-beige shadow-sm md:table-row md:border-none md:rounded-none md:shadow-none">

                                {{-- COL 1: MEDICATION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        MEDICATION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="change_med"
                                            placeholder="Type here...">{{ $changesInMedication->change_med ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 2: DOSE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DOSE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="change_dose"
                                            placeholder="Type here...">{{ $changesInMedication->change_dose ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 3: ROUTE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        ROUTE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="change_route"
                                            placeholder="Type here...">{{ $changesInMedication->change_route ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 4: FREQUENCY --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        FREQUENCY
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="change_frequency"
                                            placeholder="Type here...">{{ $changesInMedication->change_frequency ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 5: REASON --}}
                                <td class="block md:table-cell md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        REASON FOR CHANGE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="change_text"
                                            placeholder="Type here...">{{ $changesInMedication->change_text ?? '' }}</textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </fieldset>

            {{-- SUBMIT BUTTON --}}
            {{-- Updated to justify-end to align right with the table --}}
            <div class="mx-auto mb-30 mt-5 flex w-[85%] justify-end">
                <button type="submit" class="button-default">SUBMIT</button>
            </div>

        </form>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush