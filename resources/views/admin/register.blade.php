@extends('layouts.admin')

@section('content')
    <h1 class="register">Register New User</h1>

    {{-- ✅ Success Message --}}
   @if(session('sweetalert'))
        @push('scripts')
        <script>
            // Use setTimeout to ensure this doesn't block form validation JS
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
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
                }, 100); // Small delay to ensure form validation JS is ready
            });
        </script>
        @endpush
    @endif

    {{-- ❌ Error messages --}}
    @if ($errors->any())
        <div style="color:red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.attempt') }}">
        @csrf
        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <span id="username-status"></span>
        </div>
        <div>
            <label>Email:</label>
            <input type="email" name="email" id="email" required>
            <span id="email-status"></span>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <label>Confirm Password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
            <span id="password-status"></span>
        </div>
        <div>
            <label>Role:</label>
            <select name="role" required>
                @foreach ($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" id="registerBtn">Register User</button>
    </form>

    <a class="back" href="{{ route('admin-home') }}">Back to Dashboard</a>
@endsection

@push('styles')
    @vite(['resources/css/admin-register.css'])
@endpush

@push('scripts')
    <script>
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');

        const usernameStatus = document.getElementById('username-status');
        const emailStatus = document.getElementById('email-status');
        const passwordStatus = document.getElementById('password-status');
        const registerBtn = document.getElementById('registerBtn');

        let usernameValid = false;
        let emailValid = false;
        let passwordValid = false;

        function toggleRegisterButton() {
            registerBtn.disabled = !(usernameValid && emailValid && passwordValid);
        }

        //  Username check
        usernameInput.addEventListener('input', () => {
            const username = usernameInput.value;
            if (username.length < 3) {
                usernameStatus.textContent = "Too short";
                usernameStatus.style.color = "red";
                usernameValid = false;
                toggleRegisterButton();
                return;
            }
            fetch(`{{ url('/check-username') }}?username=${encodeURIComponent(username)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.available) {
                        usernameStatus.textContent = "Available";
                        usernameStatus.style.color = "green";
                        usernameValid = true;
                    } else {
                        usernameStatus.textContent = "Already taken";
                        usernameStatus.style.color = "red";
                        usernameValid = false;
                    }
                    toggleRegisterButton();
                });
        });

        //  Email check
        emailInput.addEventListener('input', () => {
            const email = emailInput.value;
            if (!email.includes("@")) {
                emailStatus.textContent = "Invalid email";
                emailStatus.style.color = "red";
                emailValid = false;
                toggleRegisterButton();
                return;
            }
            fetch(`{{ url('/check-email') }}?email=${encodeURIComponent(email)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.available) {
                        emailStatus.textContent = "Available";
                        emailStatus.style.color = "green";
                        emailValid = true;
                    } else {
                        emailStatus.textContent = "Already used";
                        emailStatus.style.color = "red";
                        emailValid = false;
                    }
                    toggleRegisterButton();
                });
        });

        //  Password confirmation check
        function checkPasswords() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            // ---  NEW PASSWORD STRENGTH LOGIC  ---
            const minLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password); // Common special chars

            let message = "";
            let isValid = true;

            if (!minLength) {
                message += "• Must be at least 8 characters.<br>";
                isValid = false;
            }
            if (!hasUpper) {
                message += "• Must contain an uppercase letter.<br>";
                isValid = false;
            }
            if (!hasNumber) {
                message += "• Must contain a number.<br>";
                isValid = false;
            }
            if (!hasSpecial) {
                message += "• Must contain a special character (e.g., !@#$).<br>";
                isValid = false;
            }
            // ------------------------------------------

            if (password !== confirmPassword && password.length > 0 && confirmPassword.length > 0) {
                message += "• Passwords do not match.<br>";
                isValid = false;
            }

            // If the password is empty, don't show complex error messages, just clear status
            if (password.length === 0) {
                passwordStatus.textContent = "";
                passwordValid = false;
            } else if (!isValid) {
                passwordStatus.innerHTML = `❌ ${message}`;
                passwordStatus.style.color = "red";
                passwordValid = false;
            } else {
                passwordStatus.textContent = "Password is strong and matches!";
                passwordStatus.style.color = "green";
                passwordValid = true;
            }

            // if (passwordStatus.querySelector('br')) {
            //     passwordStatus.querySelector('br').remove();
            // }

            toggleRegisterButton();
        }

        passwordInput.addEventListener('input', checkPasswords);
        confirmPasswordInput.addEventListener('input', checkPasswords);

        // Auto-hide success message after 3s
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.transition = "opacity 0.5s ease";
                successMessage.style.opacity = 0;
                setTimeout(() => successMessage.remove(), 500);
            }, 3000);
        }
    </script>
@endpush