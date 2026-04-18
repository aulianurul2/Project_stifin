<form action="/login" method="POST">
    @csrf
    <h2>Login STIFIN</h2>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <p>Belum punya akun? <a href="/register">Daftar di sini</a></p>
</form>