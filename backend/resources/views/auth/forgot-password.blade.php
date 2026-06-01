<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - STIFIn</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">🔑</div>
                <h2>Reset Password</h2>
                <p>Masukkan username dan password baru Anda</p>
            </div>

            {{-- Menampilkan Error Validasi Global --}}
            @if ($errors->any())
                <div style="color: #ff6b6b; margin-bottom: 15px; font-size: 0.9rem; text-align: center;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="login-form" id="forgotPasswordForm" action="{{ route('password.update') }}" method="POST" novalidate>
                @csrf
                
                {{-- Input Username --}}
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autocomplete="username">
                        <label for="username">Username Anda</label>
                        <span class="input-line"></span>
                    </div>
                </div>

                {{-- Input Password Baru --}}
                <div class="form-group">
                    <div class="input-wrapper password-wrapper">
                        <input type="password" id="password" name="password" required autocomplete="new-password">
                        <label for="password">Password Baru</label>
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                            <span class="toggle-icon"></span>
                        </button>
                        <span class="input-line"></span>
                    </div>
                </div>

                {{-- Input Konfirmasi Password Baru --}}
                <div class="form-group">
                    <div class="input-wrapper password-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <button type="button" class="password-toggle" id="passwordConfirmToggle" aria-label="Toggle password visibility">
                            <span class="toggle-icon"></span>
                        </button>
                        <span class="input-line"></span>
                    </div>
                </div>

                <div class="form-options" style="justify-content: flex-end;">
                    <a href="{{ route('login') }}" class="forgot-password">Kembali ke Sign In</a>
                </div>

                <button type="submit" class="login-btn btn">
                    <span class="btn-text">Perbarui Password</span>
                    <span class="btn-loader"></span>
                    <span class="btn-glow"></span>
                </button>
            </form>
        </div>

        <div class="background-effects">
            <div class="glow-orb glow-orb-1"></div>
            <div class="glow-orb glow-orb-2"></div>
            <div class="glow-orb glow-orb-3"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic Toggle Password Baru
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('active');
                });
            }

            // Logic Toggle Konfirmasi Password Baru
            const confirmInput = document.getElementById('password_confirmation');
            const confirmToggle = document.getElementById('passwordConfirmToggle');
            if (confirmToggle && confirmInput) {
                confirmToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const type = confirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmInput.setAttribute('type', type);
                    this.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>