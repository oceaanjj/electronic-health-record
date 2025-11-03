@extends('layouts.app')

@section('title', 'Diagnostics')

@section('content')

<style>
.container { padding: 1rem; }
.header { display: flex; align-items: center; gap: 1rem; }
.header label { color: white; font-weight: 600; }
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

<div class="container">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="header">
        <form action="{{ route('diagnostics.select') }}" method="POST" id="patient-select-form">
            @csrf
            <label for="patient_id">PATIENT NAME:</label>
            <select name="patient_id" id="patient_id" onchange="this.form.submit()" required>
                <option value="">-- Select Patient --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->patient_id }}" {{ $patientId == $patient->patient_id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if ($selectedPatient)
        <h2 style="color:white; margin-top: 1rem;">Diagnostics for: <strong>{{ $selectedPatient->name }}</strong></h2>
    @endif

    <form action="{{ route('diagnostics.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="patient_id" value="{{ $selectedPatient ? $selectedPatient->patient_id : '' }}">

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
                <div class="diagnostic-panel">
                    
                    <div class="panel-body">
                        <h2>{{ $label }}</h2>

                        <div class="preview-grid" id="preview-{{ $key }}"></div>

                        @if ($selectedPatient && isset($images[$key]) && count($images[$key]))
                            <h4 class="uploaded-title">Uploaded Files:</h4>
                            <div class="preview-grid">
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
                            onclick="clearPreview('{{ $key }}')"
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
</script>

@endsection