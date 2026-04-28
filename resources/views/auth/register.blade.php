<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">⚡</div>
                <h2>Sign Up STIFIn</h2>
                <p>Create your account</p>
            </div>

            @if ($errors->any())
                <div style="color: red;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="login-form" id="loginForm" action="{{ route('register.process') }}" method="POST" novalidate>
                @csrf
                <div class="form-group">
                    <div class="input-wrapper">
                        <label>Nama Lengkap:</label><br>
                        <input type="text" name="nama" value="{{ old('nama') }}" required>
                        <span class="input-line"></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <label>Username:</label><br>
                        <input type="text" name="username" value="{{ old('username') }}" required>
                        <span class="input-line"></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper password-wrapper">
                        <label>Password:</label><br>
                        <input type="password" name="password" required>
                        <span class="input-line"></span>
                    </div>
                </div>

<div class="form-group">
    <div class="phone-input-container">
        <span class="input-prefix">+62</span>
        <input type="text"
                name="no_hp"
                id="no_hp"
                class="phone-field"
                required
                placeholder="81234567890"
                inputmode="numeric"
                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '');">
        <label for="no_hp">Nomor HP (WhatsApp)</label>
        <span class="input-line"></span>
    </div>
</div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <textarea name="alamat" id="alamat" required placeholder=" " rows="1"></textarea>
                        <label for="alamat">Alamat Lengkap</label>
                        <span class="input-line"></span>
                    </div>
                </div>

                <button type="submit" class="login-btn btn">
                    <span class="btn-text">Sign Up</span>
                    <span class="btn-loader"></span>
                    <span class="btn-glow"></span>
                </button>

            </form>

            <div class="signup-link">
                <p>Sudah Punya Akun? <a href="{{ route('login') }}">Login di sini</a></p>
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

    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>
