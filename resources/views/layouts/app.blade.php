<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Electronic Health Record</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="preload" as="image" href="img/ehr-logo.png">
        <link rel="preload" as="image" href="img/loading.png">

        {{-- **google icons library nyhahhahaha** --}}
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        />

        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <!-- !important for instant alerts-->

        <style>
            .material-symbols-outlined {
                font-variation-settings:
                    'FILL' 1,
                    'wght' 400,
                    'GRAD' 0,
                    'opsz' 20;
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
                const isOpen = localStorage.getItem('sidebarOpen') === 'true';
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



    {{-- Sidebar --}}
    @include('components.sidebar')

        <script>
            const isOpen = localStorage.getItem('sidebarOpen') === 'true';
            const sidebar = document.getElementById('mySidenav');

            if (sidebar) {
                sidebar.style.transition = 'none';

                if (isOpen) {
                    sidebar.classList.remove('-translate-x-full');
                } else {
                    sidebar.classList.add('-translate-x-full');
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

        <div id="main" class="sidebar-transition relative min-h-screen overflow-x-hidden bg-white">
            <img
                src="{{ asset('img/bg-design-right.png') }}"
                alt="Top right design"
                class="pointer-events-none absolute top-[120px] right-0 z-0 w-[320px] object-contain opacity-90 select-none"
            />
            <img
                src="{{ asset('img/bg-design-left.png') }}"
                alt="Bottom left design"
                class="pointer-events-none absolute bottom-0 left-0 z-0 w-[320px] object-contain opacity-90 select-none"
            />

            <div class="relative z-10">
                {{-- content ng page --}}
                <main class="page-transition pt-[120px] transition-all duration-300 ease-in-out">
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
            // The mobileMenuButton should NOT be hidden when the sidebar opens,
            // as it is used to close the sidebar on mobile.
            // if (mobileMenuButton) {
            //     mobileMenuButton.classList.add("hidden");
            // }
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
                if (arrow) arrow.classList.add('hidden');
            }, 300); // 300ms matches your transition-duration
        }
    </script>

        @stack('scripts')
    </body>
</html>
