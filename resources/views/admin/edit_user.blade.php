@extends('layouts.admin')

@section('content')

    <h2 class="text-[50px] font-black mb-8 text-dark-green mt-18 text-center font-alte uppercase">Edit User:
        {{ $user->username }}</h2>

    <div class="w-[100%] md:w-[80%] lg:w-[60%] mx-auto my-12">

        <form method="POST" action="{{ route('users.update', $user->id) }}"
            class="bg-white shadow-2xl rounded-[20px] border border-gray-100 p-10 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- First Name -->
                <div class="flex flex-col space-y-2">
                    <label for="first_name" class="font-semibold text-gray-700">First Name</label>
                    <input type="text" name="first_name" id="first_name" required
                        value="{{ old('first_name', $firstName) }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    @error('first_name')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>
                            Error: {{ $message }}</p>
                    @enderror
                </div>

                <!-- Last Name -->
                <div class="flex flex-col space-y-2">
                    <label for="last_name" class="font-semibold text-gray-700">Last Name</label>
                    <input type="text" name="last_name" id="last_name" required value="{{ old('last_name', $lastName) }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    @error('last_name')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>
                            Error: {{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div class="flex flex-col space-y-2">
                    <label for="username" class="font-semibold text-gray-700">Username</label>
                    <input type="text" name="username" id="username" required value="{{ old('username', $user->username) }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    <span id="username-status" class="text-sm"></span>
                    @error('username')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>
                            Error: {{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="flex flex-col space-y-2">
                    <label for="email" class="font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    <span id="email-status" class="text-sm"></span>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>
                            Error: {{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-baseline">
                        <label for="password" class="font-semibold text-gray-700">Password</label>
                        <span class="text-[11px] text-gray-400 italic">Leave blank to keep current</span>
                    </div>
                    <input type="password" name="password" id="password"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                </div>

                <!-- Confirm Password -->
                <div class="flex flex-col space-y-2">
                    <div class="flex items-baseline">
                        <label for="password_confirmation" class="font-semibold text-gray-700">Confirm Password</label>
                        <span class="text-[11px] text-transparent select-none">&nbsp;</span> <!-- Spacer for alignment -->
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                </div>
            </div>

            <div id="password-validation-box" class="hidden p-4 rounded-lg bg-gray-50 border border-gray-200 mt-2">
                <span id="password-status" class="text-sm leading-tight block"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Age -->
                <div class="flex flex-col space-y-2">
                    <label for="age" class="font-semibold text-gray-700">Age</label>
                    <input type="number" name="age" id="age" required value="{{ old('age', $user->age) }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    @error('age')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>
                            Error: {{ $message }}</p>
                    @enderror
                </div>

                <!-- Birthdate -->
                <div class="flex flex-col space-y-2">
                    <label for="birthdate" class="font-semibold text-gray-700">Birthdate (Auto-calculated)</label>
                    <input type="date" name="birthdate" id="birthdate" required readonly
                        value="{{ old('birthdate', $user->birthdate) }}"
                        class="px-4 py-2 border-2 border-gray-200 bg-gray-50 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full cursor-not-allowed">
                    @error('birthdate')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>
                            Error: {{ $message }}</p>
                    @enderror
                </div>

                <!-- Birthplace -->
                <div class="flex flex-col space-y-2">
                    <label for="birthplace" class="font-semibold text-gray-700">Birthplace</label>
                    <input type="text" name="birthplace" id="birthplace" required
                        value="{{ old('birthplace', $user->birthplace) }}"
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    @error('birthplace')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>
                            Error: {{ $message }}</p>
                    @enderror
                </div>

                <!-- Sex -->
                <div class="flex flex-col space-y-2">
                    <label for="sex" class="font-semibold text-gray-700">Sex</label>
                    <select name="sex" id="sex" required
                        class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                        <option value="Male" {{ old('sex', $user->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex', $user->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('sex', $user->sex) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('sex')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i>
                            Error: {{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Role (Full Row) -->
            <div class="flex flex-col space-y-2 mt-6">
                <label for="role" class="font-semibold text-gray-700">Role</label>
                <select name="role" id="role" required
                    class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>{{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i> Error:
                        {{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div class="flex flex-col space-y-2 mt-6">
                <label for="address" class="font-semibold text-gray-700">Address</label>
                <textarea name="address" id="address" required rows="2"
                    class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-dark-green focus:border-dark-green outline-none transition duration-300 w-full hover:border-dark-green/30">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fa-solid fa-circle-xmark"></i> Error:
                        {{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center mt-8">
                <button type="submit" id="updateBtn"
                    class="button-default w-[200px] text-center disabled:opacity-50 disabled:cursor-not-allowed">
                    Update User
                </button>
            </div>
        </form>

        {{-- Back Button --}}
        <div class="mt-10 text-center">
            <a href="{{ route('users') }}"
                class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-6 py-2.5 rounded-full shadow-sm transition-all duration-300">
                Back to User Management
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
        const updateBtn = document.getElementById('updateBtn');

        const originalUsername = "{{ $user->username }}";
        const originalEmail = "{{ $user->email }}";

        let usernameValid = true;
        let emailValid = true;
        let passwordValid = true; // True because it's optional

        function toggleUpdateButton() {
            updateBtn.disabled = !(usernameValid && emailValid && passwordValid);
        }

        // Age to Birthdate calculation
        ageInput.addEventListener('input', () => {
            const age = parseInt(ageInput.value);
            if (!isNaN(age) && age >= 0) {
                const today = new Date();
                const birthYear = today.getFullYear() - age;
                const calculatedDate = `${birthYear}-01-01`;
                birthdateInput.value = calculatedDate;
            } else {
                birthdateInput.value = '';
            }
        });

        //  Username check
        usernameInput.addEventListener('input', () => {
            const username = usernameInput.value;
            if (username === originalUsername) {
                usernameStatus.textContent = "";
                usernameValid = true;
                toggleUpdateButton();
                return;
            }
            if (username.length < 3) {
                usernameStatus.textContent = "Too short";
                usernameStatus.style.color = "red";
                usernameValid = false;
                toggleUpdateButton();
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
                    toggleUpdateButton();
                });
        });

        //  Email check
        emailInput.addEventListener('input', () => {
            const email = emailInput.value;
            if (email === originalEmail) {
                emailStatus.textContent = "";
                emailValid = true;
                toggleUpdateButton();
                return;
            }
            if (!email.includes("@")) {
                emailStatus.textContent = "Invalid email";
                emailStatus.style.color = "red";
                emailValid = false;
                toggleUpdateButton();
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
                    toggleUpdateButton();
                });
        });

        //  Password confirmation check
        function checkPasswords() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (password.length === 0) {
                validationBox.classList.add('hidden');
                passwordValid = true; // valid because it's optional
                toggleUpdateButton();
                return;
            }

            validationBox.classList.remove('hidden');

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
                passwordStatus.innerHTML = `<div class="flex items-start gap-1 text-green-600 font-bold"><i class="fa-solid fa-circle-check mt-1"></i><span>Security requirements met! The new password is strong and matches.</span></div>`;
                passwordValid = true;
            }

            toggleUpdateButton();
        }

        passwordInput.addEventListener('input', checkPasswords);
        confirmPasswordInput.addEventListener('input', checkPasswords);
    </script>
@endpush