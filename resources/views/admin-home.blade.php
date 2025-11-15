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

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <a href="#" class="login" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            LOG OUT
        </a>


    </header>

    <!-- test alert -->
    {{-- @if (session('success'))
        <div style="background-color:green; color:white; padding:1rem; text-align:center; margin:1rem;">
            {{ session('success') }}
        </div>
    @endif --}}
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
            <a href="#" class="login">LOG IN</a>
        </div>
        <div class="welcome-image">
            <img src="img/doctor-kids.png" alt="Doctor with Kids">
        </div>
    </section>

    <!-- EHR Components -->
    <section class="ehr-container">

        <h2>ADMIN</h2>
        <p>
            Our system offers a set of integrated components that make patient data documentation
            and healthcare management simple, secure, and accessible online.
        </p>

        <div class="boxes">

            <div class="box">
                <img src="img/register.png" alt="Register Icon" class="box-icon">
                <h3>BOX</h3>
                <p>This is where new patients are registered into the system.</p>
                <div class="proceed">
                    <a href="#">PROCEED <span class="arrow">▶</span></a>
                </div>
            </div>


    </section>

</body>

</html>