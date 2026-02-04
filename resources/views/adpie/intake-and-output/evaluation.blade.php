@extends('layouts.app')
@section('title', 'Step 4: Evaluation')

@section('content')
    <div class="header mx-auto my-10 flex w-[70%] items-center gap-4">
        <label for="patient_search_input" class="font-alte text-dark-green font-bold whitespace-nowrap">
            PATIENT NAME :
        </label>
        <div class="relative w-[400px]">
            <input
                type="text"
                id="patient_search_input"
                value="{{ trim($patient->name ?? '') }}"
                readonly
                class="font-creato-bold w-full rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-[15px] shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
            />
        </div>
    </div>

    <form
        action="{{ route('nursing-diagnosis.storeEvaluation', ['component' => $component, 'nursingDiagnosisId' => $diagnosis->id]) }}"
        method="POST"
        class="cdss-form flex h-full flex-col"
        data-analyze-url="{{ route('nursing-diagnosis.analyze-field') }}"
        data-patient-id="{{ $patient->patient_id }}"
        data-component="{{ $component }}"
    >
        @csrf

        <fieldset>
            <div class="mx-auto mt-6 flex w-[70%] items-start justify-center gap-0">
                <div class="w-[68%] overflow-hidden rounded-[15px]">
                    <div class="bg-dark-green rounded-t-lg py-2 text-center font-bold text-white">
                        EVALUATION (STEP 4 of 4)
                    </div>
                    <textarea
                        id="evaluation"
                        name="evaluation"
                        class="notepad-lines cdss-input w-full rounded-b-lg shadow-sm"
                        data-field-name="evaluation"
                        style="border-top: none"
                        placeholder="Enter evaluation (e.g., Goal met, Goal not met...)..."
                    >
{{ old('evaluation', $diagnosis->evaluation ?? '') }}</textarea
                    >

                    @error('evaluation')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ml-4 w-[25%] overflow-hidden rounded-[15px]">
                    <div class="bg-dark-green mb-0 rounded-t-lg py-2 text-center font-bold text-white">
                        RECOMMENDATIONS
                    </div>
                    {{-- NEW: Pre-load alert from session ★ --}}
                    @if ($component === 'intake-and-output')
                        @php
                            $alert = session('intake-and-output-alerts')['evaluation'] ?? null;
                            $level = $alert->level ?? 'INFO';
                            $message = $alert->message ?? '<span class="text-white text-center uppercase font-semibold opacity-80">NO RECOMMENDATIONS</span>';
                            $colorClass = 'alert-green';
                            if ($level === 'CRITICAL') {
                                $colorClass = 'alert-red';
                            }
                            if ($level === 'WARNING') {
                                $colorClass = 'alert-orange';
                            }
                        @endphp

                        <div
                            class="alert-box {{ $colorClass }} my-0 flex w-full items-center justify-center rounded-b-lg px-3 py-4"
                            data-alert-for="evaluation"
                            style="border-top: none; height: 90px; margin: 2px"
                        >
                            <div class="alert-message p-1">{!! $message !!}</div>
                        </div>
                        {{-- END NEW --}}
                    @endif
                </div>
            </div>

            <div class="mx-auto mt-6 flex w-[70%] items-center justify-between">
                <div class="flex flex-col items-start space-y-2" style="min-width: 220px">
                    <a
                        href="{{ route('nursing-diagnosis.showIntervention', ['component' => $component, 'nursingDiagnosisId' => $diagnosis->id]) }}"
                        class="button-default text-center"
                    >
                        GO BACK
                    </a>
                </div>

                <div class="flex flex-row items-center justify-end space-x-2">
                    <button type="submit" name="action" value="save_and_exit" class="button-default">SUBMIT</button>
                    <button type="submit" name="action" value="save_and_finish" class="button-default">FINISH</button>
                </div>
            </div>
        </fieldset>
    </form>
@endsection

@push('scripts')
    @vite(['resources/js/adpie-alert.js'])
@endpush
