<header class="flex items-center justify-between bg-white shadow-md h-[120px] z-50 fixed top-0 left-0 right-0">
    <div class="flex items-center space-x-10 pr-10 pl-10">

        <button onclick="openNav()" class="text-yellow text-2xl font-bold focus:outline-none cursor-pointer">
            ☰
        </button>

        @php
            $user = Auth::user();

            if (!$user) {
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
            <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr logo" class="h-20">
            <div class="flex flex-col leading-tight">
                <span class="text-[30px] font-trajan-bold font-black text-black">ELECTRONIC HEALTH RECORD</span>
                <span class="text-[20px] font-creato-black font-bold text-yellow">Bachelor of Science in Nursing</span>
            </div>    
        </a>

    </div>

</header>