@extends('layouts.app')
@section('title', 'Step 3: Intervention')
@section('content')

    @php
        // Determine current step based on route or pass it as a variable
        $currentStep = 3; // Step 3 (Intervention page)
    @endphp
    
    <div class="mx-auto w-[85%] pt-8 pb-4">
        <div class="progress-stepper">
            {{-- Background track --}}
            <div class="progress-track"></div>
            {{-- Animated progress fill - dynamically set step class --}}
            <div class="progress-fill step-{{ $currentStep }}"></div>
            
            <div class="step-item {{ $currentStep >= 1 ? ($currentStep == 1 ? 'active' : 'completed') : '' }}">
                <div class="step-circle">1</div>
                <div class="step-label">Diagnosis</div>
            </div>
            <div class="step-item {{ $currentStep >= 2 ? ($currentStep == 2 ? 'active' : 'completed') : '' }}">
                <div class="step-circle">2</div>
                <div class="step-label">Planning</div>
            </div>
            <div class="step-item {{ $currentStep >= 3 ? ($currentStep == 3 ? 'active' : 'completed') : '' }}">
                <div class="step-circle">3</div>
                <div class="step-label">Intervention</div>
            </div>
            <div class="step-item {{ $currentStep >= 4 ? ($currentStep == 4 ? 'active' : 'completed') : '' }}">
                <div class="step-circle">4</div>
                <div class="step-label">Evaluation</div>
            </div>
        </div>
    </div>

    {{-- Patient Header --}}
    <div class="header mx-auto my-6 flex w-[70%] items-center gap-4">
        <label for="patient_search_input" class="font-alte text-dark-green font-bold whitespace-nowrap">
            PATIENT NAME :
        </label>
        <div class="relative w-[400px]">
            <input
                type="text"
                id="patient_search_input"
                value="{{ trim($patient->name ?? '') }}"
                readonly
                class="font-creato-bold w-full rounded-full border border-gray-300 bg-gray-100 px-4 py-2 text-[15px] shadow-sm outline-none"
            />
        </div>
    </div>

    {{-- Main Form --}}
    <form
        action="{{ route('nursing-diagnosis.storeIntervention', ['component' => $component, 'nursingDiagnosisId' => $diagnosis->id]) }}"
        method="POST"
        class="cdss-form flex h-full flex-col"
        data-analyze-url="{{ route('nursing-diagnosis.analyze-field') }}"
        data-batch-analyze-url="{{ route('nursing-diagnosis.analyze-batch-field') }}"
        data-patient-id="{{ $patient->patient_id }}"
        data-component="{{ $component }}"
    >
        @csrf

        <fieldset>
            <div class="mx-auto mt-2 w-[70%]">
                {{-- RECOMMENDATION BANNERS --}}
                @if ($component === 'intake-and-output')
                    @php
                        $alert = session('intake-and-output-alerts')['intervention'] ?? null;
                        $level = $alert->level ?? 'INFO';
                        $message = $alert->message ?? null;
                        
                        $colorClass = 'alert-green';
                        $levelIcon = 'info';
                        $levelText = 'Clinical Decision Support';
                        
                        if ($level === 'CRITICAL') {
                            $colorClass = 'alert-red';
                            $levelIcon = 'error';
                            $levelText = 'Critical Alert';
                        } elseif ($level === 'WARNING') {
                            $colorClass = 'alert-orange';
                            $levelIcon = 'warning';
                            $levelText = 'Warning';
                        }

                        $preview = $message ? Str::limit(strip_tags($message), 60) : '';
                    @endphp

                    {{-- No Recommendation State --}}
                    <div
                        id="no-recommendation-banner"
                        class="recommendation-banner alert-info {{ $message ? 'hidden' : '' }}"
                    >
                        <div class="banner-content">
                            <div class="banner-icon">
                                <span class="material-symbols-outlined">edit_note</span>
                            </div>
                            <div class="banner-text">
                                <div class="banner-title">No Recommendations Yet</div>
                                <div class="banner-subtitle">
                                    Type more details in the intervention field to receive clinical recommendations
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Active Recommendation Banner --}}
                    <div
                        id="recommendation-banner"
                        class="recommendation-banner {{ $colorClass }} {{ $message ? '' : 'hidden' }}"
                        data-alert-for="intervention"
                        onclick="openRecommendationModal(this)"
                    >
                        <div class="banner-content">
                            <div class="banner-icon">
                                <span class="material-symbols-outlined">{{ $levelIcon }}</span>
                            </div>
                            <div class="banner-text">
                                <div class="banner-title">{{ $levelText }}</div>
                                <div class="banner-subtitle" data-full-message="{!! htmlspecialchars($message ?? '') !!}">
                                    {{ $preview }}
                                </div>
                            </div>
                        </div>
                        <div class="banner-action">
                            <span>View Details</span>
                            <span class="material-symbols-outlined">arrow_forward</span>
                        </div>
                    </div>
                @endif

                {{-- Intervention Input --}}
                <div class="w-full overflow-hidden rounded-[15px] shadow-md">
                    <div class="main-header flex items-center justify-between rounded-t-lg px-6 py-3 text-white">
                        <span class="font-bold">INTERVENTION</span>
                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-sm">
                            STEP 3 of 4
                        </span> 
                    </div>
                    
                    <div class="relative bg-beige">
                        <textarea
                            id="intervention"
                            name="intervention"
                            class="notepad-lines font-typewriter cdss-input intervention-textarea w-full rounded-b-lg shadow-sm"
                            data-field-name="intervention"
                            style="border-top: none"
                            placeholder="Enter intervention (e.g., Nursing actions, treatments)..."
                            maxlength="2000"
                        >{{ old('intervention', $diagnosis->intervention ?? '') }}</textarea>

                        <div class="char-counter" id="char-counter">
                            <span id="char-count">0</span> / 2000
                        </div>
                    </div>

                    @error('intervention')
                        <div class="mx-4 mb-3 flex items-center gap-2 rounded-lg bg-red-50 p-3 text-sm text-red-600 border border-red-200">
                            <span class="material-symbols-outlined text-red-500">error</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mx-auto mt-8 mb-12 flex w-[70%] items-center justify-between">
                <div class="flex flex-col items-start">
                    <a href="{{ route('nursing-diagnosis.showPlanning', ['component' => $component, 'nursingDiagnosisId' => $diagnosis->id]) }}" class="button-default text-center">
                        GO BACK
                    </a>
                </div>

                <div class="flex flex-row items-center justify-end space-x-3">
                    <button type="submit" name="action" value="save_and_exit" class="button-default">
                        SUBMIT
                    </button>
                    <button type="submit" name="action" value="save_and_proceed" class="button-default">
                        EVALUATION
                    </button>
                </div>
            </div>
        </fieldset>
    </form>
@endsection

@push('scripts')
    @vite(['resources/js/adpie-alert.js'])
    
    <script>
        // Helper function to format message content consistently
        function formatMessageContent(message) {
            if (!message) return '';
            
            // If message already contains HTML list tags, return as is
            if (message.includes('<ul>') || message.includes('<ol>') || message.includes('<li>')) {
                return message;
            }
            
            // Clean the message and split into sentences
            // Handle various separators: newlines, periods, or numbered lists
            let sentences = [];
            
            // Check if it looks like a numbered/bulleted list (has multiple lines starting with numbers or bullets)
            const lines = message.split('\n').map(line => line.trim()).filter(line => line.length > 0);
            
            if (lines.length > 1) {
                // It's a multi-line format, treat each line as a separate item
                sentences = lines.map(line => {
                    // Remove leading numbers, bullets, or dashes
                    return line.replace(/^[\d\-\*\•]+[\.\):\s]*/, '').trim();
                }).filter(s => s.length > 0);
            } else {
                // Single paragraph - split by periods
                sentences = message
                    .split(/\.\s+/)
                    .map(s => s.trim())
                    .filter(s => s.length > 0);
            }
            
            // If we have multiple sentences, format as bullet list
            if (sentences.length > 1) {
                const listItems = sentences.map(sentence => {
                    // Add period back if it doesn't end with punctuation
                    const formatted = sentence.match(/[.!?]$/) ? sentence : sentence + '.';
                    return `<li style="margin-bottom: 0.5rem; line-height: 1.6;">${formatted}</li>`;
                }).join('');
                
                return `<ul style="margin: 0; padding-left: 1.5rem; list-style-type: disc;">${listItems}</ul>`;
            }
            
            // Single sentence - return as paragraph
            const formatted = message.match(/[.!?]$/) ? message : message + '.';
            return `<p style="margin: 0; line-height: 1.6;">${formatted}</p>`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('intervention');
            const charCount = document.getElementById('char-count');
            const charCounter = document.getElementById('char-counter');
            const recommendationBanner = document.getElementById('recommendation-banner');
            const noRecommendationBanner = document.getElementById('no-recommendation-banner');
            
            // Character counter
            function updateCharCount() {
                const count = textarea.value.length;
                charCount.textContent = count;
                charCounter.classList.remove('warning', 'danger');
                if (count > 1800) {
                    charCounter.classList.add('danger');
                } else if (count > 1500) {
                    charCounter.classList.add('warning');
                }
            }
            
            textarea.addEventListener('input', updateCharCount);
            updateCharCount();
            
            // Auto-save draft
            let saveTimeout;
            textarea.addEventListener('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    const patientId = '{{ $patient->patient_id ?? "" }}';
                    const component = '{{ $component }}';
                    if (patientId && component) {
                        localStorage.setItem(`intervention_draft_${component}_${patientId}`, textarea.value);
                    }
                }, 1500);
            });
            
            // Load draft
            const patientId = '{{ $patient->patient_id ?? "" }}';
            const component = '{{ $component }}';
            if (patientId && component) {
                const draft = localStorage.getItem(`intervention_draft_${component}_${patientId}`);
                if (draft && !textarea.value) {
                    textarea.value = draft;
                    updateCharCount();
                }
            }
            
            // Clear draft on submit
            document.querySelector('form').addEventListener('submit', function() {
                if (patientId && component) {
                    localStorage.removeItem(`intervention_draft_${component}_${patientId}`);
                }
            });

            // Keyboard shortcut (Ctrl/Cmd + R)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                    if (recommendationBanner && !recommendationBanner.classList.contains('hidden')) {
                        e.preventDefault();
                        openRecommendationModal(recommendationBanner);
                    }
                }
            });
        });

        // Modal function
        window.openRecommendationModal = function(bannerElement) {
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
    </script>
@endpush