@extends('layouts.app')

@section('title', 'Nursing Diagnosis - Step 2')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Header --}}
            <div class="text-center mb-4">
                <h2>Nursing Diagnosis Process (Step 2 of 4)</h2>
                 {{-- ✅ FIX: Check if $selectedPatient exists before using it --}}
                @if ($selectedPatient)
                    <p class="text-muted">Patient: <strong>{{ $selectedPatient->name }}</strong></p>
                @else
                    <p class="text-danger"><strong>Error: Patient data not found for this exam.</strong></p>
                @endif
            </div>

            {{-- Recommendation Box --}}
            @if (!empty($recommendation) && isset($recommendation['planning']))
                <div class="alert alert-info shadow-sm">
                    <h5 class="alert-heading">💡 Recommended Planning</h5>
                    <p>{{ $recommendation['planning'] }}</p>
                </div>
            @endif

            {{-- Form --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">2. Planning</h4>
                    <p>Establish goals and desired outcomes.</p>
                    <form action="{{ route('nursing-diagnosis.store-step-2', ['physicalExamId' => $physicalExam->id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="planning" class="form-control @error('planning') is-invalid @enderror" rows="5" placeholder="Enter the care plan..." required>{{ old('planning', session('nursing_diagnosis.planning') ?? ($recommendation['planning'] ?? '')) }}</textarea>
                             @error('planning')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('nursing-diagnosis.create-step-1', ['physicalExamId' => $physicalExam->id]) }}" class="btn btn-secondary">← Back: Diagnosis</a>
                            <button type="submit" class="btn btn-primary">Next: Intervention →</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection