@extends('layouts.app')
@section('title', 'Patient Medical History')
@section('content')


    <!-- searchable-dropdown -->
    <form action="{{ route('medical-history.select') }}" method="POST">
        @csrf
        <div class="header flex items-center gap-4 my-10 mx-auto w-[70%]">
            <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
                PATIENT NAME :
            </label>

            <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('medical-history.select') }}">
                <input type="text" id="patient_search_input" placeholder="Select or type Patient Name"
                    value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off"
                    class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm">

                {{-- Dropdown options --}}
                <div id="patient_options_container"
                    class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                    @foreach ($patients as $patient)
                        <div class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150"
                            data-value="{{ $patient->patient_id }}">
                            {{ trim($patient->name) }}
                        </div>
                    @endforeach
                </div>

                {{-- Hidden input to store selected patient ID --}}
                <input type="hidden" id="patient_id_hidden" name="patient_id"
                    value="{{ $selectedPatient->patient_id ?? '' }}">
            </div>
        </div>
    </form>
    <!-- end of searchable-dropdown -->


    <form action="{{ route('medical.store') }}" method="POST">
        @csrf

        {{-- Hidden input to send the selected patient's ID with the POST request --}}
        <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

        {{-- FORM OVERLAY --}}
        <div id="form-content-container">
            @if (!session('selected_patient_id'))
                <div
                    class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                    <span class="text-gray-600 font-creato">Please select a patient to input</span>
                </div>
            @endif
        </div>

        <fieldset @if (!session('selected_patient_id')) disabled @endif>
            <center>
                <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                    {{-- PRESENT ILLNESS --}}
                    <tr>
                        <th colspan="6" class="bg-dark-green text-white rounded-t-lg">PRESENT ILLNESS</th>
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
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="present_condition_name" placeholder="Type here..."
                                required>{{ old('present_condition_name', $presentIllness->condition_name ?? '') }}</textarea>
                        </td>

                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="present_description"
                                placeholder="Type here...">{{ old('present_description', $presentIllness->description ?? '') }}</textarea>
                        </td>

                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="present_medication"
                                placeholder="Type here...">{{ old('present_medication', $presentIllness->medication ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="present_dosage"
                                placeholder="Type here...">{{ old('present_dosage', $presentIllness->dosage ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="present_side_effect"
                                placeholder="Type here...">{{ old('present_side_effect', $presentIllness->side_effect ?? '') }}</textarea>
                        </td>
                        <td>
                            <textarea class="notepad-lines h-[200px]" name="present_comment"
                                placeholder="Type here...">{{ old('present_comment', $presentIllness->comment ?? '') }}</textarea>
                        </td>
                    </tr>
                </table>
            </center>


            <center>
                <table class="mb-2 w-[72%] border-collapse border-spacing-0">

                    {{-- PAST MEDICAL / SURGICAL --}}
                    <tr>
                        <th colspan="6" class="bg-dark-green text-white rounded-t-lg">PAST MEDICAL / SURGICAL</th>
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
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="past_condition_name"
                                placeholder="Type here...">{{ old('past_condition_name', $pastMedicalSurgical->condition_name ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="past_description"
                                placeholder="Type here...">{{ old('past_description', $pastMedicalSurgical->description ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="past_medication"
                                placeholder="Type here...">{{ old('past_medication', $pastMedicalSurgical->medication ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="past_dosage"
                                placeholder="Type here...">{{ old('past_dosage', $pastMedicalSurgical->dosage ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="past_side_effect"
                                placeholder="Type here...">{{ old('past_side_effect', $pastMedicalSurgical->side_effect ?? '') }}</textarea>
                        </td>
                        <td>
                            <textarea class="notepad-lines h-[200px]" name="past_comment"
                                placeholder="Type here...">{{ old('past_comment', $pastMedicalSurgical->comment ?? '') }}</textarea>
                        </td>
                    </tr>
                </table>
            </center>




            <center>
                <table class="mb-2 w-[72%]">


                    {{-- KNOWN CONDITION OR ALLERGIES --}}

                    <tr>
                        <th colspan="6" class="bg-dark-green text-white rounded-t-lg">KNOWN CONDITION OR ALLERGIES</th>
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
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="allergy_condition_name"
                                placeholder="Type here...">{{ old('allergy_condition_name', $allergy->condition_name ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="allergy_description"
                                placeholder="Type here...">{{ old('allergy_description', $allergy->description ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="allergy_medication"
                                placeholder="Type here...">{{ old('allergy_medication', $allergy->medication ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="allergy_dosage"
                                placeholder="Type here...">{{ old('allergy_dosage', $allergy->dosage ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="allergy_side_effect"
                                placeholder="Type here...">{{ old('allergy_side_effect', $allergy->side_effect ?? '') }}</textarea>
                        </td>
                        <td>
                            <textarea class="notepad-lines h-[200px]" name="allergy_comment"
                                placeholder="Type here...">{{ old('allergy_comment', $allergy->comment ?? '') }}</textarea>
                        </td>
                    </tr>
                </table>
            </center>

            <center>
                <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                    {{-- VACCINATION --}}
                    <tr>
                        <th colspan="6" class="bg-dark-green text-white rounded-t-lg">VACCINATION</th>
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
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="vaccine_name"
                                placeholder="Type here...">{{ old('vaccine_name', $vaccination->condition_name ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="vaccine_description"
                                placeholder="Type here...">{{ old('vaccine_description', $vaccination->description ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="vaccine_medication"
                                placeholder="Type here...">{{ old('vaccine_medication', $vaccination->medication ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="vaccine_dosage"
                                placeholder="Type here...">{{ old('vaccine_dosage', $vaccination->dosage ?? '') }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="vaccine_side_effect"
                                placeholder="Type here...">{{ old('vaccine_side_effect', $vaccination->side_effect ?? '') }}</textarea>
                        </td>
                        <td>
                            <textarea class="notepad-lines h-[200px]" name="vaccine_comment"
                                placeholder="Type here...">{{ old('vaccine_comment', $vaccination->comment ?? '') }}</textarea>
                        </td>
                    </tr>
                </table>



            </center>

            <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30">

                {{-- paasyos ako ng routing here, dapat mapupunta sa developmental history --}}
                <a href="{{ route('developmental-history') }}">
                    <button class="button-default">NEXT</button>
                </a>
            </div>

        </fieldset>
    </form>
@endsection

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush