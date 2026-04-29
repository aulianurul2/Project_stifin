<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">⚡</div>
                <h2>Sign In STIFIn</h2>
                <p>Access your account</p>
            </div>

            @if($errors->has('login'))
                <div style="color: #ff6b6b; margin-bottom: 15px; font-size: 0.9rem; text-align: center;">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <form class="login-form" id="loginForm" action="{{ route('login') }}" method="POST" novalidate>
                @csrf
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autocomplete="username">
                        <label for="username">Username</label>
                        <span class="input-line"></span>
                    </div>
                    <span class="error-message" id="usernameError"></span>
                </div>

                <div class="form-group">
                    <div class="input-wrapper password-wrapper">
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                            <span class="toggle-icon"></span>
                        </button>
                        <span class="input-line"></span>
                    </div>
                    <span class="error-message" id="passwordError"></span>
                </div>

                <div class="form-options">
                    <div class="remember-wrapper">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember" class="checkbox-label">
                            <span class="custom-checkbox"></span>
                            Keep me signed in
                        </label>
                    </div>
                    <a href="#" class="forgot-password">Lupa password?</a>
                </div>

                <button type="submit" class="login-btn btn">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-loader"></span>
                    <span class="btn-glow"></span>
                </button>

            </form>

            <div class="signup-link">
                <p>Belum Punya Akun? <a href="{{ route('register') }}">Buat Baru</a></p>
            </div>

            <div class="success-message" id="successMessage">
                <div class="success-icon">✓</div>
                <h3>Selamat Datang!</h3>
                <p>Redirecting to your dashboard...</p>
            </div>
        </div>

        <div class="background-effects">
            <div class="glow-orb glow-orb-1"></div>
            <div class="glow-orb glow-orb-2"></div>
            <div class="glow-orb glow-orb-3"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');

            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function(e) {
                    // Mencegah reload halaman
                    e.preventDefault();
                    
                    // Ubah tipe input
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Tambahkan class active jika ingin merubah icon via CSS
                    this.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>