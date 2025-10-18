@extends('layouts.app')

@section('title', 'Nursing Diagnosis - Step 1')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Header --}}
            <div class="text-center mb-4">
                <h2>Nursing Diagnosis Process (Step 1 of 4)</h2>
                {{-- ✅ FIX: Check if $selectedPatient exists before using it --}}
                @if ($selectedPatient)
                    <p class="text-muted">Patient: <strong>{{ $selectedPatient->name }}</strong></p>
                @else
                    <p class="text-danger"><strong>Error: Patient data not found for this exam.</strong></p>
                @endif
            </div>

            {{-- Recommendation Box --}}
            @if (!empty($recommendation) && isset($recommendation['diagnosis']))
                <div class="alert alert-info shadow-sm">
                    <h5 class="alert-heading">💡 Recommended Diagnosis</h5>
                    <p>{{ $recommendation['diagnosis'] }}</p>
                </div>
            @else
                <div class="alert alert-secondary">
                    <p class="mb-0">No specific recommendation was generated. Please enter a diagnosis manually.</p>
                </div>
            @endif

            {{-- Form --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">1. Diagnosis</h4>
                    <p>Define the nursing diagnosis based on the assessment.</p>
                    <form action="{{ route('nursing-diagnosis.store-step-1', ['physicalExamId' => $physicalExam->id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="diagnosis" class="form-control @error('diagnosis') is-invalid @enderror" rows="5" placeholder="Enter the nursing diagnosis..." required>{{ old('diagnosis', session('nursing_diagnosis.diagnosis') ?? ($recommendation['diagnosis'] ?? '')) }}</textarea>
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Next: Planning →</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection