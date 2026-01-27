<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electronic Health Record</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- !important for instant alerts-->
</head>

<body class="bg-white overflow-x-hidden">

    {{-- Sidebar --}}
    @include('components.doctor.sidebar')

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
            <main class="pt-[120px] px-6 transition-all duration-300 ease-in-out">

                @yield('content')
            </main>
        </div>
    </div>




    <script>

        window.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.getElementById("mySidenav");
            const main = document.getElementById("main");
            const arrow = document.getElementById("arrowBtn");


            sidebar.style.transition = "none";
            main && (main.style.transition = "none")

            const isOpen = localStorage.getItem("sidebarOpen") === "true";

            if (isOpen) {
                sidebar.classList.remove("-translate-x-full");
                main?.classList.add("ml-[260px]");
                arrow.classList.replace("-right-24", "-right-10");
                arrow.classList.remove("hidden");
            } else {
                sidebar.classList.add("-translate-x-full");
                main?.classList.remove("ml-[260px]");
                arrow.classList.replace("-right-10", "-right-24");
                arrow.classList.add("hidden");
            }

            void sidebar.offsetHeight;

            requestAnimationFrame(() => {
                sidebar.style.transition = "";
                main && (main.style.transition = "");
            });
        });

        function openNav() {
            const sidebar = document.getElementById("mySidenav");
            const main = document.getElementById("main");
            const arrow = document.getElementById("arrowBtn");

            sidebar.classList.remove("-translate-x-full");
            main.classList.add("ml-[260px]");
            arrow.classList.replace("-right-24", "-right-10");
            arrow.classList.remove("hidden");


            localStorage.setItem("sidebarOpen", "true");

        }

        function closeNav() {
            const sidebar = document.getElementById("mySidenav");
            const main = document.getElementById("main");
            const arrow = document.getElementById("arrowBtn");

            sidebar.classList.add("-translate-x-full");
            main.classList.remove("ml-[260px]");
            arrow.classList.replace("-right-10", "-right-24");

            localStorage.setItem("sidebarOpen", "false");

            setTimeout(() => {
                arrowBtn.classList.add("hidden");
            }, 0);
        }
    </script>

    @stack('scripts')


</body>

</html>