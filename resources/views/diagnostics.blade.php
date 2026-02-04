@extends('layouts.app')

@section('title', 'Diagnostics')

@section('content')
    <div
        class="mx-auto my-8 max-w-[90%] w-[90%] relative"
        data-csrf-token="{{ csrf_token() }}"
        data-patient-id="{{ $selectedPatient->patient_id ?? '' }}"
        data-delete-all-url-template="{{ route('diagnostics.destroy-all', ['type' => '__TYPE__', 'patient_id' => '__PATIENT_ID__']) }}"
    >
        {{-- DIAGNOSTICS PATIENT SELECTION --}}
        <div class="mx-auto w-full px-4 pt-10">
            <div class="ml-20 flex flex-wrap items-center gap-x-10 gap-y-4">
                {{-- PATIENT SECTION --}}
                <div class="flex items-center gap-4">
                    <label class="font-alte text-dark-green shrink-0 font-bold whitespace-nowrap">PATIENT NAME :</label>

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