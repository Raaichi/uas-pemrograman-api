<?php

declare(strict_types=1);

require __DIR__ . '/src/app.php';

start_session();

try {
    $path = request_path();
    $method = request_method();

    if ($path === '/') {
        render('home', [
            'title' => 'Beranda',
            'pageClass' => 'page-home',
            'currentUser' => current_user(),
        ]);
    }

    if ($path === '/docs') {
        render('docs', [
            'title' => 'Dokumentasi API',
            'pageClass' => 'page-docs',
            'currentUser' => current_user(),
        ]);
    }

    if ($path === '/client') {
        render('client', [
            'title' => 'Client Website',
            'pageClass' => 'page-client',
            'currentUser' => current_user(),
        ]);
    }

    if ($path === '/register') {
        if ($method === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash_set('error', 'Token keamanan tidak valid.');
                redirect_to('/register');
            }

            $name = trim((string) ($_POST['name'] ?? ''));
            $email = normalize_email((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');

            if ($name === '' || $email === '' || $password === '') {
                flash_set('error', 'Nama, email, dan password wajib diisi.');
                redirect_to('/register');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash_set('error', 'Format email tidak valid.');
                redirect_to('/register');
            }

            if (find_user_by_email($email)) {
                flash_set('error', 'Email sudah terdaftar. Silakan login atau gunakan email lain.');
                redirect_to('/register');
            }

            if (strlen($password) < 6) {
                flash_set('error', 'Password minimal 6 karakter.');
                redirect_to('/register');
            }

            $created = create_user($name, $email, $password);
            login_user($created['user']);
            flash_set('success', 'Akun berhasil dibuat dan API KEY pertama sudah aktif.');
            redirect_to('/dashboard');
        }

        render('register', [
            'title' => 'Daftar Akun',
            'pageClass' => 'page-auth',
            'currentUser' => current_user(),
        ]);
    }

    if ($path === '/login') {
        if ($method === 'POST') {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash_set('error', 'Token keamanan tidak valid.');
                redirect_to('/login');
            }

            $email = normalize_email((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $user = authenticate_user($email, $password);

            if (!$user) {
                flash_set('error', 'Email atau password salah.');
                redirect_to('/login');
            }

            login_user($user);
            flash_set('success', 'Login berhasil.');
            redirect_to('/dashboard');
        }

        render('login', [
            'title' => 'Login',
            'pageClass' => 'page-auth',
            'currentUser' => current_user(),
        ]);
    }

    if ($path === '/logout') {
        logout_user();
        flash_set('success', 'Anda sudah logout.');
        redirect_to('/');
    }

    if ($path === '/dashboard') {
        $user = require_login();
        render('dashboard', [
            'title' => 'Dashboard',
            'pageClass' => 'page-dashboard',
            'currentUser' => $user,
            'products' => list_products_for_user((int) $user['id']),
        ]);
    }

    if ($path === '/dashboard/products/create' && $method === 'POST') {
        $user = require_login();

        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            flash_set('error', 'Token keamanan tidak valid.');
            redirect_to('/dashboard');
        }

        $normalized = normalize_product_payload($_POST);

        if ($normalized['errors']) {
            flash_set('error', implode(' ', $normalized['errors']));
            redirect_to('/dashboard');
        }

        create_product_record($normalized['data'], (int) $user['id']);
        flash_set('success', 'Produk berhasil disimpan.');
        redirect_to('/dashboard');
    }

    if (preg_match('#^/dashboard/products/(\d+)/edit$#', $path, $matches)) {
        $user = require_login();
        $product = get_product_for_user((int) $user['id'], (int) $matches[1]);

        if (!$product) {
            flash_set('error', 'Data produk tidak ditemukan.');
            redirect_to('/dashboard');
        }

        render('edit-product', [
            'title' => 'Edit Produk',
            'pageClass' => 'page-dashboard',
            'currentUser' => $user,
            'product' => $product,
        ]);
    }

    if (preg_match('#^/dashboard/products/(\d+)/update$#', $path, $matches) && $method === 'POST') {
        $user = require_login();

        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            flash_set('error', 'Token keamanan tidak valid.');
            redirect_to('/dashboard');
        }

        $productId = (int) $matches[1];
        $product = get_product_for_user((int) $user['id'], $productId);

        if (!$product) {
            flash_set('error', 'Data produk tidak ditemukan.');
            redirect_to('/dashboard');
        }

        $merged = array_merge($product, $_POST);
        $normalized = normalize_product_payload($merged);

        if ($normalized['errors']) {
            flash_set('error', implode(' ', $normalized['errors']));
            redirect_to('/dashboard/products/' . $productId . '/edit');
        }

        update_product_record((int) $user['id'], $productId, $normalized['data']);
        flash_set('success', 'Produk berhasil diperbarui.');
        redirect_to('/dashboard');
    }

    if (preg_match('#^/dashboard/products/(\d+)/delete$#', $path, $matches) && $method === 'POST') {
        $user = require_login();

        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            flash_set('error', 'Token keamanan tidak valid.');
            redirect_to('/dashboard');
        }

        $productId = (int) $matches[1];

        if (!delete_product_record((int) $user['id'], $productId)) {
            flash_set('error', 'Data produk tidak ditemukan atau gagal dihapus.');
            redirect_to('/dashboard');
        }

        flash_set('success', 'Produk berhasil dihapus.');
        redirect_to('/dashboard');
    }

    if (str_starts_with($path, '/api/')) {
        if ($method === 'OPTIONS') {
            json_response([
                'success' => true,
                'message' => 'Preflight OK',
            ]);
        }

        $apiUser = require_api_user();

        if ($path === '/api/me' && $method === 'GET') {
            json_response([
                'success' => true,
                'message' => 'API key valid.',
                'data' => $apiUser,
            ]);
        }

        if ($path === '/api/products' && $method === 'GET') {
            json_response([
                'success' => true,
                'message' => 'Daftar produk berhasil diambil.',
                'data' => list_products_for_user((int) $apiUser['id']),
            ]);
        }

        if ($path === '/api/products' && $method === 'POST') {
            $normalized = normalize_product_payload(request_payload());

            if ($normalized['errors']) {
                json_response([
                    'success' => false,
                    'message' => 'Validasi gagal.',
                    'errors' => $normalized['errors'],
                ], 422);
            }

            $product = create_product_record($normalized['data'], (int) $apiUser['id']);

            json_response([
                'success' => true,
                'message' => 'Produk berhasil dibuat.',
                'data' => $product,
            ], 201);
        }

        if (preg_match('#^/api/products/(\d+)$#', $path, $matches)) {
            $productId = (int) $matches[1];
            $product = get_product_for_user((int) $apiUser['id'], $productId);

            if (!$product) {
                json_response([
                    'success' => false,
                    'message' => 'Data produk tidak ditemukan.',
                ], 404);
            }

            if ($method === 'GET') {
                json_response([
                    'success' => true,
                    'message' => 'Detail produk berhasil diambil.',
                    'data' => $product,
                ]);
            }

            if (in_array($method, ['PUT', 'PATCH'], true)) {
                $merged = array_merge($product, request_payload());
                $normalized = normalize_product_payload($merged);

                if ($normalized['errors']) {
                    json_response([
                        'success' => false,
                        'message' => 'Validasi gagal.',
                        'errors' => $normalized['errors'],
                    ], 422);
                }

                $updated = update_product_record((int) $apiUser['id'], $productId, $normalized['data']);

                json_response([
                    'success' => true,
                    'message' => 'Produk berhasil diperbarui.',
                    'data' => $updated,
                ]);
            }

            if ($method === 'DELETE') {
                if (!delete_product_record((int) $apiUser['id'], $productId)) {
                    json_response([
                        'success' => false,
                        'message' => 'Data produk tidak ditemukan atau gagal dihapus.',
                    ], 404);
                }

                json_response([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus.',
                ]);
            }
        }

        json_response([
            'success' => false,
            'message' => 'Endpoint API tidak ditemukan.',
        ], 404);
    }

    http_response_code(404);
    render('error', [
        'title' => '404',
        'pageClass' => 'page-error',
        'currentUser' => null,
        'message' => 'Halaman yang Anda tuju tidak ditemukan.',
        'code' => 404,
    ]);
} catch (Throwable $exception) {
    if (str_starts_with(request_path(), '/api/')) {
        json_response([
            'success' => false,
            'message' => 'Terjadi kesalahan server.',
            'error' => $exception->getMessage(),
        ], 500);
    }

    http_response_code(500);
    render('error', [
        'title' => 'Error',
        'pageClass' => 'page-error',
        'currentUser' => null,
        'message' => 'Aplikasi belum siap atau database lokal belum terhubung.',
        'code' => 500,
        'detail' => $exception->getMessage(),
    ]);
}
