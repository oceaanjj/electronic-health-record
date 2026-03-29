@php
    $user = Auth::user();
    if (! $user) {
        $homeRoute = 'login';
        $userName = 'Guest';
    } else {
        $userName = $user->username;
        $homeRoute = match ($user->role) {
            'Admin' => 'admin-home',
            'Nurse' => 'nurse-home',
            'Doctor' => 'doctor-home',
            default => 'login',
        };
    }
@endphp

<header
    class="fixed top-0 right-0 left-0 z-50 flex h-[80px] items-center justify-between bg-white dark:bg-slate-900 px-4 shadow-md sm:px-10 md:h-[120px] lg:px-20 transition-colors duration-300"
>
    <div class="flex items-center space-x-2 md:space-x-10">
        <button
            onclick="toggleNav()"
            class="flex h-10 w-10 shrink-0 cursor-pointer items-center justify-center rounded-full transition-all duration-200 hover:bg-gray-100 dark:hover:bg-slate-800 md:h-12 md:w-12"
        >
            <span class="material-symbols-outlined text-dark-green dark:text-emerald-500" style="font-size: 22px; md:font-size: 25px">
                dehaze
            </span>
        </button>

        <a href="{{ route($homeRoute) }}" class="flex items-center gap-2 md:gap-6 lg:gap-10">
            <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr logo" class="h-8 w-auto min-[400px]:h-10 sm:h-14 md:h-20" />

            <div class="flex flex-col leading-tight">
                <span
                    class="font-trajan-bold text-[10px] font-black whitespace-nowrap text-black dark:text-white min-[400px]:text-[13px] sm:text-[18px] sm:whitespace-normal md:text-[24px] lg:text-[30px]"
                >
                    ELECTRONIC HEALTH RECORD
                </span>
                <span
                    class="font-creato-black text-yellow text-[7px] font-bold min-[400px]:text-[10px] sm:text-[14px] md:text-[18px] lg:text-[20px]"
                >
                    Bachelor of Science in Nursing
                </span>
            </div>
        </a>
    </div>

    <div class="ml-2 flex shrink-0 items-center gap-4 sm:gap-6">
        <div class="flex flex-col items-end leading-tight">
            <span class="font-[minion] text-[12px] text-[#2D6A4F] dark:text-emerald-400 italic sm:text-[18px] md:text-[24px] lg:text-[28px]">
                Hello,
                <span class="font-bold">{{ $userName }}</span>
            </span>

            <div class="font-alte text-[10px] font-bold text-[#B2B2B2] dark:text-slate-500 sm:text-[12px] md:text-[14px] lg:text-[16px]">
                <span class="md:hidden">
                    {{ now()->format('D, M j') }}
                </span>

                <span class="hidden md:block">
                    {{ now()->format('l, F j') }}
                </span>
            </div>
        </div>
    </div>
</header>

<script>
    (function() {
        // Navigation sync
        window.isNavOpen = localStorage.getItem("sidebarOpen") === "true";
        window.toggleNav = function() {
            window.isNavOpen = !window.isNavOpen;
            if (window.isNavOpen) {
                if (typeof openNav === 'function') openNav();
            } else {
                if (typeof closeNav === 'function') closeNav();
            }
        };
    })();
</script>
