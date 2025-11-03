@extends('layouts.app')

@section('title', 'Doctor Home')

@section('content')
<div class="container">
    <h1>DOCTOR HOME</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Generate Patient Report</h2>
        <a href="{{ route('doctor.patient-report') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Generate Patient Report</a>
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