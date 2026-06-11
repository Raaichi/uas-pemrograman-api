<section class="auth-layout">
    <div class="panel auth-copy">
        <span class="eyebrow">Register</span>
        <h1>Buat akun sederhana untuk request dan generate API KEY.</h1>
        <p>
            Form registrasi ini menyimpan akun ke database lokal dan langsung menyiapkan API KEY agar bisa dipakai di Postman atau client website.
        </p>
    </div>

    <form class="panel auth-form" method="post" action="/register">
        <?= csrf_field() ?>
        <label>
            <span>Nama</span>
            <input type="text" name="name" placeholder="Nama lengkap" required>
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" placeholder="nama@contoh.com" required>
        </label>
        <label>
            <span>Password</span>
            <input type="password" name="password" placeholder="Minimal 6 karakter" required>
        </label>
        <button class="button button-primary" type="submit">Buat Akun</button>
        <p class="form-note">Sudah punya akun? <a href="/login">Login di sini</a>.</p>
    </form>
</section>
