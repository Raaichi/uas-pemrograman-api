<?php
$siteTitle = $title ?? ($app['app_name'] ?? 'Merchandise API');
$navUser = $currentUser ?? null;
$pageClass = $pageClass ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($siteTitle) ?> - <?= e($app['app_name'] ?? 'Merchandise API') ?></title>
    <link rel="stylesheet" href="<?= e(site_url('assets/style.css')) ?>">
    <script src="<?= e(site_url('assets/app.js')) ?>" defer></script>
</head>

<body class="<?= e($pageClass) ?>">
    <div class="bg-grid"></div>

    <div class="shell">
        <header class="topbar">
            <a class="brand" href="/">
                <span class="brand-mark">MA</span>
                <span>
                    <strong><?= e($app['app_name'] ?? 'Merchandise API') ?></strong>
                    <small>Anime Merchandise Management API</small>
                </span>
            </a>

            <nav class="nav-links">
                <a href="/">Homepage</a>
                <a href="/docs">Documentation</a>
                <a href="/client">Client</a>
                <?php if ($navUser): ?>
                    <a href="/dashboard">Dashboard</a>
                    <a class="nav-pill" href="/logout">Logout</a>
                <?php else: ?>
                    <a href="/login">Login</a>
                    <a class="nav-pill" href="/register">Register</a>
                <?php endif; ?>
            </nav>
        </header>

        <main class="page-main">
            <?php if (!empty($flash)): ?>
                <div class="alert alert-<?= e($flash['type'] ?? 'info') ?>">
                    <?= e($flash['message'] ?? '') ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>

        <footer class="footer">
            <p>Merchandise API © 2026 • Anime Merchandise Data Service</p>
        </footer>
    </div>
</body>

</html>