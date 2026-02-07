@extends('layouts.app')
@section('title', 'Physical Exam')
@section('content')
    <div id="form-content-container" class="mx-auto max-w-full">
        {{-- 1. THE ALERT/ERROR FIRST (Only shows if CDSS is available) --}}
        @if ($selectedPatient && isset($physicalExam) && $physicalExam)
            <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                <div id="cdss-alert-content"
                    class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined animate-pulse text-[#dcb44e]">info</span>
                        <span class="text-sm font-semibold text-[#dcb44e]">
                            Clinical Decision Support System is now available.
                        </span>
                    </div>

                    {{-- Close Button --}}
                    <button type="button" onclick="closeCdssAlert()"
                        class="group flex items-center justify-center rounded-full p-1 text-amber-700 transition-all duration-300 hover:bg-amber-200/50 active:scale-90">
                        <span
                            class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90">
                            close
                        </span>
                    </button>
                </div>
            </div>
        @endif

        {{-- 2. THE PATIENT SELECTION ROW (Synced with Vital Signs UI) --}}
        <div class="mx-auto w-full pt-10">
            <div class="mb-5 flex flex-wrap items-center justify-center gap-x-10 gap-y-4 md:ml-23">
                {{-- LINE 1: PATIENT SELECTION --}}
                <div class="flex items-center gap-4">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

                    {{-- Fixed width of 350px to match Vital Signs perfectly --}}
                    <div class="w-full md:w-[350px]">
                        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                            selectRoute="{{ route('physical-exam.select') }}"
                            inputPlaceholder="Search or type Patient Name..." inputName="patient_id"
                            inputValue="{{ session('selected_patient_id') }}" />
                    </div>
                </div>

                {{-- Add other one-line elements here in the future if needed --}}
            </div>

            {{-- 3. THE "NOT AVAILABLE" MESSAGE (Synced margin and style) --}}
            @if ($selectedPatient && (!isset($physicalExam) || !$physicalExam))
                <div class="mx-auto flex items-center gap-2 text-xs italic text-gray-500 md:ml-23">
                    <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                    Clinical Decision Support System is not yet available.
                </div>
            @endif
        </div>

        <form action="{{ route('physical-exam.store') }}" method="POST"
            class="cdss-form relative mx-auto w-full max-w-screen-2xl md:w-[85%]"
            data-analyze-url="{{ route('physical-exam.analyze-field') }}"
            data-batch-analyze-url="{{ route('physical-exam.analyze-batch') }}" data-alert-height-class="h-[90px]">
            @csrf

            {{-- HIDDEN INPUT FOR JS TO CHECK --}}
            <input type="hidden" name="patient_id" id="patient_id_hidden" value="{{ session('selected_patient_id') }}" />

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <div
                        class="mt-10 flex w-full max-w-full flex-col items-center justify-center gap-5 md:w-[98%] md:flex-row md:items-start md:gap-0">
                        <div class="w-full overflow-hidden rounded-[15px] md:mr-1 md:w-3/5 mobile-table-container">
                            <table class="w-full border-separate border-spacing-0 responsive-table">
                                <tr class="responsive-table-header-row">
                                    <th class="main-header w-[30%] rounded-tl-lg py-2 text-white">SYSTEM</th>
                                    <th class="main-header w-[55%] rounded-tr-lg py-2 text-white">FINDINGS</th>
                                </tr>

                                @php
                                    $fields = [
                                        'general_appearance' => 'GENERAL APPEARANCE',
                                        'skin_condition' => 'SKIN',
                                        'eye_condition' => 'EYES',
                                        'oral_condition' => 'ORAL CAVITY',
                                        'cardiovascular' => 'CARDIOVASCULAR',
                                        'abdomen_condition' => 'ABDOMEN',
                                        'extremities' => 'EXTREMITIES',
                                        'neurological' => 'NEUROLOGICAL',
                                    ];
                                @endphp

                                @foreach ($fields as $fieldKey => $label)
                                    <tr
                                        class="responsive-table-data-row @if ($loop->last) border-line-brown border-2 @else border-line-brown border-b-2 @endif">
                                        <th
                                            class="bg-yellow-light text-brown @if ($loop->last) rounded-bl-lg @endif responsive-table-data-label">
                                            {{ $label }}
                                            @if ($fieldKey === 'general_appearance')
                                                <br />APPEARANCE
                                            @endif
                                        </th>
                                        <td class="bg-beige @if (!$loop->last) border-line-brown/50 border-b-2 @endif responsive-table-data"
                                            data-label="{{ $label }}">
                                            <textarea name="{{ $fieldKey }}"
                                                class="notepad-lines cdss-input h-[90px] w-full border-none"
                                                data-field-name="{{ $fieldKey }}"
                                                placeholder="Type here..">{{ old($fieldKey, $physicalExam->$fieldKey ?? '') }}</textarea>

                                            {{-- Embedded Alert Box for Mobile --}}
                                            <div class="alert-box-mobile my-0.5 flex w-full  items-center justify-center px-3 py-4"
                                                data-alert-for="{{ $fieldKey }}">
                                                <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                            </div>


                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>



                        {{-- ALERTS TABLE --}}
                        <div class="w-full overflow-hidden rounded-[15px] md:ml-1 md:w-2/5 mobile-table-container">
                            <div class="main-header mb-1 rounded-[15px] py-2 text-center">ALERTS</div>
                            <table class="w-full border-collapse">
                                @php
                                    $fields = [
                                        'general_appearance' => 'GENERAL APPEARANCE',
                                        'skin_condition' => 'SKIN',
                                        'eye_condition' => 'EYES',
                                        'oral_condition' => 'ORAL CAVITY',
                                        'cardiovascular' => 'CARDIOVASCULAR',
                                        'abdomen_condition' => 'ABDOMEN',
                                        'extremities' => 'EXTREMITIES',
                                        'neurological' => 'NEUROLOGICAL',
                                    ];
                                @endphp

                                @foreach ($fields as $fieldKey => $label)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="alert-box my-0.5 flex h-[92px] w-full items-center justify-center px-3 py-4"
                                                data-alert-for="{{ $fieldKey }}">
                                                {{-- Dynamic alert content will load here --}}
                                                <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </center>

                <div class=" mx-auto mt-5 mb-20 flex w-[98%] justify-end space-x-4">
                    <!-- cdss btn m -->
                    @if (isset($physicalExam))
                        <a href="{{ route('nursing-diagnosis.start', ['component' => 'physical-exam', 'id' => $physicalExam->id]) }}"
                            class="button-default cdss-btn text-center">
                            CDSS
                        </a>
                    @endif

                    <button type="submit" class="button-default">SUBMIT</button>
                </div>
            </fieldset>
        </form>
    </div>
@endsection

@push('scripts')
    @vite([
        'resources/js/alert.js',
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/close-cdss-alert.js',
    ])
@endpush



<style>
    html,
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    * {
        box-sizing: border-box;
    }

    .alert-box-mobile {
        display: none;
    }

    .alert-box-mobile {
        border-radius: 0 0 15px 15px;
        box-sizing: border-box;
        justify-content: center;
        align-items: center;
        text-align: center;
        min-height: 40px;
        padding: 5px 10px;

    }



    @media screen and (max-width: 640px) {



        .alert-box-mobile {



            display: flex !important;



        }







        .mobile-table-container:last-of-type {



            display: none !important;



        }











        .mobile-table-container {



            display: block !important;



            width: 100% !important;



            margin: 0 auto 1.5em auto !important;



            align-self: center !important;



            max-width: none;



            box-sizing: border-box;



        }







        .responsive-table {



            display: block;



            width: 100%;



        }







        .responsive-table .responsive-table-header-row {



            display: none;



        }







        .responsive-table .responsive-table-data-row {



            display: block;



            margin: 10px;



            width: 380px;



            margin-left: 15px !important;



            box-sizing: border-box;



            border: 1px solid #c18b04;



            border-radius: 15px;



            margin-bottom: 1.5em;



            overflow: hidden;



            background-color: #F5F5DC;



        }







        .responsive-table .responsive-table-data {



            display: flex;



            flex-direction: column;



            align-items: flex-start;



            padding: 15px;



            width: 100%;



            box-sizing: border-box;



            border-bottom: 1px solid rgba(193, 139, 4, 0.2);



        }







        .responsive-table .responsive-table-data:last-child {



            border-bottom: 0;



        }







        .responsive-table .responsive-table-data::before {



            content: attr(data-label);



            position: static;



            width: 100%;



            padding-right: 0;



            font-weight: bold;



            color: #6B4226;



            text-transform: uppercase;



            font-size: 11px;



            text-align: left;



            padding-top: 0;



            margin-bottom: 5px;



        }







        .responsive-table .responsive-table-data textarea,



        .responsive-table .responsive-table-data input {



            width: 100% !important;



            padding: 2px;



            display: block;



            margin-left: 0;



            margin-top: 0;



            margin-bottom: 5px;



        }







        .responsive-table .responsive-table-data-row th.responsive-table-data-label {



            display: none;



        }







    }
</style>