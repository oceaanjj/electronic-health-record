@extends('layouts.app')

@section('title', 'Patient Medical History')

@section('content')

        <form action="{{ route('medical-history.select') }}" method="POST">
            @csrf
            <div class="header">
                    <label for="patient_id" style="color: white;">PATIENT NAME :</label>
                    <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                        <option value="">-- Select Patient --</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->patient_id }}" {{ (isset($selectedPatient) && $selectedPatient->patient_id == $patient->patient_id) ? 'selected' : '' }}>
                                {{ $patient->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
        </form>


        {{-- FORM for data submission (submits with POST) --}}
        <form action="{{ route('medical.store') }}" method="POST">
            @csrf

            {{-- Hidden input to send the selected patient's ID with the POST request --}}
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

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
                            <textarea
                                class="notepad-lines h-[200px]"
                                name="present_condition_name"
                                placeholder="Type here..."
                            >{{ $presentIllness->condition_name ?? '' }}</textarea>
                        </td>
                        
                        <td>
                            <textarea
                                class="notepad-lines h-[200px]"
                                name="present_description"
                                placeholder="Type here..."
                            >{{ $presentIllness->description ?? '' }}</textarea>
                        </td>

                        <td><textarea class="notepad-lines h-[200px]"
                                name="present_description"
                                placeholder="Type here..." 
                                name="present_medication"
                                >{{ $presentIllness->medication ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]"
                                name="present_dosage"
                                placeholder="Type here...">{{ $presentIllness->dosage ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]"
                                name="present_side_effect"
                                placeholder="Type here...">{{ $presentIllness->side_effect ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]"
                                name="present_comment"
                                placeholder="Type here...">{{ $presentIllness->comment ?? '' }}</textarea></td>
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
                            <textarea
                                class="notepad-lines h-[200px]"
                                name="past_condition_name"
                                placeholder="Type here...">{{ $pastMedicalSurgical->condition_name ?? '' }}</textarea>
                        </td>
                        <td><textarea class="notepad-lines h-[200px]"
                                name="past_description"
                                placeholder="Type here...">{{ $pastMedicalSurgical->description ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]"
                                name="past_medication"
                                placeholder="Type here...">{{ $pastMedicalSurgical->medication ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]"
                                name="past_dosage"
                                placeholder="Type here...">{{ $pastMedicalSurgical->dosage ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]"
                                name="past_side_effect"
                                placeholder="Type here...">{{ $pastMedicalSurgical->side_effect ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]"
                            name="past_comment"
                            placeholder="Type here...">{{ $pastMedicalSurgical->comment ?? '' }}</textarea></td>
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
                        <td><textarea
                                class="notepad-lines h-[200px]"
                                name="allergy_condition_name"
                                placeholder="Type here...">{{ $allergy->condition_name ?? '' }}</textarea>
                        </td>
                        <td><textarea class="notepad-lines h-[200px]" name="allergy_description" placeholder="Type here...">{{ $allergy->description ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]" name="allergy_medication" placeholder="Type here...">{{ $allergy->medication ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]" name="allergy_dosage" placeholder="Type here...">{{ $allergy->dosage ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]" name="allergy_side_effect" placeholder="Type here...">{{ $allergy->side_effect ?? '' }}</textarea></td>
                        <td><textarea class="notepad-lines h-[200px]" name="allergy_comment" placeholder="Type here...">{{ $allergy->comment ?? '' }}</textarea></td>
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
                    <td><textarea
                            class="notepad-lines h-[200px]" name="vaccine_name" placeholder="Type here...">{{ $vaccination->condition_name ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_description" placeholder="Type here...">{{ $vaccination->description ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_medication" placeholder="Type here...">{{ $vaccination->medication ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_dosage" placeholder="Type here...">{{ $vaccination->dosage ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_side_effect" placeholder="Type here...">{{ $vaccination->side_effect ?? '' }}</textarea></td>
                    <td><textarea class="notepad-lines h-[200px]" name="vaccine_comment" placeholder="Type here...">{{ $vaccination->comment ?? '' }}</textarea></td>
                </tr> 
            </table>

            
         
        </center>

                <div class="w-[72%] mx-auto flex justify-end mt-5 mb-30">

                        {{-- paasyos ako ng routing here, dapat mapupunta sa developmental history --}}
                        <a href="{{ route('developmental-history') }}">
                            <button class="button-default">NEXT</button>
                        </a>
                    </div>

                </div>

        
        </form>



@endsection
