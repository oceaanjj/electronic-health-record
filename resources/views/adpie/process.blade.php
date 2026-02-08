@extends('layouts.app')
@section('title', 'Nursing Process: ADPIE')

@section('content')
<style>
    /* 1. FIXED HEADER & STEPPER */
    .stepper-header {
        position: sticky;
        top: 0;
        /* background: rgba(255, 255, 255, 0.98); */
        /* backdrop-filter: blur(10px); */
        z-index: -100;
        padding-bottom: 1rem;
        /* border-bottom: 1px solid rgba(0,0,0,0.05); */
    }

    /* 2. SLIDER MECHANICS */
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
        padding: 0 5%;
        flex-shrink: 0;
        transition: opacity 0.4s ease;
    }

    .adpie-slide:not(.active-slide) {
        opacity: 0;
        pointer-events: none;
        height: 0;
        overflow: hidden;
    }

    .active-slide { opacity: 1; height: auto; }

    .adpie-textarea {
        min-height: 450px;
        width: 100%;
        border-radius: 0 0 15px 15px;
    }
</style>

<div class="stepper-header">
    <div class="mx-auto w-[85%] pt-8">
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

    <div class="header mx-auto mt-6 flex w-[70%] items-center gap-4">
        <label class="font-alte text-dark-green font-bold whitespace-nowrap">PATIENT NAME :</label>
        <div class="relative w-[400px]">
            <input type="text" value="{{ trim($patient->name ?? '') }}" readonly class="font-creato-bold w-full rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-[15px] shadow-sm outline-none" />
        </div>
    </div>
</div>

<form id="master-adpie-form" action="{{ route('nursing-diagnosis.storeEvaluation', ['component' => $component, 'nursingDiagnosisId' => $diagnosis->id ?? 0]) }}" method="POST" class="cdss-form mt-4">
    @csrf
    <div class="adpie-slider-container z-9999">
        <div id="slider-wrapper" class="adpie-slider-wrapper">

            <div class="adpie-slide active-slide" id="slide-1">
                @include('adpie.partials._diagnosis_step')
                <div class="mx-auto mt-8 mb-12 flex w-[70%] justify-between">
                    <a href="{{ route($component . '.index') }}" class="button-default">EXIT CDSS</a>
                    <button type="button" onclick="goToStep(2)" class="button-default">NEXT: PLANNING</button>
                </div>
            </div>

            <div class="adpie-slide" id="slide-2">
                @include('adpie.partials._planning_step')
                <div class="mx-auto mt-8 mb-12 flex w-[70%] justify-between">
                    <button type="button" onclick="goToStep(1)" class="button-default">BACK</button>
                    <button type="button" onclick="goToStep(3)" class="button-default">NEXT: INTERVENTION</button>
                </div>
            </div>

            <div class="adpie-slide" id="slide-3">
                @include('adpie.partials._intervention_step')
                <div class="mx-auto mt-8 mb-12 flex w-[70%] justify-between">
                    <button type="button" onclick="goToStep(2)" class="button-default">BACK</button>
                    <button type="button" onclick="goToStep(4)" class="button-default">NEXT: EVALUATION</button>
                </div>
            </div>

            <div class="adpie-slide" id="slide-4">
                @include('adpie.partials._evaluation_step')
                <div class="mx-auto mt-8 mb-12 flex w-[70%] justify-between">
                    <button type="button" onclick="goToStep(3)" class="button-default">BACK</button>
                    <button type="submit" name="action" value="save_and_finish" class="button-default">FINISH & SAVE</button>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
    @vite(['resources/js/adpie-alert.js'])

    <script>
        /**
         * 1. NAVIGATION & SLIDING LOGIC
         */
        function goToStep(step) {
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
                item.classList.remove('active', 'completed');
                if (i < step) item.classList.add('completed');
                if (i === step) item.classList.add('active');
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        /**
         * 2. MODAL & RECOMMENDATION LOGIC
         */
        function formatMessageContent(message) {
            if (!message) return '';

            // If message already contains HTML list tags, return as is
            if (message.includes('<ul>') || message.includes('<ol>') || message.includes('<li>')) {
                return message;
            }

            // Clean the message and split into sentences
            let sentences = [];

            // Check if it looks like a numbered/bulleted list
            const lines = message
                .split('\n')
                .map((line) => line.trim())
                .filter((line) => line.length > 0);

            if (lines.length > 1) {
                // It's a multi-line format, treat each line as a separate item
                sentences = lines
                    .map((line) => {
                        return line.replace(/^[\d\-\*\•]+[\.\):\s]*/, '').trim();
                    })
                    .filter((s) => s.length > 0);
            } else {
                // Single paragraph - split by periods
                sentences = message
                    .split(/\.\s+/)
                    .map((s) => s.trim())
                    .filter((s) => s.length > 0);
            }

            // If we have multiple sentences, format as bullet list
            if (sentences.length > 1) {
                const listItems = sentences
                    .map((sentence) => {
                        const formatted = sentence.match(/[.!?]$/) ? sentence : sentence + '.';
                        return `<li style="margin-bottom: 0.5rem; line-height: 1.6;">${formatted}</li>`;
                    })
                    .join('');

                return `<ul style="margin: 0; padding-left: 1.5rem; list-style-type: disc;">${listItems}</ul>`;
            }

            // Single sentence - return as paragraph
            const formatted = message.match(/[.!?]$/) ? message : message + '.';
            return `<p style="margin: 0; line-height: 1.6;">${formatted}</p>`;
        }

        window.openRecommendationModal = function (bannerElement) {
            let fullMessage = bannerElement.dataset.fullMessage;
            let levelText = bannerElement.dataset.levelText;
            let levelIcon = bannerElement.dataset.levelIcon;
            let levelIconColor = bannerElement.dataset.levelIconColor;

            if (!fullMessage) {
                const subtitleElement = bannerElement.querySelector('.banner-subtitle');
                fullMessage = subtitleElement?.dataset.fullMessage;
            }

            if (!levelText) {
                const titleElement = bannerElement.querySelector('.banner-title');
                levelText = titleElement?.textContent || 'Recommendation';
            }

            if (!levelIcon || !levelIconColor) {
                if (levelText.toLowerCase().includes('critical')) {
                    levelIcon = 'error';
                    levelIconColor = '#ef4444';
                } else if (levelText.toLowerCase().includes('warning')) {
                    levelIcon = 'warning';
                    levelIconColor = '#f59e0b';
                } else {
                    levelIcon = 'info';
                    levelIconColor = '#10b981';
                }
            }

            if (!fullMessage) {
                console.error('No message available');
                return;
            }

            // Format the message content consistently
            const formattedMessage = formatMessageContent(fullMessage);

            const overlay = document.createElement('div');
            overlay.className = 'alert-modal-overlay fade-in';

            const modal = document.createElement('div');
            modal.className = 'alert-modal fade-in';
            modal.innerHTML = `
                <button class="close-btn" aria-label="Close">×</button>

                <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: ${levelIconColor}15; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="color: ${levelIconColor}; font-size: 1.75rem;">${levelIcon}</span>
                    </div>
                    <div>
                        <h2 style="margin: 0; font-size: 1.5rem;">${levelText}</h2>
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; color: #6b7280;">Recommendation</p>
                    </div>
                </div>

                <div class="modal-content-scroll" style="max-height: 400px; overflow-y: auto; padding-right: 0.5rem; margin-top: 1.5rem;">
                    ${formattedMessage}
                </div>

                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; color: #6b7280;">
                        💡 Press <kbd style="padding: 2px 6px; background: #f3f4f6; border-radius: 4px; font-family: monospace;">ESC</kbd> to close
                    </span>
                    <button class="close-action-btn" style="padding: 0.625rem 1.5rem; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.875rem; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);">
                        Got it
                    </button>
                </div>
            `;

            overlay.appendChild(modal);
            document.body.appendChild(overlay);

            const close = () => {
                overlay.remove();
                document.removeEventListener('keydown', escHandler);
            };

            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) close();
            });

            modal.querySelector('.close-btn').addEventListener('click', close);
            modal.querySelector('.close-action-btn').addEventListener('click', close);

            const escHandler = (e) => {
                if (e.key === 'Escape') close();
            };
            document.addEventListener('keydown', escHandler);

            const closeBtn = modal.querySelector('.close-btn');
            if (closeBtn) closeBtn.focus();

            const actionBtn = modal.querySelector('.close-action-btn');
            actionBtn.addEventListener('mouseenter', () => {
                actionBtn.style.transform = 'translateY(-2px)';
                actionBtn.style.boxShadow = '0 4px 16px rgba(16, 185, 129, 0.4)';
                actionBtn.style.filter = 'brightness(1.1)';
            });
            actionBtn.addEventListener('mouseleave', () => {
                actionBtn.style.transform = 'translateY(0)';
                actionBtn.style.boxShadow = '0 2px 8px rgba(16, 185, 129, 0.25)';
                actionBtn.style.filter = 'brightness(1)';
            });
            actionBtn.addEventListener('mousedown', () => {
                actionBtn.style.transform = 'translateY(0) scale(0.95)';
            });
            actionBtn.addEventListener('mouseup', () => {
                actionBtn.style.transform = 'translateY(-2px) scale(1)';
            });
        };

        /**
         * 3. INITIALIZATION (Character Counters & Drafts)
         */
        document.addEventListener('DOMContentLoaded', function () {
            const steps = ['diagnosis', 'planning', 'intervention', 'evaluation'];
            const patientId = '{{ $patient->patient_id ?? '' }}';

            steps.forEach(id => {
                const textarea = document.getElementById(id);
                const charCount = document.getElementById(`char-count-${id}`);
                const charCounter = document.getElementById(`char-counter-${id}`);

                if (textarea) {
                    // Character count update
                    const updateCount = () => {
                        const count = textarea.value.length;
                        if (charCount) charCount.textContent = count;
                        if (charCounter) {
                            charCounter.classList.toggle('danger', count > 1800);
                            charCounter.classList.toggle('warning', count > 1500 && count <= 1800);
                        }
                    };
                    textarea.addEventListener('input', updateCount);
                    updateCount();

                    // Load drafts
                    if (patientId) {
                        const draft = localStorage.getItem(`${id}_draft_${patientId}`);
                        if (draft && !textarea.value) {
                            textarea.value = draft;
                            updateCount();
                        }
                        // Save drafts
                        let saveTimeout;
                        textarea.addEventListener('input', () => {
                            clearTimeout(saveTimeout);
                            saveTimeout = setTimeout(() => {
                                localStorage.setItem(`${id}_draft_${patientId}`, textarea.value);
                            }, 1000);
                        });
                    }
                }
            });

            // Clear drafts on submit
            document.getElementById('master-adpie-form').addEventListener('submit', () => {
                steps.forEach(id => localStorage.removeItem(`${id}_draft_${patientId}`));
            });
        });
    </script>
@endpush