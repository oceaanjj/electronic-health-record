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
            align-items: center;
            gap: 2px;
            color: #dc2626;
            font-size: 0.82rem;
            font-weight: 500;
            margin-top: 2px;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height 40px 0.3s ease, opacity 0.25s ease, margin 0.3s ease;
        }

        .validation-error.visible {
            max-height: 40px;
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

                @if (session('status'))
                    <div class="flex flex-col items-center text-center animate-slide-down -mt-5">
                        <div class="checkmark-container">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
                                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                            </svg>
                        </div>
                        <div style="margin-top: 5px;">
                            <h3 class="text-xl font-bold text-green-800">Success!</h3>
                            <p class="text-sm font-medium leading-relaxed text-gray-700 mt-1">{{ session('status') }}</p>
                        </div>

                        <div class="mt-6 w-full flex justify-center">
                            <a href="{{ route('login') }}"
                                class="btn-login h-[50px] bg-green-700 hover:bg-green-800 text-white font-bold rounded-lg shadow-sm transition-all flex items-center justify-center gap-2 no-underline w-full max-w-[230px]">
                                <span class="btn-text">Proceed to Login</span>
                            </a>
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

                            @error('email')
                                <div class="validation-error visible">
                                    <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <button type="submit"
                            class="btn-login mt-6 h-[50px] bg-green-700 hover:bg-green-800 text-white font-bold rounded-lg shadow-sm transition-all"
                            id="resetBtn">
                            <span class="spinner"></span>
                            <span class="btn-text">Send Reset Link</span>
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
        const resetForm = document.getElementById('resetForm');
        if (resetForm) {
            resetForm.addEventListener('submit', function () {
                const btn = document.getElementById('resetBtn');
                btn.classList.add('loading');
                btn.disabled = true;
            });
        }
    </script>
</body>

</html>