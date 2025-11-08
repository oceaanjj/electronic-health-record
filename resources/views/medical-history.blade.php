@extends('layouts.app')

@section('title', 'Patient Medical History')

@section('content')

        <form action="{{ route('medical-history.select') }}" method="POST">
            @csrf
            <div class="header flex items-center gap-4 my-10 mx-auto w-[70%]"> 
                <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
                    PATIENT NAME :
                </label>

                <div class="searchable-dropdown relative w-[400px]" data-select-url="{{ route('medical-history.select') }}">
                    <input 
                        type="text" 
                        id="patient_search_input"
                        placeholder="Select or type Patient Name" 
                        value="{{ trim($selectedPatient->name ?? '') }}"
                        autocomplete="off"
                        class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm"
                    >

                    {{-- Dropdown options --}}
                    <div id="patient_options_container" 
                        class="absolute z-50 mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                        @foreach ($patients as $patient)
                            <div 
                                class="option px-4 py-2 hover:bg-blue-100 cursor-pointer transition duration-150" 
                                data-value="{{ $patient->patient_id }}">
                                {{ trim($patient->name) }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Hidden input to store selected patient ID --}}
                    <input type="hidden" id="patient_id_hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">
                </div>
            </div>
        </form>




        
        <form action="{{ route('medical.store') }}" method="POST">
            @csrf

            {{-- Hidden input to send the selected patient's ID with the POST request --}}
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}">

            <center>
                <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                    {{-- PRESENT ILLNESS --}}
                    <tr>
                        <th colspan="6" class="main-header rounded-t-lg">PRESENT ILLNESS</th>
                    </tr>

                    
                    <tr>
                        <th class="table-header border-line-brown border-r-2">NAME</th>
                        <th class="table-header border-line-brown border-r-2">DESCRIPTION</th>
                        <th class="table-header border-line-brown border-r-2">MEDICATION</th>
                        <th class="table-header border-line-brown border-r-2">DOSAGE</th>
                        <th class="table-header border-line-brown border-r-2">SIDE EFFECT</th>
                        <th class="table-header border-line-brown">COMMENT</th>
                    </tr>

             
                    <tr class="bg-beige">
                        <td class="border-r-2 border-line-brown/70">
                            <textarea
                                class="notepad-lines h-[200px]"
                                name="present_condition_name"
                                placeholder="Type here..." required
                            >{{ $presentIllness->condition_name ?? '' }}</textarea>
                        </td>
                        
                        <td class="border-r-2 border-line-brown/70">
                            <textarea
                                class="notepad-lines h-[200px]"
                                name="present_description"
                                placeholder="Type here..."
                            >{{ $presentIllness->description ?? '' }}</textarea>
                        </td>

                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]"
                                name="present_medication"
                                placeholder="Type here..."
                            >{{ $presentIllness->medication ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]"
                                name="present_dosage"
                                placeholder="Type here...">{{ $presentIllness->dosage ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]"
                                name="present_side_effect"
                                placeholder="Type here...">{{ $presentIllness->side_effect ?? '' }}</textarea>
                        </td>
                        <td>
                                <textarea class="notepad-lines h-[200px]"
                                name="present_comment"
                                placeholder="Type here...">{{ $presentIllness->comment ?? '' }}</textarea>
                        </td>
                    </tr>
                </table>
            </center>

            
            <center>
                <table class="mb-2 w-[72%] border-collapse border-spacing-0">

                    {{-- PAST MEDICAL / SURGICAL --}}
                    <tr>
                        <th colspan="6" class="main-header rounded-t-lg">PAST MEDICAL / SURGICAL</th>
                    </tr>
                    <tr>
                        
                         <th class="table-header border-line-brown border-r-2">NAME</th>
                        <th class="table-header border-line-brown border-r-2">DESCRIPTION</th>
                        <th class="table-header border-line-brown border-r-2">MEDICATION</th>
                        <th class="table-header border-line-brown border-r-2">DOSAGE</th>
                        <th class="table-header border-line-brown border-r-2">SIDE EFFECT</th>
                        <th class="table-header border-line-brown">COMMENT</th>
                    </tr>
                    
                    <tr class="bg-beige">
                        <td class="border-r-2 border-line-brown/70">
                            <textarea
                                class="notepad-lines h-[200px]"
                                name="past_condition_name"
                                placeholder="Type here...">{{ $pastMedicalSurgical->condition_name ?? '' }}</textarea>
                        </td>
                       <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]"
                                name="past_description"
                                placeholder="Type here...">{{ $pastMedicalSurgical->description ?? '' }}</textarea>
                       </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]"
                                name="past_medication"
                                placeholder="Type here...">{{ $pastMedicalSurgical->medication ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]"
                                name="past_dosage"
                                placeholder="Type here...">{{ $pastMedicalSurgical->dosage ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]"
                                name="past_side_effect"
                                placeholder="Type here...">{{ $pastMedicalSurgical->side_effect ?? '' }}</textarea></td>
                        <td>
                            <textarea class="notepad-lines h-[200px]"
                            name="past_comment"
                            placeholder="Type here...">{{ $pastMedicalSurgical->comment ?? '' }}</textarea>
                        </td>
                    </tr>
                </table>
            </center>

            


            <center>
                <table class="mb-2 w-[72%]">

                    
                    {{-- KNOWN CONDITION OR ALLERGIES --}}
                    
                        <tr>
                            <th colspan="6" class="main-header rounded-t-lg">KNOWN CONDITION OR ALLERGIES</th>
                        </tr>
                
                

                    <tr>
                        
                        <th class="table-header border-line-brown border-r-2">NAME</th>
                        <th class="table-header border-line-brown border-r-2">DESCRIPTION</th>
                        <th class="table-header border-line-brown border-r-2">MEDICATION</th>
                        <th class="table-header border-line-brown border-r-2">DOSAGE</th>
                        <th class="table-header border-line-brown border-r-2">SIDE EFFECT</th>
                        <th class="table-header border-line-brown">COMMENT</th>
                    </tr>

                    <tr class="bg-beige">
                        <td class="border-r-2 border-line-brown/70">
                            <textarea
                                class="notepad-lines h-[200px]"
                                name="allergy_condition_name"
                                placeholder="Type here...">{{ $allergy->condition_name ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="allergy_description" placeholder="Type here...">{{ $allergy->description ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="allergy_medication" placeholder="Type here...">{{ $allergy->medication ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="allergy_dosage" placeholder="Type here...">{{ $allergy->dosage ?? '' }}</textarea>
                        </td>
                        <td class="border-r-2 border-line-brown/70">
                            <textarea class="notepad-lines h-[200px]" name="allergy_side_effect" placeholder="Type here...">{{ $allergy->side_effect ?? '' }}</textarea>
                        </td>
                        <td>
                            <textarea class="notepad-lines h-[200px]" name="allergy_comment" placeholder="Type here...">{{ $allergy->comment ?? '' }}</textarea>
                        </td>
                    </tr>
                </table>
             </center>

        <center>
            <table class="mb-2 w-[72%] border-collapse border-spacing-0">
                {{-- VACCINATION --}}
                <tr>
                    <th colspan="6" class="main-header rounded-t-lg">VACCINATION</th>
                </tr>
                <tr>
                    
                        <th class="table-header border-line-brown border-r-2">NAME</th>
                        <th class="table-header border-line-brown border-r-2">DESCRIPTION</th>
                        <th class="table-header border-line-brown border-r-2">MEDICATION</th>
                        <th class="table-header border-line-brown border-r-2">DOSAGE</th>
                        <th class="table-header border-line-brown border-r-2">SIDE EFFECT</th>
                        <th class="table-header border-line-brown">COMMENT</th>
                </tr>

                <tr class="bg-beige">
                    <td class="border-r-2 border-line-brown/70">
                        <textarea
                            class="notepad-lines h-[200px]" name="vaccine_name" placeholder="Type here...">{{ $vaccination->condition_name ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="vaccine_description" placeholder="Type here...">{{ $vaccination->description ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="vaccine_medication" placeholder="Type here...">{{ $vaccination->medication ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="vaccine_dosage" placeholder="Type here...">{{ $vaccination->dosage ?? '' }}</textarea>
                    </td>
                    <td class="border-r-2 border-line-brown/70">
                        <textarea class="notepad-lines h-[200px]" name="vaccine_side_effect" placeholder="Type here...">{{ $vaccination->side_effect ?? '' }}</textarea>
                    </td>
                    <td>
                        <textarea class="notepad-lines h-[200px]" name="vaccine_comment" placeholder="Type here...">{{ $vaccination->comment ?? '' }}</textarea>
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

                </div>
</form>
@endsection

@push('scripts')
    @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js', 'resources/js/date-day-loader.js'])
@endpush
