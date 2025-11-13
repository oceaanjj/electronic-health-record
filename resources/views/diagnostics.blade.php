@extends('layouts.app')

@section('title', 'Diagnostics')

@section('content')

<style>
.container { 
    padding: 1.5rem; 
}

/* Add outer margin for breathing room */
#form-content-container {
    margin: 2rem auto;
    max-width: 1200px;
}

/* Alerts */
.alert { 
    padding: 10px; 
    border-radius: 6px; 
    margin-bottom: 1rem; 
}
.alert-success { background: #d1e7dd; color: #0f5132; }
.alert-error { background: #f8d7da; color: #842029; }

/* --- GRID --- */
.diagnostic-grid { 
    margin-top: 2rem; 
    display: grid; 
    grid-template-columns: repeat(2, 1fr); 
    gap: 2rem; /* Added more spacing between cards */
}

/* --- CARD PANEL --- */
.diagnostic-panel {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.3s ease;
    margin: 0.5rem; /* Added margin around individual panels */
}
.diagnostic-panel:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.08);
}

/* --- HEADER AREA --- */
.panel-body {
    background: #f9fafb;
    padding: 2rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 240px;
    position: relative;
}

.panel-body h2 {
    color: #374151;
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 1rem;
    text-align: center;
}

/* --- PREVIEW GRID --- */
.preview-grid { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 8px; 
    margin-top: 1rem;
    justify-content: center;
}
.preview-item { 
    position: relative; 
    width: 100px; 
    height: 100px; 
}
.preview-item img { 
    width: 100%; 
    height: 100%; 
    object-fit: cover; 
    border-radius: 12px; 
    border: 2px solid #e5e7eb; 
}
.delete-btn { 
    position: absolute; 
    top: -6px; 
    right: -6px; 
    background: #ef4444; 
    color: white; 
    border: none; 
    border-radius: 50%; 
    width: 22px; 
    height: 22px; 
    font-size: 12px; 
    cursor: pointer; 
}

/* --- UPLOADED FILES TITLE --- */
.uploaded-title {
    margin-top: 1rem;
    margin-bottom: 0.25rem;
    color: #111827;
    font-weight: 600;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 5px;
    font-size: 0.9rem;
    width: 100%;
    text-align: left;
}

/* --- FOOTER BUTTON AREA --- */
.panel-footer {
    background: #f3f4f6;
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #e5e7eb;
}

/* --- BUTTONS --- */
.insert-btn {
    background: #2563eb;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    transition: background 0.3s ease;
}
.insert-btn:hover {
    background: #1e40af;
}

.clear-btn {
    background: #ef4444;
    color: #fff;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    border: none;
    transition: background 0.3s ease;
}
.clear-btn:hover {
    background: #b91c1c;
}

/* --- FILE INPUT HIDDEN --- */
.file-input {
    display: none;
}

/* --- DISABLED STATES --- */
.insert-btn.disabled,
.insert-btn[disabled],
button.clear-btn[disabled],
button[type=submit][disabled] {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #d1d5db;
    color: #9ca3af;
}
</style>


<div id="form-content-container">



    {{-- Styled Alerts --}}
    {{-- @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6">{{ session('error') }}</div>
    @endif --}}

    <x-searchable-patient-dropdown
        :patients="$patients"
        :selectedPatient="$selectedPatient"
        selectRoute="{{ route('diagnostics.select') }}"
        inputPlaceholder="-Select or type to search-"
        inputName="patient_id"
        inputValue="{{ session('selected_patient_id') }}"
    />

    @if ($selectedPatient)
        <h2 style="color:white; margin-top: 1rem;">Diagnostics for: <strong>{{ $selectedPatient->first_name }} {{ $selectedPatient->middle_name ? $selectedPatient->middle_name . ' ' : '' }}{{ $selectedPatient->last_name }}</strong></h2>
    @endif

    <form action="{{ route('diagnostics.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="patient_id" value="{{ $selectedPatient ? $selectedPatient->patient_id : '' }}">

        <fieldset {{ !$selectedPatient ? 'disabled' : '' }}>
            <div class="diagnostic-grid">
                @php
                    $types = [
                        'xray' => 'X-Ray',
                        'ultrasound' => 'Ultrasound',
                        'ct_scan' => 'CT Scan',
                        'echocardiogram' => 'Echocardiogram'
                    ];
                @endphp

                @foreach ($types as $key => $label)
                    <div class="diagnostic-panel" data-type="{{ $key }}"
                         data-uploaded-image-ids="{{ json_encode($selectedPatient && isset($images[$key]) ? $images[$key]->pluck('id')->toArray() : []) }}">
                        
                        <div class="panel-body">
                            <h2>{{ $label }}</h2>

                            <div class="preview-grid" id="preview-{{ $key }}"></div>

                            @if ($selectedPatient && isset($images[$key]) && count($images[$key]))
                                <h4 class="uploaded-title">Uploaded Files:</h4>
                                <div class="preview-grid" id="uploaded-files-{{ $key }}">
                                    @foreach ($images[$key] as $image)
                                        <div class="preview-item">
                                            <img src="{{ Storage::url($image->path) }}" alt="{{ $image->original_name }}">
                                            <button type="button" class="delete-btn"
                                                onclick="deleteImage('{{ route('diagnostics.destroy', $image->id) }}')">x</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="panel-footer">
                            <input 
                                type="file" 
                                name="images[{{ $key }}][]" 
                                accept="image/*" 
                                multiple 
                                onchange="previewImages(event, '{{ $key }}')" 
                                class="file-input" 
                                id="file-input-{{ $key }}"
                                {{ !$selectedPatient ? 'disabled' : '' }}>
                            
                            <label 
                                for="file-input-{{ $key }}" 
                                class="insert-btn {{ !$selectedPatient ? 'disabled' : '' }}">
                                INSERT PHOTO
                            </label>

                            <button 
                                type="button" 
                                class="clear-btn" 
                                onclick="handleClearButtonClick('{{ $key }}')"
                                {{ !$selectedPatient ? 'disabled' : '' }}>
                                CLEAR
                            </button>
                        </div>
                        
                    </div>
                @endforeach
            </div>

            {{-- Use the global button-default class --}}
            <div style="margin-top: 2rem; text-align:center;">
                <button type="submit" class="button-default" {{ !$selectedPatient ? 'disabled' : '' }}>
                    SUBMIT 
                </button>
            </div>
        </fieldset>
    </form>
</div>

<script>
function previewImages(event, type) {
    const input = event.target;
    const previewContainer = document.getElementById('preview-' + type);
    previewContainer.innerHTML = '';

    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.classList.add('relative', 'w-full', 'aspect-square');
                div.innerHTML = `<img src="${e.target.result}" alt="preview" class="w-full h-full object-cover rounded-lg border-2 border-gray-300">`;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}

function clearPreview(type) {
    const previewContainer = document.getElementById('preview-' + type);
    const input = document.getElementById('file-input-' + type); 
    previewContainer.innerHTML = '';
    input.value = '';
}

function deleteImage(url) {
    if (typeof showConfirm === 'function') {
        showConfirm('Do you really want to delete this image?', 'Delete Image?', 'Yes, delete', 'Cancel')
            .then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams({ _method: 'DELETE' })
                    })
                    .then(res => {
                        if (res.ok) {
                            location.reload();
                        } else {
                            if (typeof showError === 'function') {
                                showError('Failed to delete image.', 'Error');
                            } else {
                                alert('Failed to delete image.');
                            }
                        }
                    })
                    .catch(() => {
                        if (typeof showError === 'function') {
                            showError('Error deleting image.', 'Error');
                        } else {
                            alert('Error deleting image.');
                        }
                    });
                }
            });
    } else if (typeof Swal === 'function') {
        Swal.fire({
            title: 'Delete Image?',
            text: 'Do you really want to delete this image?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#2A1C0F',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({ _method: 'DELETE' })
                })
                .then(res => {
                    if (res.ok) location.reload();
                    else alert('Failed to delete image.');
                })
                .catch(() => alert('Error deleting image.'));
            }
        });
    } else {
        if (!confirm('Delete this image?')) return;
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({ _method: 'DELETE' })
        })
        .then(res => {
            if (res.ok) location.reload();
            else alert('Failed to delete image.');
        })
        .catch(() => alert('Error deleting image.'));
    }
}

function handleClearButtonClick(type) {
    const panel = document.querySelector(`.diagnostic-panel[data-type="${type}"]`);
    const uploadedImageIds = JSON.parse(panel.dataset.uploadedImageIds || '[]');

    if (uploadedImageIds.length > 0) {
        deleteAllImages(type, uploadedImageIds);
    } else {
        clearPreview(type);
    }
}

function deleteAllImages(type, imageIds) {
    if (typeof showConfirm === 'function') {
        showConfirm('Do you really want to delete ALL images for ' + type.toUpperCase() + '?', 'Delete All Images?', 'Yes', 'Cancel')
            .then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('diagnostics.destroy-all', ['type' => '__TYPE__', 'patient_id' => '__PATIENT_ID__']) }}'
                        .replace('__TYPE__', type)
                        .replace('__PATIENT_ID__', '{{ $selectedPatient->patient_id ?? '' }}'), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ _method: 'DELETE', image_ids: imageIds })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            if (typeof showError === 'function') {
                                showError('Failed to delete images.', 'Error');
                            } else {
                                alert('Failed to delete images.');
                            }
                        }
                    })
                    .catch(() => {
                        if (typeof showError === 'function') {
                            showError('Error deleting images.', 'Error');
                        } else {
                            alert('Error deleting images.');
                        }
                    });
                }
            });
    } else if (typeof Swal === 'function') {
        Swal.fire({
            title: 'Delete All Images?',
            text: 'Do you really want to delete ALL images for ' + type.toUpperCase() + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#2A1C0F',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route('diagnostics.destroy-all', ['type' => '__TYPE__', 'patient_id' => '__PATIENT_ID__']) }}'
                    .replace('__TYPE__', type)
                    .replace('__PATIENT_ID__', '{{ $selectedPatient->patient_id ?? '' }}'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ _method: 'DELETE', image_ids: imageIds })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) location.reload();
                    else alert('Failed to delete images.');
                })
                .catch(() => alert('Error deleting images.'));
            }
        });
    } else {
        if (!confirm('Delete ALL images for ' + type.toUpperCase() + '?')) return;
        fetch('{{ route('diagnostics.destroy-all', ['type' => '__TYPE__', 'patient_id' => '__PATIENT_ID__']) }}'
            .replace('__TYPE__', type)
            .replace('__PATIENT_ID__', '{{ $selectedPatient->patient_id ?? '' }}'), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ _method: 'DELETE', image_ids: imageIds })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Failed to delete images.');
        })
        .catch(() => alert('Error deleting images.'));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.initSearchableDropdown) window.initSearchableDropdown();
});
</script>

@endsection

@push('scripts')
    @vite(['resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
@endpush
