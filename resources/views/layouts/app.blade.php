<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'EHR')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

  @include('components.sidebar')

<div id="main" class="transition-transform duration-300 ease-in-out">
    
    <div class="flex flex-col min-h-screen">
        {{-- HEADER --}}
        @include('components.header')

        {{-- CONTENT --}}
        <main class="flex-1">
            @yield('content')
        </main>
    </div>

</div>



    <script>
        function openNav() {
            document.getElementById("mySidenav").classList.remove("-translate-x-full");
            document.getElementById("mySidenav").classList.add("translate-x-0");
            document.getElementById("main").classList.add("translate-x-75");
        }

        function closeNav() {
            document.getElementById("mySidenav").classList.remove("translate-x-0");
            document.getElementById("mySidenav").classList.add("-translate-x-full");
            document.getElementById("main").classList.remove("translate-x-75");
        }
    </script>

</body>
</html>
