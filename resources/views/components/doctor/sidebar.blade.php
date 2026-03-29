<div id="mySidenav"
    class="fixed top-0 left-0 z-40 h-full w-[260px] -translate-x-full transform bg-white dark:bg-slate-900 shadow-md transition-transform duration-300 ease-in-out flex flex-col">

    <button id="arrowBtn" onclick="closeNav()"
        class="group text-dark-green dark:text-emerald-500 rounded-oval hover:bg-dark-green dark:hover:bg-emerald-600 absolute top-1/2 -right-4 flex h-15 w-8 -translate-y-1/2 transform items-center justify-center border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-xl transition-all duration-300 ease-in-out hover:scale-105">
        <span class="material-symbols-outlined hidden group-hover:block group-hover:text-white">arrow_left</span>
    </button>

    <div id="sidebarScroll"
        class="mt-[100px] sm:mt-[140px] lg:mt-[140px] pr-[10px] pl-[10px] flex-1 overflow-y-auto no-scrollbar">

        <p class="text-[10px] font-alte tracking-[0.2em] text-slate-400 dark:text-slate-500 uppercase mb-4 px-5">Main
            Navigation</p>

        <ul class="text-dark-green dark:text-emerald-500 font-creato-black text-[13px] space-y-1">
            <li>
                <a href="{{ route('doctor-home') }}"
                    class="group hover:bg-dark-green dark:hover:bg-emerald-600 {{
    request()->routeIs('doctor-home')
    ? 'bg-dark-green dark:bg-emerald-600 font-bold text-white'
    : 'hover:bg-hover dark:hover:text-white'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                    <span class="material-symbols-outlined">dashboard</span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('doctor.patient-report') }}"
                    class="group hover:bg-dark-green dark:hover:bg-emerald-600 {{
    request()->routeIs('doctor.patient-report')
    ? 'bg-dark-green dark:bg-emerald-600 font-bold text-white'
    : 'hover:bg-hover dark:hover:text-white hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                    <span class="material-symbols-outlined">analytics</span>
                    <span>Patient Reports</span>
                </a>
            </li>

            <li>
                <a href="{{ route('doctor.recent-forms') }}"
                    class="group hover:bg-dark-green dark:hover:bg-emerald-600 {{
    request()->routeIs('doctor.recent-forms')
    ? 'bg-dark-green dark:bg-emerald-600 font-bold text-white'
    : 'hover:bg-hover dark:hover:text-white hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                    <span class="material-symbols-outlined">fact_check</span>
                    <span>Recent Records</span>
                </a>
            </li>
        </ul>

        <div class="mt-8">
            <p class="text-[10px] font-alte tracking-[0.2em] text-slate-400 dark:text-slate-500 uppercase mb-4 px-5">
                Clinical Tools</p>
            <ul class="text-dark-green dark:text-emerald-500 font-creato-black text-[13px] space-y-1">
                <li>
                    <a href="{{ route('doctor.stats.active-patients') }}"
                        class="group hover:bg-dark-green dark:hover:bg-emerald-600 {{
    request()->routeIs('doctor.stats.active-patients')
    ? 'bg-dark-green dark:bg-emerald-600 font-bold text-white'
    : 'hover:bg-hover dark:hover:text-white hover:font-bold'
                        }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                        <span class="material-symbols-outlined">person_check</span>
                        <span>Active Patients</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Sidebar Footer --}}
    <div class="text-dark-green dark:text-emerald-500 font-creato-black text-[13px] pr-[10px] pl-[10px] pb-5 space-y-4">
        <center>
            <hr class="border-gray-100 dark:border-slate-800 w-full border-t-1" />
        </center>

        {{-- Theme Switcher row --}}
        <div class="flex items-center justify-between px-5">
            <div class="flex items-center gap-2">
                <span id="sidebar-theme-icon" class="material-symbols-outlined text-slate-400"
                    style="font-size: 18px">light_mode</span>
                <span id="sidebar-theme-text"
                    class="text-[11px] font-alte uppercase tracking-[0.1em] text-slate-500 dark:text-slate-400">Dark
                    Mode</span>
            </div>

            <button id="theme-toggle"
                class="relative inline-flex h-7 w-12 items-center rounded-full bg-slate-200 dark:bg-slate-800 transition-colors duration-300 focus:outline-none shadow-inner border border-slate-300 dark:border-slate-700 cursor-pointer"
                title="Toggle Theme">
                <span id="theme-toggle-dot"
                    class="flex h-5 w-5 transform items-center justify-center rounded-full bg-white dark:bg-slate-300 shadow-md transition-all duration-300 translate-x-1 dark:translate-x-6">
                    <span id="theme-toggle-icon"
                        class="material-symbols-outlined text-[11px] transition-colors duration-300">
                        light_mode
                    </span>
                </span>
            </button>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
            @csrf
        </form>

        <a href="#" id="logout-btn"
            class="group hover:bg-hover dark:hover:bg-rose-900/20 flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200 hover:font-bold dark:hover:text-rose-400">
            <span class="material-symbols-outlined">logout</span>
            <span>LOG OUT</span>
        </a>
    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('mySidenav');
            const arrowBtn = document.getElementById('arrowBtn');
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleIcon = document.getElementById('theme-toggle-icon');
            const sidebarThemeIcon = document.getElementById('sidebar-theme-icon');
            const sidebarThemeText = document.getElementById('sidebar-theme-text');

            // --- Theme Logic ---
            function updateUI(isDark) {
                if (isDark) {
                    themeToggleIcon.textContent = 'dark_mode';
                    themeToggleIcon.classList.remove('text-amber-500');
                    themeToggleIcon.classList.add('text-blue-500');
                    sidebarThemeIcon.textContent = 'dark_mode';
                    sidebarThemeIcon.classList.add('text-blue-500');
                    if (sidebarThemeText) sidebarThemeText.textContent = 'Light Mode';
                } else {
                    themeToggleIcon.textContent = 'light_mode';
                    themeToggleIcon.classList.remove('text-blue-500');
                    themeToggleIcon.classList.add('text-amber-500');
                    sidebarThemeIcon.textContent = 'light_mode';
                    sidebarThemeIcon.classList.remove('text-blue-500');
                    if (sidebarThemeText) sidebarThemeText.textContent = 'Dark Mode';
                }
            }

            if (themeToggleBtn) {
                updateUI(document.documentElement.classList.contains('dark'));
                themeToggleBtn.addEventListener('click', function () {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                        updateUI(false);
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                        updateUI(true);
                    }
                });
            }

            // --- Navigation Logic ---
            function updateArrowVisibility() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    arrowBtn.classList.add('opacity-0', 'pointer-events-none');
                } else {
                    arrowBtn.classList.remove('opacity-0', 'pointer-events-none');
                }
            }

            updateArrowVisibility();

            const sidebarObserver = new MutationObserver(updateArrowVisibility);
            sidebarObserver.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });

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