<?php

use Illuminate\Support\Facades\Auth;

?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>EHR System - Pedia Ward</title>
        @vite(['resources/css/home-style.css'])
    </head>

    <body>
        <header class="header">
            <div class="logo">
                <a href="{{ route('home') }}"><img src="img/ehr-logo.png" alt="Hospital Logo" /></a>
                <span>ELECTRONIC HEALTH RECORD</span>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
                @csrf
            </form>
            <a
                href="#"
                class="login"
                onclick="
                    event.preventDefault();
                    document.getElementById('logout-form').submit();
                "
            >
                LOG OUT
            </a>
        </header>

        <!-- test alert -->
        {{--
            @if (session('success'))
            <div style="background-color:green; color:white; padding:1rem; text-align:center; margin:1rem;">
            {{ session('success') }}
            </div>
            @endif
        --}}
    </body>
</html>
