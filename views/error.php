<section class="panel error-panel">
    <span class="eyebrow">Error <?= e($code ?? 500) ?></span>
    <h1><?= e($message ?? 'Terjadi kesalahan.') ?></h1>
    <?php if (!empty($detail)): ?>
        <p class="muted"><?= e($detail) ?></p>
    <?php endif; ?>
    <div class="cta-row">
        <a class="button button-primary" href="/">Kembali ke Beranda</a>
        <a class="button button-secondary" href="/docs">Buka Dokumentasi</a>
    </div>
</section>
