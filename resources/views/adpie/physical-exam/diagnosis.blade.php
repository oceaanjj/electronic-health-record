@extends('layouts.app')
@section('title', 'Step 1: Nursing Diagnosis')
@section('content')
    <style>
        /* Progress Stepper - Matching Your Theme */
        .progress-stepper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            position: relative;
            margin-bottom: 2rem;
            padding: 0 2rem;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
            max-width: 150px;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e8e4d9;
            border: 3px solid #c4b896;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #8b7355;
            font-size: 18px;
            transition: all 0.3s ease;
            z-index: 2;
            position: relative;
        }

        .step-item.active .step-circle {
            background: var(--color-dark-green);
            border-color: var(--color-dark-green);
            color: white;
            box-shadow: 0 4px 15px rgba(45, 85, 75, 0.4);
            transform: scale(1.15);
        }

        .step-item.completed .step-circle {
            background: #4ade80;
            border-color: #22c55e;
            color: white;
        }

        .step-label {
            margin-top: 8px;
            font-size: 11px;
            color: #8b7355;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .step-item.active .step-label {
            color: var(--color-dark-green);
            font-size: 12px;
        }

        /* Connecting Lines */
        .step-connector {
            position: absolute;
            top: 25px;
            left: 50%;
            width: calc(100% - 50px);
            height: 3px;
            background: #c4b896;
            z-index: 1;
        }

        .step-item.completed .step-connector {
            background: #4ade80;
        }

        .step-item:last-child .step-connector {
            display: none;
        }

        /* RECOMMENDATION BANNER (NEW!) */
        .recommendation-banner {
            padding: 0.875rem 1.25rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .recommendation-banner:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .recommendation-banner.alert-green {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border: 2px solid #065f46;
        }

        .recommendation-banner.alert-orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: 2px solid #b45309;
        }

        .recommendation-banner.alert-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: 2px solid #b91c1c;
        }

        .banner-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
        }

        .banner-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            backdrop-filter: blur(4px);
        }

        .banner-icon .material-symbols-outlined {
            font-size: 1.5rem;
            color: white;
        }

        .banner-text {
            flex: 1;
        }

        .banner-title {
            font-weight: 700;
            font-size: 0.875rem;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.125rem;
        }

        .banner-subtitle {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.3;
        }

        .banner-action {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
        }

        .banner-action:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.05);
        }

        .banner-action .material-symbols-outlined {
            font-size: 1rem;
        }

        /* Pulse animation for critical/warning */
        @keyframes subtle-pulse {
            0%, 100% {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
            50% {
                box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4);
            }
        }

        .recommendation-banner.alert-red {
            animation: slideDown 0.4s ease-out, subtle-pulse 2s ease-in-out infinite;
        }

        .recommendation-banner.alert-orange {
            animation: slideDown 0.4s ease-out;
        }

        /* Hide banner state */
        .recommendation-banner.hidden {
            display: none;
        }

        /* Enhanced Textarea */
        .diagnosis-textarea {
            min-height: 450px;
            transition: all 0.3s ease;
        }

        .diagnosis-textarea:focus {
            box-shadow: 0 0 0 3px rgba(45, 85, 75, 0.1);
        }

        /* Character Counter Styling */
        .char-counter {
            position: absolute;
            bottom: 12px;
            right: 15px;
            font-size: 11px;
            color: #8b7355;
            background: rgba(255, 255, 255, 0.95);
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            border: 1px solid #e8e4d9;
        }

        .char-counter.warning {
            color: #f59e0b;
            border-color: #fbbf24;
        }

        .char-counter.danger {
            color: #ef4444;
            border-color: #f87171;
        }

        /* Loading State for Banner */
        .banner-loading {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .banner-loading-spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    {{-- Progress Stepper --}}
    <div class="mx-auto w-[85%] pt-8 pb-4">
        <div class="progress-stepper">
            <div class="step-item active">
                <div class="step-circle">1</div>
                <div class="step-label">Diagnosis</div>
                <div class="step-connector"></div>
            </div>
            <div class="step-item">
                <div class="step-circle">2</div>
                <div class="step-label">Planning</div>
                <div class="step-connector"></div>
            </div>
            <div class="step-item">
                <div class="step-circle">3</div>
                <div class="step-label">Implementation</div>
                <div class="step-connector"></div>
            </div>
            <div class="step-item">
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
        action="{{ route('nursing-diagnosis.storeDiagnosis', ['component' => $component, 'id' => $physicalExamId]) }}"
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
                {{-- RECOMMENDATION BANNER (NEW!) --}}
                @if ($component === 'physical-exam')
                    @php
                        $alert = session('physical-exam-alerts')['diagnosis'] ?? null;
                        $level = $alert->level ?? 'INFO';
                        $message = $alert->message ?? null;
                        
                        $colorClass = 'alert-green';
                        $levelIcon = 'info';
                        $levelText = 'Information';
                        
                        if ($level === 'CRITICAL') {
                            $colorClass = 'alert-red';
                            $levelIcon = 'error';
                            $levelText = 'Critical Alert';
                        } elseif ($level === 'WARNING') {
                            $colorClass = 'alert-orange';
                            $levelIcon = 'warning';
                            $levelText = 'Warning';
                        }

                        // Extract short preview from message (first 60 chars)
                        $preview = $message ? Str::limit(strip_tags($message), 60) : 'Start typing to get recommendations...';
                    @endphp

                    <div
                        id="recommendation-banner"
                        class="recommendation-banner {{ $colorClass }} {{ $message ? '' : 'hidden' }}"
                        data-alert-for="diagnosis"
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

                {{-- Diagnosis Input (Now Full Width!) --}}
                <div class="w-full overflow-hidden rounded-[15px] shadow-md">
                    <div class="main-header flex items-center justify-between rounded-t-lg px-6 py-3 text-white">
                        <span class="font-bold">DIAGNOSIS</span>
                        <span class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-sm">
                            STEP 1 of 4
                        </span> 
                    </div>
                    
                    <div class="relative bg-beige">
                        <textarea
                            id="diagnosis"
                            name="diagnosis"
                            class="notepad-lines font-typewriter cdss-input diagnosis-textarea w-full rounded-b-lg shadow-sm"
                            data-field-name="diagnosis"
                            style="border-top: none"
                            placeholder="Enter nursing diagnosis here..."
                            maxlength="2000"
                        >{{ old('diagnosis', $diagnosis->diagnosis ?? '') }}</textarea>

                        <div class="char-counter" id="char-counter">
                            <span id="char-count">0</span> / 2000
                        </div>
                    </div>

                    @error('diagnosis')
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
                    <a href="{{ route('physical-exam.index') }}" class="button-default text-center">
                        GO BACK
                    </a>
                </div>

                <div class="flex flex-row items-center justify-end space-x-3">
                    <button type="submit" name="action" value="save_and_exit" class="button-default">
                        SUBMIT
                    </button>
                    <button type="submit" name="action" value="save_and_proceed" class="button-default">
                        PLANNING
                    </button>
                </div>
            </div>
        </fieldset>
    </form>
@endsection

@push('scripts')
    @vite(['resources/js/adpie-alert.js'])
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('diagnosis');
            const charCount = document.getElementById('char-count');
            const charCounter = document.getElementById('char-counter');
            const banner = document.getElementById('recommendation-banner');
            
            // Character counter with color warnings
            function updateCharCount() {
                const count = textarea.value.length;
                charCount.textContent = count;
                
                // Remove existing classes
                charCounter.classList.remove('warning', 'danger');
                
                if (count > 1800) {
                    charCounter.classList.add('danger');
                } else if (count > 1500) {
                    charCounter.classList.add('warning');
                }
            }
            
            textarea.addEventListener('input', updateCharCount);
            updateCharCount(); // Initial count
            
            // Auto-save draft to localStorage
            let saveTimeout;
            textarea.addEventListener('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    const patientId = '{{ $patient->patient_id ?? "" }}';
                    if (patientId) {
                        localStorage.setItem(`diagnosis_draft_${patientId}`, textarea.value);
                    }
                }, 1500);
            });
            
            // Load draft on page load
            const patientId = '{{ $patient->patient_id ?? "" }}';
            if (patientId) {
                const draft = localStorage.getItem(`diagnosis_draft_${patientId}`);
                if (draft && !textarea.value) {
                    textarea.value = draft;
                    updateCharCount();
                }
            }
            
            // Clear draft on successful submit
            document.querySelector('form').addEventListener('submit', function() {
                if (patientId) {
                    localStorage.removeItem(`diagnosis_draft_${patientId}`);
                }
            });

            // Keyboard shortcut to expand recommendations (Ctrl/Cmd + R)
            document.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                    if (banner && !banner.classList.contains('hidden')) {
                        e.preventDefault();
                        openRecommendationModal(banner);
                    }
                }
            });
        });

        // Open recommendation modal from banner
        window.openRecommendationModal = function(bannerElement) {
            // Try to get data from dataset first (set by JavaScript during live typing)
            let fullMessage = bannerElement.dataset.fullMessage;
            let levelText = bannerElement.dataset.levelText;
            let levelIcon = bannerElement.dataset.levelIcon;
            let levelIconColor = bannerElement.dataset.levelIconColor;
            
            // Fallback to reading from DOM elements (for initial page load)
            if (!fullMessage) {
                const subtitleElement = bannerElement.querySelector('.banner-subtitle');
                fullMessage = subtitleElement?.dataset.fullMessage;
            }
            
            if (!levelText) {
                const titleElement = bannerElement.querySelector('.banner-title');
                levelText = titleElement?.textContent || 'Recommendation';
            }
            
            // Determine level icon if not already set
            if (!levelIcon || !levelIconColor) {
                if (levelText.toLowerCase().includes('critical')) {
                    levelIcon = 'error';
                    levelIconColor = '#ef4444';
                } else if (levelText.toLowerCase().includes('warning')) {
                    levelIcon = 'warning';
                    levelIconColor = '#f59e0b';
                } else {
                    levelIcon = 'info';
                    levelIconColor = '#059669';
                }
            }
            
            if (!fullMessage) {
                console.error('No message available to display in modal');
                return;
            }
            
            const overlay = document.createElement('div');
            overlay.className = 'alert-modal-overlay fade-in';

            const modal = document.createElement('div');
            modal.className = 'alert-modal fade-in';
            modal.innerHTML = `
                <button class="close-btn" aria-label="Close">&times;</button>
                
                <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: ${levelIconColor}15; display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined" style="color: ${levelIconColor}; font-size: 1.75rem;">${levelIcon}</span>
                    </div>
                    <div>
                        <h2 style="margin: 0; font-size: 1.5rem;">${levelText}</h2>
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem; color: #6b7280;">Clinical Decision Support</p>
                    </div>
                </div>
                
                <div class="modal-content-scroll" style="max-height: 400px; overflow-y: auto; padding-right: 0.5rem; margin-top: 1.5rem;">
                    ${fullMessage}
                </div>
                
                <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; color: #6b7280;">
                        💡 Press <kbd style="padding: 2px 6px; background: #f3f4f6; border-radius: 4px; font-family: monospace;">ESC</kbd> to close
                    </span>
                    <button class="close-action-btn" style="padding: 0.5rem 1rem; background: var(--color-dark-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
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
            
            // ESC key to close
            const escHandler = (e) => {
                if (e.key === 'Escape') close();
            };
            document.addEventListener('keydown', escHandler);

            // Focus the close button
            const closeBtn = modal.querySelector('.close-btn');
            if (closeBtn) closeBtn.focus();

            // Hover effect for action button
            const actionBtn = modal.querySelector('.close-action-btn');
            actionBtn.addEventListener('mouseenter', () => {
                actionBtn.style.transform = 'scale(1.05)';
                actionBtn.style.boxShadow = '0 4px 12px rgba(45, 85, 75, 0.3)';
            });
            actionBtn.addEventListener('mouseleave', () => {
                actionBtn.style.transform = 'scale(1)';
                actionBtn.style.boxShadow = 'none';
            });
        };
    </script>
@endpush