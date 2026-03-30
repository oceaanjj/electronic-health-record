<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/login-style.css'])
    @vite(['resources/css/app.css'])
    @vite(['resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .input-group {
            position: relative;
            margin-bottom: 4px;
        }

        .input-group input {
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
            border: 1.5px solid #ccc;
            border-radius: 8px;
            outline: none;
        }

        .input-group input:focus {
            border-color: #1a6b3c;
            box-shadow: 0 0 0 3px rgba(26, 107, 60, 0.15);
        }

        .validation-error {
            display: flex;
            align-items: flex-start;
            gap: 2px;
            color: #dc2626;
            font-size: 0.82rem;
            font-weight: 500;
            margin-top: 2px;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height 100px 0.3s ease, opacity 0.25s ease, margin 0.3s ease;
        }

        .validation-error i,
        .validation-error svg {
            margin-top: 2px;
            flex-shrink: 0;
        }

        .validation-error.visible {
            max-height: 100px;
            opacity: 1;
            margin-top: 2px;
        }

        .btn-login {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: opacity 0.2s ease, transform 0.1s ease;
        }

        .btn-login:active {
            transform: scale(0.97);
        }

        .input-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .role {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #111827;
        }

        .checkmark-container {
            width: 100px;
            height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
        }

        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: block;
            stroke-width: 4;
            stroke: #4CAF50;
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px #4CAF50;
            animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
        }

        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 4;
            stroke-miterlimit: 10;
            stroke: #4CAF50;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }

        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }

        @keyframes stroke {
            100% {
                stroke-dashoffset: 0;
            }
        }

        @keyframes scale {

            0%,
            100% {
                transform: none;
            }

            50% {
                transform: scale3d(1.1, 1.1, 1);
            }
        }

        @keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 40px #fff;
            }
        }

        .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2.5px solid rgba(255, 255, 255, 0.35);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.55s linear infinite;
        }

        .loading .spinner {
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media screen and (max-width: 821px) {
            body.login-page {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
                box-sizing: border-box;
            }

            .input-group,
            .btn-login {
                width: 230px;
            }

            .login-container {
                display: flex;
                flex-direction: column;
                width: 100%;
                max-width: 400px;
                border-radius: 12px;
                overflow: hidden;
                margin-top: -50px;
                align-items: center;
            }

            .logo-section {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 10px 0 10px 0;
                width: 100%;
            }

            .logo {
                width: 120px;
                height: auto;
                display: block;
                margin: 0 auto;
            }

            .form-section {
                margin: -20px;
                margin-top: 10px;
                padding: 10px 30px 40px 30px;
                width: 120%;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            #form-container {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            #resetForm {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .role,
            .role strong {
                margin-top: -20px;
                padding-bottom: 40px;
                font-size: 40px !important;
                text-align: center;
                margin-bottom: 20px;
                width: 150%;
                margin-left: -25%;
            }

            #upper-line {
                margin-top: -10px;
                width: calc(100% + 60px);
                height: 7px;
                background: #edb62c;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body class="login-page">
    <div class="login-container">
        <div class="logo-section">
            <a href="{{ url('/') }}">
                <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr Logo" class="logo" />
            </a>
        </div>

        <div class="form-section">
            <p id="upper-line"></p>
            <div id="form-container">
                <p class="role"><strong>FORGOT PASSWORD</strong></p>

                @if (session('throttle_error') && session('throttle_error')['source'] === 'email')
                    <div id="throttle-box"
                        class="flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm mb-4"
                        style="color: #dc2626;">
                        <i data-lucide="circle-x" class="w-5 h-5 mt-0.5 shrink-0"></i>
                        <span>Too many attempts. Please wait <strong id="throttle-timer">--:--</strong> before trying
                            again.</span>
                    </div>
                @endif

                @if (session('status'))
                    <div class="flex flex-col items-center text-center animate-slide-down -mt-5">
                        <div id="code-sent-header">
                            <div class="checkmark-container">
                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                                </svg>
                            </div>
                            <div style="margin-top: 5px;">
                                <h3 class="text-xl font-bold text-green-800">Code Sent!</h3>
                                <p class="text-sm font-medium leading-relaxed text-gray-700 mt-1">Check your inbox for a 6-digit
                                    code to
                                    reset your password. If you don't see it, please check your spam folder.</p>
                            </div>
                        </div>

                        <div id="verify-container" class="w-full">
                            <form id="verifyCodeForm" class="w-full mt-6 flex flex-col items-center">
                                <label class="block mb-4 font-semibold text-gray-700 text-sm">Enter the 6-Digit Reset
                                    Code</label>
                                <div class="flex gap-2 mb-2 justify-center" id="otp-inputs">
                                    <input type="text" maxlength="1"
                                        class="otp-box w-10 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-green-600 focus:ring-2 focus:ring-green-100 outline-none transition-all" />
                                    <input type="text" maxlength="1"
                                        class="otp-box w-10 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-green-600 focus:ring-2 focus:ring-green-100 outline-none transition-all" />
                                    <input type="text" maxlength="1"
                                        class="otp-box w-10 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-green-600 focus:ring-2 focus:ring-green-100 outline-none transition-all" />
                                    <input type="text" maxlength="1"
                                        class="otp-box w-10 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-green-600 focus:ring-2 focus:ring-green-100 outline-none transition-all" />
                                    <input type="text" maxlength="1"
                                        class="otp-box w-10 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-green-600 focus:ring-2 focus:ring-green-100 outline-none transition-all" />
                                    <input type="text" maxlength="1"
                                        class="otp-box w-10 h-12 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-green-600 focus:ring-2 focus:ring-green-100 outline-none transition-all" />
                                </div>
                                <input type="hidden" id="code_input" name="code" />

                                <div id="code-error" class="validation-error mb-4">
                                    <i data-lucide="circle-x" class="w-4 h-4 mr-1"></i>
                                    <span id="code-error-text">Invalid code. You have <span id="attempts-left">4</span>
                                        tries remaining.</span>
                                </div>

                                <button type="submit"
                                    class="btn-login mt-2 h-[50px] bg-green-700 hover:bg-green-800 text-white font-bold rounded-lg shadow-sm transition-all w-full max-w-[230px]"
                                    id="verifyBtn">
                                    <span class="spinner"></span>
                                    <span class="btn-text">Verify Code</span>
                                </button>
                            </form>
                        </div>

                        <div id="invalid-code-screen"
                            class="hidden w-full flex flex-col items-center text-center">
                            <img class="h-auto w-[150px] mb-6" src="{{ asset('img/others/403error.png') }}"
                                alt="Invalid Code" />
                            <h3 class="text-2xl font-black text-dark-red uppercase leading-none">Invalid Code</h3>
                            <p class="text-sm font-medium leading-relaxed text-gray-700 mt-4 px-4">
                                This password reset code is invalid or has already expired.
                                For your security, please request a new one.
                            </p>

                            <div class="mt-8 w-full flex flex-col items-center gap-4">
                                <a href="{{ route('password.request') }}"
                                    class="btn-login h-[50px] bg-green-700 hover:bg-green-800 text-white font-bold rounded-lg shadow-sm transition-all flex items-center justify-center no-underline w-full max-w-[230px]">
                                    Request New Code
                                </a>
                                <a href="{{ route('login') }}"
                                    class="text-sm font-bold text-gray-500 hover:text-green-700 transition-all no-underline">
                                    Return to Login
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <form action="{{ route('password.email') }}" method="POST" id="resetForm" novalidate>
                        @csrf

                        <div class="input-group">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" placeholder="Enter your registered email"
                                class="{{ $errors->has('email') ? 'input-error' : '' }} h-[50px] p-3"
                                value="{{ old('email') }}" required />

                            <div id="email-validation-error" class="validation-error">
                                <i data-lucide="circle-x" class="w-4 h-4 mr-1"></i>
                                <span id="email-error-text"></span>
                            </div>

                            @error('email')
                                <div class="validation-error visible">
                                    <i data-lucide="circle-x" class="w-4 h-4 mr-1"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <button type="submit"
                            class="btn-login mt-6 h-[50px] bg-green-700 hover:bg-green-800 text-white font-bold rounded-lg shadow-sm transition-all"
                            id="resetBtn">
                            <span class="spinner"></span>
                            <span class="btn-text">Send Reset Code</span>
                        </button>

                        <div class="mt-8 text-center">
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold text-green-700 hover:text-green-800 transition-colors">
                                Back to Login
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if (session('sweetalert'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(function () {
                    const opts = @json(session('sweetalert'));
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: opts.type || 'info',
                            title: opts.title || '',
                            text: opts.text || '',
                            timer: opts.timer || 3000,
                        });
                    }
                }, 100);
            });
        </script>
    @endif

    <script>
        lucide.createIcons();

        const emailInput = document.getElementById('email');
        const emailErrorDiv = document.getElementById('email-validation-error');
        const emailErrorText = document.getElementById('email-error-text');

        function validateEmail(isSubmit = false) {
            const email = emailInput.value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email === "") {
                if (isSubmit) {
                    emailErrorText.textContent = "Please enter your registered email.";
                    emailErrorDiv.classList.add('visible');
                } else {
                    emailErrorDiv.classList.remove('visible');
                }
                return false;
            } else if (!emailPattern.test(email)) {
                emailErrorText.textContent = "Please enter a valid email address (e.g., user@example.com).";
                emailErrorDiv.classList.add('visible');
                return false;
            } else {
                emailErrorDiv.classList.remove('visible');
                return true;
            }
        }

        if (emailInput) {
            emailInput.addEventListener('input', () => validateEmail(false));
            emailInput.addEventListener('blur', () => validateEmail(false));
        }

        const resetForm = document.getElementById('resetForm');
        if (resetForm) {
            resetForm.addEventListener('submit', function (e) {
                const isValid = validateEmail(true);

                if (!isValid) {
                    e.preventDefault();
                    return;
                }

                const btn = document.getElementById('resetBtn');
                btn.classList.add('loading');
                btn.disabled = true;
            });
        }

        const verifyCodeForm = document.getElementById('verifyCodeForm');
        if (verifyCodeForm) {
            const inputs = document.querySelectorAll('.otp-box');
            const hiddenInput = document.getElementById('code_input');

            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    if (e.target.value.length > 1) {
                        e.target.value = e.target.value.slice(0, 1);
                    }
                    if (e.target.value && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    updateHiddenInput();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                // Handle paste
                input.addEventListener('paste', (e) => {
                    const data = e.clipboardData.getData('text').slice(0, 6);
                    if (/^\d+$/.test(data)) {
                        data.split('').forEach((char, i) => {
                            if (inputs[i]) inputs[i].value = char;
                        });
                        inputs[Math.min(data.length, inputs.length - 1)].focus();
                        updateHiddenInput();
                    }
                    e.preventDefault();
                });
            });

            function updateHiddenInput() {
                const code = Array.from(inputs).map(i => i.value).join('');
                hiddenInput.value = code;
            }

            let attempts = {{ session('reset_attempts_count', 0) }};
            const maxAttempts = 4;

            // Check on load if already exceeded
            if (attempts >= maxAttempts) {
                const verifyContainer = document.getElementById('verify-container');
                const codeSentHeader = document.getElementById('code-sent-header');
                const invalidScreen = document.getElementById('invalid-code-screen');
                if (verifyContainer) verifyContainer.classList.add('hidden');
                if (codeSentHeader) codeSentHeader.classList.add('hidden');
                if (invalidScreen) invalidScreen.classList.remove('hidden');
            }

            verifyCodeForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const code = hiddenInput.value;
                const codeError = document.getElementById('code-error');
                const verifyBtn = document.getElementById('verifyBtn');

                if (!/^\d{6}$/.test(code)) {
                    document.getElementById('code-error-text').textContent = "Please enter the complete 6-digit code.";
                    codeError.classList.add('visible');
                    return;
                }

                verifyBtn.classList.add('loading');
                verifyBtn.disabled = true;

                const email = "{{ old('email', request()->input('email')) }}";

                fetch("{{ route('password.verify') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ email, code })
                })
                    .then(response => response.json())
                    .then(data => {
                        verifyBtn.classList.remove('loading');
                        verifyBtn.disabled = false;

                        if (data.valid) {
                            if (typeof showSuccess === 'function') {
                                showSuccess('Your code is correct. You can now reset your password.', 'Code Verified!').then(() => {
                                    const source = "{{ request()->input('source', 'web') }}";
                                    const baseUrl = "{{ route('password.reset', ['token' => 'TOKEN_PLACEHOLDER']) }}";
                                    const finalUrl = baseUrl.replace('TOKEN_PLACEHOLDER', code) + "?email=" + encodeURIComponent(email) + "&source=" + encodeURIComponent(source);
                                    window.location.href = finalUrl;
                                });
                            } else {
                                // Fallback
                                const source = "{{ request()->input('source', 'web') }}";
                                const baseUrl = "{{ route('password.reset', ['token' => 'TOKEN_PLACEHOLDER']) }}";
                                const finalUrl = baseUrl.replace('TOKEN_PLACEHOLDER', code) + "?email=" + encodeURIComponent(email) + "&source=" + encodeURIComponent(source);
                                window.location.href = finalUrl;
                            }
                        } else {
                            attempts = data.attempts || attempts + 1;
                            if (data.exceeded || attempts >= maxAttempts) {
                                document.getElementById('verify-container').classList.add('hidden');
                                document.getElementById('code-sent-header').classList.add('hidden');
                                document.getElementById('invalid-code-screen').classList.remove('hidden');
                            } else {
                                document.getElementById('code-error-text').innerHTML = `Invalid code. You have <strong>${maxAttempts - attempts}</strong> tries remaining.`;
                                codeError.classList.add('visible');
                                // Clear inputs on failure
                                inputs.forEach(i => i.value = '');
                                inputs[0].focus();
                                updateHiddenInput();
                            }
                        }
                    })
                    .catch(error => {
                        verifyBtn.classList.remove('loading');
                        verifyBtn.disabled = false;
                        console.error('Error:', error);
                    });
            });
        }

        (function () {
            let throttleSeconds = {{ (session('throttle_error') && session('throttle_error')['source'] === 'email') ? session('throttle_error')['seconds'] : 0 }};
            const timerDisplay = document.getElementById('throttle-timer');
            const throttleBox = document.getElementById('throttle-box');

            if (throttleSeconds > 0 && timerDisplay) {
                function updateTimer() {
                    if (throttleSeconds <= 0) {
                        if (throttleBox) {
                            throttleBox.querySelector('span').textContent = 'You can now try again.';
                        }
                        return;
                    }

                    const minutes = Math.floor(throttleSeconds / 60);
                    const seconds = throttleSeconds % 60;
                    timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                    throttleSeconds--;
                    setTimeout(updateTimer, 1000);
                }
                updateTimer();
            }
        })();
    </script>
</body>

</html>