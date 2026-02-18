@extends('layouts.app')

@section('title', 'Diagnostics')

@section('content')
    <style>
        /* --- GLOBAL & UTILITIES --- */
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --danger-color: #ef4444;
            --danger-hover: #dc2626;
            --success-color: #10b981;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
            --glass-shadow: rgba(0, 0, 0, 0.1);
        }

        #form-content-container {
            margin: 2rem auto;
            max-width: 90%;
            width: 90%;
            position: relative;
        }

        /* --- GRID --- */
        .diagnostic-grid {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }

        /* --- GLASSMORPHISM CARD PANEL --- */
        .diagnostic-panel {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(30px) saturate(200%);
            -webkit-backdrop-filter: blur(30px) saturate(200%);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow:
                0 8px 32px 0 rgba(0, 0, 0, 0.08),
                0 2px 8px 0 rgba(0, 0, 0, 0.04),
                inset 0 1px 1px 0 rgba(255, 255, 255, 0.6),
                inset 0 -1px 1px 0 rgba(0, 0, 0, 0.02);
            overflow: hidden;
            transition:
                transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                border-color 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Subtle shine effect on hover */
        .diagnostic-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s ease;
            z-index: 1;
            pointer-events: none;
        }

        .diagnostic-panel:hover::before {
            left: 150%;
        }

        .diagnostic-panel:hover {
            transform: translateY(-8px) scale(1.01);
            border-color: rgba(255, 255, 255, 0.6);
            box-shadow:
                0 16px 48px 0 rgba(0, 0, 0, 0.12),
                0 4px 16px 0 rgba(0, 0, 0, 0.08),
                inset 0 2px 2px 0 rgba(255, 255, 255, 0.7),
                inset 0 -1px 1px 0 rgba(0, 0, 0, 0.03);
        }

        /* --- HEADER AREA (Glassmorphism) --- */
        .panel-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(249, 250, 251, 0.2) 100%);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 1.5rem 1.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            position: relative;
            overflow: hidden;
        }

        /* Subtle gradient accent bar with gray tones */
        .panel-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(
                90deg,
                rgba(156, 163, 175, 0.3) 0%,
                rgba(209, 213, 219, 0.5) 50%,
                rgba(156, 163, 175, 0.3) 100%
            );
        }

        /* Add subtle light reflection at top */
        .panel-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.15) 0%, transparent 100%);
            pointer-events: none;
        }

        .panel-header h2 {
            color: #1f2937;
            font-weight: 700;
            font-size: 1.125rem;
            margin: 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 1rem;
            text-shadow:
                0 1px 2px rgba(255, 255, 255, 0.9),
                0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 1;
        }

        /* --- UPLOAD AREA (Enhanced Glassmorphism) --- */
        .panel-upload-area {
            position: relative;
            padding: 2rem;
            border: 2px dashed rgba(156, 163, 175, 0.35);
            border-radius: 18px;
            margin: 1.5rem;
            cursor: pointer;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(249, 250, 251, 0.15) 100%);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-grow: 1;
            min-height: 280px;
            overflow: hidden;
        }

        /* Glossy border effect */
        .panel-upload-area::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 18px;
            padding: 2px;
            background: linear-gradient(
                135deg,
                rgba(255, 255, 255, 0.4),
                rgba(209, 213, 219, 0.3),
                rgba(255, 255, 255, 0.4)
            );
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        /* Inner glow effect */
        .panel-upload-area::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 18px;
            box-shadow: inset 0 1px 3px rgba(255, 255, 255, 0.5);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .panel-upload-area:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(249, 250, 251, 0.25) 100%);
            border-color: rgba(156, 163, 175, 0.5);
            transform: scale(1.005);
            box-shadow:
                0 4px 16px rgba(0, 0, 0, 0.06),
                inset 0 1px 2px rgba(255, 255, 255, 0.6);
        }

        .panel-upload-area:hover::before,
        .panel-upload-area:hover::after {
            opacity: 1;
        }

        /* Drag-over state with gray tones */
        .panel-upload-area.drag-over {
            background: linear-gradient(135deg, rgba(243, 244, 246, 0.4) 0%, rgba(229, 231, 235, 0.3) 100%);
            border-color: rgba(156, 163, 175, 0.7);
            border-style: solid;
            box-shadow:
                inset 0 0 30px rgba(156, 163, 175, 0.15),
                0 0 20px rgba(156, 163, 175, 0.2),
                inset 0 2px 4px rgba(255, 255, 255, 0.5);
            transform: scale(1.01);
        }

        /* Upload content wrapper */
        .upload-content-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            flex-grow: 1;
        }

        /* Prompt for uploading */
        .upload-prompt {
            text-align: center;
            color: #6b7280;
            pointer-events: none;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .upload-icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 1rem;
            color: #9ca3af;
            stroke-width: 1.5;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .upload-prompt p {
            font-size: 0.95rem;
            font-weight: 500;
            margin: 0;
            color: #4b5563;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        .upload-prompt strong {
            color: #4b5563;
            font-weight: 700;
            position: relative;
        }

        .upload-prompt strong::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(107, 114, 128, 0.5), transparent);
        }

        /* "Accepted types" text at the bottom */
        .upload-accepted-types {
            display: block;
            text-align: center;
            font-size: 0.8rem;
            color: #6b7280;
            width: 100%;
            padding-top: 1rem;
            pointer-events: none;
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        /* --- PREVIEW GRID --- */
        .preview-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
            justify-content: center;
        }

        .preview-item {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 14px;
            border: 2px solid rgba(255, 255, 255, 0.5);
            box-shadow:
                0 4px 16px rgba(0, 0, 0, 0.12),
                0 2px 8px rgba(0, 0, 0, 0.08),
                inset 0 1px 1px rgba(255, 255, 255, 0.4),
                inset 0 -1px 1px rgba(0, 0, 0, 0.05);
            transition:
                transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                box-shadow 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .preview-item img:hover {
            transform: scale(1.08) translateY(-4px);
            box-shadow:
                0 12px 24px rgba(0, 0, 0, 0.18),
                0 6px 12px rgba(0, 0, 0, 0.12),
                inset 0 2px 2px rgba(255, 255, 255, 0.5),
                inset 0 -1px 1px rgba(0, 0, 0, 0.08);
        }

        .delete-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            width: 28px;
            height: 28px;
            font-size: 16px;
            font-weight: bold;
            line-height: 24px;
            text-align: center;
            cursor: pointer;
            box-shadow:
                0 4px 10px rgba(239, 68, 68, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            transition: all 0.2s ease;
            backdrop-filter: blur(5px);
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: scale(1.15) rotate(90deg);
            box-shadow:
                0 6px 15px rgba(239, 68, 68, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        /* --- UPLOADED FILES TITLE --- */
        .uploaded-title {
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 600;
            border-bottom: 2px solid rgba(156, 163, 175, 0.3);
            padding-bottom: 8px;
            font-size: 0.9rem;
            width: 100%;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        /* --- FOOTER BUTTON AREA (Enhanced Glassmorphism) --- */
        .panel-footer {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(249, 250, 251, 0.2) 100%);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 1.25rem 1.75rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.4);
            position: relative;
        }

        /* Top highlight */
        .panel-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
        }

        .clear-btn {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.85), rgba(220, 38, 38, 0.85));
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 9px 22px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow:
                0 4px 12px rgba(239, 68, 68, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.2),
                inset 0 -1px 0 rgba(0, 0, 0, 0.1);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .clear-btn:hover {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.95), rgba(185, 28, 28, 0.95));
            transform: translateY(-2px);
            box-shadow:
                0 6px 20px rgba(239, 68, 68, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.3),
                inset 0 -1px 0 rgba(0, 0, 0, 0.15);
        }

        .clear-btn:active {
            transform: translateY(0);
            box-shadow:
                0 2px 8px rgba(239, 68, 68, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .clear-btn[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
            background: rgba(209, 213, 219, 0.5);
            box-shadow: none;
            transform: none;
        }

        /* --- FILE INPUT HIDDEN --- */
        .file-input {
            display: none;
        }

        /* --- PATIENT HEADER (Glassmorphism) --- */
        .patient-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            padding: 1.5rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            border: 1px solid var(--glass-border);
            box-shadow:
                0 8px 32px 0 rgba(31, 38, 135, 0.15),
                inset 0 1px 0 0 rgba(255, 255, 255, 0.5);
        }

        .patient-header h2 {
            color: #4b5563;
            font-weight: 400;
            font-size: 1rem;
            margin: 0;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        .patient-header strong {
            color: #1f2937;
            font-weight: 600;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .diagnostic-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div
        id="form-content-container"
        data-csrf-token="{{ csrf_token() }}"
        data-patient-id="{{ $selectedPatient->patient_id ?? '' }}"
        data-delete-all-url-template="{{ route('diagnostics.destroy-all', ['type' => '__TYPE__', 'patient_id' => '__PATIENT_ID__']) }}"
    >
        {{-- DIAGNOSTICS PATIENT SELECTION --}}
        <div class="mx-auto w-full px-4">
            <div class="ml-5 flex flex-wrap items-center gap-x-10 gap-y-4">
                {{-- 1. PATIENT SECTION --}}
                <div class="flex items-center gap-4">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

                    <div class="w-[350px]">
                        <x-searchable-patient-dropdown
                            :patients="$patients"
                            :selectedPatient="$selectedPatient"
                            :selectRoute="route('diagnostics.select')"
                            :inputValue="$selectedPatient?->patient_id ?? ''"
                            inputPlaceholder="Search or type Patient Name..."
                            inputName="patient_id"
                        />
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('diagnostics.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input
                type="hidden"
                name="patient_id"
                value="{{ $selectedPatient ? $selectedPatient->patient_id : '' }}"
            />

            <fieldset {{ ! $selectedPatient ? 'disabled' : '' }}>
                <div class="diagnostic-grid">
                    @php
                        $types = [
                            'xray' => 'X-Ray',
                            'ultrasound' => 'Ultrasound',
                            'ct_scan' => 'CT Scan',
                            'echocardiogram' => 'Echocardiogram',
                        ];
                    @endphp

                    @foreach ($types as $key => $label)
                        <div
                            class="diagnostic-panel"
                            data-type="{{ $key }}"
                            data-uploaded-image-ids="{{ json_encode($selectedPatient && isset($images[$key]) ? $images[$key]->pluck('id')->toArray() : []) }}"
                        >
                            <div class="panel-header">
                                <h2>{{ $label }}</h2>
                            </div>

                            <label class="panel-upload-area" for="file-input-{{ $key }}" data-type="{{ $key }}">
                                <div class="upload-content-wrapper">
                                    <div class="upload-prompt" id="prompt-{{ $key }}">
                                        <svg
                                            class="upload-icon"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.5"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"
                                            />
                                        </svg>
                                        <p>
                                            Drop files here or
                                            <strong>click to browse</strong>
                                        </p>
                                    </div>

                                    <div class="preview-grid" id="preview-{{ $key }}"></div>

                                    @if ($selectedPatient && isset($images[$key]) && count($images[$key]))
                                        <h4 class="uploaded-title">Uploaded Files</h4>
                                        <div class="preview-grid" id="uploaded-files-{{ $key }}">
                                            @foreach ($images[$key] as $image)
                                                <div class="preview-item">
                                                    <img
                                                        src="{{ Storage::url($image->path) }}"
                                                        alt="{{ $image->original_name }}"
                                                    />
                                                    <button
                                                        type="button"
                                                        class="delete-btn"
                                                        onclick="
                                                            deleteImage(
                                                                event,
                                                                '{{ route('diagnostics.destroy', $image->id) }}',
                                                            )
                                                        "
                                                    >
                                                        ×
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <span class="upload-accepted-types">Accepted: Images (JPG, PNG, etc.)</span>
                            </label>

                            <div class="panel-footer">
                                <input
                                    type="file"
                                    name="images[{{ $key }}][]"
                                    accept="image/*"
                                    multiple
                                    onchange="previewImages(event, '{{ $key }}')"
                                    class="file-input"
                                    id="file-input-{{ $key }}"
                                    {{ ! $selectedPatient ? 'disabled' : '' }}
                                />

                                <button
                                    type="button"
                                    class="clear-btn"
                                    onclick="handleClearButtonClick('{{ $key }}')"
                                    {{ ! $selectedPatient ? 'disabled' : '' }}
                                >
                                    Clear All
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Using your original button class --}}
                <div class="mx-auto mt-5 mb-20 flex w-[98%] justify-end space-x-4">
                    <button
                        type="submit"
                        class="button-default text-center"
                        {{ ! $selectedPatient ? 'disabled' : '' }}
                    >
                        SUBMIT
                    </button>
                </div>
            </fieldset>
        </form>
    </div>
@endsection

@push('scripts')
    @vite([
        'resources/js/patient-loader.js',
        'resources/js/searchable-dropdown.js',
        'resources/js/diagnostics.js',
    ])
@endpush
