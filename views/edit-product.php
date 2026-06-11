<section class="panel section-stack">
    <span class="eyebrow">Edit Produk</span>
    <h1>Perbarui data produk #<?= e($product['id']) ?></h1>
    <p>Form ini mengubah data pada database lokal dan akan tercermin juga di endpoint API.</p>

    <form class="form-stack" method="post" action="/dashboard/products/<?= e($product['id']) ?>/update">
        <?= csrf_field() ?>
        <label>
            <span>Nama produk</span>
            <input type="text" name="name" value="<?= e($product['name']) ?>" required>
        </label>
        <label>
            <span>Deskripsi</span>
            <textarea name="description" rows="4"><?= e($product['description'] ?? '') ?></textarea>
        </label>
        <div class="split-fields">
            <label>
                <span>Harga</span>
                <input type="number" name="price" min="0" step="0.01" value="<?= e($product['price']) ?>" required>
            </label>
            <label>
                <span>Stok</span>
                <input type="number" name="stock" min="0" step="1" value="<?= e($product['stock']) ?>" required>
            </label>
        </div>
        <div class="cta-row">
            <button class="button button-primary" type="submit">Simpan Perubahan</button>
            <a class="button button-secondary" href="/dashboard">Kembali</a>
        </div>
    </form>
</section>
