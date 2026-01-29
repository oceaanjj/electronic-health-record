<header class="fixed top-0 right-0 left-0 z-50 flex h-[120px] items-center justify-between bg-white shadow-md">
    <div class="flex items-center space-x-10 pr-10 pl-10">
        <button onclick="toggleNav()">
            <span class="material-symbols-outlined text-dark-green cursor-pointer" style="font-size: 25px">dehaze</span>
        </button>

        <script>
            let isNavOpen = false;

            function toggleNav() {
                if (isNavOpen) {
                    closeNav();
                } else {
                    openNav();
                }

                isNavOpen = !isNavOpen;
            }
        </script>

        @php
            $user = Auth::user();

            if (! $user) {
                $homeRoute = 'login';
            } else {
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

        <a href="{{ route($homeRoute) }}" class="flex items-center gap-10">
            <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr logo" class="h-20" />
            <div class="flex flex-col leading-tight">
                <span class="font-trajan-bold text-[30px] font-black text-black">ELECTRONIC HEALTH RECORD</span>
                <span class="font-creato-black text-yellow text-[20px] font-bold">Bachelor of Science in Nursing</span>
            </div>
        </a>
    </div>
</header>
