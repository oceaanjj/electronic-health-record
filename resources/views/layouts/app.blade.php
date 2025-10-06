<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electronic Health Record</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white overflow-x-hidden">

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{--  
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
    --}}

    {{-- Main Content --}}
    <div id="main" class="transition-all duration-300 ease-in-out">
        {{-- Header --}}
        @include('components.header')

        {{-- Page Content --}}
        <main class="transition-all duration-300 ease-in-out">
            @yield('content')
        </main>
    </div>

    {{-- Sidebar Animation --}}
    <script>
        function openNav() {
            const sidebar = document.getElementById("mySidenav");
            const main = document.getElementById("main");

            sidebar.classList.remove("-translate-x-full");
            main.classList.add("ml-[260px]"); 
        }

        function closeNav() {
            const sidebar = document.getElementById("mySidenav");
            const main = document.getElementById("main");

            sidebar.classList.add("-translate-x-full");
            main.classList.remove("ml-[260px]");
        }
    </script>

</body>
</html>



