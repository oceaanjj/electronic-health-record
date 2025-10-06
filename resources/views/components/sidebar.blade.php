<div id="mySidenav"
     class="fixed top-0 left-0 h-full w-[260px] shadow-xl bg-ehr z-40 transform -translate-x-full transition-transform duration-300 ease-in-out">




    <ul class="mt-[140px] text-dark-green text-[13px] font-creato-black pr-[10px] pl-[10px]">
        <li>
            <a href="{{ route('nurse-home') }}"
                class="group flex items-center gap-3 pl-4 pb-2 pt-2
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/home-icon.png" alt="Home Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Home</span>
            </a>
        </li>

        <li>
            <a href="{{ route('patients.index') }}"
                class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/demographic-profile.png" alt="Home Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Demographic Profile</span>
            </a> 
        </li>

        <li>    
            <a href="{{ route('medical-history') }}"
                class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/medical-history.png" alt="History Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Medical History</span>
            </a>
        </li>

        <li>
            <a href="{{ route('physical-exam.index') }}"

            class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/physical-exam.png" alt="Physical Exam Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Physical Exam</span>
            </a>
        </li>

        <li>
            <a href="{{ route('vital-signs.show') }}"

             class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/vital-signs.png" alt="History Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Vital Signs</span>
            </a>
        </li>

        <li>
            <a href="{{ route('io.show') }}"
             class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/intake-and-output.png" alt="History Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Intake and Output</span>
            </a>
        </li>

        

        <li>
            <a href="{{ route('adl.show') }}"
                 class="group flex items-center gap-3 pl-4 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/activities-of-daily-living.png" alt="ADL Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Activities of Daily Living</span>
            </a>
        </li>


        <li>
            <a href="{{ route('lab-values.index') }}"
             class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/lab-values.png" alt="Lab Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Lab Values</span>
            </a>
        </li>

        <li>
            <a href="{{ route('lab-values.index') }}"
             class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/diagnostics.png" alt="Lab Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Diagnostics</span>
            </a>
        </li>

        <li>
            <a href="{{ route('ivs-and-lines') }}"
                 class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/ivs-and-lines.png" alt="IV Icon" class="w-5 h-5 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">IVs & Lines</span>
            </a>
        </li>

        <li>
            <a href="{{ route('medication-administration') }}"
                 class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/med-admini.png" alt="Medication Icon" class="w-6 h-6 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Medication Administration</span>
            </a>
        </li>

        <li>
            <a href="{{ route('medication-reconciliation') }}"
                    class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                    <img src="./img/sidebar/med-recon.png" alt="Reconciliation Icon" class="w-6 h-6 transition duration-200">
                    <span class=" group-hover:text-white group-hover:font-bold">Medication Reconciliation</span>
            </a>
        </li>

        <li>
            <a href="{{ route('discharge-planning') }}"
                class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                <img src="./img/sidebar/discharge-planning.png" alt="Discharge Icon" class="w-5 h-5 transition duration-200">
                <span class=" group-hover:text-white group-hover:font-bold">Discharge Planning</span>
            </a>
        </li>

        <li>
            <a href="about.php"
               class="group flex items-center gap-3 pl-5 pb-2 pt-2 
                        hover:bg-dark-green transition-all duration-200 rounded-l-[10px] rounded-r-[10px]">
                <img src="./img/sidebar/about.png" alt="About Icon" class="w-5 h-5 transition duration-200">
                <span class=" group-hover:text-white group-hover:font-bold">About</span>
            </a>
        </li>
    </ul>
</div>


