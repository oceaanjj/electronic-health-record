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
    min-height: 220px; /* Pinalitan mula sa height para mag-expand */
    display: flex;
    align-items: flex-start; /* Simula sa taas */
    justify-content: center;
    border-radius: 4px;
    color: #fff;
    overflow-y: auto; /* Kung masyadong madami, mag-scroll */
    flex-wrap: wrap;
    padding: 10px;
}
.image-wrapper {
    position: relative;
    display: inline-flex;
    margin: 4px;
}
.delete-image-form {
    position: absolute;
    top: -5px;
    right: -5px;
}
.delete-image-btn {
    background-color: #cf2b2b;
    color: white;
    border: 2px solid white;
    border-radius: 50%;
    cursor: pointer;
    font-weight: bold;
    line-height: 1;
    width: 24px;
    height: 24px;
    font-size: 14px;
}
.delete-image-btn:hover {
    background-color: #a02020;
}
.image-preview {
    max-height: 200px;
    max-width: 100%;
    object-fit: contain;
    margin: 0;
    border-radius: 5px;
}
/* Mag-add ng border para sa mga bagong preview */
.preview-new .image-preview {
    border: 3px dashed #f2b233;
    padding: 2px;
}
.panel-title {
    font-weight: 700;
    margin-top: 90px; /* Para ma-gitna ang text pag walang laman */
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
.clear-button { /* Pinalitan ang pangalan mula sa delete-button */
    background: #cf2b2b;
    color: white;
    padding: 0.5rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}
.clear-button.disabled {
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
    border: none;
    cursor: pointer;
}
</style>

<div class="container">

    {{-- Ipakita ang Success at Error Messages --}}
    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="error-message">
            <strong>Oops! May nagkamali:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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

        {{-- Itago ang patient_id sa form para ma-submit --}}
        @if($selectedPatient)
            <input type="hidden" name="patient_id" value="{{ $selectedPatient->patient_id }}">
        @endif

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
                        
                        {{-- Ipakita ang mga naka-save na images --}}
                        @if(isset($images[$key]) && $images[$key]->count() > 0)
                            @foreach($images[$key] as $image)
                                <div class="image-wrapper">
                                    <img src="{{ Storage::url($image->path) }}" alt="{{ $label }}" class="image-preview">
                                    
                                    {{-- REAL DELETE BUTTON (PER IMAGE) --}}
                                    <form action="{{ route('diagnostics.destroy', $image->id) }}" method="POST" class="delete-image-form" onsubmit="return confirm('Sigurado ka bang gusto mong i-delete ang image na ito?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-image-btn">X</button>
                                    </form>
                                </div>
                            @endforeach
                        @else
                            {{-- Placeholder kung walang images --}}
                            <div class="panel-title">{{ $label }}</div>
                        @endif
                        {{-- Dito lilitaw ang mga bagong preview mula sa JS --}}

                    </div>

                    <div class="button-container">
                        <label class="insert-photo {{ $selectedPatient ? 'enabled' : 'disabled' }}">
                            INSERT PHOTO
                            <input type="file" name="images[{{ $key }}][]" accept="image/*" multiple
                                   style="display:none;"
                                   onchange="previewImages(event, '{{ $key }}')"
                                   {{ $selectedPatient ? '' : 'disabled' }}>
                        </label>

                        {{-- Button para i-clear ang mga bagong pili na file (HINDI PA UPLOADED) --}}
                        <button type="button"
                                id="clear-btn-{{ $key }}"
                                class="clear-button disabled"
                                onclick="clearNewUploads('{{ $key }}', '{{ $label }}')"
                                disabled>
                            CLEAR
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="buttons">
            <button class="submit-btn" type="submit" {{ $selectedPatient ? '' : 'disabled' }}>
                Submit
            </button>
        </div>
    </form>
</div>

<script>
function previewImages(event, type) {
    const files = event.target.files;
    const previewBox = document.getElementById('preview-' + type);
    const clearBtn = document.getElementById('clear-btn-' + type);

    // 1. Alisin ang mga luma (new previews) lang, huwag galawin ang naka-save
    previewBox.querySelectorAll('.preview-new').forEach(el => el.remove());
    
    // 2. Alisin ang placeholder text kung meron man
    const placeholder = previewBox.querySelector('.panel-title');
    if (placeholder) {
        placeholder.remove();
    }

    // 3. Ipakita ang bawat bagong selected image
    Array.from(files).forEach(file => {
        const wrapper = document.createElement('div');
        wrapper.classList.add('image-wrapper', 'preview-new');
        
        const img = document.createElement('img');
        img.classList.add('image-preview');
        img.src = URL.createObjectURL(file);
        
        wrapper.appendChild(img);
        previewBox.appendChild(wrapper);
    });

    // 4. I-enable o i-disable ang "CLEAR" button
    if (files.length > 0) {
        clearBtn.classList.remove('disabled');
        clearBtn.disabled = false;
    } else {
        clearBtn.classList.add('disabled');
        clearBtn.disabled = true;
    }
}

// Para i-clear ang mga file na KAKA-SELECT pa lang
function clearNewUploads(type, label) {
    const clearBtn = document.getElementById('clear-btn-' + type);
    const previewBox = document.getElementById('preview-' + type);
    // Hanapin ang file input
    const fileInput = document.querySelector(`input[name="images[${type}][]"]`);

    if (confirm('Are you sure you want to clear the new images selected for upload?')) {
        // 1. I-clear ang file input
        fileInput.value = null;

        // 2. Alisin ang mga bagong preview sa display
        previewBox.querySelectorAll('.preview-new').forEach(el => el.remove());

        // 3. I-disable ulit ang clear button
        clearBtn.classList.add('disabled');
        clearBtn.disabled = true;
        
        // 4. Kung wala nang images (saved man o bago), ibalik ang placeholder text
        if (previewBox.querySelectorAll('.image-wrapper').length === 0) {
            previewBox.innerHTML = '<div class="panel-title">' + label + '</div>';
        }
    }
}
</script>

@endsection