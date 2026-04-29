<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        /* CSS Tambahan untuk merapikan double text */
        .login-card { color: #ffffff; }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        /* Menghilangkan label statis jika ingin menggunakan placeholder saja */
        /* Atau mengatur posisi label agar tidak menumpuk */
        .form-label {
            display: block;
            color: #cccccc;
            font-size: 0.85rem;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .input-wrapper input, 
        .input-wrapper textarea, 
        .input-wrapper select { 
            width: 100%;
            color: #ffffff !important; 
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 0;
            outline: none;
            font-size: 1rem;
        }

        .input-wrapper input:focus {
            border-bottom: 1px solid #3b82f6;
        }

        /* Merapikan tampilan select */
        select option {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        /* Container nomor HP */
        .phone-container {
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .prefix {
            color: #3b82f6;
            font-weight: bold;
            padding-right: 10px;
        }

        .phone-field {
            flex: 1;
            border: none !important;
            padding: 10px 0 !important;
        }

        ::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }
    </style>
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
                <div style="background: rgba(255, 0, 0, 0.1); color: #ff6b6b; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 0.8rem;">
                    <ul style="list-style: none; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="login-form" action="{{ route('register.process') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-wrapper">
                        <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Masukkan nama lengkap">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-wrapper">
                        <input type="text" name="username" value="{{ old('username') }}" required placeholder="Buat username">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" required placeholder="Minimal 6 karakter">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Lahir</label>
                    <div class="input-wrapper">
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required style="color-scheme: dark;">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Kelamin</label>
                    <div class="input-wrapper">
                        <select name="jenis_kelamin" required>
                            <option value="" disabled selected>Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor HP (WhatsApp)</label>
                    <div class="phone-container">
                        <span class="prefix">+62</span>
                        <input type="text" name="no_hp" class="phone-field" required placeholder="81234567890" 
                               inputmode="numeric" value="{{ old('no_hp') }}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '');">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Lengkap</label>
                    <div class="input-wrapper">
                        <textarea name="alamat" required placeholder="Masukkan alamat..." rows="1" style="background: transparent; border: none; border-bottom: 1px solid rgba(255,255,255,0.2); width: 100%; color: white; outline: none;">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="login-btn btn">Sign Up</button>

            </form>

            <div class="signup-link">
                <p>Sudah Punya Akun? <a href="{{ route('login') }}">Login di sini</a></p>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>