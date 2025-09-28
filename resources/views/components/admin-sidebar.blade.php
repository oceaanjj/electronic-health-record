<div id="mySidenav"
    class="fixed top-0 left-0 h-full w-75 bg-ehr z-50 transform -translate-x-full transition-transform duration-300 ease-in-out">



    <div class="relative flex flex-col items-center">

        <div class="w-full h-70 bg-white rounded-b-full flex flex-col items-center justify-center">
            <button onclick="closeNav()"
                class="absolute top-4 right-4 text-ehr text-2xl font-bold cursor-pointer">&times;
            </button>
            <img src="/img/ehr-logo.png" alt="Logo" class="w-40 h-40 p-2">
            <h3 class="mt-2 text-sm font-bold text-center leading-tight text-ehr">
                ELECTRONIC HEALTH <br>RECORD
            </h3>
        </div>
    </div>



    <ul class="mt-7 space-y-0.9 px-0 list-none text-white">

        <li>
            <a href="{{ route('login') }}"
                class="flex items-center gap-3 pl-9 pb-1 pt-1 pr-2 hover:bg-white/20 cursor-pointer">
                <img src="./img/sidebar/home-icon.png" alt="Home Icon" class="w-6 h-6">
                <span>Home</span>
            </a>
        </li>

        <li>
            <a href="{{ route('audit.index') }}"
                class="flex items-center gap-3 pl-9 pb-1 pt-1 pr-2 hover:bg-white/20 cursor-pointer">
                <img src="./img/sidebar/home-icon.png" alt="Home Icon" class="w-6 h-6">
                <span>Audit Log</span>
            </a>
        </li>

        <li>
            <a href="{{ route('users') }}"
                class="flex items-center gap-3 pl-9 pb-1 pt-1 pr-2 hover:bg-white/20 cursor-pointer">
                <img src="./img/sidebar/home-icon.png" alt="Home Icon" class="w-6 h-6">
                <span>Users</span>
            </a>
        </li>

        <li>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" class="flex items-center gap-3 pl-9 pb-1 pt-1 pr-2 hover:bg-white/20 cursor-pointer text-center"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <img src="./img/sidebar/home-icon.png" alt="Home Icon" class="w-6 h-6">
                <span>Log Out</span>
            </a>
        </li>


    </ul>


</div>