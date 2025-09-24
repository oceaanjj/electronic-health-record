<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'EHR')</title>
    <!-- @vite('resources/css/app.css') -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-100">

    @include('components.sidebar')

    <div id="main" class="transition-transform duration-300 ease-in-out">

        <div class="flex flex-col min-h-screen">

            @include('components.header')


            <!-- alerts -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show text-center w-75 mx-auto popup-alert"
                    role="alert" id="success-alert">
                    {{ session('success') }}
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show text-center w-75 mx-auto popup-alert"
                    role="alert" id="error-alert">
                    {{ session('error') }}
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show text-center w-75 mx-auto popup-alert"
                    role="alert" id="error-alert">
                    <h5>Errors:</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
                </div>
            @endif



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