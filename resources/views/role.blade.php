<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Electronic Health Record - Login Role</title>
  @vite(['resources/css/role-style.css'])
</head>
<body>


  <header class="navbar">
    <div class="navbar-left">
      <img src="logo.png" alt="Logo" class="logo">
      <span class="title">ELECTRONIC HEALTH RECORD</span>
    </div>
  </header>


  <main class="main-container">
    <h1 class="heading">WHICH ONE ARE YOU?</h1>

    <div class="role-container">
      
      <!-- NURSE'-->
      <div class="role">
        <div class="icon">
          <img src="#" alt="Nurse">
        </div>
        <h2>NURSE</h2>
        <p>Access patient care records, update vital signs, and manage daily care activities.</p>
        <button class="btn">Login as Nurse</button>
      </div>

      <!-- DOCTOR -->
      <div class="role">
        <div class="icon">
          <img src="#" alt="Doctor">
        </div>
        <h2>DOCTOR</h2>
        <p>Complete view access to medical records and nursing ADPIE.</p>
        <button class="btn">Login as Doctor</button>
      </div>

      <!-- ADMIN -->
      <div class="role">
        <div class="icon">
          <img src="#" alt="Admin">
        </div>
        <h2>ADMIN</h2>
        <p>Access patient care records, update vital signs, and manage daily care activities.</p>
        <button class="btn">Login as Admin</button>
      </div>

    </div>
  </main>

</body>
</html>
