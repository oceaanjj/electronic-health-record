@extends('layouts.doctor')
@section('title', 'Doctor Home')
@section('content')


    <section class="welcome">
        <div class="welcome-text">
            <h3>WELCOME TO</h3>
            <h1>ELECTRONIC HEALTH <br> RECORD SYSTEM <br>
                <span class="highlight">DOCTOR'S PORTAL</span>
            </h1>
            <p>
                This system is designed to simplify healthcare documentation by providing a secure
                platform for recording and managing patient information. It supports the complete
                documentation of medical data including patient profiles, vital signs, treatments,
                and medication records, ensuring accurate and accessible healthcare management.
            </p>
        </div>
        <div class="welcome-image">
            <img src="{{ asset('img/doctor-kids.png') }}" alt="Doctor with Kids">
        </div>
    </section>

    <!-- EHR Components -->
    <section class="ehr-container">

        <h2>EHR - COMPONENTS</h2>
        <p>
            Our system offers a set of integrated components that make patient data documentation
            and healthcare management simple, secure, and accessible online.
        </p>

        <div class="boxes">

            <div class="box">
                <img src="{{ asset('img/search-patient.png') }}" alt="Generate Report Icon" class="box-icon">
                <h3>GENERATE PATIENT REPORT</h3>
                <p>Generate a comprehensive report of a patient's medical history and records.</p>
                <a class="proceed" href="{{ route('doctor.patient-report') }}">
                    <div>PROCEED <span class="arrow">▶</span></div>
                </a>
            </div>

        </div>
    </section>

@endsection

@push('styles')
    @vite(['resources/css/home-style.css'])
@endpush