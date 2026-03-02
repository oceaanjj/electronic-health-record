<div id="mySidenav"
    class="bg-ehr bg-white fixed top-0 left-0 z-40 h-full w-[260px] -translate-x-full transform shadow-md transition-transform duration-300 ease-in-out flex flex-col">

    <!-- yung < arrow sa sidebar: -->
    <button id="arrowBtn" onclick="closeNav()"
        class="group text-dark-green rounded-oval hover:bg-dark-green absolute top-1/2 -right-4 flex h-15 w-8 -translate-y-1/2 transform items-center justify-center border border-gray-300 bg-white shadow-xl transition-all duration-300 ease-in-out hover:scale-105">
        <span class="material-symbols-outlined hidden group-hover:block group-hover:text-white">arrow_left</span>
    </button>


    <ul id="sidebarScroll"
        class="text-dark-green font-creato-black mt-[100px] sm:mt-[140px] lg:mt-[140px] pr-[10px] pl-[10px] text-[13px] flex-1 overflow-y-auto">

        <li>
            <a href="{{ route('admin-home') }}"
                class="group hover:bg-dark-green {{
    request()->routeIs('admin-home')
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover'
                }} mt-[20px] flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">home</span>
                <span>Home</span>
            </a>
        </li>

        <li>
            <a href="{{ route('audit.index') }}"
                class="group hover:bg-dark-green {{
    request()->routeIs('audit.index')
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">description</span>
                <span>Audit Log</span>
            </a>
        </li>

        <li>
            <a href="{{ route('users') }}"
                class="group hover:bg-dark-green {{
    request()->routeIs('users')
    ? 'bg-dark-green font-bold text-white'
    : 'hover:bg-hover'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200">
                <span class="material-symbols-outlined">person</span>
                <span>User</span>
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
        // 1. Scroll Position Logic
        (function () {
            const observer = new MutationObserver((mutations, obs) => {
                const sidebar = document.getElementById('sidebarScroll');
                if (sidebar) {
                    const scrollPos = sessionStorage.getItem('sidebar-scroll-pos');
                    if (scrollPos) {
                        sidebar.scrollTop = scrollPos;
                    }
                    obs.disconnect();
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
            const logoutBtn = document.getElementById('logout-btn');
            const logoutForm = document.getElementById('logout-form');

            // 2. Arrow Visibility Logic
            function updateArrowVisibility() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    arrowBtn.classList.add('opacity-0', 'pointer-events-none');
                } else {
                    arrowBtn.classList.remove('opacity-0', 'pointer-events-none');
                }
            }

            // Initialize and Observe sidebar changes
            updateArrowVisibility();
            const sidebarObserver = new MutationObserver(updateArrowVisibility);
            sidebarObserver.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });

            // 3. Save Scroll Position on Click
            if (sidebarScroll) {
                sidebarScroll.addEventListener('click', (e) => {
                    if (e.target.closest('a')) {
                        sessionStorage.setItem('sidebar-scroll-pos', sidebarScroll.scrollTop);
                    }
                });
            }

            // 4. Logout Logic
            if (logoutBtn && logoutForm) {
                logoutBtn.addEventListener('click', function (e) {
                    e.preventDefault();

                    if (typeof showConfirm === 'function') {
                        showConfirm('Do you really want to logout?', 'Are you sure?', 'Yes', 'Cancel').then(
                            (result) => {
                                if (result.isConfirmed) logoutForm.submit();
                            },
                        );
                    } else if (typeof Swal === 'function') {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'Do you really want to logout?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#2A1C0F',
                            cancelButtonColor: '#6c757d',
                        }).then((result) => {
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