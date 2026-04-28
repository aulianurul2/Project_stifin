<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun</title>
    <style>
        /* Gaya sederhana untuk menyatukan +62 dengan input */
        .phone-input-container {
            display: flex;
            align-items: center;
        }
        .prefix {
            background: #e2e2e2;
            padding: 2px 8px;
            border: 1px solid #a9a9a9;
            border-right: none;
            height: 17px; /* Menyesuaikan tinggi input default browser */
            display: flex;
            align-items: center;
            font-size: 13px;
        }
        .phone-field {
            border: 1px solid #a9a9a9;
            padding: 2px;
        }
    </style>
</head>
<body>
    <h2>Form Pendaftaran</h2>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.process') }}" method="POST">
    @csrf
    <div>
        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama" value="{{ old('nama') }}" required>
    </div>

    <div>
        <label>Username:</label><br>
        <input type="text" name="username" value="{{ old('username') }}" required>
    </div>

    <div>
        <label>Password:</label><br>
        <input type="password" name="password" required>
    </div>

    <div>
        <label>Tanggal Lahir:</label><br>
        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
    </div>

    <div>
        <label>Jenis Kelamin:</label><br>
        <select name="jenis_kelamin" required>
            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>

    <div>
        <label>Nomor HP (WhatsApp):</label><br>
        <div class="phone-input-container">
            <span class="prefix">+62</span>
            <input type="number" 
                   name="no_hp" 
                   class="phone-field"
                   placeholder="812345678" 
                   value="{{ old('no_hp') }}" 
                   oninput="if(this.value.startsWith('0')) this.value = this.value.substring(1);"
                   required>
        </div>
    </div>

    <div>
        <label>Alamat:</label><br>
        <textarea name="alamat" required>{{ old('alamat') }}</textarea>
    </div>

    <br>
    <button type="submit">Daftar Sekarang</button>
</form>
    
    <p>Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
</body>
</html>