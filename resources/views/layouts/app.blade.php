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
    
    {{-- Header --}}

    @include('components.header')

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
        <div id="main" class="relative min-h-screen overflow-y-auto overflow-x-hidden bg-white transition-all duration-300 ease-in-out">

    
            <img 
                src="{{ asset('img/bg-design-right.png') }}" 
                alt="Top right design"
                class="absolute top-[120px] right-0 w-[320px] object-contain opacity-90 select-none pointer-events-none z-0"
            >
            <img 
                src="{{ asset('img/bg-design-left.png') }}" 
                alt="Bottom left design"
                class="absolute bottom-0 left-0 w-[320px] object-contain opacity-90 select-none pointer-events-none z-0"
            >

            

            <div class="relative z-10">
             
                

                {{-- content ng page --}}
                <main class="pt-[120px] pb-0transition-all duration-300 ease-in-out">

                    @yield('content')
                </main>
            </div>
        </div>







    <script>
        function openNav() {
            const sidebar = document.getElementById("mySidenav");
            const main = document.getElementById("main");
            const arrow = document.getElementById("arrowBtn");

            sidebar.classList.remove("-translate-x-full");
            main.classList.add("ml-[260px]"); 
            arrow.classList.replace("-right-24", "-right-10");
            arrow.classList.remove("hidden");

        }

        function closeNav() {
            const sidebar = document.getElementById("mySidenav");
            const main = document.getElementById("main");
            const arrow = document.getElementById("arrowBtn");

            sidebar.classList.add("-translate-x-full");
            main.classList.remove("ml-[260px]");
            arrow.classList.replace("-right-10", "-right-24");

            setTimeout(() => {
            arrowBtn.classList.add("hidden");
        }, 0);
        }
    </script>

</body>
</html>



