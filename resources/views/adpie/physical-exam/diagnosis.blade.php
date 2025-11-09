@extends('layouts.app')
@section('title', 'Step 1: Nursing Diagnosis')
@section('content')

    {{-- ===== START OF LAYOUT CHANGE ===== --}}

    <div class="header flex items-center gap-4 my-10 mx-auto w-[70%]">
        <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">
            PATIENT NAME :
        </label>
        <div class="relative w-[400px]">
            <input type="text" id="patient_search_input" value="{{ trim($patient->name ?? '') }}" readonly
                class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm bg-gray-100">
        </div>
    </div>

    <form action="{{ route('nursing-diagnosis.storeDiagnosis', ['component' => $component, 'id' => $physicalExamId]) }}"
        method="POST" class="h-full flex flex-col cdss-form"
        data-analyze-url="{{ route('nursing-diagnosis.analyze-field') }}" data-patient-id="{{ $patient->patient_id }}"
        data-component="{{ $component }}">
        @csrf

        <fieldset>
            <div class="w-[70%] mx-auto flex justify-center items-start gap-0 mt-6">

                {{-- Left Column: Diagnosis Input --}}
                <div class="w-[68%] rounded-[15px] overflow-hidden">
                    <div class="bg-dark-green py-2 text-white rounded-t-lg text-center font-bold">
                        DIAGNOSIS (STEP 1 of 4)
                    </div>
                    <textarea id="diagnosis" name="diagnosis" class="notepad-lines w-full rounded-b-lg shadow-sm cdss-input"
                        data-field-name="diagnosis" style="border-top: none;"
                        placeholder="Enter diagnosis...">{{ old('diagnosis', $diagnosis->diagnosis ?? '') }}</textarea>

                    @error('diagnosis')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Right Column: Recommendations --}}
                <div class="w-[25%] rounded-[15px] overflow-hidden ml-4">
                    <div class="bg-dark-green text-white font-bold py-2 mb-0 text-center rounded-t-lg">
                        RECOMMENDATIONS
                    </div>
                    <div class="alert-box my-0 py-4 px-3 flex justify-center items-center w-full rounded-b-lg"
                        data-alert-for="diagnosis" style="border-top: none;">
                        <span class="opacity-70 text-white font-semibold">No Recommendations</span>
                    </div>
                </div>
            </div>

            {{-- Button Bar --}}
            <div class="w-[70%] mx-auto flex justify-between items-center mt-6">
                <div class="flex flex-col items-start space-y-2" style="min-width: 220px;">
                    <a href="javascript:window.history.back()" class="button-default text-center">
                        GO BACK
                    </a>
                </div>

                <div class="flex flex-row items-center justify-end space-x-2">
                    <button type="submit" name="action" value="save_and_exit" class="button-default">
                        SUBMIT
                    </button>
                    <button type="submit" name="action" value="save_and_proceed" class="button-default">
                        PLANNING
                    </button>
                </div>
            </div>

        </fieldset>
    </form>

    {{-- ===== END OF LAYOUT CHANGE ===== --}}

@endsection

@push('scripts')
    @vite(['resources/js/adpie-alert.js'])
@endpush