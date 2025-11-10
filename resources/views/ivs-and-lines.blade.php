@extends('layouts.app')
@section('title', 'Patient IVs and Lines')
@section('content')


    </style>

    <div id="form-content-container">

        @if (!session('selected_patient_id'))
            <div
                class="form-overlay mx-auto w-[70%] my-6 text-center border border-gray-300 rounded-lg py-6 shadow-sm bg-gray-50">
                <span class="text-gray-600 font-creato">Please select a patient to input</span>
            </div>
        @endif

        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
            selectRoute="{{ route('ivs-and-lines.select') }}" inputPlaceholder="-Select or type to search-"
            inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />

        {{-- MAIN FORM --}}
        <form action="{{ route('ivs-and-lines.store') }}" method="POST" class="cdss-form" data-analyze-url="">
            @csrf
            <input type="hidden" name="patient_id"
                value="{{ $selectedPatient->patient_id ?? session('selected_patient_id') }}">
            <fieldset @if (!session('selected_patient_id')) disabled @endif>

                <div class="w-[70%] mx-auto flex justify-center items-start gap-1 mt-6">


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
                                        class="w-full h-[45px] text-center focus:outline-none cdss-input" data-field-name="iv_fluid">
                                    @error('iv_fluid')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="p-2 bg-beige text-center">
                                    <input type="text" name="rate" placeholder="rate"
                                        value="{{ $ivsAndLineRecord->rate ?? '' }}"
                                        class="w-full h-[45px] text-center focus:outline-none cdss-input" data-field-name="rate">
                                    @error('rate')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="p-2 bg-beige text-center">
                                    <input type="text" name="site" placeholder="site"
                                        value="{{ $ivsAndLineRecord->site ?? '' }}"
                                        class="w-full h-[45px] text-center focus:outline-none cdss-input" data-field-name="site">
                                    @error('site')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="p-2 bg-beige text-center">
                                    <input type="text" name="status" placeholder="status"
                                        value="{{ $ivsAndLineRecord->status ?? '' }}"
                                        class="w-full h-[45px] text-center focus:outline-none cdss-input" data-field-name="status">
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
                <div class="w-[70%] mx-auto flex justify-end mt-5 mb-20 space-x-4">
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
        </form>
        </fieldset>

        {{-- @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif --}}


@endsection
    @push('scripts')
        @vite(['resources/js/alert.js', 'resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.initSearchableDropdown) {
                    window.initSearchableDropdown();
                }
            });
        </script>
    @endpush