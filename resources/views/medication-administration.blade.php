@extends('layouts.app')

@section('title', 'Patient Medication Administration')

@section('content')
    <div id="form-content-container">
        <form id="medication-administration-form" method="POST" action="{{ route('medication-administration.store') }}"
            class="cdss-form relative">
            @csrf

            {{-- 1. HEADER SECTION: Now matched to table width (80%) and aligned to the left side of the table footprint
            --}}
            <div class="mx-auto w-[92%] md:w-[80%] pt-10">
                <div class="flex flex-col lg:flex-row items-start mb-7 lg:items-center gap-y-4 lg:gap-x-10">

                    {{-- PATIENT SECTION --}}
                    <div class="flex items-center md:pl-12 lg:pl-0 gap-4 w-full md:w-auto justify-start">
                        <label for="patient_search_input"
                            class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">
                            PATIENT NAME :
                        </label>

                        <div class="searchable-dropdown relative w-full md:w-[350px]"
                            data-select-url="{{ route('medication-administration.select-patient') }}">
                            <input type="text" id="patient_search_input" placeholder="Select or type Patient Name..."
                                value="{{ trim($selectedPatient->name ?? '') }}" autocomplete="off"
                                class="font-creato-bold w-full rounded-full border border-gray-300 px-4 py-2 text-[15px] shadow-sm transition-all outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 focus:outline-none" />

                            <div id="patient_options_container"
                                class="absolute z-50 mt-2 hidden max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-lg">
                                @foreach ($patients as $patient)
                                    <div class="option cursor-pointer px-4 py-2 transition duration-150 hover:bg-blue-100"
                                        data-value="{{ $patient->patient_id }}">
                                        {{ trim($patient->name) }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <input type="hidden" name="patient_id" id="patient_id_for_form"
                            value="{{ $selectedPatient->patient_id ?? '' }}" />
                        <input type="hidden" id="patient_id_hidden" value="{{ $selectedPatient->patient_id ?? '' }}" />
                    </div>

                    {{-- DATE SELECTOR --}}
                    <div class="flex items-center gap-4 md:pl-12 lg:pl-0 cursor-not-allowed ">
                        <label for="date_selector" class="font-alte text-dark-green font-bold whitespace-nowrap">
                            DATE :
                        </label>
                        <input type="date" id="date_selector" name="date"
                            value="{{ $selectedDate ?? now()->format('Y-m-d') }}"
                            class="pointer-events-none font-creato-bold w-[150px] rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-[15px] shadow-sm opacity-80 outline-none"
                            @if(!$selectedPatient) disabled @endif />
                    </div>
                </div>
            </div>

            <fieldset @if(!$selectedPatient) disabled @endif>

                {{-- 2. TITLE SECTION --}}
                <center>
                    <p class="main-header mb-1 w-[92%] md:w-[80%] rounded-[15px]">MEDICATION ADMINISTRATION</p>
                </center>

                <div class="mx-auto mt-2 flex w-full items-start justify-center gap-1 px-4 md:px-0">
                    <center class="w-full">

                        {{-- 3. TABLE CONTAINER: w-[80%] matching the header above --}}
                        <div class="w-full md:w-[80%] overflow-hidden rounded-[15px]">
                            <table class="w-full md:table-fixed border-collapse border-spacing-y-0">
                                <thead class="hidden md:table-header-group">
                                    <tr>
                                        <th class="main-header w-[20%] rounded-tl-[15px]">MEDICATION</th>
                                        <th class="main-header w-[15%]">DOSE</th>
                                        <th class="main-header w-[15%]">ROUTE</th>
                                        <th class="main-header w-[15%]">FREQUENCY</th>
                                        <th class="main-header w-[20%]">COMMENTS</th>
                                        <th class="main-header w-[15%] rounded-tr-[15px]">TIME</th>
                                    </tr>
                                </thead>

                                <tbody class="block md:table-row-group">
                                    {{-- ROW 1 (10:00 AM) --}}
                                    <tr
                                        class="md:h-[100px] flex flex-col md:table-row border border-line-brown/70 md:border-b-2 md:border-t-0 md:border-x-0 mb-6 md:mb-0 rounded-lg md:rounded-none overflow-hidden shadow-sm md:shadow-none bg-beige">
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                MEDICATION</div>
                                            <div class="p-2 md:p-0">
                                                <textarea name="medication[]" placeholder="Medication" rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                DOSE</div>
                                            <div class="p-2 md:p-0"><textarea name="dose[]" placeholder="Dose" rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                ROUTE</div>
                                            <div class="p-2 md:p-0"><textarea name="route[]" placeholder="Route" rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                FREQUENCY</div>
                                            <div class="p-2 md:p-0"><textarea name="frequency[]" placeholder="Frequency"
                                                    rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                COMMENTS</div>
                                            <div class="p-2 md:p-0"><textarea name="comments[]" placeholder="Comments"
                                                    rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="order-first md:order-none block md:table-cell bg-yellow-light text-brown font-semibold p-0 border-b border-line-brown md:border-b-0">
                                            <div class="md:hidden w-full main-header text-[14px] font-bold p-3 text-center">
                                                TIME: 10:00 AM</div>
                                            <span class="hidden md:block py-4 text-center">10:00 AM</span>
                                            <input type="hidden" name="time[]" value="10:00:00" />
                                        </td>
                                    </tr>

                                    {{-- ROW 2 (2:00 PM) --}}
                                    <tr
                                        class="md:h-[100px] flex flex-col md:table-row border border-line-brown/70 md:border-b-2 md:border-t-0 md:border-x-0 mb-6 md:mb-0 rounded-lg md:rounded-none overflow-hidden shadow-sm md:shadow-none bg-beige">
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                MEDICATION</div>
                                            <div class="p-2 md:p-0"><textarea name="medication[]" placeholder="Medication"
                                                    rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                DOSE</div>
                                            <div class="p-2 md:p-0"><textarea name="dose[]" placeholder="Dose" rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                ROUTE</div>
                                            <div class="p-2 md:p-0"><textarea name="route[]" placeholder="Route" rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                FREQUENCY</div>
                                            <div class="p-2 md:p-0"><textarea name="frequency[]" placeholder="Frequency"
                                                    rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                COMMENTS</div>
                                            <div class="p-2 md:p-0"><textarea name="comments[]" placeholder="Comments"
                                                    rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="order-first md:order-none block md:table-cell bg-yellow-light text-brown font-semibold p-0 border-b border-line-brown md:border-b-0">
                                            <div class="md:hidden w-full main-header text-[14px] font-bold p-3 text-center">
                                                TIME: 2:00 PM</div>
                                            <span class="hidden md:block py-4 text-center">2:00 PM</span>
                                            <input type="hidden" name="time[]" value="14:00:00" />
                                        </td>
                                    </tr>

                                    {{-- ROW 3 (6:00 PM) --}}
                                    <tr
                                        class="md:h-[100px] flex flex-col md:table-row border border-line-brown/70 md:border-b-2 md:border-t-0 md:border-x-0 mb-6 md:mb-0 rounded-lg md:rounded-none overflow-hidden shadow-sm md:shadow-none bg-beige">
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                MEDICATION</div>
                                            <div class="p-2 md:p-0"><textarea name="medication[]" placeholder="Medication"
                                                    rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                DOSE</div>
                                            <div class="p-2 md:p-0"><textarea name="dose[]" placeholder="Dose" rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                ROUTE</div>
                                            <div class="p-2 md:p-0"><textarea name="route[]" placeholder="Route" rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                FREQUENCY</div>
                                            <div class="p-2 md:p-0"><textarea name="frequency[]" placeholder="Frequency"
                                                    rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="block md:table-cell bg-beige text-center p-0 border-b md:border-b-0 border-gray-300/50">
                                            <div
                                                class="md:hidden w-full bg-yellow-light text-brown text-[13px] font-bold p-2 border-b border-line-brown text-left">
                                                COMMENTS</div>
                                            <div class="p-2 md:p-0"><textarea name="comments[]" placeholder="Comments"
                                                    rows="1"
                                                    class="w-full bg-beige focus:outline-none notepad-lines h-[100px] text-left p-2 md:h-[45px] md:text-center md:p-3 md:bg-none md:resize-none"></textarea>
                                            </div>
                                        </td>
                                        <td
                                            class="order-first md:order-none block md:table-cell bg-yellow-light text-brown font-semibold p-0 border-b border-line-brown md:border-b-0">
                                            <div class="md:hidden w-full main-header text-[14px] font-bold p-3 text-center">
                                                TIME: 6:00 PM</div>
                                            <span class="hidden md:block py-4 text-center">6:00 PM</span>
                                            <input type="hidden" name="time[]" value="18:00:00" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- 4. BUTTON SECTION --}}
                        <div class="w-[80%] mx-auto flex justify-end md:mx-0 mt-5 mb-30 space-x-4">
                            <button class="button-default" type="submit" id="submit_button">SUBMIT</button>
                        </div>
                    </center>
                </div>
            </fieldset>
        </form>
    </div>
@endsection

@push('styles')
    @vite(['resources/css/medication-administration.css'])
@endpush

@push('scripts')
    @vite([
        'resources/js/date-day-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/medication-administration-loader.js',
        'resources/js/medication-form-validation.js',
    ])
@endpush