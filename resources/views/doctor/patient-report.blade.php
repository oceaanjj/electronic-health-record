@extends('layouts.app')

@section('title', 'Generate Patient Report')

@section('content')
<div class="container">
    <h1>Generate Patient Report</h1>

    <div class="card">
        <div class="card-header">Generate Patient Report</div>
        <div class="card-body">
            <form id="reportForm" action="{{ route('doctor.generate-report') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="patient_search">Search Patient:</label>
                    <input type="text" id="patient_search" class="form-control" placeholder="Search by name or ID">
                </div>
                <div class="form-group">
                    <label for="patient_id">Select Patient:</label>
                    <select name="patient_id" id="patient_id" class="form-control" required>
                        <option value="">-- Select a Patient --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->patient_id }}">{{ $patient->name }} (ID: {{ $patient->patient_id }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Generate Report</button>
                <a id="downloadPdfBtn" href="#" class="btn btn-success" style="display: none;" target="_blank">Download PDF Report</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const patientSearch = document.getElementById('patient_search');
        const patientSelect = document.getElementById('patient_id');
        const downloadPdfBtn = document.getElementById('downloadPdfBtn');
        const allPatientOptions = Array.from(patientSelect.options).filter(option => option.value !== '');

        function updateDownloadLink(patientId) {
            if (patientId) {
                downloadPdfBtn.href = `/doctor/patient-report/${patientId}/pdf`;
                downloadPdfBtn.style.display = 'inline-block';
            } else {
                downloadPdfBtn.href = '#';
                downloadPdfBtn.style.display = 'none';
            }
        }

        patientSearch.addEventListener('keyup', function () {
            const searchTerm = patientSearch.value.toLowerCase();
            patientSelect.innerHTML = '<option value="">-- Select a Patient --</option>';

            allPatientOptions.forEach(option => {
                if (option.textContent.toLowerCase().includes(searchTerm)) {
                    patientSelect.appendChild(option.cloneNode(true));
                }
            });
            // If only one option left, select it
            if (patientSelect.options.length === 2) {
                patientSelect.selectedIndex = 1;
                updateDownloadLink(patientSelect.value);
            } else {
                updateDownloadLink('');
            }
        });

        patientSelect.addEventListener('change', function () {
            updateDownloadLink(this.value);
        });

        // Initial state
        updateDownloadLink(patientSelect.value);
    });
</script>
@endpush
