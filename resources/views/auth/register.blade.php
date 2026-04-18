<!DOCTYPE html>
<html>
<head>
    <title>Daftar Akun</title>
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
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
        </select>
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