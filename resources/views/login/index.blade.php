<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="icon" href="{{ asset('img/ehr-icon.png') }}">
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

                <p class="role"><strong>LOG IN</strong></p>

                <a href="{{ route('login.nurse') }}" class="return-home"> NURSE </a>
                <br>
                <a href="{{ route('login.doctor') }}" class="return-home"> DOCTOR </a>
                <br>
                <a href="{{ route('login.admin') }}" class="return-home"> ADMIN </a>
                <br>

                <hr>

                @error('name') <p style="color:red;">{{ $message }}</p> @enderror
                @error('password') <p style="color:red;">{{ $message }}</p> @enderror

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