<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'EHR')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

    <span class="p-4 cursor-pointer bg-ehr text-white" onclick="openNav()">☰</span>

    <!-- trying include... connecting side bar (copy pasting the code from sidebar.blade.php) -->
    @include('components.sidebar')


    <div id="main" class="p-6 transition-transform duration-300 ease-in-out">
        <!-- naglalagay ng content from other views file -->
        <!-- NOTE : dito yung main pages natin -->
        @yield('content')
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
