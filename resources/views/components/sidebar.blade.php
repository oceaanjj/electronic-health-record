<div id="mySidenav"
    class="fixed top-0 left-0 h-full w-[260px] shadow-md bg-ehr z-40 transform -translate-x-full transition-transform duration-300 ease-in-out">


    <button id="arrowBtn" onclick="closeNav()" class="group absolute top-1/2 -right-4 transform -translate-y-1/2 
        bg-white text-dark-green border border-gray-300 rounded-oval
        w-8 h-15 flex items-center justify-center 
        shadow-xl hover:bg-dark-green hover:scale-105 
        transition-all duration-300 ease-in-out">

        <img src="./img/sidebar/close-arrow.png" class="w-3 block group-hover:hidden" alt="arrow">


        <img src="./img/sidebar/close-arrow-hover.png" class="w-3 hidden group-hover:block" alt="arrow-hover">

    </button>



    <ul class="mt-[140px] text-dark-green text-[13px] font-creato-black pr-[10px] pl-[10px]">
        <li>
            <a href="{{ route('nurse-home') }}"
                class="group flex items-center gap-3 pl-4 pb-2 pt-2 mt-[20px]
                        hover:bg-hover transition-all duration-200 rounded-l-[10px] rounded-r-[10px] hover:font-bold">
                    <img src="./img/sidebar/home-icon.png" alt="Home Icon" class="w-5 h-5 transition duration-200">
                    <span >Home</span>
            </a>
        </li>

        <li>
            <a href="{{ route('patients.index') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                            {{ request()->routeIs('patients.index')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover' }}">

                <img src="./img/sidebar/demographic-profile.png" alt="Home Icon"
                    class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('patients.index') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Demographic
                    Profile</span>
            </a>
        </li>





        <li>
            <a href="{{ route('medical-history') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('medical-history')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold' }}">
                <img src="./img/sidebar/medical-history.png" alt="History Icon" class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('medical-history') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Medical
                    History</span>
            </a>
        </li>

        <li>
            <a href="{{ route('physical-exam.index') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('physical-exam.index')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold'}}">
                <img src="./img/sidebar/physical-exam.png" alt="Physical Exam Icon"
                    class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('physical-exam.index') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Physical
                    Exam</span>
            </a>
        </li>

        <li>
            <a href="{{ route('vital-signs.show') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('vital-signs.show')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold' }}">
                <img src="./img/sidebar/vital-signs.png" alt="History Icon" class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('vital-signs.show') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Vital
                    Signs</span>
            </a>
        </li>

        <li>
            <a href="{{ route('io.show') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('io.show')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold' }}">
                <img src="./img/sidebar/intake-and-output.png" alt="History Icon"
                    class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('io.show') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Intake
                    and Output</span>
            </a>
        </li>



        <li>
            <a href="{{ route('adl.show') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('adl.show')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold' }}">
                <img src="./img/sidebar/activities-of-daily-living.png" alt="ADL Icon"
                    class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('adl.show') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Activities
                    of Daily Living</span>
            </a>
        </li>


        <li>
            <a href="{{ route('lab-values.index') }}" class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('lab-values.index')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold' }}">
                <img src="./img/sidebar/lab-values.png" alt="Lab Icon" class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('lab-values.index') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Lab
                    Values</span>
            </a>
        </li>

        <li>
            <a href="{{ route('diagnostics.index') }}"
             class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('diagnostics.show') 
                            ? 'bg-dark-green text-white font-bold' 
                            : 'hover:bg-hover hover:font-bold' }}">
                    <img src="./img/sidebar/diagnostics.png" alt="Lab Icon" class="w-6 h-6 transition duration-200">
                    <span class="{{ request()->routeIs('diagnostics.show') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Diagnostics</span>
            </a>
        </li>



        {{-- NOTEE : MAY PROBLEM SA MAIN PAGE NG IV & LINES --}}
        <li>
            <a href="{{ route('ivs-and-lines') }}" class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('ivs-and-lines') 
                            ? 'bg-dark-green text-white font-bold' 
                            : 'hover:bg-hover hover:font-bold' }}">
                    <img src="./img/sidebar/ivs-and-lines.png" alt="IV Icon" class="w-4 h-4 transition duration-200">
                    <span class="{{ request()->routeIs('ivs-and-lines') ? 'text-white font-bold' : 'group-hover:font-bold' }}">IVs & Lines</span>
            </a>
        </li>

        <li>
            <a href="{{ route('medication-administration') }}"
                 class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('medication-administration')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold' }}">
                <img src="./img/sidebar/med-admini.png" alt="Medication Icon" class="w-6 h-6 transition duration-200">
                <span
                    class="{{ request()->routeIs('medication-administration') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Medication
                    Administration</span>
            </a>
        </li>

        <li>
            <a href="{{ route('medication-reconciliation') }}"
                    class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('medication-reconciliation')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover' }}">
                <img src="./img/sidebar/med-recon.png" alt="Reconciliation Icon"
                    class="w-6 h-6 transition duration-200">
                <span
                    class="{{ request()->routeIs('medication-reconciliation') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Medication
                    Reconciliation</span>
            </a>
        </li>

        <li>
            <a href="{{ route('discharge-planning') }}" class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('discharge-planning') 
                            ? 'bg-dark-green text-white font-bold' 
                            : 'hover:bg-hover' }}">
                <img src="./img/sidebar/discharge-planning.png" alt="Discharge Icon" class="w-5 h-5 transition duration-200">
                <span class="{{ request()->routeIs('discharge-planning') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Discharge Planning</span>
            </a>
        </li>


        <li>
            <center>
                 <hr class="w-full mt-[120px] border-dark-green border-t-1">
            </center>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <a href="#" class="group flex items-center gap-3 pl-5 pb-2 pt-2 mt-[20px]
                        hover:bg-hover transition-all duration-200 rounded-l-[10px] rounded-r-[10px] hover:font-bold 
                        " onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <img src="./img/sidebar/logout.png" alt="Discharge Icon" class="w-6 h-6 transition duration-200">
                <span>LOG OUT</span>
            </a>
        </li>
        

        {{-- 
        <li>
            <a href="about.php" class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('about')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover' }}">
                <img src="./img/sidebar/about.png" alt="About Icon" class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('about') ? 'text-white font-bold' : 'group-hover:font-bold' }}">About</span>
            </a>
        </li> --}}
    </ul>
</div>