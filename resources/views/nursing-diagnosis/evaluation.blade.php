@extends('layouts.app')

@section('title', 'Nursing Evaluation')

@section('content')

<body>
    {{-- PATIENT DROP-DOWN (to maintain context) --}}
    <div class="header">
        <label for="patient_id" style="color: white;">PATIENT NAME :</label>
        <select id="patient_info" name="patient_id" disabled>
            <option>{{ $selectedPatient->name ?? 'No Patient Selected' }}</option>
        </select>
    </div>

    {{-- Main Content --}}
    <div class="nursing-container">
        <div class="nursing-sidebar">
            <h2>EVALUATION</h2>
        </div>
        <div class="nursing-main-content">
            <form action="{{ route('nursing-diagnosis.store', ['physicalExamId' => $physicalExam->id]) }}" method="POST" class="nursing-form-area">
                @csrf
                <textarea name="evaluation" placeholder="Enter evaluation details here...">{{ session('nursing_diagnosis.evaluation') }}</textarea>
                
                <div class="nursing-button-container">
                    <div>
                        <a href="{{ route('nursing-diagnosis.create-step-3', ['physicalExamId' => $physicalExam->id]) }}" class="nursing-btn">GO BACK</a>
                    </div>
                    <div class="nursing-right-buttons">
                        <button type="submit" class="nursing-btn">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
@endsection

@push('styles')
    @vite(['resources/css/nursing-diagnosis-style.css'])
@endpush
```
