<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Login</title>
    @vite(['resources/css/login-style.css'])
</head>

<body class="login-page">
    <div class="login-container">
        <div class="logo-section">
            <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr Logo" class="logo">
        </div>

        <div class="form-section">
            <p id="upper-line"></p>
            <div id="form-container">

                <p class="role"> NURSE <strong>LOG IN</strong></p>

                <form action="{{ route('login.authenticate.nurse') }}" method="POST">
                    @csrf
                    <label for="nurse_id">Nurse ID / Username</label>
                    <input type="text" id="name" name="name" placeholder="Enter your Nurse ID">

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password">

                    @error('name') <p style="color:red;">{{ $message }}</p> @enderror
                    @error('password') <p style="color:red;">{{ $message }}</p> @enderror

                    <button type="submit" class="btn-login">Sign In</button>
                </form>

                <hr>
                <div>
                    <!-- picture ng home icon -->
                    <a href="{{ route('home') }}" class="return-home">
                        <img src="{{ asset('img/home-icon.png') }}" alt="home icon" class="home-icon">
                        RETURN HOME</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>