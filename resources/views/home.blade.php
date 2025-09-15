<?php

use Illuminate\Support\Facades\Auth;

?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EHR System - Pedia Ward</title>
    @vite(['resources/css/home-style.css'])
</head>

<body>

    <header class="header">
        <div class="logo">
            <a href="{{ route('home') }}"> <img src="img/ehr-logo.png" alt="Hospital Logo"> </a>
            <span>ELECTRONIC HEALTH RECORD</span>
        </div>
        <a href="{{ route('login.index') }}" class="login">LOG IN</a>
    </header>

    <!-- test alert -->
    @if (session('success'))
        <div style="background-color:green; color:white; padding:1rem; text-align:center; margin:1rem;">
            {{ session('success') }}
        </div>
    @endif
    <!-- test alert -->

    <section class="welcome">
        <div class="welcome-text">
            <h3>WELCOME TO</h3>
            <h1>ELECTRONIC HEALTH <br> RECORD SYSTEM <br>
                <span class="highlight">PEDIA WARD</span>
            </h1>
            <p>
                This system is designed to simplify healthcare documentation by providing a secure
                platform for recording and managing patient information. It supports the complete
                documentation of medical data including patient profiles, vital signs, treatments,
                and medication records, ensuring accurate and accessible healthcare management.
            </p>
            <a href="{{ route('login.index') }}" class="login">LOG IN</a>
        </div>
        <div class="welcome-image">
            <img src="img/doctor-kids.png" alt="Doctor with Kids">
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
                <img src="img/register.png" alt="Register Icon" class="box-icon">
                <h3>REGISTER</h3>
                <p>This is where new patients are registered into the system.</p>
                <div class="proceed">
                    <a href="{{ route('patients.index') }}">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/search-patient.png" alt="Search Icon" class="box-icon">
                <h3>SEARCH PATIENT</h3>
                <p>For viewing and finding existing patients and records to continue documentation.</p>
                <div class="proceed">
                    <a href="#">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/demographic-profile.png" alt="Demographic Icon" class="box-icon">
                <h3>DEMOGRAPHIC PROFILE</h3>
                <p>Store and manage patient information.</p>
                <div class="proceed">
                    <a href="#">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/medical-history.png" alt="History Icon" class="box-icon">
                <h3>MEDICAL HISTORY</h3>
                <p>Document past illnesses, surgeries, allergies, and family medical background.</p>
                <div class="proceed">
                    <a href="{{ route('medical-history') }}">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/physical-exam.png" alt="Exam Icon" class="box-icon">
                <h3>PHYSICAL EXAM</h3>
                <p>Record findings from clinical examinations and physical assessments.</p>
                <div class="proceed">
                    <a href="{{ route('physical-exam') }}">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/vital-signs.png" alt="Vitals Icon" class="box-icon">
                <h3>VITAL SIGNS</h3>
                <p>Track and update measurements such as temperature, blood pressure, pulse, and respiration.</p>
                <div class="proceed">
                    <a href="{{ route('vital-signs') }}">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/intake-and-output.png" alt="Intake Icon" class="box-icon">
                <h3>INTAKE AND OUTPUT</h3>
                <p>Monitor and log a patient’s fluid intake and output for accurate care management.</p>
                <div class="proceed">
                    <a href="{{ route('intake-and-output') }}">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/activities-of-daily-living.png" alt="ADL Icon" class="box-icon">
                <h3>ACTIVITIES OF DAILY LIVING</h3>
                <p>Assess a patient’s ability to perform daily tasks such as eating, bathing, and mobility.</p>
                <div class="proceed">
                    <a href="{{ route('act-of-daily-living') }}">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/lab-values.png" alt="Lab Icon" class="box-icon">
                <h3>LAB VALUES</h3>
                <p>Record laboratory test results and integrate findings into the patient’s medical record.</p>
                <div class="proceed">
                    <a href="{{ route('lab-values') }}">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/diagnostics.png" alt="Diagnostics Icon" class="box-icon">
                <h3>DIAGNOSTICS</h3>
                <p>Document diagnostic procedures and results such as imaging, scans, and other tests.</p>
                <div class="proceed">
                    <a href="#">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>


            <div class="box">
                <img src="img/iv-and-lines.png" alt="IVs & Lines icon" class="box-icon">
                <h3>IV's & LINES</h3>
                <p>Manage intravenous lines, infusions, and related treatments.</p>
                <div class="proceed">
                    <a href="{{ route('ivs-and-lines') }}">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/med-admini.png" alt="Medical administration icon" class="box-icon">
                <h3>MEDICAL ADMINISTRATION</h3>
                <p>Track prescribed medicines and record their administration schedules.</p>
                <div class="proceed">
                    <a href="#">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/med-recon.png" alt="Medical reconciliation" class="box-icon">
                <h3>MEDICAL RECONCILIATION</h3>
                <p>Compare medications to ensure accuracy and prevent duplication or errors.</p>
                <div class="proceed">
                    <a href="#">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/discharge-planning.png" alt="Discharge Icon" class="box-icon">
                <h3>DISCHARGE PLANNING</h3>
                <p>Plan and document the patient’s care instructions upon discharge.</p>
                <div class="proceed">
                    <a href="#">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>

            <div class="box">
                <img src="img/about.png" alt="about icon" class="box-icon">
                <h3>ABOUT</h3>
                <p>Provides system information, purpose, and guidelines for users.</p>
                <div class="proceed">
                    <a href="#">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>


        </div>
    </section>

</body>

</html>