@extends('layouts.app')

@section('title', 'Nursing Diagnosis')

@section('content')

<body>
    {{-- PATIENT DROP-DOWN FORM --}}
    <div class="header">
        <label for="patient_id" style="color: white;">PATIENT NAME :</label>
        <select id="patient_info" name="patient_id" disabled>
             <option>{{ $selectedPatient->name ?? 'No Patient Selected' }}</option>
        </select>
    </div>

    {{-- Main Content --}}
    <div class="nursing-container">
        <div class="nursing-sidebar">
            <h2>NURSING DIAGNOSIS</h2>
        </div>
        <div class="nursing-main-content">
            <form action="{{ route('nursing-diagnosis.store-step-1', ['physicalExamId' => $physicalExam->id]) }}" method="POST" class="nursing-form-area">
                @csrf
                
                <textarea name="diagnosis" placeholder="Enter nursing diagnosis here...">{{ session('nursing_diagnosis.diagnosis') }}</textarea>
                
                <div class="nursing-button-container">
                    <div>
                        <a href="{{ route('physical-exam.index') }}" class="nursing-btn">GO BACK</a>
                    </div>
                    <div class="nursing-right-buttons">
                         <button type="submit" class="nursing-btn">SUBMIT</button>
                        <button type="submit" class="nursing-btn">PROCEED TO PLANNING</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
@endsection


