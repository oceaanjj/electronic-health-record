<header class="fixed top-0 right-0 left-0 z-50 flex h-[80px] md:h-[120px] items-center justify-between bg-white shadow-md px-4 sm:px-10 lg:px-20">
    <div class="flex items-center space-x-2 md:space-x-10">
        <button onclick="toggleNav()" 
            class="flex items-center justify-center w-10 h-10 md:w-12 md:h-12 rounded-full hover:bg-gray-100 cursor-pointer transition-all duration-200 shrink-0">
            <span class="material-symbols-outlined text-dark-green" style="font-size: 22px; md:font-size: 25px">
                dehaze
            </span>
        </button>

        <script>
            let isNavOpen = false;
            function toggleNav() {
                isNavOpen = !isNavOpen;
                if (isNavOpen) {
                    openNav();
                } else {
                    closeNav();
                }
            }
        </script>

        @php
            $user = Auth::user();
            if (!$user) {
                $homeRoute = 'login';
                $userName = 'Guest';
            } else {
                $userName = $user->username;
                $homeRoute = match($user->role) {
                    'Admin' => 'admin-home',
                    'Nurse' => 'nurse-home',
                    'Doctor' => 'doctor-home',
                    default => 'login',
                };
            }
        @endphp

        <a href="{{ route($homeRoute) }}" class="flex items-center gap-2 md:gap-6 lg:gap-10">
            <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr logo" class="h-10 sm:h-14 md:h-20 w-auto" />
            
            <div class="flex flex-col leading-tight">
                <span class="font-trajan-bold text-[14px] sm:text-[18px] md:text-[24px] lg:text-[30px] font-black text-black whitespace-nowrap sm:whitespace-normal">
                    ELECTRONIC HEALTH RECORD
                </span>
                <span class="font-creato-black text-yellow text-[10px] sm:text-[14px] md:text-[18px] lg:text-[20px] font-bold">
                    Bachelor of Science in Nursing
                </span>
            </div>
        </a>
    </div>

    <div class="flex flex-col items-end leading-tight ml-2 shrink-0">
        <span class="font-[minion] italic text-[#2D6A4F] text-[12px] sm:text-[18px] md:text-[24px] lg:text-[28px]">
            Hello, <span class="font-bold">{{ $userName }}</span>
        </span>
        
        <div class="font-alte text-[#B2B2B2] text-[10px] sm:text-[12px] md:text-[14px] lg:text-[16px] font-bold">
            <span class="md:hidden">
                {{ now()->format('D, M j') }}
            </span>
            
            <span class="hidden md:block">
                {{ now()->format('l, F j') }}
            </span>
        </div>
    </div>
</header>