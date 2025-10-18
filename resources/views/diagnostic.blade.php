@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div style="background-color: #1a472a; padding: 2rem 3rem; border-radius: 15px; color: white; border: 3px solid #daa520;">

        <h2 class="text-center mb-4" style="color: #f0ead6;">Diagnostic Imaging</h2>

        {{-- Patient Selection Form --}}
        <form action="{{ route('diagnostic.index') }}" method="GET">
            <div class="header d-flex justify-content-center align-items-center mb-5">
                <label for="patient_id" class="form-label me-3 fw-bold fs-5">PATIENT NAME:</label>
                <select id="patient_id" name="patient_id" class="form-select w-50" onchange="this.form.submit()">
                    <option value="">-- Select a Patient to Begin --</option>
                    @foreach ($allPatients as $patient)
                        <option value="{{ $patient->patient_id }}"
                            {{ (isset($selectedPatient) && $selectedPatient->patient_id == $patient->patient_id) ? 'selected' : '' }}>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if(isset($selectedPatient))
            <h4 class="text-center mb-5" style="color: #daa520;">Managing Images for: {{ $selectedPatient->name }}</h4>

            @if(isset($selectedPatient))
    <h4 class="text-center mb-5" style="color: #daa520;">Managing Images for: {{ $selectedPatient->name }}</h4>

    {{-- --- ADD THIS ERROR DISPLAY BLOCK --- --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops! Something went wrong.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- --- END ERROR DISPLAY BLOCK --- --}}

    <form action="{{ route('diagnostics.update', $selectedPatient->patient_id) }}" method="POST" enctype="multipart/form-data">
        {{-- ... rest of your form ... --}}
    </form>
@endif

            <form action="{{ route('diagnostics.update', $selectedPatient->patient_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @php
                    $diagnosticTypes = [
                        'X-RAY' => 'xray',
                        'ULTRASOUND' => 'ultrasound',
                        'CT SCAN / MRI' => 'ct_mri',
                        'ECHOCARDIOGRAM' => 'echocardiogram'
                    ];
                @endphp

                @foreach ($diagnosticTypes as $displayName => $inputName)
                <div class="card mb-4" style="background-color: transparent; border: 2px solid #daa520;">
                    <div class="row g-0">
                        <div class="col-md-2 d-flex align-items-center justify-content-center p-2" style="background-color: #1a472a;">
                            <span class="fw-bold" style="color: #daa520; writing-mode: vertical-rl; transform: rotate(180deg);">{{ $displayName }}</span>
                        </div>

                        <div class="col-md-10" style="background-color: #f0ead6;">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div id="preview-container-{{ $inputName }}" class="d-flex flex-wrap align-items-center p-2" style="min-height: 150px; border: 2px dashed #ccc; background-color: #f8f9fa;">
                                            @if($diagnosticsByType->has($displayName) && $diagnosticsByType->get($displayName)->isNotEmpty())
                                                @foreach($diagnosticsByType->get($displayName) as $image)
                                                    <div class="me-2 mb-2 position-relative text-center">
                                                        <img src="{{ $image->image_url }}" alt="{{ $displayName }}" style="height: 100px; width: auto; border: 1px solid #ddd;">
                                                        <div class="form-check mt-1">
                                                            <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $image->id }}" id="delete_{{ $image->id }}">
                                                            <label class="form-check-label text-danger small" for="delete_{{ $image->id }}">
                                                                Mark for Deletion
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div id="placeholder-{{ $inputName }}" class="w-100 text-center text-muted">IMAGE PLACEHOLDER</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-3 d-flex flex-column justify-content-center">
                                        <label for="{{ $inputName }}_upload" class="btn fw-bold w-100 mb-2" style="background-color: #daa520; color: #1a472a;">INSERT PHOTO</label>
                                        <input type="file" id="{{ $inputName }}_upload" name="images[{{ $inputName }}][]" class="d-none image-upload-input" multiple data-target="preview-container-{{ $inputName }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-lg fw-bold" style="background-color: #daa520; color: #1a472a; padding: 10px 40px;">SUBMIT</button>
                </div>
            </form>
        @else
            <div class="text-center p-5">
                <p>Please select a patient from the dropdown menu to manage their diagnostic images.</p>
            </div>
        @endif
    </div>
</div>

{{-- JAVASCRIPT FOR IMAGE PREVIEW --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Find all the file input elements with the class 'image-upload-input'
    const imageUploadInputs = document.querySelectorAll('.image-upload-input');

    imageUploadInputs.forEach(function (input) {
        input.addEventListener('change', function (event) {
            // Get the unique ID of the container where previews should go
            const previewContainerId = event.target.dataset.target;
            const previewContainer = document.getElementById(previewContainerId);

            // Find the placeholder text to hide it
            const placeholder = document.getElementById(previewContainerId.replace('preview-container-', 'placeholder-'));
            if (placeholder) {
                placeholder.style.display = 'none';
            }

            // Loop through all the files the user just selected
            for (const file of event.target.files) {
                const reader = new FileReader();
                
                reader.onload = function (e) {
                    // Create a new image element
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    
                    // Style the preview image
                    img.style.height = '100px';
                    img.style.width = 'auto';
                    img.style.border = '1px solid #ddd';
                    img.style.margin = '4px';

                    // Add the new image to the preview container
                    previewContainer.appendChild(img);
                };
                
                // Read the file so the browser can display it
                reader.readAsDataURL(file);
            }
        });
    });
});
</script>
@endsection