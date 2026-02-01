@extends('layouts.app')
@section('title', 'Patient Medical History')
@section('content')
    <div id="form-content-container">


        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient" selectRoute=""
            inputPlaceholder="-Selected Patient-" inputName="patient_id" inputValue="{{ session('selected_patient_id') }}"
            :disabled="true" />

        {{-- FORM for data submission (submits with POST) --}}
        <form action="{{ route('developmental.store') }}" method="POST">
            @csrf

            {{-- Hidden input to send the selected patient's ID with the POST request --}}
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id ?? '' }}" />

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <p class="main-header mb-1 w-[72%] rounded-[15px]">DEVELOPMENTAL HISTORY</p>
                </center>

                <center>
                    {{-- DEVELOPMENTAL HISTORY --}}
                    <table class="bg-beige mb-1.5 w-[72%] border-separate border-spacing-0">
                        {{-- GROSS MOTOR --}}
                        <tr>
                            <th rowspan="2" class="main-header w-[200px] rounded-l-lg">GROSS MOTOR</th>
                            <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                FINDINGS
                            </th>
                        </tr>

                        <tr>
                            <td class="rounded-br-lg">
                                <textarea class="notepad-lines h-[100px]" name="gross_motor" placeholder="Type here...">
    {{ $developmentalHistory->gross_motor ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>

                    {{-- FINE MOTOR --}}
                    <table class="bg-beige mb-1.5 w-[72%] border-separate border-spacing-0">
                        <tr>
                            <th rowspan="2" class="main-header w-[200px] rounded-l-lg">FINE MOTOR</th>
                            <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                FINDINGS
                            </th>
                        </tr>

                        <tr>
                            <td class="rounded-br-lg">
                                <textarea class="notepad-lines h-[100px]" name="fine_motor" placeholder="Type here...">
    {{ $developmentalHistory->fine_motor ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>

                    {{-- LANGUAGE --}}
                    <table class="bg-beige mb-1.5 w-[72%] border-separate border-spacing-0">
                        <tr>
                            <th rowspan="2" class="main-header w-[200px] rounded-l-lg">LANGUAGE</th>
                            <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                FINDINGS
                            </th>
                        </tr>

                        <tr>
                            <td class="rounded-br-lg">
                                <textarea class="notepad-lines h-[100px]" name="language" placeholder="Type here...">
    {{ $developmentalHistory->language ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>

                    {{-- COGNITIVE --}}
                    <table class="bg-beige mb-1.5 w-[72%] border-separate border-spacing-0">
                        <tr>
                            <th rowspan="2" class="main-header w-[200px] rounded-l-lg">COGNITIVE</th>
                            <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                FINDINGS
                            </th>
                        </tr>

                        <tr>
                            <td class="rounded-br-lg">
                                <textarea class="notepad-lines h-[100px]" name="cognitive" placeholder="Type here...">
    {{ $developmentalHistory->cognitive ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>

                    {{-- SOCIAL --}}
                    <table class="bg-beige mb-1.5 w-[72%] border-separate border-spacing-0">
                        <tr>
                            <th rowspan="2" class="main-header w-[200px] rounded-l-lg">SOCIAL</th>
                            <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                FINDINGS
                            </th>
                        </tr>

                        <tr>
                            <td class="rounded-br-lg">
                                <textarea class="notepad-lines h-[100px]" name="social" placeholder="Type here...">
    {{ $developmentalHistory->social ?? '' }}</textarea>
                            </td>
                        </tr>
                    </table>
                </center>

                <div class="mx-auto mt-5 mb-30 flex w-[72%] justify-end space-x-4">
                    {{-- paayos ako ng routing here, dapat babalik sa medical history --}}
                    <a href="{{ route('medical-history') }}">
                        <button type="button" class="button-default">BACK</button>
                    </a>

                    {{-- mapupunta na dapat sa database lahat ng input sa medical history & developmental history --}}
                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </fieldset>
        </form>

        <div class="buttons">
            <button type="submit" class="btn">Submit</button>
        </div>
    </div>
@endsection

@push('scripts')
    @vite([
        'resources/js/alert.js',
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/date-day-loader.js',
    ])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const patientSearchInput = document.getElementById('patient_search_input');
            if (patientSearchInput) {
                patientSearchInput.setAttribute('readonly', true);
                patientSearchInput.style.backgroundColor = '#e9ecef'; // Light gray background
                patientSearchInput.style.cursor = 'not-allowed'; // Not-allowed cursor
                // Also disable the parent searchable-dropdown div to prevent dropdown from opening
                const searchableDropdownDiv = patientSearchInput.closest('.searchable-dropdown');
                if (searchableDropdownDiv) {
                    searchableDropdownDiv.style.pointerEvents = 'none';
                }
            }
        });
    </script>
@endpush