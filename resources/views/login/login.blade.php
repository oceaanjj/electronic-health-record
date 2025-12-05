<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/login-style.css'])
    @vite(['resources/css/app.css'])
    @vite(['resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
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

                @if (session('error'))
                    <div class="error-box">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('login.authenticate') }}" method="POST">
                    @csrf
                    <label for="id">Username</label>
                    <br>
                    <input type="text" name="username" placeholder="Enter your username" class="h-[50px]"
                        value="{{ old('username') }}">
                    @error('username')
                        <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                    @enderror
                    <br>

                    <label for="password">Password</label><br>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="h-[50px]"
                            placeholder="Enter your password">
                        <span id="togglePassword" class="toggle-password" onclick="togglePassword()">
                            <i data-lucide="eye-off" class="eye-icon"></i>
                        </span>

                    </div>
                    @error('password')
                        <p class="text-red-600 mt-1 text-sm">{{ $message }}</p>
                    @enderror

                    {{-- <p class="forgot">Forgot password?</p> --}}

                    <!-- This will now show "Invalid login details." or "Unrecognized role." -->
                    {{--@error('username') <p style="color:red;">{{ $message }}</p> @enderror
                    @error('password') <p style="color:red;">{{ $message }}</p> @enderror--}}

                    <button type="submit" class="btn-login mt-6">Sign in</button>
                </form>


                <!-- <div>
                    <a href="{{ route('home') }}" class="return-home">
                        <img src="{{ asset('img/home-icon.png') }}" alt="home icon" class="home-icon">
                        RETURN HOME</a>
                </div> -->
            </div>
        </div>
    </div>

    @if(session('sweetalert'))
        <script>
            // Use setTimeout to ensure this doesn't block page rendering
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    const opts = @json(session('sweetalert'));
                    if (typeof showSuccess === 'function' && opts.type === 'success') {
                        showSuccess(opts.text || opts.title, opts.title || 'Success!', opts.timer);
                    } else if (typeof showError === 'function' && opts.type === 'error') {
                        showError(opts.text || opts.title, opts.title || 'Error!', opts.timer);
                    } else if (typeof showWarning === 'function' && opts.type === 'warning') {
                        showWarning(opts.text || opts.title, opts.title || 'Warning!', opts.timer);
                    } else if (typeof showInfo === 'function' && opts.type === 'info') {
                        showInfo(opts.text || opts.title, opts.title || 'Info', opts.timer);
                    } else if (typeof Swal === 'function') {
                        Swal.fire({
                            icon: opts.type || 'info',
                            title: opts.title || '',
                            text: opts.text || '',
                            timer: opts.timer || 2000
                        });
                    }
                }, 100); // Small delay to ensure page is fully loaded
            });
        </script>
    @endif

    <script>
        lucide.createIcons();

        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const togglePassword = document.getElementById("togglePassword");

            // Clear previous icon
            togglePassword.innerHTML = '';

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                // Show open-eye when visible
                togglePassword.innerHTML = '<i data-lucide="eye"></i>';
            } else {
                passwordInput.type = "password";
                // Show closed-eye when hidden
                togglePassword.innerHTML = '<i data-lucide="eye-off"></i>';
            }

            lucide.createIcons();
        }
    </script>

</body>

</html>