@extends('layouts.app')

@section('title', 'Nursing Intervention')

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
            <h2>INTERVENTION</h2>
        </div>
        <div class="nursing-main-content">
            <form action="{{ route('nursing-diagnosis.store-step-3', ['physicalExamId' => $physicalExam->id]) }}" method="POST" class="nursing-form-area">
                @csrf
                <textarea name="intervention" placeholder="Enter intervention details here...">{{ session('nursing_diagnosis.intervention') }}</textarea>
                
                <div class="nursing-button-container">
                    <div>
                        <a href="{{ route('nursing-diagnosis.create-step-2', ['physicalExamId' => $physicalExam->id]) }}" class="nursing-btn">GO BACK</a>
                    </div>
                    <div class="nursing-right-buttons">
                        <button type="submit" class="nursing-btn">SUBMIT</button>
                        <button type="submit" class="nursing-btn">PROCEED TO EVALUATION</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
@endsection


