<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Login</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="login-page">
    <div class="login-container">
        <div class="logo-section">
            <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr Logo" class="logo">
        </div>

        <div class="form-section">
            <p><span>NURSE</span></p>
            <p><strong>LOG IN</strong></p>

            <form>
                <label for="nurse_id">Nurse ID / Username</label>
                <input type="text" id="nurse_id" placeholder="Enter your Nurse ID">

                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Enter your password">

                <a href="#" class="forgot">Forgot password?</a>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="footer">
                <!-- picture ng home icon -->
                <a href="{{ url('/') }}">RETURN HOME</a>
            </div>
        </div>
    </div>
</body>
</html>
