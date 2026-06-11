<section class="hero panel hero-grid">
    <div class="hero-copy">
        <span class="eyebrow">Homepage</span>
        <h1>Kelola dan akses data merchandise anime melalui API lokal dengan API KEY, dokumentasi, dan client demo.</h1>
        <p>
            Project ini memenuhi kebutuhan UAS: register dan login sederhana, pembuatan API KEY,
            CRUD data produk, dokumentasi penggunaan, contoh implementasi di Postman, dan website client.
        </p>
        <div class="cta-row">
            <a class="button button-primary" href="/register">Mulai Daftar</a>
            <a class="button button-secondary" href="/docs">Lihat Dokumentasi</a>
            <a class="button button-ghost" href="/client">Coba Client</a>
        </div>
        <div class="mini-metrics">
            <div>
                <strong>CRUD</strong>
                <span>View, Create, Update, Delete</span>
            </div>
            <div>
                <strong>API KEY</strong>
                <span>Akses endpoint JSON</span>
            </div>
            <div>
                <strong>Client</strong>
                <span>Website pemakai API</span>
            </div>
        </div>
    </div>

    <aside class="hero-panel">
        <h2>Fitur inti</h2>
        <ul class="feature-list">
            <li>Login dan daftar akun untuk request / create API KEY.</li>
            <li>API memvalidasi key dari header X-API-KEY atau Authorization Bearer.</li>
            <li>Data produk dapat dilihat, dibuat, diubah, dan dihapus lewat endpoint JSON.</li>
            <li>Client website memakai fetch untuk memanggil API yang sama.</li>
        </ul>
        <div class="code-sample">
            <span>Contoh endpoint</span>
            <code><?= e(site_url('/api/products')) ?></code>
        </div>
    </aside>
</section>

<section class="section-stack">
    <span class="eyebrow">Alur kerja</span>
    <div class="section-heading">    
        <h2>Cara menggunakan project ini</h2>
    </div>
    <div class="card-grid three-up">
        <article class="panel small-card">
            <h3>1. Daftar akun</h3>
            <p>Pengguna mendaftar, lalu sistem otomatis membuat API KEY pertama.</p>
        </article>
        <article class="panel small-card">
            <h3>2. Pakai Postman</h3>
            <p>Import collection Postman, isi base URL dan API KEY, lalu jalankan request CRUD.</p>
        </article>
        <article class="panel small-card">
            <h3>3. Client website</h3>
            <p>Halaman client mengonsumsi endpoint API untuk menampilkan dan mengelola data.</p>
        </article>
    </div>
</section>
