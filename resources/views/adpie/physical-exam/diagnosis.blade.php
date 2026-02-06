@extends('layouts.app')
@section('title', 'Step 1: Nursing Diagnosis')
@section('content')
    <style>
        /* ============================================
           ANIMATED PROGRESS STEPPER WITH GLASSMORPHISM
           ============================================ */
        .progress-stepper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            margin: 0 auto 3rem;
            max-width: 800px;
            padding: 0;
        }

        /* Background track (the full line) */
        .progress-track {
            position: absolute;
            top: 25px;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(196, 184, 150, 0.25);
            border-radius: 2px;
            z-index: 0;
        }

        /* Active progress line (animated fill) */
        .progress-fill {
            position: absolute;
            top: 25px;
            left: 0;
            height: 4px;
            background: linear-gradient(90deg, #2d554b 0%, #3a6d60 50%, #4a9d7f 100%);
            border-radius: 2px;
            z-index: 1;
            width: 0%;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 0 12px rgba(45, 85, 75, 0.5);
        }

        /* FIXED: Proper progress percentages for 4 steps */
        .progress-fill.step-1 { width: 0%; }          /* At step 1, no progress yet */
        .progress-fill.step-2 { width: 33.33%; }      /* 1/3 of the way (between step 1 and 2) */
        .progress-fill.step-3 { width: 66.66%; }      /* 2/3 of the way (between step 2 and 3) */
        .progress-fill.step-4 { width: 100%; }        /* Complete (reached step 4) */

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        /* Glassmorphism Step Circle */
        .step-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            background: rgba(240, 237, 230, 0.9);
            border: 2px solid rgba(196, 184, 150, 0.5);
            color: #9ca3af;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            animation: scaleIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            animation-fill-mode: both;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Stagger animation delays */
        .step-item:nth-child(1) .step-circle { animation-delay: 0.1s; }
        .step-item:nth-child(2) .step-circle { animation-delay: 0.2s; }
        .step-item:nth-child(3) .step-circle { animation-delay: 0.3s; }
        .step-item:nth-child(4) .step-circle { animation-delay: 0.4s; }

        /* Active step - enhanced glassmorphism with vibrant green */
        .step-item.active .step-circle {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%);
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: white;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4),
                        0 0 20px rgba(16, 185, 129, 0.2),
                        inset 0 1px 2px rgba(255, 255, 255, 0.3);
            transform: scale(1.15);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Completed step - check mark with emerald gradient */
        .step-item.completed .step-circle {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.95) 0%, rgba(16, 185, 129, 0.95) 100%);
            border: 2px solid rgba(255, 255, 255, 0.4);
            color: white;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35),
                        inset 0 1px 2px rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .step-item.completed .step-circle::before {
            content: "check";
            font-family: 'Material Symbols Outlined';
            position: absolute;
            font-size: 22px;
            font-weight: bold;
        }

        /* Hide number when completed */
        .step-item.completed .step-circle {
            font-size: 0;
        }

        .step-label {
            margin-top: 12px;
            font-size: 11px;
            color: #9ca3af;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            transition: all 0.3s ease;
            opacity: 0;
            animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .step-item:nth-child(1) .step-label { animation-delay: 0.2s; }
        .step-item:nth-child(2) .step-label { animation-delay: 0.3s; }
        .step-item:nth-child(3) .step-label { animation-delay: 0.4s; }
        .step-item:nth-child(4) .step-label { animation-delay: 0.5s; }

        .step-item.active .step-label {
            color: #059669;
            font-size: 12px;
            font-weight: 800;
        }

        .step-item.completed .step-label {
            color: #10b981;
        }

        /* GLASSMORPHISM BANNERS - Enhanced Colors */
        .recommendation-banner {
            padding: 0.875rem 1.25rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            position: relative;
            overflow: hidden;
            animation: slideDown 0.4s ease-out;
        }

        .recommendation-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .recommendation-banner:hover::before {
            left: 100%;
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
            transform: translateY(-2px) scale(1.01);
        }

        /* Enhanced Green - Success/Safe */
        .recommendation-banner.alert-green {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9) 0%, rgba(5, 150, 105, 0.95) 100%);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3),
                        inset 0 1px 1px rgba(255, 255, 255, 0.3),
                        0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .recommendation-banner.alert-green:hover {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 1) 100%);
            box-shadow: 0 12px 40px rgba(16, 185, 129, 0.4),
                        inset 0 1px 1px rgba(255, 255, 255, 0.4),
                        0 8px 24px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced Amber - Warning */
        .recommendation-banner.alert-orange {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.9) 0%, rgba(245, 158, 11, 0.95) 100%);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 8px 32px rgba(251, 146, 60, 0.3),
                        inset 0 1px 1px rgba(255, 255, 255, 0.3),
                        0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .recommendation-banner.alert-orange:hover {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.95) 0%, rgba(245, 158, 11, 1) 100%);
            box-shadow: 0 12px 40px rgba(251, 146, 60, 0.4),
                        inset 0 1px 1px rgba(255, 255, 255, 0.4),
                        0 8px 24px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced Red - Critical */
        .recommendation-banner.alert-red {
            background: linear-gradient(135deg, rgba(248, 113, 113, 0.9) 0%, rgba(239, 68, 68, 0.95) 100%);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3),
                        inset 0 1px 1px rgba(255, 255, 255, 0.3),
                        0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .recommendation-banner.alert-red:hover {
            background: linear-gradient(135deg, rgba(248, 113, 113, 0.95) 0%, rgba(239, 68, 68, 1) 100%);
            box-shadow: 0 12px 40px rgba(239, 68, 68, 0.45),
                        inset 0 1px 1px rgba(255, 255, 255, 0.4),
                        0 8px 24px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced Slate - Info */
        .recommendation-banner.alert-info {
            background: linear-gradient(135deg, rgba(148, 163, 184, 0.8) 0%, rgba(100, 116, 139, 0.85) 100%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(100, 116, 139, 0.2),
                        inset 0 1px 1px rgba(255, 255, 255, 0.25),
                        0 4px 16px rgba(0, 0, 0, 0.08);
            cursor: default;
        }

        .recommendation-banner.alert-info:hover {
            transform: none;
            box-shadow: 0 8px 32px rgba(100, 116, 139, 0.2),
                        inset 0 1px 1px rgba(255, 255, 255, 0.25),
                        0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .recommendation-banner.alert-info::before {
            display: none;
        }

        @keyframes subtle-pulse {
            0%, 100% {
                box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3),
                            inset 0 1px 1px rgba(255, 255, 255, 0.3),
                            0 4px 16px rgba(0, 0, 0, 0.1);
            }
            50% {
                box-shadow: 0 12px 48px rgba(239, 68, 68, 0.55),
                            0 0 35px rgba(239, 68, 68, 0.45),
                            inset 0 1px 1px rgba(255, 255, 255, 0.4);
            }
        }

        .recommendation-banner.alert-red {
            animation: slideDown 0.4s ease-out, subtle-pulse 2s ease-in-out infinite;
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
            background: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1),
                        inset 0 1px 1px rgba(255, 255, 255, 0.4);
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
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .banner-subtitle {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.3;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        }

        .banner-action {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1),
                        inset 0 1px 1px rgba(255, 255, 255, 0.2);
        }

        .banner-action:hover {
            background: rgba(255, 255, 255, 0.35);
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15),
                        inset 0 1px 1px rgba(255, 255, 255, 0.3);
        }

        .banner-action .material-symbols-outlined {
            font-size: 1rem;
        }

        .recommendation-banner.hidden {
            display: none;
        }

        .diagnosis-textarea {
            min-height: 450px;
            transition: all 0.3s ease;
        }

        .diagnosis-textarea:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

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
    @php
        // Determine current step based on route or pass it as a variable
        $currentStep = 1; // Default to step 1 (Diagnosis page)
        
        // You can modify this logic based on your routing:
        // if (request()->routeIs('nursing-diagnosis.planning')) $currentStep = 2;
        // if (request()->routeIs('nursing-diagnosis.implementation')) $currentStep = 3;
        // if (request()->routeIs('nursing-diagnosis.evaluation')) $currentStep = 4;
        
        // Or if you pass it as a variable from the controller:
        // $currentStep = $step ?? 1;
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
                {{-- RECOMMENDATION BANNERS --}}
                @if ($component === 'physical-exam')
                    @php
                        $alert = session('physical-exam-alerts')['diagnosis'] ?? null;
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
                                    Type more details in the diagnosis field to receive clinical recommendations
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Active Recommendation Banner --}}
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

                {{-- Diagnosis Input --}}
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
            const textarea = document.getElementById('diagnosis');
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
                    if (patientId) {
                        localStorage.setItem(`diagnosis_draft_${patientId}`, textarea.value);
                    }
                }, 1500);
            });
            
            // Load draft
            const patientId = '{{ $patient->patient_id ?? "" }}';
            if (patientId) {
                const draft = localStorage.getItem(`diagnosis_draft_${patientId}`);
                if (draft && !textarea.value) {
                    textarea.value = draft;
                    updateCharCount();
                }
            }
            
            // Clear draft on submit
            document.querySelector('form').addEventListener('submit', function() {
                if (patientId) {
                    localStorage.removeItem(`diagnosis_draft_${patientId}`);
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