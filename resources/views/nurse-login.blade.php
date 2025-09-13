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


                <form>
                    <label for="nurse_id">Nurse ID / Username</label>
                    <br>
                    <input type="text" id="nurse_id" placeholder="Enter your Nurse ID">
                    <br>

                    <label for="password">Password</label>
                    <br>
                    <input type="password" id="password" placeholder="Enter your password">

                    <p class="forgot">Forgot password?</p>

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