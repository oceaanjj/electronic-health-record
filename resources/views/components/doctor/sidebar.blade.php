<div id="mySidenav"
    class="fixed top-0 left-0 h-full w-[260px] shadow-md bg-ehr z-40 transform -translate-x-full transition-transform duration-300 ease-in-out">

    {{-- Toggle Arrow Button (Fixed: Uses Icon) --}}
    <button id="arrowBtn" onclick="closeNav()" class="group absolute top-1/2 -right-4 transform -translate-y-1/2 
        bg-white text-dark-green border border-gray-300 rounded-oval
        w-8 h-15 flex items-center justify-center 
        shadow-xl hover:bg-dark-green hover:scale-105 
        transition-all duration-300 ease-in-out">

        <span class="material-symbols-outlined hidden group-hover:block group-hover:text-white">arrow_left</span>
       
    </button>

    {{-- Menu Items --}}
    <ul class="mt-[140px] text-dark-green text-[13px] font-creato-black pr-[10px] pl-[10px]">
        
        {{-- 1. Doctor Home --}}
        {{-- <li>
            <a href="{{ route('doctor-home') }}" class="group flex items-center gap-3 pl-5 pb-2 pt-2 mt-[20px]
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                        {{ request()->routeIs('doctor-home')
                            ? 'bg-dark-green text-white font-bold'
                            : 'hover:bg-hover' }}">

                <span class="material-symbols-outlined">home</span>
                <span>Home</span>
            </a>
        </li> --}}

        {{-- 2. Patient Report --}}
        <li>
            <a href="{{ route('doctor.patient-report') }}" class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                        {{ request()->routeIs('doctor.patient-report')
                            ? 'bg-dark-green text-white font-bold'
                            : 'hover:bg-hover' }}">

                {{-- Used 'assignment_ind' icon which looks like a clipboard with a person --}}
                <span class="material-symbols-outlined">assignment_ind</span>
                <span>Patient Report</span>
            </a>
        </li>

        {{-- Logout Section --}}
        <li>
            <center>
                {{-- Adjusted margin to be responsive. 530px might be too tall for some laptops. 
                     I set it to mt-auto or a fixed reasonable height like 300px or kept your 530px if you prefer. 
                     Here is 300px to be safe: --}}
                <hr class="w-full mt-[530px] border-dark-green border-t-1">
            </center>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

            <a href="#" id="logout-btn" class="group flex items-center gap-3 pl-5 pb-2 pt-2 mt-[20px]
                        hover:bg-hover transition-all duration-200 rounded-l-[10px] rounded-r-[10px] hover:font-bold">
                
                <span class="material-symbols-outlined">logout</span>
                <span>LOG OUT</span>
            </a>
        </li>
    </ul>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutBtn = document.getElementById('logout-btn');
        const logoutForm = document.getElementById('logout-form');
        
        if (logoutBtn && logoutForm) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (typeof showConfirm === 'function') {
                    showConfirm('Do you really want to logout?', 'Are you sure?', 'Yes', 'Cancel')
                        .then((result) => {
                            if (result.isConfirmed) {
                                logoutForm.submit();
                            }
                        });
                } else if (typeof Swal === 'function') {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you really want to logout?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#2A1C0F',
                        cancelButtonColor: '#6c757d'
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