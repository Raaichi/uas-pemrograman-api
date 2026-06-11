<section class="dashboard-grid">
    <div class="panel dashboard-summary">
        <span class="eyebrow">Dashboard</span>
        <h1>Selamat datang, <?= e($currentUser['name'] ?? 'User') ?>.</h1>
        <p>Email: <?= e($currentUser['email'] ?? '-') ?></p>

        <div class="api-key-box">
            <div>
                <span class="muted">API KEY : </span>
                <code id="api-key-value"><?= e($currentUser['api_key_plain'] ?? 'API key belum tersimpan untuk akun lama.') ?></code>
                <?php if (!empty($currentUser['api_key_plain'])): ?>
                    <button class="button button-ghost copy-button" type="button" data-copy="#api-key-value">Copy</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="panel">
        <span class="eyebrow">Tambah Data</span>
        <h2>Input produk baru</h2>
        <form class="form-stack" method="post" action="/dashboard/products/create">
            <?= csrf_field() ?>
            <label>
                <span>Nama produk</span>
                <input type="text" name="name" placeholder="Nama produk" required>
            </label>
            <label>
                <span>Deskripsi</span>
                <textarea name="description" rows="4" placeholder="Deskripsi singkat"></textarea>
            </label>
            <div class="split-fields">
                <label>
                    <span>Harga</span>
                    <input type="number" name="price" min="0" step="0.01" placeholder="75000" required>
                </label>
                <label>
                    <span>Stok</span>
                    <input type="number" name="stock" min="0" step="1" placeholder="10" required>
                </label>
            </div>
            <button class="button button-primary" type="submit">Simpan Data</button>
        </form>
    </div>
</section>

<section class="panel section-stack">
    <div class="section-heading compact">
        <div>
            <span class="eyebrow">Data Produk</span>
            <h2>View, Update, Delete</h2>
        </div>
        <a class="button button-ghost" href="/docs">Lihat dokumentasi endpoint</a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Update</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="6">Belum ada data produk.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= e($product['id']) ?></td>
                        <td>
                            <strong><?= e($product['name']) ?></strong>
                            <small><?= e($product['description'] ?: '-') ?></small>
                        </td>
                        <td>Rp <?= number_format((float) $product['price'], 2, ',', '.') ?></td>
                        <td><?= e($product['stock']) ?></td>
                        <td><?= e($product['updated_at']) ?></td>
                        <td class="table-actions">
                            <a class="button button-secondary button-small" href="/dashboard/products/<?= e($product['id']) ?>/edit">Edit</a>
                            <form method="post" action="/dashboard/products/<?= e($product['id']) ?>/delete" onsubmit="return confirm('Hapus data ini?');">
                                <?= csrf_field() ?>
                                <button class="button button-danger button-small" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
