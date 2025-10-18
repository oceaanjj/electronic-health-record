@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- Assuming a patient relationship exists on your PhysicalExam model --}}
        <h1>Nursing Diagnoses</h1>
        @if($physicalExam->patient)
            <h4 class="text-muted">Patient: {{ $physicalExam->patient->name }}</h4>
        @endif
    </div>

    {{-- Session Messages for User Feedback --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        {{-- Column for Creating New Diagnoses --}}
        <div class="col-md-5">
            <h3>Add New Diagnosis</h3>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Automatic DPIE Generation</h5>
                    <p class="card-text text-muted">Use the rule-based system to generate a diagnosis based on the physical exam findings.</p>
                    {{-- Form to trigger the automatic recommendation --}}
                    <form action="{{ route('nursing-diagnosis.generate', $physicalExam->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to generate and save an automatic diagnosis?');">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">✨ Generate Automatic DPIE</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manual Entry</h5>
                     {{-- NOTE: Make sure you have a named route like ->name('nursing-diagnosis.store') in your routes file --}}
                    <form action="{{-- route('nursing-diagnosis.store', $physicalExam->id) --}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="diagnosis" class="form-label"><strong>Diagnosis</strong></label>
                            <textarea class="form-control @error('diagnosis') is-invalid @enderror" id="diagnosis" name="diagnosis" rows="3" required>{{ old('diagnosis') }}</textarea>
                            @error('diagnosis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="planning" class="form-label"><strong>Planning</strong></label>
                            <textarea class="form-control @error('planning') is-invalid @enderror" id="planning" name="planning" rows="3" required>{{ old('planning') }}</textarea>
                            @error('planning')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="intervention" class="form-label"><strong>Intervention</strong></label>
                            <textarea class="form-control @error('intervention') is-invalid @enderror" id="intervention" name="intervention" rows="3" required>{{ old('intervention') }}</textarea>
                            @error('intervention')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="evaluation" class="form-label"><strong>Evaluation</strong></label>
                            <textarea class="form-control @error('evaluation') is-invalid @enderror" id="evaluation" name="evaluation" rows="3" required>{{ old('evaluation') }}</textarea>
                            @error('evaluation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100">Save Manual Diagnosis</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Column for Displaying Existing Diagnoses --}}
        <div class="col-md-7">
            <h3>Recorded Diagnoses</h3>
            @forelse ($physicalExam->nursingDiagnoses as $diag)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <strong>Diagnosis #{{ $diag->id }}</strong>
                        <span class="text-muted">{{ $diag->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Diagnosis:</dt>
                            <dd class="col-sm-9">{{ $diag->diagnosis }}</dd>

                            <dt class="col-sm-3">Planning:</dt>
                            <dd class="col-sm-9">{{ $diag->planning }}</dd>

                            <dt class="col-sm-3">Intervention:</dt>
                            <dd class="col-sm-9">{{ $diag->intervention }}</dd>

                            <dt class="col-sm-3">Evaluation:</dt>
                            <dd class="col-sm-9">{{ $diag->evaluation }}</dd>
                        </dl>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    No nursing diagnoses have been recorded for this exam yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection