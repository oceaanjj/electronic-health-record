<header class="flex items-center justify-between bg-ehr shadow px-6 py-4">
    <div class="flex items-center space-x-4">

        <button onclick="openNav()" class="text-white text-2xl font-bold focus:outline-none cursor-pointer">
            ☰
        </button>

       
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <img src="{{ asset('img/ehr-logo.png') }}" alt="Hospital Logo" class="h-10">
            <span class="text-lg font-bold text-white">ELECTRONIC HEALTH RECORD</span>
        </a>
    </div>

</header>
