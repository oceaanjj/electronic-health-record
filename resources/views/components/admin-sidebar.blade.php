<div id="mySidenav"
    class="fixed top-0 left-0 h-full w-[260px] shadow-md bg-ehr z-40 transform -translate-x-full transition-transform duration-300 ease-in-out">


    <button id="arrowBtn" onclick="closeNav()" class="group absolute top-1/2 -right-4 transform -translate-y-1/2 
        bg-white text-dark-green border border-gray-300 rounded-oval
        w-8 h-15 flex items-center justify-center 
        shadow-xl hover:bg-dark-green hover:scale-105 
        transition-all duration-300 ease-in-out">

        <img src="{{ asset('img/sidebar/close-arrow.png') }}" class="w-3 block group-hover:hidden" alt="arrow">


        <img src="{{ asset('img/sidebar/close-arrow-hover.png') }}" class="w-3 hidden group-hover:block"
            alt="arrow-hover">

    </button>



    <ul class="mt-[140px] text-dark-green text-[13px] font-creato-black pr-[10px] pl-[10px]">
        <ul class="mt-[140px] text-dark-green text-[13px] font-creato-black pr-[10px] pl-[10px]">
        <li>
            <a href="{{ route('admin-home') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 mt-[20px]
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                        {{ request()->routeIs('admin-home')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover' }}">

                <img src="{{ asset('img/sidebar/home-icon.png') }}" alt="Home Icon"
                    class="w-5 h-5 transition duration-200">
                <span>Home</span>
            </a>
        </li>

        <li>
            <a href="{{ route('audit.index') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                            {{ request()->routeIs('audit.index')
    ? 'bg-dark-green text-white font-bold'
    : 'hover:bg-hover' }}">

                <img src="{{ asset('img/sidebar/log.png') }}" alt="Home Icon"
                    class="w-4 h-4 transition duration-200">
                <span
                    class="{{ request()->routeIs('audit.index') ? 'text-white font-bold' : 'group-hover:font-bold' }}">Audit Log</span>
            </a>
        </li>
 

        <li>
            <a href="{{ route('users') }}" class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]
                            {{ request()->routeIs('users')
    ? 'bg-dark-green  font-bold'
    : 'hover:bg-hover' }}">

                <img src="{{ asset('img/sidebar/user.png') }}" alt="Users Icon"
                    class="w-4 h-4 transition duration-200">
                <span
                    class="{{ request()->routeIs('users') ? 'text-white font-bold' : 'group-hover:font-bold' }}">User</span>
            </a>
        </li>

        
        <li>
            <center>
                <hr class="w-full mt-[500px] border-dark-green border-t-1">
            </center>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="#" id="logout-btn" class="group flex items-center gap-3 pl-5 pb-2 pt-2 mt-[20px]
                        hover:bg-hover transition-all duration-200 rounded-l-[10px] rounded-r-[10px] hover:font-bold">
                <img src="{{ asset('img/sidebar/logout.png') }}" alt="Discharge Icon"
                    class="w-6 h-6 transition duration-200">
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