@extends('layouts.app')
@section('title', 'Physical Exam')
@section('content')

    <div id="form-content-container">
        {{-- CDSS ALERT BANNER --}}
        @if ($selectedPatient && isset($physicalExam) && $physicalExam)
            <div id="cdss-alert-wrapper" class="w-full overflow-hidden px-5 transition-all duration-500">
                <div
                    id="cdss-alert-content"
                    class="animate-alert-in relative mt-3 flex items-center justify-between rounded-lg border border-amber-400/50 bg-amber-100/70 px-5 py-3 shadow-sm backdrop-blur-md"
                >
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined animate-pulse text-[#dcb44e]">info</span>
                        <span class="text-sm font-semibold text-[#dcb44e]">
                            Clinical Decision Support System is now available.
                        </span>
                    </div>

                    <button
                        type="button"
                        onclick="closeCdssAlert()"
                        class="group flex items-center justify-center rounded-full p-1 text-amber-700 transition-all duration-300 hover:bg-amber-200/50 active:scale-90"
                    >
                        <span
                            class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90"
                        >
                            close
                        </span>
                    </button>
                </div>
            </div>
        @endif

        {{-- HEADER SECTION --}}
        <div class="m-10 ml-20 flex items-center gap-4">
            <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

            <div class="w-[350px]">
                <x-searchable-patient-dropdown
                    :patients="$patients"
                    :selectedPatient="$selectedPatient"
                    selectRoute="{{ route('physical-exam.select') }}"
                    inputPlaceholder="Search or type Patient Name..."
                    inputName="patient_id"
                    inputValue="{{ session('selected_patient_id') }}"
                />
            </div>
        </div>

        {{-- NOT AVAILABLE MESSAGE --}}
        @if ($selectedPatient && (!isset($physicalExam) || !$physicalExam))
            <div class="mt-4 ml-20 flex items-center gap-2 text-xs text-gray-500 italic">
                <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                Clinical Decision Support System is not yet available.
            </div>
        @endif

        {{-- FORM --}}
        <form
            action="{{ route('physical-exam.store') }}"
            method="POST"
            class="cdss-form"
            data-analyze-url="{{ route('physical-exam.analyze-field') }}"
            data-batch-analyze-url="{{ route('physical-exam.analyze-batch') }}"
            data-alert-height-class="h-[90px]"
        >
            @csrf
            <input
                type="hidden"
                name="patient_id"
                id="patient_id_hidden"
                value="{{ session('selected_patient_id') }}"
            />

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    {{-- Title only spans the table width, not the alerts --}}
                    <div class="flex w-[80%] items-center gap-1 mt-2">
                        <div class="flex-1">
                            <p class="main-header mb-1 rounded-[15px]">PHYSICAL EXAMINATION</p>
                        </div>
                        <div class="w-[60px]"></div>
                    </div>
                </center>

                <center>
                    {{-- GENERAL APPEARANCE --}}
                    <div class="mb-1.5 flex w-[80%] items-center gap-1">
                        <table class="bg-beige flex-1 border-separate border-spacing-0">
                            <tr>
                                <th rowspan="2" class="main-header w-[200px] rounded-l-lg text-wrap p-2">GENERAL APPEARANCE</th>
                                <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                    FINDINGS
                                </th>
                            </tr>
                            <tr>
                                <td class="rounded-br-lg">
                                    <textarea
                                        class="notepad-lines cdss-input h-[100px]"
                                        name="general_appearance"
                                        placeholder="Type here..."
                                        data-field-name="general_appearance"
                                    >{{ old('general_appearance', $physicalExam->general_appearance ?? '') }}</textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="general_appearance">
                            <div class="alert-icon-btn is-empty">
                                <span class="material-symbols-outlined">notifications</span>
                            </div>
                        </div>
                    </div>

                    {{-- SKIN --}}
                    <div class="mb-1.5 flex w-[80%] items-center gap-1">
                        <table class="bg-beige flex-1 border-separate border-spacing-0">
                            <tr>
                                <th rowspan="2" class="main-header w-[200px] rounded-l-lg">SKIN</th>
                                <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                    FINDINGS
                                </th>
                            </tr>
                            <tr>
                                <td class="rounded-br-lg">
                                    <textarea
                                        class="notepad-lines cdss-input h-[100px]"
                                        name="skin_condition"
                                        placeholder="Type here..."
                                        data-field-name="skin_condition"
                                    >{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}</textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="skin_condition">
                            <div class="alert-icon-btn is-empty">
                                <span class="material-symbols-outlined">notifications</span>
                            </div>
                        </div>
                    </div>

                    {{-- EYES --}}
                    <div class="mb-1.5 flex w-[80%] items-center gap-1">
                        <table class="bg-beige flex-1 border-separate border-spacing-0">
                            <tr>
                                <th rowspan="2" class="main-header w-[200px] rounded-l-lg">EYES</th>
                                <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                    FINDINGS
                                </th>
                            </tr>
                            <tr>
                                <td class="rounded-br-lg">
                                    <textarea
                                        class="notepad-lines cdss-input h-[100px]"
                                        name="eye_condition"
                                        placeholder="Type here..."
                                        data-field-name="eye_condition"
                                    >{{ old('eye_condition', $physicalExam->eye_condition ?? '') }}</textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="eye_condition">
                            <div class="alert-icon-btn is-empty">
                                <span class="material-symbols-outlined">notifications</span>
                            </div>
                        </div>
                    </div>

                    {{-- ORAL CAVITY --}}
                    <div class="mb-1.5 flex w-[80%] items-center gap-1">
                        <table class="bg-beige flex-1 border-separate border-spacing-0">
                            <tr>
                                <th rowspan="2" class="main-header w-[200px] rounded-l-lg">ORAL CAVITY</th>
                                <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                    FINDINGS
                                </th>
                            </tr>
                            <tr>
                                <td class="rounded-br-lg">
                                    <textarea
                                        class="notepad-lines cdss-input h-[100px]"
                                        name="oral_condition"
                                        placeholder="Type here..."
                                        data-field-name="oral_condition"
                                    >{{ old('oral_condition', $physicalExam->oral_condition ?? '') }}</textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="oral_condition">
                            <div class="alert-icon-btn is-empty">
                                <span class="material-symbols-outlined">notifications</span>
                            </div>
                        </div>
                    </div>

                    {{-- CARDIOVASCULAR --}}
                    <div class="mb-1.5 flex w-[80%] items-center gap-1">
                        <table class="bg-beige flex-1 border-separate border-spacing-0">
                            <tr>
                                <th rowspan="2" class="main-header w-[200px] rounded-l-lg">CARDIOVASCULAR</th>
                                <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                    FINDINGS
                                </th>
                            </tr>
                            <tr>
                                <td class="rounded-br-lg">
                                    <textarea
                                        class="notepad-lines cdss-input h-[100px]"
                                        name="cardiovascular"
                                        placeholder="Type here..."
                                        data-field-name="cardiovascular"
                                    >{{ old('cardiovascular', $physicalExam->cardiovascular ?? '') }}</textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="cardiovascular">
                            <div class="alert-icon-btn is-empty">
                                <span class="material-symbols-outlined">notifications</span>
                            </div>
                        </div>
                    </div>

                    {{-- ABDOMEN --}}
                    <div class="mb-1.5 flex w-[80%] items-center gap-1">
                        <table class="bg-beige flex-1 border-separate border-spacing-0">
                            <tr>
                                <th rowspan="2" class="main-header w-[200px] rounded-l-lg">ABDOMEN</th>
                                <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                    FINDINGS
                                </th>
                            </tr>
                            <tr>
                                <td class="rounded-br-lg">
                                    <textarea
                                        class="notepad-lines cdss-input h-[100px]"
                                        name="abdomen_condition"
                                        placeholder="Type here..."
                                        data-field-name="abdomen_condition"
                                    >{{ old('abdomen_condition', $physicalExam->abdomen_condition ?? '') }}</textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="abdomen_condition">
                            <div class="alert-icon-btn is-empty">
                                <span class="material-symbols-outlined">notifications</span>
                            </div>
                        </div>
                    </div>

                    {{-- EXTREMITIES --}}
                    <div class="mb-1.5 flex w-[80%] items-center gap-1">
                        <table class="bg-beige flex-1 border-separate border-spacing-0">
                            <tr>
                                <th rowspan="2" class="main-header w-[200px] rounded-l-lg">EXTREMITIES</th>
                                <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                    FINDINGS
                                </th>
                            </tr>
                            <tr>
                                <td class="rounded-br-lg">
                                    <textarea
                                        class="notepad-lines cdss-input h-[100px]"
                                        name="extremities"
                                        placeholder="Type here..."
                                        data-field-name="extremities"
                                    >{{ old('extremities', $physicalExam->extremities ?? '') }}</textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="extremities">
                            <div class="alert-icon-btn is-empty">
                                <span class="material-symbols-outlined">notifications</span>
                            </div>
                        </div>
                    </div>

                    {{-- NEUROLOGICAL --}}
                    <div class="mb-1.5 flex w-[80%] items-center gap-1">
                        <table class="bg-beige flex-1 border-separate border-spacing-0">
                            <tr>
                                <th rowspan="2" class="main-header w-[200px] rounded-l-lg">NEUROLOGICAL</th>
                                <th class="bg-yellow-light text-brown border-line-brown rounded-tr-lg text-[13px]">
                                    FINDINGS
                                </th>
                            </tr>
                            <tr>
                                <td class="rounded-br-lg">
                                    <textarea
                                        class="notepad-lines cdss-input h-[100px]"
                                        name="neurological"
                                        placeholder="Type here..."
                                        data-field-name="neurological"
                                    >{{ old('neurological', $physicalExam->neurological ?? '') }}</textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="flex h-[100px] w-[70px] pl-5 items-center justify-center" data-alert-for="neurological">
                            <div class="alert-icon-btn is-empty">
                                <span class="material-symbols-outlined">notifications</span>
                            </div>
                        </div>
                    </div>
                </center>

                {{-- BUTTONS - Fixed to be on one line --}}
                <div class="mx-auto mt-5 mb-30 flex w-[80%] flex-row justify-end gap-4">
                    @if (isset($physicalExam))
                        <a
                            href="{{ route('nursing-diagnosis.process', ['component' => 'physical-exam', 'id' => $physicalExam->id]) }}"
                            class="button-default cdss-btn inline-block text-center"
                        >
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