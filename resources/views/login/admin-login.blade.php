<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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

                <p class="role"> ADMIN <strong>LOG IN</strong></p>

                <form action="{{ route('login.authenticate') }}" method="POST">
                    @csrf
                    <label for="admin_id">Admin ID / Username</label>
                    <br>
                    <input type="text" name="name" placeholder="Enter your Admin ID">
                    <br>

                    <label for="password">Password</label>
                    <br>
                    <input type="password" name="password" placeholder="Enter your password">

                    <p class="forgot">Forgot password?</p>

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