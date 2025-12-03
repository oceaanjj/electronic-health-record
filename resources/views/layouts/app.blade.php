<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electronic Health Record</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- **google icons library nyhahhahaha** --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- !important for instant alerts-->

    <style>
        .material-symbols-outlined {
        font-variation-settings:
        'FILL' 1,
        'wght' 400,
        'GRAD' 0,
        'opsz' 20
        }
    </style>
</head>

<body class="bg-white overflow-x-hidden">

    {{-- Sidebar --}}
    @include('components.sidebar')

   <script>
        // FIX: Only position the sidebar here, as the #main element isn't available yet.
        const isOpen = localStorage.getItem("sidebarOpen") === "true";
        const sidebar = document.getElementById("mySidenav");

        if (sidebar) {
            // Stop the initial animation instantly
            sidebar.style.transition = 'none';

            if (isOpen) {
                sidebar.classList.remove("-translate-x-full");
            } else {
                sidebar.classList.add("-translate-x-full");
            }

            // Re-enable transition after state is set
            setTimeout(() => {
                sidebar.style.transition = '';
            }, 0);
        }
    </script>

    {{-- Header --}}
    @include('components.header')

    {{-- SweetAlert Messages Component --}}
    <x-sweetalert-messages />



    {{-- Main Content --}}
    <div id="main" class="relative min-h-screen overflow-x-hidden bg-white transition-all duration-300 ease-in-out">
        

        <img src="{{ asset('img/bg-design-right.png') }}" alt="Top right design"
            class="absolute top-[120px] right-0 w-[320px] object-contain opacity-90 select-none pointer-events-none z-0">
        <img src="{{ asset('img/bg-design-left.png') }}" alt="Bottom left design"
            class="absolute bottom-0 left-0 w-[320px] object-contain opacity-90 select-none pointer-events-none z-0">



        <div class="relative z-10">

            {{-- content ng page --}}
            <main class="pt-[120px] transition-all duration-300 ease-in-out">

                @yield('content')
            </main>
        </div>
    </div>




    <script>

        // 🚀 START: CORRECTED DOMContentLoaded BLOCK 🚀
        // This now instantly sets the margin on #main when it becomes available.
        window.addEventListener("DOMContentLoaded", function () {
            const main = document.getElementById("main");
            const arrow = document.getElementById("arrowBtn");
            const isOpen = localStorage.getItem("sidebarOpen") === "true";

            // 1. Instantly apply state to #main without transition (Flicker Fix)
            if (main) {
                main.style.transition = 'none'; // Disable transition temporarily
                
                if (isOpen) {
                    main.classList.add("ml-[260px]");
                } else {
                    main.classList.remove("ml-[260px]");
                }
            }
            
            // 2. Set initial arrow state (as before)
            if (arrow) {
                if (isOpen) {
                    arrow.classList.replace("-right-24", "-right-10");
                    arrow.classList.remove("hidden");
                } else {
                    arrow.classList.replace("-right-10", "-right-24");
                    arrow.classList.add("hidden");
                }
            }

            // 3. Re-enable transition for interaction
            requestAnimationFrame(() => {
                if (main) main.style.transition = ''; // Re-enable CSS transition
            });
        });
        // 🚀 END: CORRECTED DOMContentLoaded BLOCK 🚀


        // 2. openNav function: Handles click-to-open logic (No changes needed here).
        function openNav() {
            const sidebar = document.getElementById("mySidenav");
            const main = document.getElementById("main");
            const arrow = document.getElementById("arrowBtn");

            if (sidebar) sidebar.classList.remove("-translate-x-full");
            if (main) main.classList.add("ml-[260px]");
            
            if (arrow) {
                arrow.classList.replace("-right-24", "-right-10");
                arrow.classList.remove("hidden");
            }

            localStorage.setItem("sidebarOpen", "true");
        }

        // 3. closeNav function: Handles click-to-close logic (No changes needed here).
        function closeNav() {
            const sidebar = document.getElementById("mySidenav");
            const main = document.getElementById("main");
            const arrow = document.getElementById("arrowBtn");

            if (sidebar) sidebar.classList.add("-translate-x-full");
            if (main) main.classList.remove("ml-[260px]");
            
            if (arrow) arrow.classList.replace("-right-10", "-right-24");

            localStorage.setItem("sidebarOpen", "false");

            setTimeout(() => {
                if (arrow) arrow.classList.add("hidden"); 
            }, 0);
        }
    </script>

    @stack('scripts')





</body>

</html>