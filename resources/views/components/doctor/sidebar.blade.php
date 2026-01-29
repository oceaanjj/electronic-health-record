<div
    id="mySidenav"
    class="bg-ehr fixed top-0 left-0 z-40 h-full w-[260px] -translate-x-full transform shadow-md transition-transform duration-300 ease-in-out"
>
    {{-- Toggle Arrow Button (Fixed: Uses Icon) --}}
    <button
        id="arrowBtn"
        onclick="closeNav()"
        class="group text-dark-green rounded-oval hover:bg-dark-green absolute top-1/2 -right-4 flex h-15 w-8 -translate-y-1/2 transform items-center justify-center border border-gray-300 bg-white shadow-xl transition-all duration-300 ease-in-out hover:scale-105"
    >
        <span class="material-symbols-outlined hidden group-hover:block group-hover:text-white">arrow_left</span>
    </button>

    {{-- Menu Items --}}
    <ul class="text-dark-green font-creato-black mt-[140px] pr-[10px] pl-[10px] text-[13px]">
        {{-- 1. Doctor Home --}}
        {{--
            <li>
            <a href="{{ route('doctor-home') }}" class="group flex items-center gap-3 pl-5 pb-2 pt-2 mt-[20px]
            hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
            {{ request()->routeIs('doctor-home')
            ? 'bg-dark-green text-white font-bold'
            : 'hover:bg-hover' }}">
            
            <span class="material-symbols-outlined">home</span>
            <span>Home</span>
            </a>
            </li>
        --}}

        {{-- 2. Patient Report --}}
        <li>
            <a
                href="{{ route('doctor.patient-report') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('doctor.patient-report')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                {{-- Used 'assignment_ind' icon which looks like a clipboard with a person --}}
                <span class="material-symbols-outlined">assignment_ind</span>
                <span>Patient Report</span>
            </a>
        </li>

        {{-- Logout Section --}}
        <li>
            <center>
                {{--
                    Adjusted margin to be responsive. 530px might be too tall for some laptops.
                    I set it to mt-auto or a fixed reasonable height like 300px or kept your 530px if you prefer.
                    Here is 300px to be safe:
                --}}
                <hr class="border-dark-green mt-[530px] w-full border-t-1" />
            </center>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
                @csrf
            </form>

            <a
                href="#"
                id="logout-btn"
                class="group hover:bg-hover mt-[20px] flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200 hover:font-bold"
            >
                <span class="material-symbols-outlined">logout</span>
                <span>LOG OUT</span>
            </a>
        </li>
    </ul>
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
                        showConfirm('Do you really want to logout?', 'Are you sure?', 'Yes', 'Cancel').then(
                            (result) => {
                                if (result.isConfirmed) {
                                    logoutForm.submit();
                                }
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
