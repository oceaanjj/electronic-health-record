@extends('layouts.admin')

@section('content')

    <h2 class="text-[50px] font-black mb-8 text-dark-green mt-18 text-center font-alte">REGISTER NEW USER</h2>

    <div class="w-[100%] md:w-[80%] lg:w-[60%] mx-auto my-12">

        @if(session('sweetalert'))
            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        setTimeout(function () {
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
                <div class="font-bold text-red-800 flex items-center gap-1 mb-2">
                    <i class="fa-solid fa-circle-xmark"></i>Errors:
                </div>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div class="flex flex-col space-y-2">
                    <label for="first_name" class="font-semibold text-gray-700">First Name</label>
                    <input type="text" name="first_name" id="first_name" required value="{{ old('first_name') }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                </div>

                <!-- Last Name -->
                <div class="flex flex-col space-y-2">
                    <label for="last_name" class="font-semibold text-gray-700">Last Name</label>
                    <input type="text" name="last_name" id="last_name" required value="{{ old('last_name') }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                </div>

                <!-- Username -->
                <div class="flex flex-col space-y-2">
                    <label for="username" class="font-semibold text-gray-700">Username</label>
                    <input type="text" name="username" id="username" required value="{{ old('username') }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    <span id="username-status" class="text-sm"></span>
                </div>

                <!-- Email -->
                <div class="flex flex-col space-y-2">
                    <label for="email" class="font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    <span id="email-status" class="text-sm"></span>
                </div>

                <!-- Password -->
                <div class="flex flex-col space-y-2">
                    <label for="password" class="font-semibold text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                </div>

                <!-- Confirm Password -->
                <div class="flex flex-col space-y-2">
                    <label for="password_confirmation" class="font-semibold text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                </div>
            </div>

            <div id="password-validation-box" class="hidden p-4 rounded-lg bg-gray-50 border border-gray-200 mt-2">
                <span id="password-status" class="text-sm leading-relaxed block"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Age -->
                <div class="flex flex-col space-y-2">
                    <label for="age" class="font-semibold text-gray-700">Age</label>
                    <input type="number" name="age" id="age" required value="{{ old('age') }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                </div>

                <!-- Birthdate -->
                <div class="flex flex-col space-y-2">
                    <label for="birthdate" class="font-semibold text-gray-700">Birthdate (Auto-calculated)</label>
                    <input type="date" name="birthdate" id="birthdate" required readonly value="{{ old('birthdate') }}"
                        class="px-4 py-2 border-2 border-gray-200 bg-gray-50 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full cursor-not-allowed">
                </div>

                <!-- Birthplace -->
                <div class="flex flex-col space-y-2">
                    <label for="birthplace" class="font-semibold text-gray-700">Birthplace</label>
                    <input type="text" name="birthplace" id="birthplace" required value="{{ old('birthplace') }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                </div>

                <!-- Sex -->
                <div class="flex flex-col space-y-2">
                    <label for="sex" class="font-semibold text-gray-700">Sex</label>
                    <select name="sex" id="sex" required
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                        <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('sex') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <!-- Role (Full Row) -->
            <div class="flex flex-col space-y-2 mt-6">
                <label for="role" class="font-semibold text-gray-700">Role</label>
                <select name="role" id="role" required
                    class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Address -->
            <div class="flex flex-col space-y-2 mt-6">
                <label for="address" class="font-semibold text-gray-700">Address</label>
                <textarea name="address" id="address" required rows="2"
                    class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">{{ old('address') }}</textarea>
            </div>

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
        const ageInput = document.getElementById('age');
        const birthdateInput = document.getElementById('birthdate');

        const usernameStatus = document.getElementById('username-status');
        const emailStatus = document.getElementById('email-status');
        const passwordStatus = document.getElementById('password-status');
        const validationBox = document.getElementById('password-validation-box');
        const registerBtn = document.getElementById('registerBtn');

        // Age to Birthdate calculation
        ageInput.addEventListener('input', () => {
            const age = parseInt(ageInput.value);
            if (!isNaN(age) && age >= 0) {
                const today = new Date();
                const birthYear = today.getFullYear() - age;
                // Default to January 1st of that year for the calculated birthdate
                const calculatedDate = `${birthYear}-01-01`;
                birthdateInput.value = calculatedDate;
            } else {
                birthdateInput.value = '';
            }
        });

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

            if (password.length === 0) {
                validationBox.classList.add('hidden');
                passwordValid = false;
                toggleRegisterButton();
                return;
            }

            validationBox.classList.remove('hidden');

            // ---  NEW PASSWORD STRENGTH LOGIC  ---
            const minLength = password.length >= 8;
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

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

            if (password !== confirmPassword && confirmPassword.length > 0) {
                message += "• Passwords do not match.<br>";
                isValid = false;
            }

            if (!isValid) {
                if (message.endsWith('<br>')) {
                    message = message.slice(0, -4);
                }
                passwordStatus.innerHTML = `
                    <div class="flex items-start gap-1" style="color: #dc2626">
                        <i class="fa-solid fa-circle-xmark mt-1"></i>
                        <div class="leading-tight font-bold">
                            <strong>Errors:</strong>
                            <div class="mt-0 font-normal">${message}</div>
                        </div>
                    </div>`;
                passwordValid = false;
            } else {
                passwordStatus.innerHTML = `<div class="flex items-start gap-1 text-green-600 font-bold"><i class="fa-solid fa-circle-check mt-1"></i><span>Strong Password! Passwords match and meet requirements.</span></div>`;
                passwordValid = true;
            }

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