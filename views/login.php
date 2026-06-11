<section class="auth-layout">
    <div class="panel auth-copy">
        <span class="eyebrow">Login</span>
        <h1>Login untuk mengelola API KEY dan data produk.</h1>
        <p>
            Setelah login, Anda bisa melihat API KEY penuh, lalu memakainya di Postman atau client website untuk mengakses data CRUD.
        </p>
    </div>

    <form class="panel auth-form" method="post" action="/login">
        <?= csrf_field() ?>
        <label>
            <span>Email</span>
            <input type="email" name="email" placeholder="nama@contoh.com" required>
        </label>
        <label>
            <span>Password</span>
            <input type="password" name="password" placeholder="Password akun" required>
        </label>
        <button class="button button-primary" type="submit">Login</button>
        <p class="form-note">Belum punya akun? <a href="/register">Daftar sekarang</a>.</p>
    </form>
</section>
