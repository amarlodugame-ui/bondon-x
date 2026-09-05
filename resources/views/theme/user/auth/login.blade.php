<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: #fff;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 65px;
            height: 65px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: #fff;
            font-size: 28px;
            font-weight: bold;
        }

        .logo h1 {
            color: #111827;
            font-size: 27px;
            margin-bottom: 7px;
        }

        .logo p {
            color: #6b7280;
            font-size: 14px;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            height: 48px;
            padding: 0 14px 0 42px;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            outline: none;
            font-size: 15px;
            color: #111827;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            color: #6b7280;
            font-size: 13px;
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #6b7280;
            cursor: pointer;
        }

        .remember input {
            accent-color: #2563eb;
        }

        .forgot-password {
            color: #2563eb;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 9px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
        }

        .register-text {
            text-align: center;
            margin-top: 25px;
            color: #6b7280;
            font-size: 14px;
        }

        .register-text a {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .register-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 25px 20px;
            }

            .logo h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<div class="login-container">

    <div class="login-card">

        {{-- Logo / Header --}}
        <div class="logo">
            <div class="logo-icon">
                U
            </div>

            <h1>Welcome Back!</h1>
            <p>Login to your account</p>
        </div>


        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- Session Error --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif


        {{-- Login Form --}}
        <form method="POST" action="{{ route('user.login') }}">

            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Email Address</label>

                <div class="input-wrapper">
                    <span class="input-icon">✉</span>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        required
                        autofocus
                    >
                </div>
            </div>


            {{-- Password --}}
            <div class="form-group">
                <label for="password">Password</label>

                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword()"
                    >
                        Show
                    </button>
                </div>
            </div>


            {{-- Remember + Forgot --}}
            <div class="login-options">

                <label class="remember">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                    >

                    Remember me
                </label>

                {{-- যদি forgot password route থাকে --}}
                {{-- <a href="{{ route('user.password.request') }}"
                   class="forgot-password">
                    Forgot password?
                </a> --}}

            </div>


            {{-- Login Button --}}
            <button type="submit" class="btn-login">
                Login
            </button>

        </form>


        {{-- Register --}}
        <div class="register-text">
            Don't have an account?
            <a href="{{ route('user.register') }}">
                Create Account
            </a>
        </div>

    </div>

</div>


<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const button = document.querySelector('.password-toggle');

        if (password.type === 'password') {
            password.type = 'text';
            button.textContent = 'Hide';
        } else {
            password.type = 'password';
            button.textContent = 'Show';
        }
    }
</script>

</body>
</html>
