@extends('layouts.app')
@section('title', 'Step 4: Evaluation')



@section('content')

{{-- ===== START OF LAYOUT CHANGE ===== --}}

    {{-- COPIED FROM physical-exam.blade.php --}}
    <div class="header flex items-center gap-4 my-10 mx-auto w-[70%]">
        <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
            PATIENT NAME :
        </label>

        {{-- Patient name is read-only since we are in a wizard --}}
        <div class="relative w-[400px]">
            <input 
                type="text" 
                id="patient_search_input" 
                value="{{ trim($patient->name ?? '') }}" 
                readonly
                class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm bg-gray-100"
            >
        </div>
    </div>

    {{-- COPIED FROM physical-exam.blade.php (with <fieldset> and <center>) --}}
    <form action="{{ route('nursing-diagnosis.storeEvaluation', $diagnosis->id) }}" method="POST" class="h-full flex flex-col cdss-form"
            data-analyze-url="{{ route('nursing-diagnosis.analyze-field') }}"
            data-patient-id="{{ $patient->patient_id }}">
        @csrf

        <fieldset> 
            <center>
                
                {{-- This is your original two-column layout, now inside w-[70%] --}}
                <div class="w-[70%] mx-auto flex justify-center items-start gap-0 mt-6">

                    {{-- Left Column: Evaluation Input --}}
                    <div class="w-[68%] rounded-[15px] overflow-hidden">
                        <div class="bg-dark-green py-2 text-white rounded-t-lg text-center font-bold">
                            EVALUATION (STEP 4 of 4)
                        </div>
                        <textarea id="evaluation" name="evaluation"
                            class="notepad-lines w-full rounded-b-lg shadow-sm cdss-input"
                            data-field-name="evaluation"
                            style="border-top: none;" {{-- Remove double border --}}
                            placeholder="Enter evaluation (e.g., Goal met, Goal not met...)...">{{ old('evaluation', $diagnosis->evaluation) }}</textarea>
                        
                        @error('evaluation')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Right Column: Recommendations --}}
                    <div class="w-[25%] rounded-[15px] overflow-hidden ml-4">
                        <div class="bg-dark-green text-white font-bold py-2 mb-0 text-center rounded-t-lg">
                            RECOMMENDATIONS
                        </div>
                        <div class="alert-box my-0 py-4 px-3 flex justify-center items-center w-full rounded-b-lg"
                                data-alert-for="evaluation"
                                style="border-top: none;"> {{-- Remove double border --}}
                            <span class="opacity-70 text-white font-semibold">No Recommendations</span>
                        </div>
                    </div>

                </div>


            </center>

            {{-- Button Bar (COPIED FROM physical-exam.blade.php layout) --}}
            <div class="w-[70%] mx-auto flex justify-between items-center mt-6">
                <div>
                    <a href="javascript:window.history.back()" class="button-default">
                        GO BACK
                    </a>
                </div>
                
                <div class="flex flex-col items-end space-y-2" style="min-width: 220px;">
                    {{-- Both buttons do the same thing: save and exit to physical exam page --}}
                    <button typeD="submit" name="action" value="save_and_exit" class="button-default">
                        SUBMIT
                    </button>
                    <button type="submit" name="action" value="save_and_exit" class="button-default">
                        FINISH
                    </button>
                </div>
            </div>

        </fieldset>
    </form>

{{-- ===== END OF LAYOUT CHANGE ===== --}}

@endsection

@push('scripts')
    @vite(['resources/js/alert.js'])
@endpush