@extends('layouts.admin')

@section('content')

<h2 class="text-[50px] font-black mb-8 text-dark-green mt-18 text-center font-alte">REGISTER NEW USER</h2>

<div class="w-[100%] md:w-[80%] lg:w-[60%] mx-auto my-12">

    @if(session('sweetalert'))
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    const opts = @json(session('sweetalert'));
                    if (typeof showSuccess === 'function' && opts.type === 'success') {
                        showSuccess(opts.text || opts.title, opts.title || 'Success!', opts.timer);
                    } else if (typeof showError === 'function' && opts.type === 'error') {
                        showError(opts.text || opts.title, opts.title || 'Error!', opts.timer);
                    } else if (typeof Swal === 'function') {
                        Swal.fire({
                            icon: opts.type || 'info',
                            title: opts.title || '',
                            text: opts.text || '',
                            timer: opts.timer || 2000
                        });
                    }
                }, 100);
            });
        </script>
        @endpush
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-300 text-red-700 rounded-xl p-4 mb-6">
            <ul class="list-disc pl-6">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.attempt') }}" 
          class="bg-white shadow-2xl rounded-[20px] border border-gray-100 p-10 space-y-6">
        @csrf

        <div class="flex flex-col space-y-2">
            <label for="username" class="font-semibold text-gray-700">Username</label>
            <input type="text" 
                   name="username" 
                   id="username" 
                   required
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full">
            <span id="username-status" class="text-sm"></span>
        </div>

        <div class="flex flex-col space-y-2">
            <label for="email" class="font-semibold text-gray-700">Email</label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   required
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full">
            <span id="email-status" class="text-sm"></span>
        </div>

        <div class="flex flex-col space-y-2">
            <label for="password" class="font-semibold text-gray-700">Password</label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   required
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full">
        </div>

        <div class="flex flex-col space-y-2">
            <label for="password_confirmation" class="font-semibold text-gray-700">Confirm Password</label>
            <input type="password" 
                   name="password_confirmation" 
                   id="password_confirmation" 
                   required
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full">
            <span id="password-status" class="text-sm leading-relaxed"></span>
        </div>

        <div class="flex flex-col space-y-2">
            <label for="role" class="font-semibold text-gray-700">Role</label>
            <select name="role" 
                    id="role" 
                    required
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full">
                @foreach ($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>

        <div class="flex justify-center mt-8">
            <button type="submit" id="registerBtn" 
                class="button-default w-[200px] text-center disabled:opacity-50 disabled:cursor-not-allowed">
                Register User
            </button>
        </div>
    </form>

    {{-- Back Button --}}
    <div class="mt-10 text-center">
        <a href="{{ route('admin-home') }}" 
           class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-6 py-2.5 rounded-full shadow-sm transition-all duration-300">
           Back to Dashboard
        </a>
    </div>

</div>

@endsection

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