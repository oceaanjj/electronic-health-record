<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Electronic Health Record - Login Role</title>
  @vite('resources/css/app.css')
</head>

<body class="font-sans bg-white text-gray-900">

  <!-- NAVBAR -->
  <header class="bg-ehr text-white p-4 flex items-center">
    <div class="flex items-center gap-4">

      <img src="logo.png" alt="Logo" class="h-10">
      <a href="{{ route('home') }}">
        <span class="font-bold text-lg">ELECTRONIC HEALTH RECORD</span>
      </a>

    </div>
  </header>

  <main class="text-center p-8">
    <h1 class="text-3xl font-bold text-ehr">WHICH ONE ARE YOU?</h1>

    <div class="flex flex-wrap justify-center gap-8">

      <!-- NURSE -->
      <div
        class="border-2 border-ehr rounded-xl p-6 w-64 bg-white text-center hover:-translate-y-1 hover:shadow-lg transition">
        <div class="mb-4">
          <img src="#" alt="Nurse" class="w-12 mx-auto">
        </div>
        <h2 class="text-ehr font-semibold text-xl mb-2">NURSE</h2>
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
        <div class="mb-4">
          <img src="#" alt="Doctor" class="w-12 mx-auto">
        </div>
        <h2 class="text-ehr font-semibold text-xl mb-2">DOCTOR</h2>
        <p class="text-sm text-gray-700 mb-4">
          Complete view access to medical records and nursing ADPIE.
        </p>

        <a href="{{ route('login.doctor') }}">
          <button
            class="bg-ehr text-white px-4 py-2 rounded-full font-bold hover:bg-green-900 transition cursor-pointer">
            Login as Doctor
          </button>
        </a>

      </div>

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

    </div>
  </main>

</body>

</html>