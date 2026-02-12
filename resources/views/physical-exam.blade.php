@extends('layouts.app')
@section('title', 'Physical Exam')
@section('content')

    <div id="form-content-container" class="w-full overflow-x-hidden">
        {{-- CDSS ALERT BANNER --}}
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

        {{-- HEADER SECTION - Aligned to the left side of the table --}}
        <div class="mx-auto mt-10 mb-5 flex w-[90%] flex-col items-start gap-4 md:w-[80%] md:flex-row md:items-center">
            <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

            <div class="w-full px-2 md:w-[350px] md:px-0">
                <x-searchable-patient-dropdown :patients="$patients" :selectedPatient="$selectedPatient"
                    selectRoute="{{ route('physical-exam.select') }}" inputPlaceholder="Search or type Patient Name..."
                    inputName="patient_id" inputValue="{{ session('selected_patient_id') }}" />
            </div>
        </div>

        {{-- NOT AVAILABLE MESSAGE - Aligned to the left side of the table --}}
        @if ($selectedPatient && (!isset($physicalExam) || !$physicalExam))
            <div class="mx-auto mt-2 mb-4 flex w-[90%] items-center gap-2 text-xs text-gray-500 italic md:w-[80%]">
                <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                Clinical Decision Support System is not yet available.
            </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('physical-exam.store') }}" method="POST" class="cdss-form"
            data-analyze-url="{{ route('physical-exam.analyze-field') }}"
            data-batch-analyze-url="{{ route('physical-exam.analyze-batch') }}" data-alert-height-class="h-[90px]">
            @csrf
            <input type="hidden" name="patient_id" id="patient_id_hidden" value="{{ session('selected_patient_id') }}" />

            <fieldset @if (!session('selected_patient_id')) disabled @endif>
                <center>
                    <div class="mt-2 flex w-[90%] items-center gap-1 md:w-[80%]">
                        <div class="flex-1 text-left">
                            <p class="main-header mb-3 md:mb-1 rounded-[15px]">PHYSICAL EXAMINATION</p>
                        </div>
                        <div class="hidden md:block md:w-[60px]"></div>
                    </div>
                </center>

                <center>
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

                    @foreach ($fields as $key => $label)
                        {{-- Row Wrapper: Card styling for mobile, original flex for desktop --}}
                        <div
                            class="relative mb-6 flex w-[90%] flex-col items-center overflow-hidden rounded-[15px] border border-[#c18b04] bg-beige md:mb-1.5 md:w-[80%] md:flex-row md:items-center md:gap-1 md:overflow-visible md:rounded-none md:border-none md:bg-transparent">

                            {{-- Mobile Card Label --}}
                            <div class="main-header w-full pl-3 p-4 pr-12 text-left text-[15px] md:hidden">
                                {{ $label }}
                            </div>

                            {{-- Main Table Section --}}
                            <table class="w-full flex-1 border-separate border-spacing-0 md:bg-beige md:table md:border-none">
                                <tbody class="block md:table-row-group">
                                    <tr class="block md:table-row">
                                        {{-- System Header --}}
                                        <th rowspan="2"
                                            class="main-header hidden w-full p-2 text-wrap md:table-cell md:w-[200px] md:rounded-l-lg">
                                            {{ $label }}
                                        </th>
                                        <th
                                            class="bg-yellow-light text-brown border-line-brown block p-1 md:p-0 text-[12px] font-bold md:table-cell md:rounded-tr-lg md:text-[13px]">
                                            FINDINGS
                                        </th>
                                    </tr>

                                    <tr class="block md:table-row">
                                        <td class="block md:table-cell md:rounded-br-lg">
                                            <textarea
                                                class="notepad-lines cdss-input h-[120px] w-full border-none bg-transparent p-4 pr-18 md:h-[100px] md:p-4 md:pr-4"
                                                name="{{ $key }}" placeholder="Type findings here..."
                                                data-field-name="{{ $key }}">{{ old($key, $physicalExam->$key ?? '') }}</textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>



                            {{-- ALERT CONTAINER (Pinned to top-right on mobile) --}}
                            <div class="absolute right-4 top-2.5 z-10 flex items-center justify-center md:static md:h-[100px] md:w-[70px] md:pl-5"
                                data-alert-for="{{ $key }}">
                                <div class="alert-icon-btn is-empty">
                                    <span class="material-symbols-outlined">notifications</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </center>

                {{-- BUTTONS --}}
                <div class="mx-auto mt-5 mb-20 flex w-[90%] flex-row justify-end gap-4 md:mb-30 md:w-[80%]">
                    @if (isset($physicalExam))
                        <a href="{{ route('nursing-diagnosis.process', ['component' => 'physical-exam', 'id' => $physicalExam->id]) }}"
                            class="button-default cdss-btn inline-block text-center">
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