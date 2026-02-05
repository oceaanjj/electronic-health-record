<header class="fixed top-0 right-0 left-0 z-50 flex h-20 md:h-[120px] items-center justify-between bg-white shadow-md px-4 md:px-20">
    <div class="flex items-center space-x-2 md:space-x-10">
        {{-- Hamburger menu for mobile --}}
        <button id="mobileMenuButton" class="p-2 focus:outline-none focus:ring md:hidden" onclick="toggleNav()">
            <span class="material-symbols-outlined text-ehr text-3xl">menu</span>
        </button>

        {{-- Toggle button for desktop/tablet --}}
        <button onclick="toggleNav()" class="hidden md:block">
            <span class="material-symbols-outlined text-dark-green cursor-pointer" style="font-size: 25px">dehaze</span>
        </button>

        <script>
            let isNavOpen = localStorage.getItem("sidebarOpen") === "true"; // Initialize from local storage

            function toggleNav() {
                if (isNavOpen) {
                    closeNav();
                } else {
                    openNav();
                }

                isNavOpen = !isNavOpen;
            }

            // Listen for changes in local storage or documentElement class to keep isNavOpen in sync
            window.addEventListener('storage', () => {
                isNavOpen = localStorage.getItem("sidebarOpen") === "true";
            });
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        isNavOpen = document.documentElement.classList.contains('sidebar-open');
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
        </script>

        @php
            $user = Auth::user();

            if (! $user) {
                $homeRoute = 'login';
                $userName = 'Guest';
            } else {
                $userName = $user->username;
                switch ($user->role) {
                    case 'Admin':
                        $homeRoute = 'admin-home';
                        break;
                    case 'Nurse':
                        $homeRoute = 'nurse-home';
                        break;
                    case 'Doctor':
                        $homeRoute = 'doctor-home';
                        break;
                    default:
                        $homeRoute = 'login';
                }
            }
        @endphp

        <a href="{{ route($homeRoute) }}" class="flex items-center gap-2 md:gap-10">
            <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr logo" class="h-10 md:h-20" />
            <div class="flex flex-col leading-tight">
                <span class="font-trajan-bold text-lg md:text-[30px] font-black text-black">ELECTRONIC HEALTH RECORD</span>
                <span class="font-creato-black text-xs md:text-[20px] font-bold text-yellow">Bachelor of Science in Nursing</span>
            </div>
        </a>
    </div>

    <div class="flex flex-col items-end leading-tight text-right">
        <span class="font-[minion] italic text-[#2D6A4F] text-base md:text-[28px]">
            Hello, <span class="font-[minion] italic text-[#2D6A4F] text-base md:text-[28px] font-bold">{{ $userName }}</span>
        </span>
        <span class="font-alte text-[#B2B2B2] text-xs md:text-[16px] font-bold hidden sm:block"> <!-- Hidden on extra small, shown on sm and up -->
            {{ now()->format('l, F j') }}
        </span>
    </div>
</header>
