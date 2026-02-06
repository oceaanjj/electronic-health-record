<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Login</title>
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
                transition:
                    border-color 0.25s ease,
                    box-shadow 0.25s ease;
                border: 1.5px solid #ccc;
                border-radius: 8px;
                outline: none;
            }

            .input-group input:focus {
                border-color: #1a6b3c;
                box-shadow: 0 0 0 3px rgba(26, 107, 60, 0.15);
            }

            .input-group input.input-valid {
                border-color: #1a6b3c;
            }

            .input-group input.input-error {
                border-color: #dc2626;
                box-shadow: none;
            }

            .input-group input.input-error:focus {
                box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
            }

            .validation-error {
                display: flex;
                align-items: center;
                gap: 2px;
                color: #dc2626;
                font-size: 0.82rem;
                font-weight: 500;
                margin-top: 2px;
                max-height: 0;
                overflow: hidden;
                opacity: 0;
                transition:
                    max-height 0.3s ease,
                    opacity 0.25s ease,
                    margin 0.3s ease;
            }

            .validation-error.visible {
                max-height: 40px;
                opacity: 1;
                margin-top: 2px;
            }

            .validation-error svg {
                flex-shrink: 0;
                width: 14px;
                height: 14px;
            }

            .error-box {
                display: flex;
                align-items: center;
                gap: 10px;
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 8px;
                padding: 12px 14px;
                margin-bottom: 18px;
                color: #991b1b;
                font-size: 0.85rem;
                font-weight: 500;
                animation: slideDown 0.3s ease;
            }

            .error-box svg {
                flex-shrink: 0;
                width: 18px;
                height: 18px;
                color: #dc2626;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-8px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .password-wrapper {
                position: relative;
                margin-bottom: 0;
            }

            .password-wrapper input {
                padding-right: 42px !important;
            }

            .toggle-password {
                position: absolute;
                right: 14px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #9ca3af;
                transition:
                    color 0.2s ease,
                    transform 0.2s ease;
                display: flex;
                align-items: center;
            }

            .toggle-password svg {
                width: 18px;
                height: 18px;
                stroke-width: 1.8;
            }

            .toggle-password:hover {
                color: #1a6b3c;
                transform: translateY(-50%) scale(1.1);
            }

            .btn-login {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                cursor: pointer;
                transition:
                    opacity 0.2s ease,
                    transform 0.1s ease;
            }

            .btn-login:active {
                transform: scale(0.97);
            }

            .btn-login:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            .btn-login .spinner {
                display: none;
                width: 18px;
                height: 18px;
                border: 2.5px solid rgba(255, 255, 255, 0.35);
                border-top-color: #fff;
                border-radius: 50%;
                animation: spin 0.55s linear infinite;
            }

            .btn-login.loading .spinner {
                display: inline-block;
            }

            .btn-login.loading .btn-text {
                opacity: 0.7;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            /* ─── Labels ─── */
            .input-group label {
                display: block;
                margin-bottom: 6px;
                font-weight: 600;
                color: #1f2937;
                font-size: 0.9rem;
            }
        </style>
    </head>

    <body class="login-page">
        <div class="login-container">
            <div class="logo-section">
                <img src="{{ asset('img/ehr-logo.png') }}" alt="ehr Logo" class="logo" />
            </div>

            <div class="form-section">
                <p id="upper-line"></p>
                <div id="form-container">
                    <p class="role"><strong>LOG IN</strong></p>

                    @if (session('error'))
                        <div class="error-box">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('login.authenticate') }}" method="POST" id="loginForm" novalidate>
                        @csrf

                        <div class="input-group">
                            <label for="username">Username</label>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                placeholder="Enter your username"
                                class="{{ $errors->has('username') ? 'input-error' : '' }} h-[50px]"
                                value="{{ old('username') }}"
                                autocomplete="username"
                            />

                            <div
                                class="validation-error {{ $errors->has('username') ? 'visible' : '' }}"
                                id="username-error"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="15" y1="9" x2="9" y2="15" />
                                    <line x1="9" y1="9" x2="15" y2="15" />
                                </svg>
                                <span id="username-error-text">
                                    @error('username')
                                        {{ $message }}
                                    @else
                                            The username field is required.
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password">Password</label>
                            <div class="password-wrapper">
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    placeholder="Enter your password"
                                    class="{{ $errors->has('password') ? 'input-error' : '' }} h-[50px]"
                                    autocomplete="current-password"
                                />
                                <span
                                    id="togglePassword"
                                    class="toggle-password"
                                    onclick="togglePassword()"
                                    role="button"
                                    aria-label="Toggle password visibility"
                                >
                                    <i data-lucide="eye-off" class="eye-icon"></i>
                                </span>
                            </div>

                            <div
                                class="validation-error {{ $errors->has('password') ? 'visible' : '' }}"
                                id="password-error"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="15" y1="9" x2="9" y2="15" />
                                    <line x1="9" y1="9" x2="15" y2="15" />
                                </svg>
                                <span id="password-error-text">
                                    @error('password')
                                        {{ $message }}
                                    @else
                                            The password field is required.
                                    @enderror
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn-login mt-6" id="loginBtn">
                            <span class="spinner"></span>
                            <span class="btn-text">Sign in</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if (session('sweetalert'))
            <script>
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
                                timer: opts.timer || 2000,
                            });
                        }
                    }, 100);
                });
            </script>
        @endif

        <script>
            lucide.createIcons();

            const eyeOffPath =
                'M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24';
            const eyePath = 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z';
            const eyeCircle = true;

            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const toggleSpan = document.getElementById('togglePassword');
                const svg = toggleSpan.querySelector('svg');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';

                    svg.innerHTML = '<circle cx="12" cy="12" r="3"></circle><path d="' + eyePath + '"></path>';
                } else {
                    passwordInput.type = 'password';

                    svg.innerHTML = '<path d="' + eyeOffPath + '"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
                }
                passwordInput.focus();
            }

            function loginMarkError(input, errorDiv, message) {
                input.classList.add('input-error');
                input.classList.remove('input-valid');
                errorDiv.querySelector('span').textContent = message;
                errorDiv.classList.add('visible');
            }

            function loginClearError(input, errorDiv) {
                input.classList.remove('input-error');
                errorDiv.classList.remove('visible');
            }

            function loginMarkValid(input) {
                input.classList.remove('input-error');
                input.classList.add('input-valid');
            }

            const usernameInput = document.getElementById('username');
            const usernameError = document.getElementById('username-error');

            usernameInput.addEventListener('blur', function () {
                if (this.value.trim() === '') {
                    loginMarkError(this, usernameError, 'The username field is required.');
                } else {
                    loginClearError(this, usernameError);
                    loginMarkValid(this);
                }
            });

            usernameInput.addEventListener('input', function () {
                if (usernameError.classList.contains('visible') && this.value.trim() !== '') {
                    loginClearError(this, usernameError);
                    loginMarkValid(this);
                }
                if (this.value.trim() === '') {
                    this.classList.remove('input-valid');
                }
            });

            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('password-error');

            passwordInput.addEventListener('blur', function () {
                if (this.value === '') {
                    loginMarkError(this, passwordError, 'The password field is required.');
                } else {
                    loginClearError(this, passwordError);
                    loginMarkValid(this);
                }
            });

            passwordInput.addEventListener('input', function () {
                if (passwordError.classList.contains('visible') && this.value !== '') {
                    loginClearError(this, passwordError);
                    loginMarkValid(this);
                }
                if (this.value === '') {
                    this.classList.remove('input-valid');
                }
            });

            document.getElementById('loginForm').addEventListener('submit', function (e) {
                let valid = true;

                if (usernameInput.value.trim() === '') {
                    loginMarkError(usernameInput, usernameError, 'The username field is required.');
                    valid = false;
                } else {
                    loginClearError(usernameInput, usernameError);
                    loginMarkValid(usernameInput);
                }

                if (passwordInput.value === '') {
                    loginMarkError(passwordInput, passwordError, 'The password field is required.');
                    valid = false;
                } else {
                    loginClearError(passwordInput, passwordError);
                    loginMarkValid(passwordInput);
                }

                if (!valid) {
                    e.preventDefault();
                    if (usernameInput.classList.contains('input-error')) usernameInput.focus();
                    else if (passwordInput.classList.contains('input-error')) passwordInput.focus();
                    return;
                }

                const btn = document.getElementById('loginBtn');
                btn.classList.add('loading');
                btn.disabled = true;
            });

            (function () {
                if (usernameInput.classList.contains('input-error')) {
                    usernameError.classList.add('visible');
                }
                if (passwordInput.classList.contains('input-error')) {
                    passwordError.classList.add('visible');
                }
            })();
        </script>
    </body>
</html>
