<section class="panel section-stack">
    <span class="eyebrow">Documentation</span>
    <h1>Petunjuk penggunaan API</h1>
    <p>
        Semua endpoint API menerima API KEY melalui header <strong>X-API-KEY</strong> atau <strong>Authorization: Bearer &lt;API_KEY&gt;</strong>.
    </p>

    <div class="doc-note">
        Base URL lokal: <code><?= e(site_url()) ?></code>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Endpoint</th>
                    <th>Fungsi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>GET</td>
                    <td><code>/api/me</code></td>
                    <td>Cek validitas API KEY dan informasi user.</td>
                </tr>
                <tr>
                    <td>GET</td>
                    <td><code>/api/products</code></td>
                    <td>View data produk.</td>
                </tr>
                <tr>
                    <td>POST</td>
                    <td><code>/api/products</code></td>
                    <td>Create / simpan data produk.</td>
                </tr>
                <tr>
                    <td>GET</td>
                    <td><code>/api/products/{id}</code></td>
                    <td>View detail data satu produk.</td>
                </tr>
                <tr>
                    <td>PUT/PATCH</td>
                    <td><code>/api/products/{id}</code></td>
                    <td>Update data produk.</td>
                </tr>
                <tr>
                    <td>DELETE</td>
                    <td><code>/api/products/{id}</code></td>
                    <td>Delete data produk.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="doc-columns">
        <article class="panel nested-panel">
            <h2>Headers</h2>
            <pre><code>X-API-KEY: merch_xxxxxxxxxxxxxxxxxx
Authorization: Bearer merch_xxxxxxxxxxxxxxxxxx
Content-Type: application/json</code></pre>
        </article>
        <article class="panel nested-panel">
            <h2>Contoh body JSON</h2>
            <pre><code>{
  "name": "Produk Baru",
  "description": "Deskripsi singkat",
  "price": 125000,
  "stock": 20
}</code></pre>
        </article>
    </div>

    <div class="doc-note">
        Import file Postman collection dari folder <code>postman/</code> lalu isi variabel <code>apiKey</code> dengan key dari dashboard.
    </div>
</section>