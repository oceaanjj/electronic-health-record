<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sidebar</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

<span class="p-4 cursor-pointer bg-ehr text-white" onclick="openNav()">☰</span>

<div id="mySidenav" 
     class="fixed top-0 left-0 h-full w-75 bg-ehr z-50 transform -translate-x-full transition-transform duration-300 ease-in-out">

  <button onclick="closeNav()" 
          class="absolute top-4 right-4 text-ehr text-2xl font-bold">&times;</button>

    <div class="relative flex flex-col items-center">

        <div class="w-full h-60 bg-white rounded-b-full flex flex-col items-center justify-center">
          <img src="/img/ehr-logo.png" alt="Logo" class="w-40 h-40 p-2">
        </div>

    <h3 class="mt-2 text-sm font-bold text-center leading-tight text-white">
      ELECTRONIC HEALTH RECORD
    </h3>
  </div>

  <ul class="mt-4 space-y-2 px-4 list-none text-white">
    <li class="flex items-center gap-3 p-y-0.5 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/home-icon.png" alt="Home Icon" class="w-6 h-6"> <span>Home</span>
    </li>
    <li class="flex items-center gap-3 p-y-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/search-patient.png" alt="Search Icon" class="w-6 h-6"> <span>Search Patient</span>
    </li>
    <li class="flex items-center gap-3 p-y-6 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/demographic-profile.png" alt="Profile Icon" class="w-6 h-6"> <span>Demographic Profile</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/medical-history.png" alt="History Icon" class="w-6 h-6"> <span>Medical History</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/physical-exam.png" alt="Exam Icon" class="w-6 h-6"> <span>Physical Exam</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/vital-signs.png" alt="Vitals Icon" class="w-6 h-6"> <span>Vital Signs</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/intake-and-output.png" alt="Intake Icon" class="w-6 h-6"> <span>Intake and Output</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/activities-of-daily-living.png" alt="ADL Icon" class="w-6 h-6"> <span>Activities of Daily Living</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/lab-values.png" alt="Lab Icon" class="w-6 h-6"> <span>Lab Values</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/diagnostics.png" alt="Diagnostics Icon" class="w-6 h-6"> <span>Diagnostics</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/ivs-and-lines.png" alt="IV Icon" class="w-6 h-6"> <span>IVs & Lines</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/med-admini.png" alt="Medication Icon" class="w-6 h-6"> <span>Medication Administration</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/med-recon.png" alt="Reconciliation Icon" class="w-6 h-6"> <span>Medication Reconciliation</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/discharge-planning.png" alt="Discharge Icon" class="w-6 h-6"> <span>Discharge Planning</span>
    </li>
    <li class="flex items-center gap-3 p-2 rounded hover:bg-white/20 cursor-pointer">
      <img src="./img/sidebar/about.png" alt="About Icon" class="w-6 h-6"> <span>About</span>
    </li>
  </ul>
</div>


<!-- ipasok content dito -->
<!-- NOTE : how to just call this in every page -->
<div id="main" class="p-6 transition-transform duration-300 ease-in-out">
  <h1 class="text-2xl font-bold">Main Content</h1>
  <p>Click the ☰ Menu button to open the sidebar.</p>
</div>

      <script>
          function openNav() {
            document.getElementById("mySidenav").classList.remove("-translate-x-full");
            document.getElementById("mySidenav").classList.add("translate-x-0");

            document.getElementById("main").classList.add("translate-x-75"); 
          }

          function closeNav() {
            document.getElementById("mySidenav").classList.remove("translate-x-0");
            document.getElementById("mySidenav").classList.add("-translate-x-full");

            document.getElementById("main").classList.remove("translate-x-75"); 
          }
      </script>

</body>
</html>
