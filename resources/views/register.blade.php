<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    @vite(['./resources/css/login-style.css'])
</head>

<body class="login-page">
    <div class="login-container">
        <div class="logo-section">
            <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr Logo" class="logo">
        </div>

        <div class="form-section">
            <p id="upper-line"></p>
            <div id="form-container">

                <p class="role"> <strong>REGISTER</strong></p>

                <form method="POST" action="{{ route('register.attempt') }}">
                    @csrf

                    <div>
                        <label for="name">Username</label>
                        <br>
                        <input type="text" name="username" id="username" placeholder="Enter name" value="{{ old('username') }}"
                            required>
                        @error('name')
                            <p style="color:red;">{{ $message }}</p>
                        @enderror
                    </div>
                    <br>

                    <div>
                        <label for="email">Email</label>
                        <br>
                        <input type="email" name="email" id="email" placeholder="Enter email" value="{{ old('email') }}"
                            required>
                        @error('email')
                            <p style="color:red;">{{ $message }}</p>
                        @enderror
                    </div>
                    <br>

                    <div>
                        <label for="password">Password</label>
                        <br>
                        <input type="password" name="password" id="password" placeholder="Enter password" required>
                        @error('password')
                            <p style="color:red;">{{ $message }}</p>
                        @enderror
                    </div>
                    <br>

                    <div>
                        <label for="password_confirmation">Confirm Password</label>
                        <br>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Confirm password" required>
                    </div>
                    <br>

                    <div>
                        <label for="role">Role</label>
                        <br>
                        <select name="role" id="role" required>
                            <option value="">Select a Role</option>
                            <option value="Nurse" {{ old('role') == 'Nurse' ? 'selected' : '' }}>Nurse</option>
                            <option value="Doctor" {{ old('role') == 'Doctor' ? 'selected' : '' }}>Doctor</option>
                            <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p style="color:red;">{{ $message }}</p>
                        @enderror
                    </div>
                    <br>

                    <button type="submit" class="btn-login">Register</button>
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