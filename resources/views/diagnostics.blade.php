
@extends('layouts.app')

@section('title', 'Diagnostics')

@section('content')
<div class="container">
    <form action="{{ route('diagnostics.select') }}" method="POST">
        @csrf
        <div class="header">
            <label for="patient_id">PATIENT NAME :</label>
            <select id="patient_info" name="patient_id" onchange="this.form.submit()">
                <option value="">-- Select Patient --</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->patient_id }}" {{ (isset($selectedPatient) && $selectedPatient->patient_id == $patient->patient_id) ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

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
                <div class="image-container">
                    @if(isset($images[$key]) && $images[$key]->count() > 0)
                        @php $latest = $images[$key]->last(); @endphp
                        <img src="{{ Storage::url($latest->path) }}" alt="{{ $label }}" class="image-preview">
                    @else
                        <div class="panel-title">{{ $label }}</div>
                    @endif
                </div>

                <div class="button-container">
                    <form action="{{ route('diagnostics.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patientId ?? '' }}">
                        <input type="hidden" name="type" value="{{ $key }}">
                        
                        <label class="insert-photo {{ $patientId ? 'enabled' : 'disabled' }}">
                            INSERT PHOTO
                            <input type="file" name="image" accept="image/*" onchange="this.form.submit()" style="display:none;" @if(!$patientId) disabled @endif>
                        </label>
                    </form>

                    @if(isset($images[$key]) && $images[$key]->count() > 0)
                        @php $latest = $images[$key]->last(); @endphp
                        <form action="{{ route('diagnostics.destroy', $latest->id) }}" method="POST" onsubmit="return confirm('Delete this image?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button">DELETE</button>
                        </form>
                    @else
                        <button disabled class="delete-button disabled">DELETE</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="buttons">
      <button class="btn" type="submit">Submit</button>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/diagnostics.css'])
@endpush