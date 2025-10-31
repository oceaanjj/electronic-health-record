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
}
.image-preview {
    max-height: 200px;
    max-width: 100%;
    object-fit: contain;
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
}
.insert-photo.enabled {
    background: #f2b233;
    color: #222;
    cursor: pointer;
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
    <form action="{{ route('diagnostics.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="header">
            <label for="patient_id">PATIENT NAME :</label>
            <select id="patient_info" name="patient_id">
                <option value="">-- Select Patient --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->patient_id }}" 
                        {{ (isset($selectedPatient) && $selectedPatient->patient_id == $patient->patient_id) ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>

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
                            @php $latest = $images[$key]->last(); @endphp
                            <img src="{{ Storage::url($latest->path) }}" alt="{{ $label }}" class="image-preview">
                        @else
                            <div class="panel-title">{{ $label }}</div>
                        @endif
                    </div>

                    <div class="button-container">
                        <label class="insert-photo enabled">
                        INSERT PHOTO
                        <input type="file" name="images[{{ $key }}]" accept="image/*"
                            style="display:none;"
                            onchange="previewImage(event, '{{ $key }}')">
                    </label>

                        @if(isset($images[$key]) && $images[$key]->count() > 0)
                            @php $latest = $images[$key]->last(); @endphp
                            <form action="{{ route('diagnostics.destroy', $latest->id) }}" method="POST" onsubmit="return confirm('Delete this image?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-button">DELETE</button>
                            </form>
                        @else
                            <button disabled class="delete-button disabled">DELETE</button>
                        @endif
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
function previewImage(event, type) {
    const file = event.target.files[0];
    if (!file) return;

    const previewBox = document.getElementById('preview-' + type);
    previewBox.innerHTML = '';

    const img = document.createElement('img');
    img.classList.add('image-preview');
    img.src = URL.createObjectURL(file);

    previewBox.appendChild(img);
}
</script>

@endsection
