<div id="mySidenav"
    class="bg-ehr bg-white fixed top-0 left-0 z-40 h-full w-[260px] -translate-x-full transform shadow-md transition-transform duration-300 ease-in-out flex flex-col">

    <button id="arrowBtn" onclick="closeNav()"
        class="group text-dark-green rounded-oval hover:bg-dark-green absolute top-1/2 -right-4 flex h-15 w-8 -translate-y-1/2 transform items-center justify-center border border-gray-300 bg-white shadow-xl transition-all duration-300 ease-in-out hover:scale-105">
        <span class="material-symbols-outlined hidden group-hover:block group-hover:text-white">arrow_left</span>
    </button>

    <ul id="sidebarScroll"
        class="text-dark-green font-creato-black mt-[100px] sm:mt-[140px] lg:mt-[140px] pr-[10px] pl-[10px] text-[13px] flex-1 overflow-y-auto">
        <li>
            <a href="{{ route('nurse-home') }}"
                class="group hover:bg-dark-green {{
    request()->routeIs('nurse-home')
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover'
                }} mt-[20px] flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">home</span>
                <span>Home</span>
            </a>
        </li>

        <li>
            <a href="{{ route('patients.index') }}"
                class="group hover:bg-dark-green {{
    request()->routeIs('patients.index')
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">article_person</span>
                <span>Demographic Profile</span>
            </a>
        </li>

        <li>
            <a href="{{ route('medical-history') }}"
                class="group hover:bg-dark-green {{
    request()->routeIs('medical-history')
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">history</span>
                <span>History</span>
            </a>
        </li>



        <!-- ----------- components that has cdss --------------- -->
        <li>
            <a href="{{ route('physical-exam.index') }}" class="group hover:bg-dark-green {{
    (request()->routeIs('physical-exam.index') || (request()->routeIs('nursing-diagnosis.process') && request()->route('component') == 'physical-exam'))
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover hover:font-bold'
        }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">physical_therapy</span>
                <span>Physical Exam</span>
            </a>
        </li>

        <li>
            <a href="{{ route('vital-signs.show') }}"
                class="group hover:bg-dark-green {{
    (request()->routeIs('vital-signs.show') || (request()->routeIs('nursing-diagnosis.process') && request()->route('component') == 'vital-signs'))
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">ecg_heart</span>
                <span>Vital Signs</span>
            </a>
        </li>

        <li>
            <a href="{{ route('io.show') }}"
                class="group hover:bg-dark-green {{
    (request()->routeIs('io.show') || (request()->routeIs('nursing-diagnosis.process') && request()->route('component') == 'intake-and-output'))
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">pill</span>
                <span>Intake and Output</span>
            </a>
        </li>

        <li>
            <a href="{{ route('adl.show') }}"
                class="group hover:bg-dark-green {{
    (request()->routeIs('adl.show') || (request()->routeIs('nursing-diagnosis.process') && request()->route('component') == 'adl'))
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">toys_and_games</span>
                <span class="{{ request()->routeIs('adl.show') ? 'font-bold text-white' : 'group-hover:font-bold' }}">
                    Activities of Daily Living
                </span>
            </a>
        </li>

        <li>
            <a href="{{ route('lab-values.index') }}"
                class="group hover:bg-dark-green {{
    (request()->routeIs('lab-values.index') || (request()->routeIs('nursing-diagnosis.process') && request()->route('component') == 'lab-values'))
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">experiment</span>
                <span>Lab Values</span>
            </a>
        </li>
        <!-- ----------- components that has cdss --------------- -->



        <li>
            <a href="{{ route('diagnostics.index') }}"
                class="group hover:bg-dark-green {{ request()->routeIs('diagnostics.index') ? 'bg-dark-green font-bold text-white' : 'hover:bg-hover hover:font-bold' }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">diagnosis</span>
                <span>Diagnostics</span>
            </a>
        </li>

        <li>
            <a href="{{ route('ivs-and-lines') }}"
                class="group hover:bg-dark-green {{
    request()->routeIs('ivs-and-lines')
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">blood_pressure</span>
                <span
                    class="{{ request()->routeIs('ivs-and-lines') ? 'font-bold text-white' : 'group-hover:font-bold' }}">
                    IVs & Lines
                </span>
            </a>
        </li>

        <li>
            <a href="{{ route('medication-administration') }}"
                class="group hover:bg-dark-green {{
    request()->routeIs('medication-administration')
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">medication</span>
                <span>Medication Administration</span>
            </a>
        </li>

        <li>
            <a href="{{ route('medication-reconciliation') }}"
                class="group hover:bg-dark-green {{
    request()->routeIs('medication-reconciliation')
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">admin_meds</span>
                <span
                    class="{{ request()->routeIs('medication-reconciliation') ? 'font-bold text-white' : 'group-hover:font-bold' }}">
                    Medication Reconciliation
                </span>
            </a>
        </li>
    </ul>

    <div class="text-dark-green font-creato-black text-[13px] pr-[10px] pl-[10px] pb-5">
        <center>
            <hr class="border-dark-green w-full border-t-1" />
        </center>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
            @csrf
        </form>

        <a href="#" id="logout-btn"
            class="group hover:bg-hover mt-[20px] flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200 hover:font-bold">
            <span class="material-symbols-outlined">logout</span>
            <span>LOG OUT</span>
        </a>
    </div>

</div>

@push('scripts')
    <script>
        // We use a MutationObserver to catch the element the millisecond it's added to the DOM
        // This is usually faster than DOMContentLoaded and reduces the flicker
        (function () {
            const observer = new MutationObserver((mutations, obs) => {
                const sidebar = document.getElementById('sidebarScroll');
                if (sidebar) {
                    const scrollPos = sessionStorage.getItem('sidebar-scroll-pos');
                    if (scrollPos) {
                        sidebar.scrollTop = scrollPos;
                    }
                    obs.disconnect(); // Stop looking once we found it
                }
            });

            observer.observe(document.documentElement, {
                childList: true,
                subtree: true
            });
        })();

        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('mySidenav');
            const sidebarScroll = document.getElementById('sidebarScroll');
            const arrowBtn = document.getElementById('arrowBtn');

            // Function to update arrow visibility based on sidebar state
            function updateArrowVisibility() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    // Sidebar is closed
                    arrowBtn.classList.add('opacity-0', 'pointer-events-none');
                } else {
                    // Sidebar is open
                    arrowBtn.classList.remove('opacity-0', 'pointer-events-none');
                }
            }

            // Initialize arrow visibility on page load
            updateArrowVisibility();

            // Update arrow visibility whenever sidebar state changes
            // This handles cases where the sidebar is toggled
            const sidebarObserver = new MutationObserver(updateArrowVisibility);
            sidebarObserver.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });

            // Save position on any click within the sidebar
            sidebarScroll.addEventListener('click', (e) => {
                if (e.target.closest('a')) {
                    sessionStorage.setItem('sidebar-scroll-pos', sidebarScroll.scrollTop);
                }
            });

            // Original Logout Logic
            const logoutBtn = document.getElementById('logout-btn');
            const logoutForm = document.getElementById('logout-form');

            if (logoutBtn && logoutForm) {
                logoutBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (typeof showConfirm === 'function') {
                        showConfirm('Do you really want to logout?', 'Are you sure?', 'Yes', 'Cancel').then((result) => {
                            if (result.isConfirmed) logoutForm.submit();
                        });
                    } else {
                        if (confirm('Are you sure you want to logout?')) logoutForm.submit();
                    }
                });
            }
        });
    </script>
@endpush