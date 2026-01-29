<div
    id="mySidenav"
    class="bg-ehr fixed top-0 left-0 z-40 h-full w-[260px] -translate-x-full transform shadow-md transition-transform duration-300 ease-in-out"
>
    <button
        id="arrowBtn"
        onclick="closeNav()"
        class="group text-dark-green rounded-oval hover:bg-dark-green absolute top-1/2 -right-4 flex h-15 w-8 -translate-y-1/2 transform items-center justify-center border border-gray-300 bg-white shadow-xl transition-all duration-300 ease-in-out hover:scale-105"
    >
        <span class="material-symbols-outlined hidden group-hover:block group-hover:text-white">arrow_left</span>
    </button>

    <ul class="text-dark-green font-creato-black mt-[140px] pr-[10px] pl-[10px] text-[13px]">
        <li>
            <a
                href="{{ route('nurse-home') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('nurse-home')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover'
                }} mt-[20px] flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">home</span>
                <span>Home</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('patients.index') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('patients.index')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">article_person</span>
                <span>Demographic Profile</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('medical-history') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('medical-history')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">history</span>
                <span>History</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('physical-exam.index') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('physical-exam.index')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">physical_therapy</span>
                <span>Physical Exam</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('vital-signs.show') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('vital-signs.show')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">ecg_heart</span>
                <span>Vital Signs</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('io.show') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('io.show')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">pill</span>
                <span>Intake and Output</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('adl.show') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('adl.show')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">toys_and_games</span>
                <span class="{{ request()->routeIs('adl.show') ? 'font-bold text-white' : 'group-hover:font-bold' }}">
                    Activities of Daily Living
                </span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('lab-values.index') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('lab-values.index')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">experiment</span>
                <span>Lab Values</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('diagnostics.index') }}"
                class="group hover:bg-dark-green {{ request()->routeIs('diagnostics.index') ? 'bg-dark-green font-bold text-white' : 'hover:bg-hover hover:font-bold' }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">diagnosis</span>
                <span>Diagnostics</span>
            </a>
        </li>

        {{-- NOTEE : MAY PROBLEM SA MAIN PAGE NG IV & LINES --}}
        <li>
            <a
                href="{{ route('ivs-and-lines') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('ivs-and-lines')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">blood_pressure</span>
                <span
                    class="{{ request()->routeIs('ivs-and-lines') ? 'font-bold text-white' : 'group-hover:font-bold' }}"
                >
                    IVs & Lines
                </span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('medication-administration') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('medication-administration')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover hover:font-bold'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">medication</span>
                <span>Medication Administration</span>
            </a>
        </li>

        <li>
            <a
                href="{{ route('medication-reconciliation') }}"
                class="group hover:bg-dark-green {{
                    request()->routeIs('medication-reconciliation')
                        ? 'bg-dark-green font-bold text-white'
                        : 'hover:bg-hover'
                }} flex items-center gap-3 rounded-l-[10px] rounded-r-[10px] pt-2 pb-2 pl-5 transition-all duration-200"
            >
                <span class="material-symbols-outlined">admin_meds</span>
                <span
                    class="{{ request()->routeIs('medication-reconciliation') ? 'font-bold text-white' : 'group-hover:font-bold' }}"
                >
                    Medication Reconciliation
                </span>
            </a>
        </li>

        {{--
            -
            <li>
            <a href="{{ route('discharge-planning') }}" class="group flex items-center gap-3 pl-5 pb-2 pt-2
            hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
            {{ request()->routeIs('discharge-planning')
            ? 'bg-dark-green text-white font-bold'
            : 'hover:bg-hover' }}">
            <img src="{{ asset('img/sidebar/discharge-planning.png') }}" alt="Discharge Icon"
            class="w-5 h-5 transition duration-200">
            <span
            class="{{ request()->routeIs('discharge-planning') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Discharge
            Planning</span>
            </a>
            </li>
        --}}

        <li>
            <center>
                <hr class="border-dark-green mt-[120px] w-full border-t-1" />
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

        {{--
            <li>
            <a href="about.php" class="group flex items-center gap-3 pl-5 pb-2 pt-2
            hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
            {{ request()->routeIs('about')
            ? 'bg-dark-green text-white font-bold'
            : 'hover:bg-hover' }}">
            <img src="./img/sidebar/about.png" alt="About Icon" class="w-5 h-5 transition duration-200">
            <span
            class="{{ request()->routeIs('about') ? 'text-white font-bold' : 'group-hover:font-bold' }}">About</span>
            </a>
            </li>
        --}}
    </ul>
</div>
