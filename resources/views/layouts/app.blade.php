<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'EHR')</title>
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body class="bg-gray-100">

  @include('components.sidebar')

<div id="main" class="transition-transform duration-300 ease-in-out">
    
    <div class="flex flex-col min-h-screen">

        @include('components.header')

     
        <main class="flex-1">
            @yield('content')
        </main>
    </div>

</div>



    {{-- para sa side bar ito, mag slide yung sidebar and yung main content is sasama sa pag-usog --}}
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
