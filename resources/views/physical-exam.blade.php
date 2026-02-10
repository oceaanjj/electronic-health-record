@extends('layouts.app')

@section('title', 'Physical Exam')

@section('content')
    <div id="form-content-container" class="mx-auto max-w-full">

        {{-- ALERT/ERROR --}}
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

        {{-- PATIENT SELECTION ROW --}}
        <div class="mx-auto w-full pt-10">
            <div class="mobile-dropdown-container mb-10 flex flex-col items-start gap-2 mx-auto md:w-[85%]">

                {{-- LINE 1: PATIENT SELECTION --}}
                <div class="flex flex-wrap items-center justify-start gap-4 w-full">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

                    {{-- Fixed width of 350px --}}
                    <div class="w-full sm:w-[350px]">
                        <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                            selectRoute="{{ route('physical-exam.select') }}"
                            inputPlaceholder="Search or type Patient Name..." inputName="patient_id"
                            inputValue="{{ session('selected_patient_id') }}" />
                    </div>
                </div>

                {{-- LINE 2: "NOT AVAILABLE" MESSAGE --}}
                @if ($selectedPatient && (!isset($physicalExam) || !$physicalExam))
                    {{-- Also changed to 'justify-start' to match the label alignment --}}
                    <div class="w-full flex items-center justify-start gap-2 text-xs italic text-gray-500 mt-4">
                        <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                        Clinical Decision Support System is not yet available.
                    </div>
                @endif
            </div>


            <form action="{{ route('physical-exam.store') }}" ... <form action="{{ route('physical-exam.store') }}"
                method="POST" class="cdss-form relative w-full"
                data-analyze-url="{{ route('physical-exam.analyze-field') }}"
                data-batch-analyze-url="{{ route('physical-exam.analyze-batch') }}" data-alert-height-class="h-[90px]">
                @csrf

                <input type="hidden" name="patient_id" id="patient_id_hidden"
                    value="{{ session('selected_patient_id') }}" />

                <fieldset @if (!session('selected_patient_id')) disabled @endif class="w-full">

                    {{--
                    MAIN CONTENT CONTAINER
                    Matches the md:w-[85%] of the search bar above
                    --}}
                    <div
                        class="mx-auto mt-2 flex w-full flex-col items-start justify-center gap-5 md:w-[85%] md:flex-row md:items-start md:gap-0">

                        {{-- FINDINGS TABLE --}}
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
                                    <tr class="responsive-table-data-row border-line-brown border-b-2">
                                        <th
                                            class="bg-yellow-light text-brown @if ($loop->last) rounded-bl-lg @endif responsive-table-data-label">
                                            {{ $label }}
                                        </th>

                                        <td class="bg-beige @if (!$loop->last) border-line-brown/50 border-b-2 @endif responsive-table-data"
                                            data-label="{{ $label }}">
                                            <textarea name="{{ $fieldKey }}"
                                                class="notepad-lines cdss-input h-[95px] w-full border-none"
                                                data-field-name="{{ $fieldKey }}"
                                                placeholder="Type here..">{{ old($fieldKey, $physicalExam->$fieldKey ?? '') }}</textarea>

                                            <div class="alert-box-mobile my-0.5 flex w-full items-center justify-center px-3 py-4"
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
                            <div class="main-header mb-1 rounded-[15px] py-2">ALERTS</div>
                            <table class="w-full border-collapse">
                                @foreach ($fields as $fieldKey => $label)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="alert-box my-0.5 flex h-[91px] w-full items-center justify-center px-3 py-4"
                                                data-alert-for="{{ $fieldKey }}">
                                                <span class="font-semibold text-white opacity-70">NO ALERTS</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>

                    </div>

                    {{-- BUTTONS CONTAINER --}}
                    <div class="mx-auto mt-5 mb-20 flex w-full justify-end space-x-4 responsive-btns md:w-[85%]">
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

        /* =========================
       MOBILE ALERT
    ========================= */
        .alert-box-mobile {
            display: none;
            border-radius: 0 0 15px 15px;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 40px;
            padding-bottom: 0px;
        }

        /* =========================
       MOBILE (PHONES)
       <= 640px
    ========================= */
        @media screen and (max-width: 640px) {

            body {
                margin-top: -40px !important;
            }

            /* Adjusted dropdown container to be responsive */
            .mobile-dropdown-container {
                display: flex !important;
                flex-wrap: wrap;
                width: 90% !important;
                margin: 0 auto 15px auto !important;
                box-sizing: border-box;
            }

            /* Show mobile alerts */
            .alert-box-mobile {
                display: flex !important;
                padding: 0px !important;
            }

            /* Hide right alerts table */
            .mobile-table-container:last-of-type {
                display: none !important;
            }

            /* Force table container width */
            .mobile-table-container {
                width: 90% !important;
                margin: 0 auto !important;
            }

            /* BREAK TABLE LAYOUT */
            .responsive-table,
            .responsive-table tbody,
            .responsive-table-data-row,
            .responsive-table-data-label,
            .responsive-table-data {
                display: block;
                width: 100%;
            }

            /* Remove desktop header row */
            .responsive-table-header-row {
                display: none;
            }

            /* Card container */
            .responsive-table-data-row {
                margin: 0 auto 1.5rem auto;
                border-radius: 15px;
                background-color: #F5F5DC;
                overflow: hidden;
                width: 100%;
                border: 1px solid #c18b04;

            }

            /* SYSTEM header (th) */
            .responsive-table-data-label {
                text-align: left;
                justify-content: left;
                padding: 10px 14px;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 13px;
                color: #6B4226;
                background: linear-gradient(180deg, #ffd966, #f4b400);
                font-family: var(--font-creato-bold);
            }

            /* FINDINGS cell (td) */
            .responsive-table-data {
                padding: 14px;
                border-bottom: none;
            }

            /* TEXTAREA */
            .responsive-table-data textarea {
                width: 100% !important;
                min-height: 80px;
                box-sizing: border-box;
            }

            /* Buttons aligned right like desktop */
            .responsive-btns {
                width: 90% !important;
                margin: 1.5rem auto 2.5rem auto;
                display: flex;
                justify-content: flex-end !important;
                gap: 0.75rem;
            }

            .responsive-btns .button-default,
            .responsive-btns .cdss-btn {
                min-width: 100px;
                text-align: center;
            }
        }
    </style>