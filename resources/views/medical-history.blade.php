@extends('layouts.app')
@section('title', 'Patient Medical History')
@section('content')

    <div id="form-content-container">
        <div class="mx-auto w-full pt-10 px-4 md:px-0 md:w-[85%]">
            <div class="mb-8 w-full">
                {{-- PATIENT SECTION --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 ">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>
                    <div class="w-full sm:w-[350px] mt-2">
                        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                            selectRoute="{{ route('medical-history.select') }}"
                            inputPlaceholder="Search or type Patient Name..." inputName="patient_id"
                            inputValue="{{ session('selected_patient_id') }}" />
                    </div>
                </div>
            </div>
        </div>


        <form action="{{ route('medical.store') }}" method="POST" class="relative">
            @csrf

            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}" id="patient_id_hidden">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>

                {{-- TABLE 1: PRESENT ILLNESS --}}
                <div class="flex w-full justify-center px-4 md:px-0">
                    <table
                        class="mb-8 w-full border-collapse border-spacing-0 overflow-hidden rounded-[15px] md:w-[85%] md:table-fixed">

                        {{-- HEADERS --}}
                        <thead class="block md:table-header-group">
                            {{-- Main Header --}}
                            <tr class="block md:table-row">
                                <th colspan="6" class="main-header block w-full rounded-t-lg md:table-cell text-white">
                                    PRESENT ILLNESS
                                </th>
                            </tr>
                            {{-- Sub Headers (Hidden on Mobile) --}}
                            <tr class="hidden md:table-row">
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-line-brown">COMMENT</th>
                            </tr>
                        </thead>

                        {{-- BODY --}}
                        <tbody class="block md:table-row-group">
                            <tr
                                class="block overflow-hidden rounded-b-lg border border-line-brown/50 bg-beige shadow-sm md:table-row md:border-none md:rounded-none md:shadow-none">

                                {{-- COL 1: NAME --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        NAME
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="present_condition_name"
                                            placeholder="Type here...">{{ $presentIllness->condition_name ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 2: DESCRIPTION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DESCRIPTION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="present_description"
                                            placeholder="Type here...">{{ $presentIllness->description ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 3: MEDICATION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        MEDICATION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="present_medication"
                                            placeholder="Type here...">{{ $presentIllness->medication ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 4: DOSAGE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DOSAGE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="present_dosage"
                                            placeholder="Type here...">{{ $presentIllness->dosage ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 5: SIDE EFFECT --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        SIDE EFFECT
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="present_side_effect"
                                            placeholder="Type here...">{{ $presentIllness->side_effect ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 6: COMMENT --}}
                                <td class="block md:table-cell md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        COMMENT
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="present_comment"
                                            placeholder="Type here...">{{ $presentIllness->comment ?? '' }}</textarea>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TABLE 2: PAST MEDICAL / SURGICAL --}}
                <div class="flex w-full justify-center px-4 md:px-0">
                    <table
                        class="mb-8 w-full border-collapse border-spacing-0 overflow-hidden rounded-[15px] md:w-[85%] md:table-fixed">

                        <thead class="block md:table-header-group">
                            <tr class="block md:table-row">
                                <th colspan="6" class="main-header block w-full rounded-t-lg md:table-cell text-white">
                                    PAST MEDICAL / SURGICAL
                                </th>
                            </tr>
                            <tr class="hidden md:table-row">
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-line-brown">COMMENT</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group">
                            <tr
                                class="block overflow-hidden rounded-b-lg border border-line-brown/50 bg-beige shadow-sm md:table-row md:border-none md:rounded-none md:shadow-none">

                                {{-- COL 1: NAME --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/40 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        NAME
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="past_condition_name"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->condition_name ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 2: DESCRIPTION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DESCRIPTION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="past_description"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->description ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 3: MEDICATION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        MEDICATION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="past_medication"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->medication ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 4: DOSAGE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DOSAGE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="past_dosage"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->dosage ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 5: SIDE EFFECT --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        SIDE EFFECT
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="past_side_effect"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->side_effect ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 6: COMMENT --}}
                                <td class="block md:table-cell md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        COMMENT
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="past_comment"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->comment ?? '' }}</textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TABLE 3: KNOWN CONDITION OR ALLERGIES --}}
                <div class="flex w-full justify-center px-4 md:px-0">
                    <table
                        class="mb-8 w-full border-collapse border-spacing-0 overflow-hidden rounded-[15px] md:w-[85%] md:table-fixed">

                        <thead class="block md:table-header-group">
                            <tr class="block md:table-row">
                                <th colspan="6" class="main-header block w-full rounded-t-lg md:table-cell text-white">
                                    KNOWN CONDITION OR ALLERGIES
                                </th>
                            </tr>
                            <tr class="hidden md:table-row">
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-line-brown">COMMENT</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group">
                            <tr
                                class="block overflow-hidden rounded-b-lg border border-line-brown/50 bg-beige shadow-sm md:table-row md:border-none md:rounded-none md:shadow-none">

                                {{-- COL 1: NAME --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        NAME
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="allergy_condition_name"
                                            placeholder="Type here...">{{ $allergy->condition_name ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 2: DESCRIPTION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DESCRIPTION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="allergy_description"
                                            placeholder="Type here...">{{ $allergy->description ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 3: MEDICATION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        MEDICATION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="allergy_medication"
                                            placeholder="Type here...">{{ $allergy->medication ?? '' }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 4: DOSAGE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DOSAGE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="allergy_dosage"
                                            placeholder="Type here...">{{ old('allergy_dosage', $allergy->dosage ?? '') }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 5: SIDE EFFECT --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        SIDE EFFECT
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="allergy_side_effect"
                                            placeholder="Type here...">{{ old('allergy_side_effect', $allergy->side_effect ?? '') }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 6: COMMENT --}}
                                <td class="block md:table-cell md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        COMMENT
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="allergy_comment"
                                            placeholder="Type here...">{{ old('allergy_comment', $allergy->comment ?? '') }}</textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- TABLE 4: VACCINATION --}}
                <div class="flex w-full justify-center px-4 md:px-0">
                    <table
                        class="mb-2 w-full border-collapse border-spacing-0 overflow-hidden rounded-[15px] md:w-[85%] md:table-fixed">

                        <thead class="block md:table-header-group">
                            <tr class="block md:table-row">
                                <th colspan="6" class="main-header block w-full rounded-t-lg md:table-cell text-white">
                                    VACCINATION
                                </th>
                            </tr>
                            <tr class="hidden md:table-row">
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION
                                </th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                                <th class="bg-yellow-light text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-line-brown">COMMENT</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group">
                            <tr
                                class="block overflow-hidden rounded-b-lg border border-line-brown/50 bg-beige shadow-sm md:table-row md:border-none md:rounded-none md:shadow-none">

                                {{-- COL 1: NAME --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        NAME
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="vaccine_name"
                                            placeholder="Type here...">{{ old('vaccine_name', $vaccination->condition_name ?? '') }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 2: DESCRIPTION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DESCRIPTION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="vaccine_description"
                                            placeholder="Type here...">{{ old('vaccine_description', $vaccination->description ?? '') }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 3: MEDICATION --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        MEDICATION
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="vaccine_medication"
                                            placeholder="Type here...">{{ old('vaccine_medication', $vaccination->medication ?? '') }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 4: DOSAGE --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        DOSAGE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="vaccine_dosage"
                                            placeholder="Type here...">{{ old('vaccine_dosage', $vaccination->dosage ?? '') }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 5: SIDE EFFECT --}}
                                <td
                                    class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:border-r-2 md:border-line-brown/50 md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        SIDE EFFECT
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]"
                                            name="vaccine_side_effect"
                                            placeholder="Type here...">{{ old('vaccine_side_effect', $vaccination->side_effect ?? '') }}</textarea>
                                    </div>
                                </td>

                                {{-- COL 6: COMMENT --}}
                                <td class="block md:table-cell md:p-0">
                                    <div
                                        class="w-full border-b border-line-brown bg-yellow-light p-2 text-[13px] font-bold text-brown md:hidden">
                                        COMMENT
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <textarea class="notepad-lines h-[120px] w-full md:h-[200px]" name="vaccine_comment"
                                            placeholder="Type here...">{{ old('vaccine_comment', $vaccination->comment ?? '') }}</textarea>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mx-auto mt-5 mb-30 flex w-[85%] justify-end ">
                    <a href="{{ route('developmental-history') }}">
                        <button class="button-default">NEXT</button>
                    </a>
                </div>

            </fieldset>
        </form>
    </div>

@endsection

@push('scripts')
    @vite([
        'resources/js/alert.js',
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/date-day-loader.js'
    ])
@endpush