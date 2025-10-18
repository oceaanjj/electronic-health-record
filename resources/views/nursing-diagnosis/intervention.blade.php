@extends('layouts.app')

@section('title', 'Nursing Diagnosis - Step 4')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Header --}}
            <div class="text-center mb-4">
                <h2>Nursing Diagnosis Process (Step 4 of 4)</h2>
                 {{-- ✅ FIX: Check if $selectedPatient exists before using it --}}
                @if ($selectedPatient)
                    <p class="text-muted">Patient: <strong>{{ $selectedPatient->name }}</strong></p>
                @else
                    <p class="text-danger"><strong>Error: Patient data not found for this exam.</strong></p>
                @endif
            </div>

            {{-- Recommendation Box --}}
            @if (!empty($recommendation) && isset($recommendation['evaluation']))
                <div class="alert alert-info shadow-sm">
                    <h5 class="alert-heading">💡 Recommended Evaluation</h5>
                    <p>{{ $recommendation['evaluation'] }}</p>
                </div>
            @endif

            {{-- Form --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title">4. Evaluation</h4>
                    <p>Assess the patient's response to the interventions.</p>
                    <form action="{{ route('nursing-diagnosis.store', ['physicalExamId' => $physicalExam->id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="evaluation" class="form-control @error('evaluation') is-invalid @enderror" rows="5" placeholder="Enter the evaluation..." required>{{ old('evaluation', session('nursing_diagnosis.evaluation') ?? ($recommendation['evaluation'] ?? '')) }}</textarea>
                             @error('evaluation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('nursing-diagnosis.create-step-3', ['physicalExamId' => $physicalExam->id]) }}" class="btn btn-secondary">← Back: Intervention</a>
                            <button type="submit" class="btn btn-success">✓ Save Complete Diagnosis</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection