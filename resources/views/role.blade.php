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
      <span class="font-bold text-lg">ELECTRONIC HEALTH RECORD</span>
    </div>
  </header>

  <main class="text-center p-8">
    <h1 class="text-3xl font-bold text-ehr">WHICH ONE ARE YOU?</h1>

    <div class="flex flex-wrap justify-center gap-8">

      <!-- NURSE -->
      <div class="border-2 border-ehr rounded-xl p-6 w-64 bg-white text-center hover:-translate-y-1 hover:shadow-lg transition">
        <div class="mb-4">
          <img src="#" alt="Nurse" class="w-12 mx-auto">
        </div>
        <h2 class="text-ehr font-semibold text-xl mb-2">NURSE</h2>
        <p class="text-sm text-gray-700 mb-4">
          Access patient care records, update vital signs, and manage daily care activities.
        </p>
        <button class="bg-ehr text-white px-4 py-2 rounded-full font-bold hover:bg-green-900 transition">
          Login as Nurse
        </button>
      </div>

      <!-- DOCTOR -->
      <div class="border-2 border-ehr rounded-xl p-6 w-64 bg-white text-center hover:-translate-y-1 hover:shadow-lg transition">
        <div class="mb-4">
          <img src="#" alt="Doctor" class="w-12 mx-auto">
        </div>
        <h2 class="text-ehr font-semibold text-xl mb-2">DOCTOR</h2>
        <p class="text-sm text-gray-700 mb-4">
          Complete view access to medical records and nursing ADPIE.
        </p>
        <button class="bg-ehr text-white px-4 py-2 rounded-full font-bold hover:bg-green-900 transition">
          Login as Doctor
        </button>
      </div>

      <!-- ADMIN -->
      <div class="border-2 border-ehr rounded-xl p-6 w-64 bg-white text-center hover:-translate-y-1 hover:shadow-lg transition">
        <div class="mb-4">
          <img src="#" alt="Admin" class="w-12 mx-auto">
        </div>
        <h2 class="text-ehr font-semibold text-xl mb-2">ADMIN</h2>
        <p class="text-sm text-gray-700 mb-4">
          Access patient care records, update vital signs, and manage daily care activities.
        </p>
        <button class="bg-ehr text-white px-4 py-2 rounded-full font-bold hover:bg-green-900 transition">
          Login as Admin
        </button>
      </div>

    </div>
  </main>

</body>
</html>
