<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin - STIFIn</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* Pengaturan ukuran logo yang diperbesar dan dioptimalkan warna aslinya */
        .logo-container-img {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px; /* Jarak bawah seimbang dengan logo besar */
        }
        .logo-img {
            width: 240px; /* Ukuran diperbesar maksimal agar dominan */
            max-width: 90%; /* Responsif untuk layar HP */
            height: auto;
            object-fit: contain;
            
            /* PERBAIKAN: Kecerahan diatur ke 2.0 agar warna asli menyala tajam */
            filter: brightness(2.0) contrast(1.1);
        }

        /* FORCE REPAIR: Memastikan ikon bawaan template muncul 
           dan merespons class .active dari style.css dengan benar
        */
        .password-toggle {
            display: flex !important;
            align-items: center;
            justify-content: center;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            z-index: 10;
            width: 24px;
            height: 24px;
        }

        .toggle-icon {
            display: inline-block !important;
            position: relative;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container-img">
                    <img src="{{ asset('assets/img/kaiadmin/logo_light.png') }}" alt="Logo STIFIn" class="logo-img">
                </div>
                <h2>Login Admin</h2>
                <p>Masuk ke sistem manajemen admin</p>
            </div>

            @if($errors->has('login'))
                <div style="color: #ff6b6b; margin-bottom: 15px; font-size: 0.9rem; text-align: center;">
                    {{ $errors->first('login') }}
                </div>
            @endif
            @if(session('success_reset'))
                <div style="color: #4cd137; margin-bottom: 15px; font-size: 0.9rem; text-align: center; font-weight: bold;">
                    {{ session('success_reset') }}
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
                    <div class="input-wrapper password-wrapper" style="position: relative;">
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                        <label for="password">Password</label>
                        
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="Tampilkan password">
                            <span class="toggle-icon"></span>
                        </button>
                        
                        <span class="input-line"></span>
                    </div>
                    <span class="error-message" id="passwordError"></span>
                </div>

                <div class="form-options" style="justify-content: flex-end;">
                    <a href="{{ route('password.request') }}" class="forgot-password">Lupa password?</a>
                </div>

                <button type="submit" class="login-btn btn">
                    <span class="btn-text">Masuk</span>
                    <span class="btn-loader"></span>
                    <span class="btn-glow"></span>
                </button>

            </form>

            <div class="success-message" id="successMessage">
                <div class="success-icon">✓</div>
                <h3>Selamat Datang!</h3>
                <p>Mengalihkan Anda ke dashboard manajemen...</p>
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
                    e.preventDefault();
                    
                    // 1. Tukar tipe input password/text
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // 2. Trigger class .active ke BUTTON agar memicu perubahan gaya visual ikon di style.css
                    this.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>