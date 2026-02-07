@extends('layouts.app')
@section('title', 'Patient IVs and Lines')
@section('content')

    <div id="form-content-container">

        {{-- PATIENT SELECTION --}}
        <div class="mx-auto w-full pt-10 px-4">
            {{-- 
               RESPONSIVE ALIGNMENT:
               Mobile: justify-center (Centered)
               Desktop: md:ml-20 (Original Web Layout Indent)
            --}}
           <div class="flex flex-wrap items-center gap-x-10 gap-y-4 ml-0 md:ml-20 justify-center md:justify-start">
                
                {{-- PATIENT SECTION --}}
                <div class="flex items-center gap-4">
                    <label class="font-alte text-dark-green font-bold whitespace-nowrap shrink-0">
                        PATIENT NAME :
                    </label>
                    
                    {{-- 
                        RESPONSIVE WIDTH:
                        Mobile: w-full (for better touch targets)
                        Desktop: w-[350px] (Original Web Fixed Width)
                    --}}
                    <div class="w-full md:w-[350px]">
                        <x-searchable-patient-dropdown 
                            :patients="$patients" 
                            :selectedPatient="$selectedPatient"
                            selectRoute="{{ route('ivs-and-lines.select') }}" 
                            inputPlaceholder="Search or type Patient Name..."
                            inputName="patient_id" 
                            inputValue="{{ session('selected_patient_id') }}" 
                        />
                    </div>
                </div>

            </div>
        </div>

        {{-- MAIN FORM --}}
        <form action="{{ route('ivs-and-lines.store') }}" method="POST" class="cdss-form" data-analyze-url="">
            @csrf
            <input type="hidden" name="patient_id"
                value="{{ $selectedPatient->patient_id ?? session('selected_patient_id') }}">
            <fieldset @if (!session('selected_patient_id')) disabled @endif>

                <div class="w-[85%] mx-auto flex justify-center items-start gap-1 mt-10">

                    {{-- HYBRID TABLE / STACK CONTAINER --}}
                    <div class="w-full rounded-[15px] overflow-hidden">
                        
                        {{-- 
                            1. DESKTOP HEADERS 
                            Visible only on Desktop (md:flex). Hidden on Mobile.
                            Matches the original <th> styles.
                        --}}
                        <div class="hidden md:flex w-full text-center">
                            <div class="w-[25%] main-header rounded-tl-[15px] py-2">IV FLUID</div>
                            <div class="w-[25%] main-header py-2">RATE</div>
                            <div class="w-[25%] main-header py-2">SITE</div>
                            <div class="w-[25%] main-header rounded-tr-[15px] py-2">STATUS</div>
                        </div>

                        {{-- 
                            2. INPUT ROW 
                            Desktop: Flex Row (looks like table cells).
                            Mobile: Flex Column (stacks vertically).
                        --}}
                        <div class="flex flex-col md:flex-row w-full bg-beige">

                            {{-- CELL 1: IV FLUID --}}
                            <div class="w-full md:w-[25%] p-2 flex flex-col justify-center">
                                {{-- Mobile Only Label --}}
                                <label class="md:hidden main-header text-center mb-2 rounded-[5px]">IV FLUID</label>
                                
                                {{-- Input: h-[100px] on Desktop (original), h-[45px] on Mobile --}}
                                <input type="text" name="iv_fluid" placeholder="iv fluid"
                                    value="{{ $ivsAndLineRecord->iv_fluid ?? '' }}"
                                    class="w-full md:h-[100px] h-[45px] text-center focus:outline-none cdss-input"
                                    data-field-name="iv_fluid">
                                @error('iv_fluid')
                                    <span class="text-red-500 text-xs text-center block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- CELL 2: RATE --}}
                            <div class="w-full md:w-[25%] p-2 flex flex-col justify-center">
                                <label class="md:hidden main-header text-center mb-2 rounded-[5px]">RATE</label>
                                
                                <input type="text" name="rate" placeholder="rate"
                                    value="{{ $ivsAndLineRecord->rate ?? '' }}"
                                    class="w-full h-[45px] text-center focus:outline-none cdss-input"
                                    data-field-name="rate">
                                @error('rate')
                                    <span class="text-red-500 text-xs text-center block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- CELL 3: SITE --}}
                            <div class="w-full md:w-[25%] p-2 flex flex-col justify-center">
                                <label class="md:hidden main-header text-center mb-2 rounded-[5px]">SITE</label>
                                
                                <input type="text" name="site" placeholder="site"
                                    value="{{ $ivsAndLineRecord->site ?? '' }}"
                                    class="w-full h-[45px] text-center focus:outline-none cdss-input"
                                    data-field-name="site">
                                @error('site')
                                    <span class="text-red-500 text-xs text-center block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- CELL 4: STATUS --}}
                            <div class="w-full md:w-[25%] p-2 flex flex-col justify-center">
                                <label class="md:hidden main-header text-center mb-2 rounded-[5px]">STATUS</label>
                                
                                <input type="text" name="status" placeholder="status"
                                    value="{{ $ivsAndLineRecord->status ?? '' }}"
                                    class="w-full h-[45px] text-center focus:outline-none cdss-input"
                                    data-field-name="status">
                                @error('status')
                                    <span class="text-red-500 text-xs text-center block">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>

                </div>

                {{-- BUTTONS --}}
                <div class="w-[85%] mx-auto flex justify-center md:justify-end mt-5 mb-20 space-x-4">
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
                
        </form>
        </fieldset>

@endsection

@push('scripts')
    @vite([
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js'
    ])

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.initSearchableDropdown) {
                window.initSearchableDropdown();
            }
        });
    </script>
@endpush