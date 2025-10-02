<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Electronic Health Record - Login Role</title>
  @vite('resources/css/app.css')
</head>

<!-- Alerts -->
@vite(['resources/js/app.js'])

@if (session('success'))
  <div class="alert alert-success alert-dismissible fade show text-center w-75 mx-auto popup-alert" role="alert"
    id="success-alert">
    {{ session('success') }}
    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
  </div>
@endif
@if (session('error'))
  <div class="alert alert-danger alert-dismissible fade show text-center w-75 mx-auto popup-alert" role="alert"
    id="error-alert">
    {{ session('error') }}
    <!-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> -->
  </div>
@endif

<body class="font-sans bg-white text-gray-900">

  <!-- NAVBAR -->
  <header class="bg-ehr text-white p-4 flex items-center">
    <div class="flex items-center gap-4">

      <img src="img/ehr-logo.png" alt="Logo" class="h-10">
      <a href="{{ route('home') }}">
        <span class="font-bold text-lg">ELECTRONIC HEALTH RECORD</span>
      </a>

    </div>
  </header>

  <main class="text-center p-8">
    <h1 class="text-6xl p-10 font-bold text-ehr">WHICH ONE ARE YOU?</h1>

    <div class="flex flex-wrap justify-center gap-8">

      <!-- NURSE -->
      <div
        class="border-2 border-ehr rounded-xl p-6 w-64 h-83 bg-white text-center hover:-translate-y-1 hover:shadow-lg transition">
        <div class="mb-4 pt-6">
          <img src="img/NURSE.png" alt="Nurse" class="w-12 mx-auto">
        </div>
        <h2 class="text-ehr font-bold text-xl mb-2 pt-5">NURSE</h2>
        <p class="text-sm text-gray-700 mb-4">
          Access patient care records, update vital signs, and manage daily care activities.
        </p>

        <a href="{{ route('login.nurse') }}">
          <button
            class="bg-ehr text-white px-4 py-2 rounded-full font-bold hover:bg-green-900 transition cursor-pointer">
            Login as Nurse
          </button>
        </a>

      </div>

      <!-- DOCTOR -->
      <div
        class="border-2 border-ehr rounded-xl p-6 w-64 bg-white text-center hover:-translate-y-1 hover:shadow-lg transition">
        <div class="mb-4 pt-6">
          <img src="img/DOCTOR.png" alt="Doctor" class="w-12 mx-auto">
        </div>
        <h2 class="text-ehr font-bold text-xl mb-2 pt-5">DOCTOR</h2>
        <p class="text-sm text-gray-700 mb-4">
          Complete view access to medical records and nursing ADPIE.
        </p>

        <a href="{{ route('login.doctor') }}">
          <button
            class="bg-ehr text-white px-4 py-2 mt-4 rounded-full font-bold hover:bg-green-900 transition cursor-pointer">
            Login as Doctor
          </button>
        </a>

      </div>

      {{--  
      <!-- ADMIN -->
      <div
        class="border-2 border-ehr rounded-xl p-6 w-64 bg-white text-center hover:-translate-y-1 hover:shadow-lg transition">
        <div class="mb-4">
          <img src="#" alt="Admin" class="w-12 mx-auto">
        </div>
        <h2 class="text-ehr font-semibold text-xl mb-2">ADMIN</h2>
        <p class="text-sm text-gray-700 mb-4">
          Access patient care records, update vital signs, and manage daily care activities.
        </p>

        <a href="{{ route('login.admin') }}">
          <button
            class="bg-ehr text-white px-4 py-2 rounded-full font-bold hover:bg-green-900 transition cursor-pointer">
            Login as Admin
          </button>
      </div>
      </a>
      --}}

    </div>


  </main>

</body>

</html>