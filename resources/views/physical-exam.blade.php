@extends('layouts.app')
@section('title', 'Physical Exam')
@section('content')

<style>
/* Circular Icon Base */
.alert-icon-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background-color: #e5e7eb; /* Gray for empty */
    color: #9ca3af;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Yellow state when Findings exist */
.alert-icon-btn.is-active {
    background-color: #fbbf24; /* Yellow-400 */
    border: 2px solid #f59e0b; /* Yellow-500 */
    color: #ffffff;
    animation: pulse-yellow 2s infinite;
}

/* Gray state for No Alerts */
.alert-icon-btn.is-no-alert {
    background-color: #10b981; /* Green (or stay gray) */
    color: white;
    cursor: default;
}

.alert-icon-btn:hover:not(.is-empty) {
    transform: scale(1.1);
}

@keyframes pulse-yellow {
    0% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(251, 191, 36, 0); }
    100% { box-shadow: 0 0 0 0 rgba(251, 191, 36, 0); }
}

/* Spinner adjustment */
.loading-spinner-small {
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }


</style>

    <div id="form-content-container">
        {{-- 1. THE ALERT/ERROR FIRST (Only shows if CDSS is available) --}}
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

                    {{-- Close Button --}}
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

        {{-- 2. THE PATIENT SELECTION ROW (Synced with Vital Signs UI) --}}
        <div class="mx-auto w-full pt-10">
            <div class="mb-5 ml-23 flex flex-wrap items-center gap-x-10 gap-y-4">
                {{-- LINE 1: PATIENT SELECTION --}}
                <div class="flex items-center gap-4">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

                    {{-- Fixed width of 350px to match Vital Signs perfectly --}}
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

                {{-- Add other one-line elements here in the future if needed --}}
            </div>

            {{-- 3. THE "NOT AVAILABLE" MESSAGE (Synced margin and style) --}}
            @if ($selectedPatient && (! isset($physicalExam) || ! $physicalExam))
                <div class="ml-23 flex items-center gap-2 text-xs text-gray-500 italic">
                    <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                    Clinical Decision Support System is not yet available.
                </div>
            @endif
        </div>

        <form
            action="{{ route('physical-exam.store') }}"
            method="POST"
            class="cdss-form relative mx-auto w-[85%]"
            data-analyze-url="{{ route('physical-exam.analyze-field') }}"
            data-batch-analyze-url="{{ route('physical-exam.analyze-batch') }}"
            data-alert-height-class="h-[90px]"
        >
            @csrf

            {{-- HIDDEN INPUT FOR JS TO CHECK --}}
            <input
                type="hidden"
                name="patient_id"
                id="patient_id_hidden"
                value="{{ session('selected_patient_id') }}"
            />

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <div class="mt-10 flex w-[100%] items-start justify-center gap-0">
                        <div class="mr-1 w-full overflow-hidden rounded-[15px]">
                            <table class="w-full border-separate border-spacing-0">
                                <tr>
                                    <th class="main-header w-[30%] rounded-tl-lg py-2 text-white">SYSTEM</th>
                                    <th class="main-header w-[55%] rounded-tr-lg py-2 text-white">FINDINGS</th>
                                </tr>

                                {{-- GENERAL APPEARANCE --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-line-brown border-b-2">
                                        GENERAL
                                        <br />
                                        APPEARANCE
                                    </th>
                                    <td class="bg-beige border-line-brown/50 border-b-2">
                                        <textarea
                                            name="general_appearance"
                                            class="notepad-lines cdss-input h-[90px] w-full border-none"
                                            data-field-name="general_appearance"
                                            placeholder="Type here.."
                                        >
{{ old('general_appearance', $physicalExam->general_appearance ?? '') }}</textarea
                                        >
                                    </td>
                                </tr>

                                {{-- SKIN --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-line-brown border-b-2">SKIN</th>
                                    <td class="bg-beige border-line-brown/50 border-b-2">
                                        <textarea
                                            name="skin_condition"
                                            class="notepad-lines cdss-input h-[90px] w-full border-none"
                                            data-field-name="skin_condition"
                                            placeholder="Type here.."
                                        >
{{ old('skin_condition', $physicalExam->skin_condition ?? '') }}</textarea
                                        >
                                    </td>
                                </tr>

                                {{-- EYES --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-line-brown border-b-2">EYES</th>
                                    <td class="bg-beige border-line-brown/50 border-b-2">
                                        <textarea
                                            name="eye_condition"
                                            class="notepad-lines cdss-input h-[90px] w-full border-none"
                                            data-field-name="eye_condition"
                                            placeholder="Type here.."
                                        >
{{ old('eye_condition', $physicalExam->eye_condition ?? '') }}</textarea
                                        >
                                    </td>
                                </tr>

                                {{-- ORAL CAVITY --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-line-brown border-b-2">ORAL CAVITY</th>
                                    <td class="bg-beige border-line-brown/50 border-b-2">
                                        <textarea
                                            name="oral_condition"
                                            class="notepad-lines cdss-input h-[90px] w-full border-none"
                                            data-field-name="oral_condition"
                                            placeholder="Type here.."
                                        >
{{ old('oral_condition', $physicalExam->oral_condition ?? '') }}</textarea
                                        >
                                    </td>
                                </tr>

                                {{-- CARDIOVASCULAR --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-line-brown border-b-2">
                                        CARDIOVASCULAR
                                    </th>
                                    <td class="bg-beige border-line-brown/50 border-b-2">
                                        <textarea
                                            name="cardiovascular"
                                            class="notepad-lines cdss-input h-[90px] w-full border-none"
                                            data-field-name="cardiovascular"
                                            placeholder="Type here.."
                                        >
{{ old('cardiovascular', $physicalExam->cardiovascular ?? '') }}</textarea
                                        >
                                    </td>
                                </tr>

                                {{-- ABDOMEN --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-line-brown border-b-2">ABDOMEN</th>
                                    <td class="bg-beige border-line-brown/50 border-b-2">
                                        <textarea
                                            name="abdomen_condition"
                                            class="notepad-lines cdss-input h-[90px] w-full border-none"
                                            data-field-name="abdomen_condition"
                                            placeholder="Type here.."
                                        >
{{ old('abdomen_condition', $physicalExam->abdomen_condition ?? '') }}</textarea
                                        >
                                    </td>
                                </tr>

                                {{-- EXTREMITIES --}}
                                <tr>
                                    <th class="bg-yellow-light text-brown border-line-brown border-b-2">EXTREMITIES</th>
                                    <td class="bg-beige border-line-brown border-b-2">
                                        <textarea
                                            name="extremities"
                                            class="notepad-lines cdss-input h-[90px] w-full border-none"
                                            data-field-name="extremities"
                                            placeholder="Type here.."
                                        >
{{ old('extremities', $physicalExam->extremities ?? '') }}</textarea
                                        >
                                    </td>
                                </tr>

                                {{-- NEUROLOGICAL --}}
                                <tr class="border-line-brown border-2">
                                    <th class="bg-yellow-light text-brown rounded-bl-lg">NEUROLOGICAL</th>
                                    <td class="bg-beige">
                                        <textarea
                                            name="neurological"
                                            class="notepad-lines cdss-input"
                                            data-field-name="neurological"
                                            placeholder="Type here.."
                                        >
{{ old('neurological', $physicalExam->neurological ?? '') }}</textarea
                                        >
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- ALERTS TABLE --}}
                        <div class="w-[10%] overflow-hidden rounded-[15px]">
                            <div class="mb-1 rounded-[15px] py-2 text-center"></div>
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
                                        <td class="align-middle text-left">
                                            {{-- Wrapper for the icon --}}
                                            <div 
                                                class="alert-container flex items-center justify-left ml-3 h-[97px]" 
                                                data-alert-for="{{ $fieldKey }}"
                                            >
                                                {{-- Default state: Grey/Disabled icon --}}
                                                <div class="alert-icon-btn is-empty">
                                                    <span class="material-symbols-outlined">notifications</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </center>

                <div class="mx-auto mt-5 mb-20 flex w-[98%] justify-end space-x-4">
                    <!-- cdss btn m -->
                    @if (isset($physicalExam))
                        <a
                            href="{{ route('nursing-diagnosis.process', ['component' => 'physical-exam', 'id' => $physicalExam->id]) }}"
                            class="button-default cdss-btn text-center"
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
