<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Electronic Health Record</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')

        {{-- Google Icons --}}
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        />

        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <style>
            .material-symbols-outlined {
                font-variation-settings:
                    'FILL' 1,
                    'wght' 400,
                    'GRAD' 0,
                    'opsz' 20;
            }

            /* Logic from Snippet 1: CSS handles the margin */
            .sidebar-open #main {
                margin-left: 260px;
            }

            .sidebar-transition {
                transition: margin-left 0.3s ease-in-out;
            }

            /* Page enter animation */
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

        {{-- Instant State Check (Prevents FOUC/Flicker) --}}
        <script>
            (function () {
                const isOpen = localStorage.getItem('sidebarOpen') === 'true';
                const doc = document.documentElement;
                if (isOpen) {
                    doc.classList.add('sidebar-open');
                } else {
                    doc.classList.remove('sidebar-open');
                }
            })();
        </script>
    </head>

    <body class="overflow-x-hidden bg-white">
        {{-- Sidebar --}}
        @include('components.admin-sidebar')

        {{-- Script to position the sidebar element immediately --}}
        <script>
            const isOpen = localStorage.getItem('sidebarOpen') === 'true';
            const sidebar = document.getElementById('mySidenav');

            if (sidebar) {
                sidebar.style.transition = 'none'; // Disable transition for initial load
                if (isOpen) {
                    sidebar.classList.remove('-translate-x-full');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
                // Re-enable transition after small delay
                setTimeout(() => {
                    sidebar.style.transition = '';
                }, 0);
            }
        </script>

        {{-- Header --}}
        @include('components.header')

        {{-- SweetAlert Messages Component --}}
        <x-sweetalert-messages />

        {{-- Main Content Wrapper --}}
        {{-- Added 'sidebar-transition' class here --}}
        <div id="main" class="sidebar-transition relative min-h-screen overflow-x-hidden bg-white">
            {{-- Background Images --}}
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
                {{-- Content --}}
                <main class="page-transition px-6 pt-[120px] transition-all duration-300 ease-in-out">
                    @yield('content')
                </main>
            </div>
        </div>

        {{-- JavaScript Logic --}}
        <script>
            function openNav() {
                const sidebar = document.getElementById('mySidenav');
                const arrow = document.getElementById('arrowBtn');

                // Slide sidebar in
                if (sidebar) sidebar.classList.remove('-translate-x-full');

                // Add class to HTML to trigger CSS margin shift on #main
                document.documentElement.classList.add('sidebar-open');

                // Handle Arrow Button if it exists
                if (arrow) {
                    arrow.classList.replace('-right-24', '-right-10');
                    arrow.classList.remove('hidden');
                }

                localStorage.setItem('sidebarOpen', 'true');
            }

            function closeNav() {
                const sidebar = document.getElementById('mySidenav');
                const arrow = document.getElementById('arrowBtn');

                // Slide sidebar out
                if (sidebar) sidebar.classList.add('-translate-x-full');

                // Remove class from HTML to reset #main margin
                document.documentElement.classList.remove('sidebar-open');

                // Handle Arrow Button
                if (arrow) arrow.classList.replace('-right-10', '-right-24');

                localStorage.setItem('sidebarOpen', 'false');

                setTimeout(() => {
                    if (arrow) arrow.classList.add('hidden');
                }, 200);
            }
        </script>

        @stack('scripts')
    </body>
</html>
