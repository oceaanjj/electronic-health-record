@extends('layouts.app')
@section('title', 'Patient IVs and Lines')
@section('content')

    <div id="form-content-container" class="mx-auto max-w-full">

        {{-- PATIENT SELECTION: Aligned to Left of Table --}}
        <div class="mx-auto w-full pt-10 px-4 md:px-0 md:w-[85%]">
            <div class="mb-5 flex flex-col items-start gap-y-4">
                {{-- PATIENT SECTION --}}
                <div class="flex w-full flex-wrap items-center justify-start gap-4">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                        PATIENT NAME :
                    </label>

                    <div class="w-full md:w-[350px]">
                        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                            selectRoute="{{ route('ivs-and-lines.select') }}"
                            inputPlaceholder="Search or type Patient Name..." inputName="patient_id"
                            inputValue="{{ session('selected_patient_id') }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN FORM --}}
        <form action="{{ route('ivs-and-lines.store') }}" method="POST" class="cdss-form">
            @csrf
            <input type="hidden" name="patient_id"
                value="{{ $selectedPatient->patient_id ?? session('selected_patient_id') }}">

            <fieldset @if (!session('selected_patient_id')) disabled @endif class="w-full">

                {{-- TITLE HEADER SECTION --}}
                <center>
                    <p class="main-header mb-2 w-[92%] md:w-[85%] rounded-[15px]">IVS AND LINES</p>
                </center>

                <div class="flex w-full justify-center px-4 md:px-0">
                    <table class="mb-2 w-full border-collapse border-spacing-0 overflow-hidden rounded-[15px] md:w-[85%]">

                        {{-- DESKTOP HEADERS --}}
                        <thead class="block md:table-header-group">
                            <tr class="hidden md:table-row">
                                <th class="main-header rounded-tl-[15px] py-2">IV FLUID</th>
                                <th class="main-header py-2">RATE</th>
                                <th class="main-header py-2">SITE</th>
                                <th class="main-header rounded-tr-[15px] py-2">STATUS</th>
                            </tr>
                        </thead>

                        <tbody class="block md:table-row-group">
                            {{-- DATA ROW --}}
                            <tr
                                class="block overflow-hidden rounded-b-[15px] border border-line-brown/50 bg-beige text-center shadow-sm md:table-row md:border-none md:rounded-none md:shadow-none">

                                {{-- CELL 1: IV FLUID --}}
                                <td class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:p-0">
                                    {{-- Mobile Header: Changed color to Green --}}
                                    <div class="w-full main-header p-2 text-[13px] font-bold md:hidden">
                                        IV FLUID
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <input type="text" name="iv_fluid" placeholder="iv fluid"
                                            value="{{ $ivsAndLineRecord->iv_fluid ?? '' }}"
                                            class="w-full md:h-[100px] h-[45px] text-center focus:outline-none cdss-input"
                                            data-field-name="iv_fluid">
                                    </div>
                                </td>

                                {{-- CELL 2: RATE --}}
                                <td class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:p-0">
                                    {{-- Mobile Header: Changed color to Green --}}
                                    <div class="w-full main-header p-2 text-[13px] font-bold md:hidden">
                                        RATE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <input type="text" name="rate" placeholder="rate"
                                            value="{{ $ivsAndLineRecord->rate ?? '' }}"
                                            class="w-full h-[45px] md:h-[100px] text-center focus:outline-none cdss-input"
                                            data-field-name="rate">
                                    </div>
                                </td>

                                {{-- CELL 3: SITE --}}
                                <td class="block border-b border-line-brown/50 md:table-cell md:border-b-0 md:p-0">
                                    {{-- Mobile Header: Changed color to Green --}}
                                    <div class="w-full main-header p-2 text-[13px] font-bold md:hidden">
                                        SITE
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <input type="text" name="site" placeholder="site"
                                            value="{{ $ivsAndLineRecord->site ?? '' }}"
                                            class="w-full h-[45px] md:h-[100px] text-center focus:outline-none cdss-input"
                                            data-field-name="site">
                                    </div>
                                </td>

                                {{-- CELL 4: STATUS --}}
                                <td class="block md:table-cell md:p-0">
                                    {{-- Mobile Header: Changed color to Green --}}
                                    <div class="w-full main-header p-2 text-[13px] font-bold md:hidden">
                                        STATUS
                                    </div>
                                    <div class="p-2 md:p-0">
                                        <input type="text" name="status" placeholder="status"
                                            value="{{ $ivsAndLineRecord->status ?? '' }}"
                                            class="w-full h-[45px] md:h-[100px] text-center focus:outline-none cdss-input"
                                            data-field-name="status">
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- SUBMIT BUTTON --}}
                <div class="mx-auto mt-5 mb-30 flex w-full justify-end px-4 md:px-0 md:w-[85%]">
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>

            </fieldset>
        </form>
    </div>
@endsection