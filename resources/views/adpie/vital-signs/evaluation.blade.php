
@extends('layouts.app')

@section('title', 'Step 4: Evaluation')



@section('content')



    <div class="header flex items-center gap-4 my-10 mx-auto w-[70%]">

        <label for="patient_search_input" class="whitespace-nowrap font-alte font-bold text-dark-green">

            PATIENT NAME :

        </label>

        <div class="relative w-[400px]">

            <input type="text" id="patient_search_input" value="{{ trim($patient->name ?? '') }}" readonly

                class="w-full text-[15px] font-creato-bold px-4 py-2 rounded-full border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none shadow-sm bg-gray-100">

        </div>

    </div>



    <form

        action="{{ route('nursing-diagnosis.storeEvaluation', ['component' => $component, 'nursingDiagnosisId' => $diagnosis->id]) }}"

        method="POST" class="h-full flex flex-col cdss-form"

        data-analyze-url="{{ route('nursing-diagnosis.analyze-field') }}" data-patient-id="{{ $patient->patient_id }}"

        data-component="{{ $component }}">

        @csrf



        <fieldset>

            <div class="w-[70%] mx-auto flex justify-center items-start gap-0 mt-6">



                <div class="w-[68%] rounded-[15px] overflow-hidden">

                    <div class="bg-dark-green py-2 text-white rounded-t-lg text-center font-bold">

                        EVALUATION (STEP 4 of 4)

                    </div>

                    <textarea id="evaluation" name="evaluation"

                        class="notepad-lines w-full rounded-b-lg shadow-sm cdss-input" data-field-name="evaluation"

                        style="border-top: none;"

                        placeholder="Enter evaluation">{{ old('evaluation', $diagnosis->evaluation ?? '') }}</textarea>



                    @error('evaluation')

                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>

                    @enderror

                </div>



                <div class="w-[25%] rounded-[15px] overflow-hidden ml-4">

                    <div class="bg-dark-green text-white font-bold py-2 mb-0 text-center rounded-t-lg">

                        RECOMMENDATIONS

                    </div>

                                                                                {{-- NEW: Pre-load alert from session ★ --}}

                                                                                @if ($component === 'vital-signs')

                                                                                    @php

                                                                                        $alert = session('vital-signs-alerts')['evaluation'] ?? null;

                                                                                        $level = $alert->level ?? 'INFO';

                                                                                        $message = $alert->message ?? '<span class="text-white text-center uppercase font-semibold opacity-80">NO RECOMMENDATIONS</span>';

                                                                                        $colorClass = 'alert-green';

                                                                                        if ($level === 'CRITICAL')

                                                                                            $colorClass = 'alert-red';

                                                                                        if ($level === 'WARNING')

                                                                                            $colorClass = 'alert-orange';

                                                                                    @endphp

                                                                

                                                                                    <div class="alert-box my-0 py-4 px-3 flex justify-center items-center w-full rounded-b-lg {{ $colorClass }}"

                                                                                        data-alert-for="evaluation" style="border-top: none; height: 90px; margin: 2px;">

                                                                                        <div class="alert-message p-1">{!! $message !!}</div>

                                                                                    </div>

                                                                                @endif

                                                                                {{-- END NEW --}}

                </div>

            </div>



            <div class="w-[70%] mx-auto flex justify-between items-center mt-6">

                <div class="flex flex-col items-start space-y-2" style="min-width: 220px;">

                    <a href="{{ route('nursing-diagnosis.showIntervention', ['component' => $component, 'nursingDiagnosisId' => $diagnosis->id]) }}"

                        class="button-default text-center">

                        GO BACK

                    </a>

                </div>



                <div class="flex flex-row items-center justify-end space-x-2">

                    <button type="submit" name="action" value="save_and_exit" class="button-default">

                        SUBMIT

                    </button>

                    <button type="submit" name="action" value="save_and_finish" class="button-default">

                        FINISH

                    </button>

                </div>

            </div>



        </fieldset>

    </form>



@endsection



@push('scripts')

    @vite(['resources/js/adpie-alert.js'])

@endpush
