@extends('layouts.app')

@section('title', 'Patient Medical History')

@section('content')

    <x-searchable-dropdown :patients="$patients" :selectedPatient="$selectedPatient ?? null"
        selectUrl="{{ route('medical-history.select') }}" />

    <div id="form-content-container">
        {{-- DISABLED input overlay --}}
        @if (!session('selected_patient_id'))
            <div class="form-overlay" style="margin-left:15rem;">
                <span>Please select a patient to input</span> {{-- message --}}
            </div>
        @endif

        {{-- FORM for data submission (submits with POST) --}}
        <form action="{{ route('medical.store') }}" method="POST">
            @csrf
            <fieldset @if (!session('selected_patient_id')) disabled @endif>
        @csrf

        {{-- Hidden input to send the selected patient's ID with the POST request --}}
        <input type="hidden" name="patient_id" value="{{ session('selected_patient_id') }}">

        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                {{-- PRESENT ILLNESS --}}
                <tr>
                    <th colspan="6" class="bg-dark-green text-white rounded-t-lg">PRESENT ILLNESS</th>
                </tr>


                <tr>
                    <th class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-line-brown">NAME</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">COMMENT</th>
                </tr>


                <tr>
                    <td class="rounded-bl-lg">
                        <textarea class="notepad-lines h-[200px]" name="present_condition_name" placeholder="Type here..."
                            required>{{ old('present_condition_name', $presentIllness->condition_name ?? '') }}</textarea>
                    </td>

                    <td>
                        <textarea class="notepad-lines h-[200px]" name="present_description"
                            placeholder="Type here...">{{ old('present_description', $presentIllness->description ?? '') }}</textarea>
                    </td>

                    <td><textarea class="notepad-lines h-[200px]" name="present_medication" placeholder="Type here...">{{ old('present_medication', $presentIllness->medication ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="present_dosage"
                            placeholder="Type here...">{{ old('present_dosage', $presentIllness->dosage ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="present_side_effect"
                            placeholder="Type here...">{{ old('present_side_effect', $presentIllness->side_effect ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="present_comment"
                            placeholder="Type here...">{{ old('present_comment', $presentIllness->comment ?? '') }}</textarea></td>
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

                    <th class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-line-brown">NAME</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">COMMENT</th>
                </tr>
                <tr>
                    <td>
                        <textarea class="notepad-lines h-[200px]" name="past_condition_name"
                            placeholder="Type here...">{{ old('past_condition_name', $pastMedicalSurgical->condition_name ?? '') }}</textarea>
                    </td>
                    <td><textarea class="notepad-lines h-[200px]" name="past_description"
                            placeholder="Type here...">{{ old('past_description', $pastMedicalSurgical->description ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="past_medication"
                            placeholder="Type here...">{{ old('past_medication', $pastMedicalSurgical->medication ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="past_dosage"
                            placeholder="Type here...">{{ old('past_dosage', $pastMedicalSurgical->dosage ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="past_side_effect"
                            placeholder="Type here...">{{ old('past_side_effect', $pastMedicalSurgical->side_effect ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="past_comment"
                            placeholder="Type here...">{{ old('past_comment', $pastMedicalSurgical->comment ?? '') }}</textarea></td>
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

                    <th class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-line-brown">NAME</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">COMMENT</th>
                </tr>

                <tr>
                    <td><textarea class="notepad-lines h-[200px]" name="allergy_condition_name"
                            placeholder="Type here...">{{ old('allergy_condition_name', $allergy->condition_name ?? '') }}</textarea>
                    </td>
                    <td><textarea class="notepad-lines h-[200px]" name="allergy_description"
                            placeholder="Type here...">{{ old('allergy_description', $allergy->description ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="allergy_medication"
                            placeholder="Type here...">{{ old('allergy_medication', $allergy->medication ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="allergy_dosage"
                            placeholder="Type here...">{{ old('allergy_dosage', $allergy->dosage ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="allergy_side_effect"
                            placeholder="Type here...">{{ old('allergy_side_effect', $allergy->side_effect ?? '') }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="allergy_comment"
                            placeholder="Type here...">{{ old('allergy_comment', $allergy->comment ?? '') }}</textarea></td>
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

                    <th class="bg-yellow-light text-brown text-[13px] border-l-2 border-r-2 border-line-brown">NAME</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DESCRIPTION</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">MEDICATION</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">DOSAGE</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">SIDE EFFECT</th>
                    <th class="bg-yellow-light text-brown text-[13px] border-r-2 border-line-brown">COMMENT</th>
                </tr>

                <tr>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_name"
                            placeholder="Type here...">{{ $vaccination->condition_name ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_description"
                            placeholder="Type here...">{{ $vaccination->description ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_medication"
                            placeholder="Type here...">{{ $vaccination->medication ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_dosage"
                            placeholder="Type here...">{{ $vaccination->dosage ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_side_effect"
                            placeholder="Type here...">{{ $vaccination->side_effect ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_comment"
                            placeholder="Type here...">{{ $vaccination->comment ?? '' }}</textarea></td>
                </tr>
            </table>
        </center>

        <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30">

            {{-- paasyos ako ng routing here, dapat mapupunta sa developmental history --}}

            <button type="submit" class="button-default">NEXT</button>
        </div>
        </fieldset>
        </form>
    </div>


        @push('scripts')
            @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
        @endpush

    </form>
@endsection