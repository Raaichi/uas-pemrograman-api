<section class="panel section-stack client-hero">
    <span class="eyebrow">Website Client</span>
    <h1>Client website yang langsung memakai API.</h1>
    <p>
        Masukkan API KEY, lalu client ini akan memanggil endpoint JSON untuk menampilkan, menambah, mengubah, dan menghapus data produk.
    </p>
    <div class="doc-note">
        Default base URL mengikuti domain ini: <code><?= e(site_url()) ?></code>
    </div>
</section>

<section class="client-layout">
    <div class="panel client-controls">
        <h2>Pengaturan API</h2>
        <label>
            <span>Base URL</span>
            <input id="client-base-url" type="text" value="<?= e(site_url()) ?>">
        </label>
        <label>
            <span>API KEY</span>
            <input id="client-api-key" type="text" placeholder="Tempel API KEY di sini">
        </label>
        <div class="cta-row">
            <button class="button button-primary" id="load-products" type="button">Load Data</button>
            <button class="button button-secondary" id="clear-products" type="button">Clear</button>
        </div>

        <h3>Buat / Update Produk</h3>
        <form id="client-product-form" class="form-stack">
            <input type="hidden" id="client-product-id" value="">
            <label>
                <span>Nama</span>
                <input id="client-name" type="text" required>
            </label>
            <label>
                <span>Deskripsi</span>
                <textarea id="client-description" rows="3"></textarea>
            </label>
            <div class="split-fields">
                <label>
                    <span>Harga</span>
                    <input id="client-price" type="number" min="0" step="0.01" required>
                </label>
                <label>
                    <span>Stok</span>
                    <input id="client-stock" type="number" min="0" step="1" required>
                </label>
            </div>
            <div class="cta-row">
                <button class="button button-primary" type="submit">Simpan ke API</button>
                <button class="button button-ghost" id="reset-client-form" type="button">Reset</button>
            </div>
        </form>

        <div id="client-status" class="doc-note">Status request akan muncul di sini.</div>
    </div>

    <div class="panel client-results">
        <div class="section-heading compact">
            <div>
                <span class="eyebrow">Hasil API</span>
                <h2>Daftar produk dari server</h2>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody id="client-table-body">
                <tr>
                    <td colspan="5">Klik Load Data untuk memanggil API.</td>
                </tr>
                </tbody>
            </table>
        </div>

        <pre id="client-response" class="response-box">Response JSON akan ditampilkan di sini.</pre>
    </div>
</section>

<script src="/assets/client.js" defer></script>
