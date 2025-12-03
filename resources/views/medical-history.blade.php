@extends('layouts.app')
@section('title', 'Patient Medical History')
@section('content')



    {{-- FORM OVERLAY (ALERT) --}}
    <div id="form-content-container">
        @if (!session('selected_patient_id'))
            <div
                class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                <span class="text-gray-600 font-creato">Please select a patient to input</span>
            </div>
        @endif

        <!-- dropdown component -->
        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
            selectRoute="{{ route('medical-history.select') }}" inputPlaceholder="-Select or type to search-"
            inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />

        <form action="{{ route('medical.store') }}" method="POST">
            @csrf

            {{-- Hidden input to send the selected patient's ID with the POST request --}}
            <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <div class="w-[85%] mx-auto flex justify-center items-start gap-1">


                        <div class="w-full rounded-[15px] overflow-hidden">
                            <table class="w-full table-fixed mb-2 border-collapse border-spacing-y-0">
        
                                {{-- PRESENT ILLNESS --}}
                                <tr>
                                    <th colspan="6" class="main-header text-white rounded-t-lg">PRESENT ILLNESS</th>
                                </tr>

                                <tr>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                                    <th class="bg-yellow-light text-brown text-[13px]  border-line-brown">COMMENT</th>
                                </tr>

                                <tr class="bg-beige">
                                    <td class="border-r-2 border-line-brown/50">
                                        <textarea class="notepad-lines h-[200px]" name="present_condition_name"
                                            placeholder="Type here..."
                                            >{{ $presentIllness->condition_name ?? '' }}</textarea>
                                    </td>

                                    <td class="border-r-2 border-line-brown/50">
                                        <textarea class="notepad-lines h-[200px]" name="present_description"
                                            placeholder="Type here...">{{ $presentIllness->description ?? '' }}</textarea>
                                    </td>

                                    <td class="border-r-2 border-line-brown/50">
                                        <textarea class="notepad-lines h-[200px]" name="present_medication"
                                            placeholder="Type here...">{{ $presentIllness->medication ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50">
                                        <textarea class="notepad-lines h-[200px]" name="present_dosage"
                                            placeholder="Type here...">{{ $presentIllness->dosage ?? '' }}</textarea>
                                    </td>
                                    <td class="border-r-2 border-line-brown/50">
                                        <textarea class="notepad-lines h-[200px]" name="present_side_effect"
                                            placeholder="Type here...">{{ $presentIllness->side_effect ?? '' }}</textarea>
                                    </td>
                                    <td>
                                        <textarea class="notepad-lines h-[200px]" name="present_comment"
                                            placeholder="Type here...">{{ $presentIllness->comment ?? '' }}</textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </center>

                <center>
                    <div class="w-[85%] mx-auto flex justify-center items-start gap-1">


                        <div class="w-full rounded-[15px] overflow-hidden">
                            <table class="w-full table-fixed mb-2 border-collapse border-spacing-y-0">

                            {{-- PAST MEDICAL / SURGICAL --}}
                            <tr>
                                <th colspan="6" class="main-header text-white rounded-t-lg">PAST MEDICAL / SURGICAL</th>
                            </tr>
                            <tr>

                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                                <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                                <th class="bg-yellow-light text-brown text-[13px]  border-line-brown">COMMENT</th>
                            </tr>

                            <tr class="bg-beige">
                                <td class="border-r-2 border-line-brown/40">
                                    <textarea class="notepad-lines h-[200px]" name="past_condition_name"
                                        placeholder="Type here...">{{ $pastMedicalSurgical->condition_name ?? '' }}</textarea>
                                </td>
                                <td class="border-r-2 border-line-brown/50">
                                    <textarea class="notepad-lines h-[200px]" name="past_description"
                                        placeholder="Type here...">{{ $pastMedicalSurgical->description ?? '' }}</textarea>
                                </td>
                                <td class="border-r-2 border-line-brown/50">
                                    <textarea class="notepad-lines h-[200px]" name="past_medication"
                                        placeholder="Type here...">{{ $pastMedicalSurgical->medication ?? '' }}</textarea>
                                </td>
                                <td class="border-r-2 border-line-brown/50">
                                    <textarea class="notepad-lines h-[200px]" name="past_dosage"
                                        placeholder="Type here...">{{ $pastMedicalSurgical->dosage ?? '' }}</textarea>
                                </td>
                                <td class="border-r-2 border-line-brown/50">
                                    <textarea class="notepad-lines h-[200px]" name="past_side_effect"
                                        placeholder="Type here...">{{ $pastMedicalSurgical->side_effect ?? '' }}</textarea>
                                </td>
                                <td>
                                    <textarea class="notepad-lines h-[200px]" name="past_comment"
                                        placeholder="Type here...">{{ $pastMedicalSurgical->comment ?? '' }}</textarea>
                                </td>
                            </tr>
                        </table>
                        </div>
                    </div>
                </center>

                <center>
                    <div class="w-[85%] mx-auto flex justify-center items-start gap-1">


                        <div class="w-full rounded-[15px] overflow-hidden">
                            <table class="w-full table-fixed mb-2 border-collapse border-spacing-y-0">

                        {{-- KNOWN CONDITION OR ALLERGIES --}}

                        <tr>
                            <th colspan="6" class="main-header text-white rounded-t-lg">KNOWN CONDITION OR ALLERGIES</th>
                        </tr>

                        <tr>

                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                            <th class="bg-yellow-light text-brown text-[13px]  border-line-brown">COMMENT</th>
                        </tr>

                        <tr class="bg-beige">
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="allergy_condition_name"
                                    placeholder="Type here...">{{ $allergy->condition_name ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="allergy_description"
                                    placeholder="Type here...">{{ $allergy->description ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="allergy_medication"
                                    placeholder="Type here...">{{ $allergy->medication ?? '' }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="allergy_dosage"
                                    placeholder="Type here...">{{ old('allergy_dosage', $allergy->dosage ?? '') }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="allergy_side_effect"
                                    placeholder="Type here...">{{ old('allergy_side_effect', $allergy->side_effect ?? '') }}</textarea>
                            </td>
                            <td>
                                <textarea class="notepad-lines h-[200px]" name="allergy_comment"
                                    placeholder="Type here...">{{ old('allergy_comment', $allergy->comment ?? '') }}</textarea>
                            </td>
                        </tr>
                    </table>
                        </div>
                    </div>
                </center>

                <center>
                    <div class="w-[85%] mx-auto flex justify-center items-start gap-1">


                        <div class="w-full rounded-[15px] overflow-hidden">
                            <table class="w-full table-fixed mb-2 border-collapse border-spacing-y-0">
                        {{-- VACCINATION --}}
                        <tr>
                            <th colspan="6" class="main-header text-white rounded-t-lg">VACCINATION</th>
                        </tr>
                        <tr>

                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">NAME</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                            <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                            <th class="bg-yellow-light text-brown text-[13px]  border-line-brown">COMMENT</th>
                        </tr>

                        <tr class="bg-beige">
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="vaccine_name"
                                    placeholder="Type here...">{{ old('vaccine_name', $vaccination->condition_name ?? '') }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="vaccine_description"
                                    placeholder="Type here...">{{ old('vaccine_description', $vaccination->description ?? '') }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="vaccine_medication"
                                    placeholder="Type here...">{{ old('vaccine_medication', $vaccination->medication ?? '') }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="vaccine_dosage"
                                    placeholder="Type here...">{{ old('vaccine_dosage', $vaccination->dosage ?? '') }}</textarea>
                            </td>
                            <td class="border-r-2 border-line-brown/50">
                                <textarea class="notepad-lines h-[200px]" name="vaccine_side_effect"
                                    placeholder="Type here...">{{ old('vaccine_side_effect', $vaccination->side_effect ?? '') }}</textarea>
                            </td>
                            <td>
                                <textarea class="notepad-lines h-[200px]" name="vaccine_comment"
                                    placeholder="Type here...">{{ old('vaccine_comment', $vaccination->comment ?? '') }}</textarea>
                            </td>
                        </tr>
                    </table>

                        </div>
                    </div>
                </center>

                <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30">

                    {{-- paasyos ako ng routing here, dapat mapupunta sa developmental history --}}
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