@extends('layouts.app')
@section('title', 'Nursing Process: ADPIE')

@section('content')
    <style>
        .stepper-header {
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .adpie-slider-container {
            overflow: hidden;
            width: 100%;
            position: relative;
        }

        .adpie-slider-wrapper {
            display: flex;
            width: 400%;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            align-items: flex-start;
        }

        .adpie-slide {
            width: 25%;
            flex-shrink: 0;
            transition: opacity 0.4s ease;
        }

        .adpie-slide:not(.active-slide) {
            opacity: 0;
            pointer-events: none;
            height: 0;
            overflow: hidden;
        }

        .active-slide {
            opacity: 1;
            height: auto;
        }
    </style>

    <div class="stepper-header">
        <div class="mx-auto w-[85%] pt-8 pb-4 bg-transparent">
            <div class="progress-stepper">
                <div class="progress-track"></div>
                <div id="js-progress-fill" class="progress-fill step-1"></div>
                <div class="flex justify-between w-full z-10 relative">
                    @foreach(['Diagnosis', 'Planning', 'Intervention', 'Evaluation'] as $index => $label)
                        <div class="step-item {{ $index === 0 ? 'active' : '' }}" id="stepper-{{ $index + 1 }}">
                            <div class="step-circle">{{ $index + 1 }}</div>
                            <div class="step-label">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="header mx-auto my-6 flex w-[70%] items-center gap-4">
            <label for="patient_search_input" class="font-alte text-dark-green font-bold whitespace-nowrap">
                PATIENT NAME :
            </label>
            <div class="relative w-[400px]">
                <input type="text" id="patient_search_input" value="{{ trim($patient->name ?? '') }}" readonly
                    class="font-creato-bold w-full rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-[15px] shadow-sm outline-none" />
            </div>
        </div>
    </div>

    <form id="master-adpie-form"
        action="{{ route('nursing-diagnosis.storeFullProcess', ['component' => $component, 'id' => $recordId]) }}"
        method="POST" data-analyze-url="{{ route('nursing-diagnosis.analyze-field') }}"
        data-patient-id="{{ $patient->patient_id }}" data-component="{{ $component }}"
        class="cdss-form flex h-full flex-col">

        @csrf
        <input type="hidden" name="current_step" id="current_step_input" value="1">

        <div class="adpie-slider-container">
            <div id="slider-wrapper" class="adpie-slider-wrapper">

                {{-- STEP 1: DIAGNOSIS --}}
                <div class="adpie-slide active-slide" id="slide-1">
                    <div class="mx-auto mt-2 w-[70%]">
                        @include('adpie.partials._diagnosis_step')
                    </div>

                    <div class="mx-auto mt-8 mb-12 flex w-[70%] items-center justify-between">
                        <div class="flex flex-col items-start">
                            <a href="{{ route($indexRoute) }}" class="button-default text-center">GO BACK</a>
                        </div>
                        <div class="flex flex-row items-center justify-end space-x-3">
                            <button type="submit" name="action" value="save_and_exit" class="button-default">SUBMIT</button>
                            <button type="button" onclick="goToStep(2)" class="button-default">PLANNING</button>
                        </div>
                    </div>
                </div>

                {{-- STEP 2: PLANNING --}}
                <div class="adpie-slide" id="slide-2">
                    <div class="mx-auto mt-2 w-[70%]">
                        @include('adpie.partials._planning_step')
                    </div>
                    <div class="mx-auto mt-8 mb-12 flex w-[70%] items-center justify-between">
                        <div class="flex flex-col items-start">
                            <button type="button" onclick="goToStep(1)" class="button-default text-center">GO BACK</button>
                        </div>
                        <div class="flex flex-row items-center justify-end space-x-3">
                            <button type="submit" name="action" value="save_and_exit" class="button-default">SUBMIT</button>
                            <button type="button" onclick="goToStep(3)"
                                class="button-default text-center">INTERVENTION</button>
                        </div>
                    </div>
                </div>

                {{-- STEP 3: INTERVENTION --}}
                <div class="adpie-slide" id="slide-3">
                    <div class="mx-auto mt-2 w-[70%]">
                        @include('adpie.partials._intervention_step')
                    </div>
                    <div class="mx-auto mt-8 mb-12 flex w-[70%] items-center justify-between">
                        <div class="flex flex-col items-start">
                            <button type="button" onclick="goToStep(2)" class="button-default text-center">GO BACK</button>
                        </div>
                        <div class="flex flex-row items-center justify-end space-x-3">
                            <button type="submit" name="action" value="save_and_exit" class="button-default">SUBMIT</button>
                            <button type="button" onclick="goToStep(4)" class="button-default">EVALUATION</button>
                        </div>
                    </div>
                </div>

                {{-- STEP 4: EVALUATION --}}
                <div class="adpie-slide" id="slide-4">
                    <div class="mx-auto mt-2 w-[70%]">
                        @include('adpie.partials._evaluation_step')
                    </div>
                    <div class="mx-auto mt-8 mb-12 flex w-[70%] items-center justify-between">
                        <div class="flex flex-col items-start">
                            <button type="button" onclick="goToStep(3)" class="button-default text-center">GO BACK</button>
                        </div>
                        <div class="flex flex-row items-center justify-end space-x-3">
                            <button type="submit" name="action" value="save_and_exit" class="button-default">SUBMIT</button>
                            <button type="submit" name="action" value="save_and_proceed" class="button-default">FINISH &
                                SAVE</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection

@push('scripts')
    @vite(['resources/js/adpie-alert.js'])
    <script>
        function goToStep(step) {
            document.getElementById('current_step_input').value = step;
            const wrapper = document.getElementById('slider-wrapper');
            const fill = document.getElementById('js-progress-fill');
            const translateX = (step - 1) * -25;
            wrapper.style.transform = `translateX(${translateX}%)`;

            document.querySelectorAll('.adpie-slide').forEach((s, idx) => {
                s.classList.toggle('active-slide', (idx + 1) === step);
            });

            fill.className = `progress-fill step-${step}`;
            for (let i = 1; i <= 4; i++) {
                const item = document.getElementById(`stepper-${i}`);
                if (item) {
                    item.classList.remove('active', 'completed');
                    if (i < step) item.classList.add('completed');
                    if (i === step) item.classList.add('active');
                }
            }

            // Notify the CDSS system that the step has changed
            document.dispatchEvent(new CustomEvent('cdss:step-changed', {
                detail: { step: step, form: document.getElementById('master-adpie-form') }
            }));

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Initialize to saved step if available
        document.addEventListener('DOMContentLoaded', () => {
            const savedStep = {{ session('current_step', 1) }};
            if (savedStep > 1) {
                // Use a slight timeout to ensure transitions are ready if needed
                setTimeout(() => goToStep(savedStep), 100);
            }
        });
    </script>
@endpush