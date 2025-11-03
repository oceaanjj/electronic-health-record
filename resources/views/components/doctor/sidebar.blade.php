<div id="mySidenav"
    class="fixed top-0 left-0 h-full w-[260px] shadow-md bg-ehr z-40 transform -translate-x-full transition-transform duration-300 ease-in-out">


    <button id="arrowBtn" onclick="closeNav()" class="group absolute top-1/2 -right-4 transform -translate-y-1/2 
        bg-white text-dark-green border border-gray-300 rounded-oval
        w-8 h-15 flex items-center justify-center 
        shadow-xl hover:bg-dark-green hover:scale-105 
        transition-all duration-300 ease-in-out">

        <img src="{{ asset('img/sidebar/close-arrow.png') }}" class="w-3 block group-hover:hidden" alt="arrow">


        <img src="{{ asset('img/sidebar/close-arrow-hover.png') }}" class="w-3 hidden group-hover:block"
            alt="arrow-hover">

    </button>



    <ul class="mt-[140px] text-dark-green text-[13px] font-creato-black pr-[10px] pl-[10px]">
        <li>
            <a href="{{ route('doctor-home') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                <img src="{{ asset('img/sidebar/home-icon.png') }}" alt="Home Icon"
                    class="w-5 h-5 transition duration-200">
                <span class=" group-hover:text-white group-hover:font-bold">Home</span>
            </a>
        </li>

        <li>
            <a href="{{ route('doctor.patient-report') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                            {{ request()->routeIs('doctor.patient-report')
    ? 'bg-dark-green text-white font-bold'
    : '' }}">

                <img src="{{ asset('img/sidebar/demographic-profile.png') }}" alt="Home Icon"
                    class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('doctor.patient-report') ? 'text-white font-bold' : 'group-hover:font-bold group-hover:text-white' }}">Patient
                    Report</span>
            </a>
        </li>
        <li>
            <form action="{{ route('logout') }}" method="POST" class="flex items-center gap-3 pl-4 pb-2 pt-2
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                @csrf
                <button type="submit" class="group flex items-center gap-3 w-full text-left">
                    <img src="{{ asset('img/sidebar/logout-icon.png') }}" alt="Logout Icon"
                        class="w-5 h-5 transition duration-200">
                    <span class="group-hover:text-white group-hover:font-bold">Logout</span>
                </button>
            </form>
        </li>
    </ul>
</div>