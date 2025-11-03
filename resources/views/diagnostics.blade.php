@extends('layouts.app')

@section('title', 'Diagnostics')

@section('content')

<style>
.container { padding: 1rem; }
.header { display: flex; align-items: center; gap: 1rem; }
.header label { color: white; font-weight: 600; }
.diagnostic-grid { margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 1rem; }
.diagnostic-panel { background: #f7edc9; border-radius: 12px; box-shadow: 0 0 5px #b0a87e; padding: 1rem; flex: 1 1 300px; }
.diagnostic-panel h2 { background: #a87f00; color: white; padding: 0.5rem; border-radius: 8px; text-align: center; margin-bottom: 0.5rem; font-size: 1.1rem; }
.preview-grid { display: flex; flex-wrap: wrap; gap: 8px; }
.preview-item { position: relative; width: 100px; height: 100px; }
.preview-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 2px solid #ccc; }
.delete-btn { position: absolute; top: -6px; right: -6px; background: red; color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 12px; cursor: pointer; }
.clear-btn { margin-top: 6px; background: #d9534f; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; }
.alert { padding: 10px; border-radius: 6px; margin-bottom: 1rem; }
.alert-success { background: #d1e7dd; color: #0f5132; }
.alert-error { background: #f8d7da; color: #842029; }
button[type=submit] { background: #007bff; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
</style>

<div class="container">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    {{-- PATIENT SELECT --}}
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

        <form action="{{ route('diagnostics.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id }}">

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
                        <h2>{{ $label }}</h2>

                        <input type="file" name="images[{{ $key }}][]" accept="image/*" multiple onchange="previewImages(event, '{{ $key }}')">
                        <div class="preview-grid" id="preview-{{ $key }}"></div>
                        <button type="button" class="clear-btn" onclick="clearPreview('{{ $key }}')">Clear</button>

                        @if (isset($images[$key]) && count($images[$key]))
                            <h4 style="margin-top: 10px;">Uploaded Files:</h4>
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
                @endforeach
            </div>

            <div style="margin-top: 1rem;">
                <button type="submit">Submit</button>
            </div>
        </form>
    @endif
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
