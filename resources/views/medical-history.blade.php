@extends('layouts.app')
@section('title', 'Patient Medical History')
@section('content')

    <div id="form-content-container">
        <!-- patient searchable-dropdown -->
        <center> {{-- To center the lg:w-[85%] block --}}
            <div class="w-full flex justify-start items-start gap-2 pt-10 lg:w-[85%]"> {{-- Match form width and left align
                content --}}
                <div class="mb-10 w-full mobile-dropdown-container"> {{-- Original flex layout for items --}}
                    {{-- PATIENT SECTION --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4">
                        <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

                        {{-- Fixed 350px width matches your global clinical dashboard standard --}}
                        <div class="w-full sm:w-[10px]">
                            <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                                selectRoute="{{ route('medical-history.select') }}"
                                inputPlaceholder="Search or type Patient Name..." inputName="patient_id"
                                inputValue="{{ session('selected_patient_id') }}" />
                        </div>
                    </div>
                </div>
            </div>
        </center>


        <form action="{{ route('medical.store') }}" method="POST" class="relative">
            @csrf

            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}" id="patient_id_hidden">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <div class="w-full flex justify-center items-start gap-2 mt-2 lg:w-[85%]">
                        <div class="w-full rounded-[15px] overflow-hidden mobile-table-container">
                            <table class="w-full table-fixed mb-2 border-collapse border-spacing-y-0 responsive-table">

                                {{-- PRESENT ILLNESS --}}
                                <tr>
                                    <th colspan="6" class="main-header text-white rounded-t-lg">PRESENT ILLNESS</th>
                                </tr>

                                <tr class="responsive-table-header-row">
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME
                                    </th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">
                                        DESCRIPTION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">
                                        MEDICATION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE
                                    </th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE
                                        EFFECT</th>
                                    <th class="bg-yellow-light text-brown text-[13px]  border-line-brown">COMMENT</th>
                                </tr>

                                <tr class="bg-beige responsive-table-data-row">
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data" data-label="NAME">
                                        <textarea class="notepad-lines h-[200px]" name="present_condition_name"
                                            placeholder="Type here...">{{ $presentIllness->condition_name ?? '' }}</textarea>
                                    </td>

                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="DESCRIPTION">
                                        <textarea class="notepad-lines h-[200px]" name="present_description"
                                            placeholder="Type here...">{{ $presentIllness->description ?? '' }}</textarea>
                                    </td>

                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="MEDICATION">
                                        <textarea class="notepad-lines h-[200px]" name="present_medication"
                                            placeholder="Type here...">{{ $presentIllness->medication ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data" data-label="DOSAGE">
                                        <textarea class="notepad-lines h-[200px]" name="present_dosage"
                                            placeholder="Type here...">{{ $presentIllness->dosage ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="SIDE EFFECT">
                                        <textarea class="notepad-lines h-[200px]" name="present_side_effect"
                                            placeholder="Type here...">{{ $presentIllness->side_effect ?? '' }}</textarea>
                                    </td>
                                    <td class="responsive-table-data" data-label="COMMENT">
                                        <textarea class="notepad-lines h-[200px]" name="present_comment"
                                            placeholder="Type here...">{{ $presentIllness->comment ?? '' }}</textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </center>

                <center>
                    <div class="w-full mx-auto flex justify-center items-start gap-1 lg:w-[85%]">
                        <div class="w-full rounded-[15px] overflow-hidden mobile-table-container">
                            <table class="w-full table-fixed mb-2 border-collapse border-spacing-y-0 responsive-table">

                                {{-- PAST MEDICAL / SURGICAL --}}
                                <tr>
                                    <th colspan="6" class="main-header text-white rounded-t-lg">PAST MEDICAL / SURGICAL</th>
                                </tr>
                                <tr class="responsive-table-header-row">

                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME
                                    </th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">
                                        DESCRIPTION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">
                                        MEDICATION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE
                                    </th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE
                                        EFFECT</th>
                                    <th class="bg-yellow-light text-brown text-[13px]  border-line-brown">COMMENT</th>
                                </tr>

                                <tr class="bg-beige responsive-table-data-row">
                                    <td class="border-r-2 border-line-brown/40 responsive-table-data" data-label="NAME">
                                        <textarea class="notepad-lines h-[200px]" name="past_condition_name"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->condition_name ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="DESCRIPTION">
                                        <textarea class="notepad-lines h-[200px]" name="past_description"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->description ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="MEDICATION">
                                        <textarea class="notepad-lines h-[200px]" name="past_medication"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->medication ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data" data-label="DOSAGE">
                                        <textarea class="notepad-lines h-[200px]" name="past_dosage"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->dosage ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="SIDE EFFECT">
                                        <textarea class="notepad-lines h-[200px]" name="past_side_effect"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->side_effect ?? '' }}</textarea>
                                    </td>
                                    <td class="responsive-table-data" data-label="COMMENT">
                                        <textarea class="notepad-lines h-[200px]" name="past_comment"
                                            placeholder="Type here...">{{ $pastMedicalSurgical->comment ?? '' }}</textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </center>

                <center>
                    <div class="w-full mx-auto flex justify-center items-start gap-1 lg:w-[85%]">
                        <div class="w-full rounded-[15px] overflow-hidden mobile-table-container">
                            <table class="w-full table-fixed mb-2 border-collapse border-spacing-y-0 responsive-table">

                                {{-- KNOWN CONDITION OR ALLERGIES --}}

                                <tr>
                                    <th colspan="6" class="main-header text-white rounded-t-lg">KNOWN CONDITION OR ALLERGIES
                                    </th>
                                </tr>

                                <tr class="responsive-table-header-row">

                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME
                                    </th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">
                                        DESCRIPTION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">
                                        MEDICATION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE
                                    </th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE
                                        EFFECT</th>
                                    <th class="bg-yellow-light text-brown text-[13px]  border-line-brown">COMMENT</th>
                                </tr>

                                <tr class="bg-beige responsive-table-data-row">
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data" data-label="NAME">
                                        <textarea class="notepad-lines h-[200px]" name="allergy_condition_name"
                                            placeholder="Type here...">{{ $allergy->condition_name ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="DESCRIPTION">
                                        <textarea class="notepad-lines h-[200px]" name="allergy_description"
                                            placeholder="Type here...">{{ $allergy->description ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="MEDICATION">
                                        <textarea class="notepad-lines h-[200px]" name="allergy_medication"
                                            placeholder="Type here...">{{ $allergy->medication ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data" data-label="DOSAGE">
                                        <textarea class="notepad-lines h-[200px]" name="allergy_dosage"
                                            placeholder="Type here...">{{ old('allergy_dosage', $allergy->dosage ?? '') }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="SIDE EFFECT">
                                        <textarea class="notepad-lines h-[200px]" name="allergy_side_effect"
                                            placeholder="Type here...">{{ old('allergy_side_effect', $allergy->side_effect ?? '') }}</textarea>
                                    </td>
                                    <td class="responsive-table-data" data-label="COMMENT">
                                        <textarea class="notepad-lines h-[200px]" name="allergy_comment"
                                            placeholder="Type here...">{{ old('allergy_comment', $allergy->comment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </center>

                <center>
                    <div class="w-full mx-auto flex justify-center items-start gap-1 lg:w-[85%]">
                        <div class="w-full rounded-[15px] overflow-hidden mobile-table-container">
                            <table class="w-full table-fixed mb-2 border-collapse border-spacing-y-0 responsive-table">
                                {{-- VACCINATION --}}
                                <tr>
                                    <th colspan="6" class="main-header text-white rounded-t-lg">VACCINATION</th>
                                </tr>
                                <tr class="responsive-table-header-row">
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME
                                    </th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">
                                        DESCRIPTION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">
                                        MEDICATION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE
                                    </th>
                                    <th class="bg-yellow-light text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                                    <th class="bg-yellow-light text-brown text-[13px]  border-line-brown">COMMENT</th>
                                </tr>

                                <tr class="bg-beige responsive-table-data-row">
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data" data-label="NAME">
                                        <textarea class="notepad-lines h-[200px]" name="vaccine_name"
                                            placeholder="Type here...">{{ old('vaccine_name', $vaccination->condition_name ?? '') }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="DESCRIPTION">
                                        <textarea class="notepad-lines h-[200px]" name="vaccine_description"
                                            placeholder="Type here...">{{ old('vaccine_description', $vaccination->description ?? '') }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="MEDICATION">
                                        <textarea class="notepad-lines h-[200px]" name="vaccine_medication"
                                            placeholder="Type here...">{{ old('vaccine_medication', $vaccination->medication ?? '') }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data" data-label="DOSAGE">
                                        <textarea class="notepad-lines h-[200px]" name="vaccine_dosage"
                                            placeholder="Type here...">{{ old('vaccine_dosage', $vaccination->dosage ?? '') }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50 responsive-table-data"
                                        data-label="SIDE EFFECT">
                                        <textarea class="notepad-lines h-[200px]" name="vaccine_side_effect"
                                            placeholder="Type here...">{{ old('vaccine_side_effect', $vaccination->side_effect ?? '') }}</textarea>
                                    </td>
                                    <td class="responsive-table-data" data-label="COMMENT">
                                        <textarea class="notepad-lines h-[200px]" name="vaccine_comment"
                                            placeholder="Type here...">{{ old('vaccine_comment', $vaccination->comment ?? '') }}</textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="w-full flex justify-center items-start gap-1 lg:w-[85%]">
                        <div class="w-full rounded-[15px] overflow-hidden mobile-table-container">
                            <div class="w-full flex justify-end mt-5 mb-30 mobile-next-button-align">
                                <a href="{{ route('developmental-history') }}">
                                    <button class="button-default">NEXT</button>
                                </a>
                            </div>
                        </div>
                    </div>

                </center>
            </fieldset>
        </form>
    </div>

@endsection

<style>
    @media screen and (max-width: 640px) {

        .mobile-table-container {
            display: block !important;
            width: 90% !important;
            margin: 0 0 1.5em auto !important;
            align-self: center !important;
            max-width: none;
            box-sizing: border-box;
        }

        /* 2. Responsive Table Structure */
        .responsive-table {
            display: block;
            width: 100%;
        }

        /* Hide the old desktop header */
        .responsive-table .responsive-table-header-row {
            display: none;
        }

        /* Card-style row */
        .responsive-table .responsive-table-data-row {
            display: block;
            border: 1px solid #c18b04;
            border-radius: 15px;
            margin-bottom: 1.5em;
            overflow: hidden;
            background-color: #F5F5DC;
        }

        /* 3. FLEXBOX LAYOUT FOR ROWS (Label + Input) */
        .responsive-table .responsive-table-data {
            display: flex;
            align-items: center;
            padding: 15px;
            width: 100%;
            box-sizing: border-box;
            border-bottom: 1px solid rgba(193, 139, 4, 0.2);
        }

        .responsive-table .responsive-table-data:last-child {
            border-bottom: 0;
        }

        /* Labels (35%) */
        .responsive-table .responsive-table-data::before {
            content: attr(data-label);
            position: static;
            width: 30%;
            flex-shrink: 0;
            padding-right: 10px;
            font-weight: bold;
            color: #6B4226;
            text-transform: uppercase;
            font-size: 11px;
            text-align: left;
            padding-top: 0;
        }

        /* Inputs (65%) */
        .responsive-table .responsive-table-data textarea,
        .responsive-table .responsive-table-data input {
            width: 180px !important;
            padding: 2px;
            display: block;
            margin-left: 20px;
        }

        .mobile-dropdown-container {
            display: block !important;
            width: 77% !important;
            /* Corrected to 90% */
            margin: 0 auto 15px auto !important;
            box-sizing: border-box;
        }

        .mobile-next-button-align {
            padding-right: 50px !important;
            padding-top: -30px;
        }
    }
</style>

@push('scripts')
    @vite([
        'resources/js/alert.js',
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/date-day-loader.js'
    ])
@endpush