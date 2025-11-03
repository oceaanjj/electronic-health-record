@extends('layouts.app')

@section('title', 'Diagnostics')

@section('content')

<style>
.container {
    padding: 1rem;
}
.success-message {
    background: #d4edda;
    color: #155724;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 1rem;
}
.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 1rem;
}
.header {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.header label {
    color: white;
}
.diagnostic-grid {
    margin-top: 1rem;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}
.diagnostic-panel {
    background: #f7edc9;
    border-radius: 6px;
    width: 48%;
    min-width: 360px;
    padding: 0.5rem;
}
.image-container {
    background: linear-gradient(#9edc9e, #1e4e2a);
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    color: #fff;
    overflow: hidden;
    flex-wrap: wrap;
}
.image-preview {
    max-height: 200px;
    max-width: 100%;
    object-fit: contain;
    margin: 4px;
    border-radius: 5px;
}
.panel-title {
    font-weight: 700;
}
.button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 0.6rem;
}
.insert-photo {
    padding: 0.5rem;
    border-radius: 4px;
    cursor: pointer;
}
.insert-photo.enabled {
    background: #f2b233;
    color: #222;
}
.insert-photo.disabled {
    background: #ccc;
    color: #666;
    cursor: not-allowed;
}
.delete-button {
    background: #cf2b2b;
    color: white;
    padding: 0.5rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}
.delete-button.disabled {
    background: #ccc;
    color: #666;
    cursor: not-allowed;
}
.submit-btn {
    margin-top: 1rem;
    background: #2b7a2b;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
}
</style>

<div class="container">

    {{-- Patient select form --}}
    <form action="{{ route('diagnostics.select') }}" method="POST" style="margin-bottom:1rem;">
        @csrf
        <label for="patient_select" style="color:white;margin-right:0.5rem;">PATIENT NAME :</label>
        <select id="patient_select" name="patient_id" onchange="this.form.submit()">
            <option value="">-- Select Patient --</option>
            @foreach ($patients as $patient)
                <option value="{{ $patient->patient_id }}"
                    {{ (isset($selectedPatient) && $selectedPatient->patient_id == $patient->patient_id) ? 'selected' : '' }}>
                    {{ $patient->name }}
                </option>
            @endforeach
        </select>
    </form>

    <form action="{{ route('diagnostics.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="diagnostic-grid">
            @php
                $types = [
                    'xray' => 'XRAY',
                    'ultrasound' => 'ULTRASOUND',
                    'ct_scan' => 'CT SCAN',
                    'echocardiogram' => 'ECHOCARDIOGRAM'
                ];
            @endphp

            @foreach($types as $key => $label)
                <div class="diagnostic-panel">
                    <div class="image-container" id="preview-{{ $key }}">
                        @if(isset($images[$key]) && $images[$key]->count() > 0)
                            @foreach($images[$key] as $image)
                                <div class="image-wrapper">
                                    <img src="{{ Storage::url($image->path) }}" alt="{{ $label }}" class="image-preview">
                                </div>
                            @endforeach
                        @else
                            <div class="panel-title">{{ $label }}</div>
                        @endif
                    </div>

                    <div class="button-container">
                        <label class="insert-photo enabled">
                            INSERT PHOTO
                            <input type="file" name="images[{{ $key }}][]" accept="image/*" multiple
                                   style="display:none;"
                                   onchange="previewImages(event, '{{ $key }}')">
                        </label>

                        <form id="delete-form-{{ $key }}" action="#" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button"
                                    id="delete-btn-{{ $key }}"
                                    class="delete-button {{ (isset($images[$key]) && $images[$key]->count() > 0) ? '' : 'disabled' }}"
                                    onclick="deleteImages('{{ $key }}')"
                                    {{ (!isset($images[$key]) || $images[$key]->count() == 0) ? 'disabled' : '' }}>
                                DELETE
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="buttons">
            <button class="submit-btn" type="submit">Submit</button>
        </div>
    </form>
</div>

<script>
function previewImages(event, type) {
    const files = event.target.files;
    const previewBox = document.getElementById('preview-' + type);
    const deleteBtn = document.getElementById('delete-btn-' + type);

    // Clear old previews
    previewBox.innerHTML = '';

    // Show each selected image
    Array.from(files).forEach(file => {
        const img = document.createElement('img');
        img.classList.add('image-preview');
        img.src = URL.createObjectURL(file);
        previewBox.appendChild(img);
    });

    // Enable the delete button when files exist
    if (files.length > 0) {
        deleteBtn.classList.remove('disabled');
        deleteBtn.disabled = false;
    } else {
        deleteBtn.classList.add('disabled');
        deleteBtn.disabled = true;
    }
}

// Handle delete button click
function deleteImages(type) {
    const deleteBtn = document.getElementById('delete-btn-' + type);
    const previewBox = document.getElementById('preview-' + type);

    if (confirm('Are you sure you want to remove the selected images?')) {
        // Clear previews (client-side)
        previewBox.innerHTML = '<div class="panel-title">' + type.toUpperCase() + '</div>';
        deleteBtn.classList.add('disabled');
        deleteBtn.disabled = true;
    }
}
</script>

@endsection
    