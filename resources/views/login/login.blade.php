<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

                <form action="{{ route('login.authenticate') }}" method="POST">
                    @csrf
                    <label for="id">Username</label>
                    <br>
                    <input type="text" name="username" placeholder="Enter your username" value="{{ old('username') }}">
                    <br>

                    <label for="password">Password</label>
                    <br>
                    <input type="password" name="password" placeholder="Enter your password">

                    <p class="forgot">Forgot password?</p>

                    <!-- This will now show "Invalid login details." or "Unrecognized role." -->
                    @error('username') <p style="color:red;">{{ $message }}</p> @enderror
                    @error('password') <p style="color:red;">{{ $message }}</p> @enderror

                    <button type="submit" class="btn-login">Sign In</button>
                </form>

                <hr>
                <!-- <div>
                    <a href="{{ route('home') }}" class="return-home">
                        <img src="{{ asset('img/home-icon.png') }}" alt="home icon" class="home-icon">
                        RETURN HOME</a>
                </div> -->
            </div>
        </div>
    </div>
</body>

</html>