@extends('layouts.app')

@section('title', 'Diagnostics')

@section('content')
    <style>
        /* --- GLOBAL & UTILITIES --- */
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1e40af;
            --danger-color: #ef4444;
            --danger-hover: #b91c1c;
            --gray-100: #f9fafb;
            --gray-200: #f3f4f6;
            --gray-300: #e5e7eb;
            --gray-400: #d1d5db;
            --gray-500: #9ca3af;
            --gray-700: #374151;
            --gray-800: #1f2937;
        }

        .container {
            padding: 1.5rem;
        }

        #form-content-container {
            margin: 2rem auto;
            max-width: 1200px;
        }

        /* --- BUTTONS (Material Style) --- */
        .btn {
            display: inline-block;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            text-align: center;
            text-decoration: none;
            transition:
                background-color 0.3s ease,
                box-shadow 0.3s ease,
                transform 0.1s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: scale(0.98);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: #fff;
        }

        .btn-danger:hover {
            background-color: var(--danger-hover);
        }

        .btn[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: var(--gray-400);
            color: var(--gray-500);
            box-shadow: none;
            transform: none;
        }

        /* --- GRID --- */
        .diagnostic-grid {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            /* Responsive grid */
            gap: 2rem;
        }

        /* --- CARD PANEL (Material Style) --- */
        .diagnostic-panel {
            background: #ffffff;
            border-radius: 16px;
            /* Material 3 style */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .diagnostic-panel:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        /* --- HEADER AREA --- */
        .panel-header {
            background: var(--gray-100);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-300);
            background-color: rgba(4, 127, 0, 1);
        }

        .panel-header h2 {
            color: var(--gray-800);
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0;
            color: white;
        }

        /* --- UPLOAD AREA (The 'panel-body') --- */
        .panel-upload-area {
            position: relative;
            padding: 1.5rem;
            border: 2px dashed var(--gray-400);
            border-radius: 12px;
            margin: 1.5rem;
            cursor: pointer;
            background: #fff;
            transition:
                background-color 0.2s ease,
                border-color 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Pushes prompt to top, accepted-types to bottom */
            flex-grow: 1;
            /* Makes the upload area fill the card */
            min-height: 280px;
        }

        .panel-upload-area:hover {
            background-color: var(--gray-100);
            border-color: var(--primary-color);
        }

        /* Drag-over state */
        .panel-upload-area.drag-over {
            background-color: #e0eaff;
            border-color: var(--primary-color);
            border-style: solid;
        }

        /* New wrapper for the main content (prompt/previews) */
        .upload-content-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            flex-grow: 1;
            /* Allows this to center vertically */
        }

        /* Prompt for uploading */
        .upload-prompt {
            text-align: center;
            color: var(--gray-500);
            pointer-events: none;
            /* Allows clicks to pass through to the label */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .upload-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            color: var(--gray-400);
        }

        .upload-prompt p {
            font-size: 1rem;
            font-weight: 500;
            margin: 0;
        }

        /* "Accepted types" text at the bottom */
        .upload-accepted-types {
            display: block;
            text-align: center;
            font-size: 0.85rem;
            color: var(--gray-500);
            width: 100%;
            padding-top: 1rem;
            /* Space from content above */
            pointer-events: none;
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
            border-radius: 12px;
            border: 2px solid var(--gray-300);
        }

        .delete-btn {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--danger-color);
            color: white;
            border: 2px solid white;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            font-size: 14px;
            font-weight: bold;
            line-height: 22px;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* --- UPLOADED FILES TITLE --- */
        .uploaded-title {
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--gray-800);
            font-weight: 600;
            border-bottom: 2px solid var(--gray-200);
            padding-bottom: 8px;
            font-size: 1rem;
            width: 100%;
            text-align: left;
        }

        /* --- FOOTER BUTTON AREA --- */
        .panel-footer {
            background: var(--gray-100);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-top: 1px solid var(--gray-300);
        }

        /* --- FILE INPUT HIDDEN --- */
        .file-input {
            display: none;
        }
    </style>

    <div
        id="form-content-container"
        data-csrf-token="{{ csrf_token() }}"
        data-patient-id="{{ $selectedPatient->patient_id ?? '' }}"
        data-delete-all-url-template="{{ route('diagnostics.destroy-all', ['type' => '__TYPE__', 'patient_id' => '__PATIENT_ID__']) }}"
    >
        {{-- DIAGNOSTICS PATIENT SELECTION (Synced with Vital Signs UI - No CDSS) --}}
        <div class="mx-auto w-full px-4 pt-10">
            <div class="ml-20 flex flex-wrap items-center gap-x-10 gap-y-4">
                {{-- PATIENT SECTION --}}
                <div class="flex items-center gap-4">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

                    {{-- Fixed width to match Vital Signs perfectly --}}
                    <div class="w-[350px]">
                        <x-searchable-patient-dropdown
                            :patients="$patients"
                            :selectedPatient="$selectedPatient"
                            selectRoute="{{ route('diagnostics.select') }}"
                            inputPlaceholder="Search or type Patient Name..."
                            inputName="patient_id"
                            inputValue="{{ session('selected_patient_id') }}"
                        />
                    </div>
                </div>
            </div>
        </div>

        @if ($selectedPatient)
            <h2 style="color: white; margin-top: 1rem; font-weight: 300">
                Diagnostics for:
                <strong style="font-weight: 600">
                    {{ $selectedPatient->first_name }}
                    {{ $selectedPatient->middle_name ? $selectedPatient->middle_name . ' ' : '' }}{{ $selectedPatient->last_name }}
                </strong>
            </h2>
        @endif

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
                                        <h4 class="uploaded-title">Uploaded Files:</h4>
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
                                    class="cursor-pointer rounded-full bg-red-500 px-3 py-1 text-white hover:bg-red-700"
                                    onclick="handleClearButtonClick('{{ $key }}')"
                                    {{ ! $selectedPatient ? 'disabled' : '' }}
                                >
                                    Clear All
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Use the global button classes --}}
                <div style="margin-top: 2rem; text-align: center">
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
