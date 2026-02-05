@extends('layouts.app')
@section('title', 'Patient IVs and Lines')
@section('content')

    <div id="form-content-container">

        {{-- IVs AND LINES PATIENT SELECTION (Synced with Vital Signs UI) --}}
        <div class="mx-auto w-full pt-10 px-4">
            <div class="flex flex-wrap items-center gap-x-10 gap-y-4 ml-20">
                
                {{-- PATIENT SECTION --}}
                <div class="flex items-center gap-4">
                    <label class="font-alte text-dark-green font-bold whitespace-nowrap shrink-0">
                        PATIENT NAME :
                    </label>
                    
                    {{-- Fixed 350px width ensures the UI doesn't "jump" when switching pages --}}
                    <div class="w-[350px]">
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


                    <div class="w-full rounded-[15px] overflow-hidden">
                        <table class="w-full table-fixed border-collapse border-spacing-y-0">
                            <tr>
                                <th class="w-[25%] main-header rounded-tl-[15px]">IV FLUID</th>
                                <th class="w-[25%] main-header">RATE</th>
                                <th class="w-[25%] main-header">SITE</th>
                                <th class="w-[25%] main-header rounded-tr-[15px]">STATUS</th>
                            </tr>

                            <tr>
                                <td class="p-2 bg-beige text-center">
                                    <input type="text" name="iv_fluid" placeholder="iv fluid"
                                        value="{{ $ivsAndLineRecord->iv_fluid ?? '' }}"
                                        class="w-full h-[100px] text-center focus:outline-none cdss-input"
                                        data-field-name="iv_fluid">
                                    @error('iv_fluid')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="p-2 bg-beige text-center">
                                    <input type="text" name="rate" placeholder="rate"
                                        value="{{ $ivsAndLineRecord->rate ?? '' }}"
                                        class="w-full h-[45px] text-center focus:outline-none cdss-input"
                                        data-field-name="rate">
                                    @error('rate')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="p-2 bg-beige text-center">
                                    <input type="text" name="site" placeholder="site"
                                        value="{{ $ivsAndLineRecord->site ?? '' }}"
                                        class="w-full h-[45px] text-center focus:outline-none cdss-input"
                                        data-field-name="site">
                                    @error('site')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="p-2 bg-beige text-center">
                                    <input type="text" name="status" placeholder="status"
                                        value="{{ $ivsAndLineRecord->status ?? '' }}"
                                        class="w-full h-[45px] text-center focus:outline-none cdss-input"
                                        data-field-name="status">
                                    @error('status')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- ALERTS TABLE--}}

                    {{--
                    <div class="w-[25%] rounded-[15px] overflow-hidden">
                        <div class="main-header rounded-[15px]">
                            ALERTS
                        </div>
                        <table class="w-full border-collapse text-center">
                            <tr>
                                <td>
                                    <div class="alert-box my-[3px] h-[53px] flex justify-center items-center">
                                        <span class="opacity-70 text-white font-semibold">NO ALERTS</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    --}}
                </div>

                {{-- BUTTONS --}}
                <div class="w-[85%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
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