@extends('layouts.app')

@section('title', 'Diagnostics')

@section('content')

<style>
.container { padding: 1rem; }
.alert { padding: 10px; border-radius: 6px; margin-bottom: 1rem; }
.alert-success { background: #d1e7dd; color: #0f5132; }
.alert-error { background: #f8d7da; color: #842029; }

.diagnostic-grid { 
    margin-top: 1rem; 
    display: flex; 
    flex-wrap: wrap; 
    gap: 1rem; 
}

.diagnostic-panel {
    border-radius: 12px;
    box-shadow: 0 0 5px #b0a87e;
    flex: 1 1 300px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.panel-body {
    background: linear-gradient(135deg, #5a9c5a, #4a8c4a);
    padding: 1rem;
    flex-grow: 1;
    min-height: 250px;
    display: flex;
    flex-direction: column;
}

.panel-body h2 {
    color: white;
    font-weight: bold;
    text-align: center;
    font-size: 1.5rem;
    margin: 0;
    padding: 1rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
}

.panel-footer {
    background: #f7edc9;
    padding: 0.75rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.insert-btn {
    background: green;
    color: #fff;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.8rem;
    font-weight: bold;
    text-transform: uppercase;
}

.clear-btn {
    background: red;
    padding: 6px 12px;
    border-radius: 4px;
    border: none;
    color: #fff;
    cursor: pointer;
    font-weight: bold;
    font-size: 0.8rem;
    text-transform: uppercase;
}

button[type=submit] { 
    background: #007bff; 
    color: white; 
    padding: 8px 16px; 
    border: none; 
    border-radius: 6px; 
    cursor: pointer; 
    font-weight: bold; 
}

.file-input {
    display: none;
}

.preview-grid { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 8px; 
    margin-top: 1rem;
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
    border-radius: 8px; 
    border: 2px solid #ccc; 
}
.delete-btn { 
    position: absolute; 
    top: -6px; 
    right: -6px; 
    background: red; 
    color: white; 
    border: none; 
    border-radius: 50%; 
    width: 22px; 
    height: 22px; 
    font-size: 12px; 
    cursor: pointer; 
}

.uploaded-title {
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: white;
    font-weight: bold;
    border-bottom: 1px solid rgba(255,255,255,0.3);
    padding-bottom: 5px;
}

/* --- button disabledd --- */
.insert-btn.disabled,
.insert-btn[disabled],
button.clear-btn[disabled],
button[type=submit][disabled] {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #e0e0e0;
    color: #999;
}
button.clear-btn[disabled] {
    background: none;
    opacity: 0.4;
}
</style>

<div id="form-content-container">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

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

            <div style="margin-top: 1rem;">
                <button type="submit" {{ !$selectedPatient ? 'disabled' : '' }}>Submit</button>
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
                div.classList.add('preview-item');
                div.innerHTML = `<img src="${e.target.result}" alt="preview">`;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}

function clearPreview(type) {
    const previewContainer = document.getElementById('preview-' + type);
    const input = document.querySelector(`input[name="images[${type}][]"]`); 
    previewContainer.innerHTML = '';
    input.value = '';
}

function deleteImage(url) {
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

function handleClearButtonClick(type) {
    const panel = document.querySelector(`.diagnostic-panel[data-type="${type}"]`);
    const uploadedImageIds = JSON.parse(panel.dataset.uploadedImageIds || '[]');

    if (uploadedImageIds.length > 0) {
        // If there are uploaded images, trigger bulk delete
        deleteAllImages(type, uploadedImageIds);
    } else {
        // Otherwise, just clear the client-side preview
        clearPreview(type);
    }
}

function deleteAllImages(type, imageIds) {
    if (!confirm('Delete ALL images for ' + type.toUpperCase() + '? This action cannot be undone.')) return;

    fetch('{{ route('diagnostics.destroy-all', ['type' => '__TYPE__', 'patient_id' => '__PATIENT_ID__']) }}'
        .replace('__TYPE__', type)
        .replace('__PATIENT_ID__', '{{ $selectedPatient->patient_id ?? '' }}'), {
        method: 'POST', // Laravel uses POST for DELETE via _method
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
            location.reload(); // Reload page to reflect changes
        } else {
            alert('Failed to delete images: ' + (data.message || 'Unknown error.'));
        }
    })
    .catch(error => {
        console.error('Error deleting images:', error);
        alert('Error deleting images.');
    });
}

// Initialize searchable dropdown on page load
document.addEventListener('DOMContentLoaded', () => {
    if (window.initSearchableDropdown) {
        window.initSearchableDropdown();
    }
});
</script>

@endsection

@push('scripts')
    @vite(['resources/js/patient-loader.js', 'resources/js/searchable-dropdown.js'])
@endpush