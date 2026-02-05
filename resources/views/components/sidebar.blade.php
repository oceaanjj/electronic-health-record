<div id="mySidenav"
    class="fixed top-0 left-0 h-full w-full md:w-[260px] shadow-md bg-ehr z-40 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col pt-20 md:pt-[120px]">


    <button id="arrowBtn" onclick="closeNav()" class="group absolute top-1/2 -right-4 transform -translate-y-1/2 
        bg-white text-dark-green border border-gray-300 rounded-oval
        w-8 h-15 flex items-center justify-center 
        shadow-xl hover:bg-dark-green hover:scale-105 
        transition-all duration-300 ease-in-out
        hidden md:flex"> <!-- Hidden on small screens, flex on md and up -->

        <span class="material-symbols-outlined hidden group-hover:block group-hover:text-white">arrow_left</span>


    </button>

    <button id="closeMobileNavBtn" onclick="closeNav()"
        class="absolute top-4 right-4 p-2 focus:outline-none focus:ring md:hidden">
        <span class="material-symbols-outlined text-white text-3xl">close</span>
    </button>

    <ul class="text-[13px] font-creato-black pr-[10px] pl-[10px] flex-grow">
        <li>
            <a href="{{ route('nurse-home') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 mt-2 md:mt-[20px]
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                        {{ request()->routeIs('nurse-home')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover text-dark-green' }}">


                <span class="material-symbols-outlined">home</span>
                <span>Home</span>



            </a>
        </li>

        <li>
            <a href="{{ route('patients.index') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                            {{ request()->routeIs('patients.index')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover text-dark-green' }}">

                <span class="material-symbols-outlined">article_person</span>
                <span>Demographic Profile</span>
            </a>
        </li>





        <li>
            <a href="{{ route('medical-history') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('medical-history')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold text-dark-green' }}">
                <span class="material-symbols-outlined">history</span>
                <span>History</span>
            </a>
        </li>

        <li>
            <a href="{{ route('physical-exam.index') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('physical-exam.index')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold text-dark-green'}}">
                <span class="material-symbols-outlined">physical_therapy</span>
                <span>Physical Exam</span>
            </a>
        </li>

        <li>
            <a href="{{ route('vital-signs.show') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('vital-signs.show')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold text-dark-green' }}">
                <span class="material-symbols-outlined">ecg_heart</span>
                <span>Vital Signs</span>
            </a>
        </li>

        <li>
            <a href="{{ route('io.show') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('io.show')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold text-dark-green' }}">
                <span class="material-symbols-outlined">pill</span>
                <span>Intake and Output</span>
            </a>
        </li>



        <li>
            <a href="{{ route('adl.show') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('adl.show')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold text-dark-green' }}">
                <span class="material-symbols-outlined">toys_and_games</span>
                <span
                    class="{{ request()->routeIs('adl.show') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Activities
                    of Daily Living</span>
            </a>
        </li>

        <li>
            <a href="{{ route('lab-values.index') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('lab-values.index')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold text-dark-green' }}">
                <span class="material-symbols-outlined">experiment</span>
                <span>Lab Values</span>
            </a>
        </li>

        <li>
            <a href="{{ route('diagnostics.index') }}"
                class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('diagnostics.index') ? 'bg-dark-green text-white font-bold' : 'hover:bg-hover hover:font-bold text-dark-green' }}">
                <span class="material-symbols-outlined">diagnosis</span>
                <span>Diagnostics</span>
            </a>
        </li>




        {{-- NOTEE : MAY PROBLEM SA MAIN PAGE NG IV & LINES --}}
        <li>
            <a href="{{ route('ivs-and-lines') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('ivs-and-lines')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold text-dark-green' }}">
                <span class="material-symbols-outlined">blood_pressure</span>
                <span
                    class="{{ request()->routeIs('ivs-and-lines') ? 'text-white font-bold' : 'group-hover:font-bold' }}">IVs
                    & Lines</span>
            </a>
        </li>

        <li>
            <a href="{{ route('medication-administration') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('medication-administration')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover hover:font-bold text-dark-green' }}">
                <span class="material-symbols-outlined">medication</span>
                <span>Medication Administration</span>
            </a>
        </li>

        <li>
            <a href="{{ route('medication-reconciliation') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('medication-reconciliation')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover text-dark-green' }}">
                <span class="material-symbols-outlined">admin_meds</span>
                <span
                    class="{{ request()->routeIs('medication-reconciliation') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Medication
                    Reconciliation</span>
            </a>
        </li>


        {{---
        <li>
            <a href="{{ route('discharge-planning') }}" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                         {{ request()->routeIs('discharge-planning')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover' }}">
                <img src="{{ asset('img/sidebar/discharge-planning.png') }}" alt="Discharge Icon"
                    class="w-5 h-5 transition duration-200">
                <span
                    class="{{ request()->routeIs('discharge-planning') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Discharge
                    Planning</span>
            </a>
        </li>--}}
    </ul>

    <div class="mt-auto"> <!-- This will push the logout section to the bottom -->
        <div class="pr-[10px] pl-[10px]"> <!-- Added div to apply padding around logout items -->
            <center>
                <hr class="w-full mt-5 md:mt-[110px] border-dark-green border-t-1">
            </center>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <a href="#" id="logout-btn"
                class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 mt-[5px]
                        hover:bg-hover transition-all duration-200 rounded-l-[10px] rounded-r-[10px] hover:font-bold text-dark-green">
                <span class="material-symbols-outlined">logout</span>

                <span>LOG OUT</span>
            </a>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const logoutBtn = document.getElementById('logout-btn');
                const logoutForm = document.getElementById('logout-form');

                if (logoutBtn && logoutForm) {
                    logoutBtn.addEventListener('click', function (e) {
                        e.preventDefault();

                        if (typeof showConfirm === 'function') {
                            showConfirm('Do you really want to logout?', 'Are you sure?', 'Yes', 'Cancel')
                                .then((result) => {
                                    if (result.isConfirmed) {
                                        logoutForm.submit();
                                    }
                                });
                        } else if (typeof Swal === 'function') {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'Do you really want to logout?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#2A1C0F',
                                cancelButtonColor: '#6c757d'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    logoutForm.submit();
                                }
                            });
                        } else {
                            if (confirm('Are you sure you want to logout?')) {
                                logoutForm.submit();
                            }
                        }
                    });
                }
            });
        </script>
    @endpush


    {{--
    <li>
        <a href="about.php" class="group flex items-center gap-3 px-3 md:pl-5 pb-2 pt-2 
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