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

        /* Adjust main content margin only for medium and larger screens when sidebar is open */
        @media (min-width: 768px) { /* md breakpoint */
            .sidebar-open #main {
                margin-left: 260px;
            }
        }

        .sidebar-transition {
            transition: margin-left 0.3s ease-in-out;
        }


        @keyframes pageEnter {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-transition {
            animation: pageEnter 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        }
    </style>

    <script>
        (function () {
            const isOpen = localStorage.getItem("sidebarOpen") === "true";
            const doc = document.documentElement;

            if (isOpen) {
                // For small screens, always close sidebar initially
                if (window.innerWidth < 768) { // md breakpoint
                    doc.classList.remove("sidebar-open");
                    localStorage.setItem("sidebarOpen", "false");
                } else {
                    doc.classList.add("sidebar-open");
                }
            } else {
                doc.classList.remove("sidebar-open");
            }
        })();
    </script>

</head>

<body class="bg-white overflow-x-hidden">

    {{-- Hamburger menu for mobile --}}
    <button id="mobileMenuButton" class="absolute top-4 left-4 z-50 p-2 focus:outline-none focus:ring md:hidden" onclick="openNav()">
        <span class="material-symbols-outlined text-ehr text-3xl">menu</span>
    </button>

    {{-- Sidebar --}}
    @include('components.sidebar')

   <script>
        const isOpen = localStorage.getItem("sidebarOpen") === "true";
        const sidebar = document.getElementById("mySidenav");

        if (sidebar) {
            sidebar.style.transition = 'none';

            if (isOpen) {
                sidebar.classList.remove("-translate-x-full");
            } else {
                sidebar.classList.add("-translate-x-full");
            }

            setTimeout(() => {
                sidebar.style.transition = '';
            }, 0);
        }
    </script>

    {{-- Header --}}
    @include('components.header')

    {{-- SweetAlert Messages Component --}}
    <x-sweetalert-messages />




    <div id="main" class="relative min-h-screen overflow-x-hidden bg-white sidebar-transition">


        <img src="{{ asset('img/bg-design-right.png') }}" alt="Top right design"
            class="absolute top-[120px] right-0 w-[320px] object-contain opacity-90 select-none pointer-events-none z-0">
        <img src="{{ asset('img/bg-design-left.png') }}" alt="Bottom left design"
            class="absolute bottom-0 left-0 w-[320px] object-contain opacity-90 select-none pointer-events-none z-0">



        <div class="relative z-10">

            {{-- content ng page --}}
            <main class="pt-[120px] transition-all duration-300 ease-in-out page-transition">


                @yield('content')
            </main>
        </div>
    </div>




    <script>
        function openNav() {
            const sidebar = document.getElementById("mySidenav");
            const arrow = document.getElementById("arrowBtn");
            const mobileMenuButton = document.getElementById("mobileMenuButton"); // Get mobile menu button

            if (sidebar) sidebar.classList.remove("-translate-x-full");
            document.documentElement.classList.add("sidebar-open");
            localStorage.setItem("sidebarOpen", "true");

            if (arrow && window.innerWidth >= 768) { // Only show arrow on md and up
                arrow.classList.replace("-right-24", "-right-10");
            }
            if (mobileMenuButton) { // Hide mobile menu button when sidebar opens
                mobileMenuButton.classList.add("hidden");
            }
        }

        function closeNav() {
            const sidebar = document.getElementById("mySidenav");
            const arrow = document.getElementById("arrowBtn");
            const mobileMenuButton = document.getElementById("mobileMenuButton"); // Get mobile menu button

            if (sidebar) sidebar.classList.add("-translate-x-full");
            document.documentElement.classList.remove("sidebar-open");
            localStorage.setItem("sidebarOpen", "false");

            if (arrow && window.innerWidth >= 768) { // Only hide arrow on md and up
                arrow.classList.replace("-right-10", "-right-24");
            }
            if (mobileMenuButton) { // Show mobile menu button when sidebar closes
                mobileMenuButton.classList.remove("hidden");
            }

            setTimeout(() => {
                if (arrow) arrow.classList.add("hidden");
            }, 200);
        }
    </script>

    @stack('scripts')

    
</body>

</html>