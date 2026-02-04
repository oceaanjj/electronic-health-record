<div id="mySidenav"
    class="fixed top-0 left-0 h-full w-[260px] shadow-md bg-ehr z-40 transform -translate-x-full transition-transform duration-300 ease-in-out">


    <button id="arrowBtn" onclick="closeNav()" class="group absolute top-1/2 -right-4 transform -translate-y-1/2 
        bg-white text-dark-green border border-gray-300 rounded-oval
        w-8 h-15 flex items-center justify-center 
        shadow-xl hover:bg-dark-green hover:scale-105 
        transition-all duration-300 ease-in-out">

        <i class="fa-solid fa-chevron-left"></i>

    </button>



    <ul class="mt-[140px] text-dark-green text-[13px] font-creato-black pr-[10px] pl-[10px]">
        <li>
            <a href="{{ route('doctor-home') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                <i class="fa-solid fa-house w-5 h-5 mt-3 transition duration-200 group-hover:text-white"></i>
                <span class=" group-hover:text-white group-hover:font-bold">Home</span>
  
            </a>
        </li>

        <li>
            <a href="{{ route('doctor.patient-report') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                            {{ request()->routeIs('doctor.patient-report')
    ? 'bg-dark-green text-white font-bold'
    : '' }}">

                <i class="fa-solid fa-file-alt w-5 h-5 mt-3 transition duration-200 group-hover:text-white"></i>
                <span
                    class="{{ request()->routeIs('doctor.patient-report') ? 'text-white font-bold' : 'group-hover:font-bold group-hover:text-white' }}">Patient
                    Report</span>
            </a>
        </li>
        <li>
            <center>
                <hr class="w-full mt-[530px] border-dark-green border-t-1">
            </center>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" id="logout-btn" class="group flex items-center gap-3 pl-5 pb-2 pt-2 mt-[20px]
                        hover:bg-hover transition-all duration-200 rounded-l-[10px] rounded-r-[10px] hover:font-bold">
               <i class="fa-solid fa-right-from-bracket w-6 h-6 transition duration-200"></i>
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