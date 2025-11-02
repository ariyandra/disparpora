<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        /* Additional Login Specific Styles */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.8s ease-out;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header .logo {
            justify-content: center;
            margin-bottom: 16px;
            font-size: 24px;
        }

        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            color: #666;
            font-size: 14px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .form-input {
            padding: 14px 16px;
            border: 2px solid rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #667eea;
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input::placeholder {
            color: #999;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 16px;
        }

        .input-icon .form-input {
            padding-left: 48px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #666;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #667eea;
        }

        .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
            color: #999;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(0, 0, 0, 0.1);
        }

        .social-login {
            display: flex;
            gap: 12px;
        }

        .social-btn {
            flex: 1;
            padding: 12px;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            border-color: #667eea;
            background: rgba(255, 255, 255, 1);
            transform: translateY(-1px);
        }

        .social-btn.google {
            color: #db4437;
        }

        .social-btn.facebook {
            color: #4267b2;
        }

        .signup-link {
            text-align: center;
            margin-top: 24px;
            color: #666;
            font-size: 14px;
        }

        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            color: #764ba2;
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }

        .success-message {
            background: #d1fae5;
            color: #059669;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #a7f3d0;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
                margin: 0 16px;
            }

            .login-title {
                font-size: 24px;
            }

            .social-login {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Background Elements -->
        <div class="bg-elements">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
            <div class="circle circle-3"></div>
        </div>

        <!-- Login Container -->
        <div class="login-container">
            <div class="login-card">
                <!-- Header -->
                <div class="login-header">
                    <h1 class="login-title">Selamat Datang Kembali</h1>
                    <p class="login-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <!-- Error/Success Messages -->
                @if($errors->any())
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Login Form -->
                <form class="login-form" method="POST" action="{{ route('login.admin') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input 
                                type="text" 
                                id="email" 
                                name="email" 
                                class="form-input" 
                                placeholder="Masukkan email kamu"
                                value="{{ old('email') }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="Masukkan password mu!"
                                required
                            >
                        </div>
                    </div>

                    <p class="login-subtitle">Masuk sebagai <a href="{{ route('login.pelatih') }}">Pelatih</a> / <a href="{{ route('login.atlet') }}">Atlet</a> </p>

                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add focus effects to form inputs
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Add loading state to login button
            const loginForm = document.querySelector('.login-form');
            const loginBtn = document.querySelector('.login-btn');
            
            loginForm.addEventListener('submit', function() {
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
                loginBtn.disabled = true;
            });
        });

        // Social login functions (placeholder)
        function loginWithGoogle() {
            // Implement Google OAuth login
            console.log('Login with Google');
        }

        function loginWithFacebook() {
            // Implement Facebook OAuth login
            console.log('Login with Facebook');
        }

        // Add some visual feedback for form validation
        function validateForm() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                showMessage('Please fill in all fields', 'error');
                return false;
            }
            
            return true;
        }

        function showMessage(message, type) {
            const existingMessage = document.querySelector('.error-message, .success-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = type === 'error' ? 'error-message' : 'success-message';
            messageDiv.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation' : 'check'}-circle"></i> ${message}`;
            
            const form = document.querySelector('.login-form');
            form.parentNode.insertBefore(messageDiv, form);
        }
    </script>
</body>
</html>