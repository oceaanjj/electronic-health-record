<div
    id="mySidenav"
    class="bg-ehr fixed top-0 left-0 z-40 h-full w-[260px] -translate-x-full transform shadow-md transition-transform duration-300 ease-in-out"
>
    <button
        id="arrowBtn"
        onclick="closeNav()"
        class="group text-dark-green rounded-oval hover:bg-dark-green absolute top-1/2 -right-4 flex h-15 w-8 -translate-y-1/2 transform items-center justify-center border border-gray-300 bg-white shadow-xl transition-all duration-300 ease-in-out hover:scale-105"
    >
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <ul class="text-dark-green font-creato-black mt-[140px] pr-[10px] pl-[10px] text-[13px]">
        <li>
            <a
                href="{{ route('doctor-home') }}"
                class="group hover:bg-dark-green flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-4 transition-all duration-200"
            >
                <i class="fa-solid fa-house mt-3 h-5 w-5 transition duration-200 group-hover:text-white"></i>
                <span class="group-hover:font-bold group-hover:text-white">Home</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('doctor.patient-report') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('doctor.patient-report')
                        ? 'bg-dark-green font-bold text-white'
                        : ''
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-4 transition-all duration-200"
            >
                <i class="fa-solid fa-file-alt mt-3 h-5 w-5 transition duration-200 group-hover:text-white"></i>
                <span
                    class="{{ request()->routeIs('doctor.patient-report') ? 'font-bold text-white' : 'group-hover:font-bold group-hover:text-white' }}"
                >
                    Patient Report
                </span>
            </a>
        </li>
        <li>
            <center>
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
                <i class="fa-solid fa-right-from-bracket h-6 w-6 transition duration-200"></i>
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
