<?php

declare(strict_types=1);

function base_path(string $path = ''): string
{
    $root = dirname(__DIR__);

    if ($path === '') {
        return $root;
    }

    return $root . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
}

function app_config(): array
{
    static $config = null;

    if ($config === null) {
        $config = require base_path('config.php');
    }

    return $config;
}

function start_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function now(): string
{
    return (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
}

function site_url(string $path = ''): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
          || (($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '') === 'on');

    $scheme = $https ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base   = rtrim($scheme . '://' . $host, '/');

    if ($path === '') {
        return $base;
    }

    return $base . '/' . ltrim($path, '/');
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash_set(string $type, string $message): void
{
    start_session();
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function flash_pull(): ?array
{
    start_session();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return is_array($flash) ? $flash : null;
}

function csrf_token(): string
{
    start_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    start_session();

    return is_string($token) && hash_equals((string) ($_SESSION['csrf_token'] ?? ''), $token);
}

function request_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/');

    return $path === '' ? '/' : $path;
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function request_headers(): array
{
    if (function_exists('getallheaders')) {
        $headers = getallheaders();

        if (is_array($headers)) {
            return array_change_key_case($headers, CASE_LOWER);
        }
    }

    $headers = [];

    foreach ($_SERVER as $key => $value) {
        if (str_starts_with($key, 'HTTP_')) {
            $normalized = strtolower(str_replace('_', '-', substr($key, 5)));
            $headers[$normalized] = $value;
        }
    }

    return $headers;
}

function request_payload(): array
{
    $contentType = strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? ''));

    if (str_contains($contentType, 'application/json')) {
        $raw = file_get_contents('php://input') ?: '';
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    return $_POST;
}

function redirect_to(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function api_headers(): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, X-API-KEY, Authorization');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
}

function json_response(array $payload, int $status = 200): never
{
    api_headers();
    http_response_code($status);
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function pdo_server(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $db = app_config()['db'];
    $dsn = sprintf('mysql:host=%s;port=%d;charset=%s', $db['host'], $db['port'], $db['charset']);
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function ensure_database(): void
{
    $db = app_config()['db'];
    $pdo = pdo_server();
    $database = str_replace('`', '``', $db['name']);
    $pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', $database));
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    ensure_database();

    $db = app_config()['db'];
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $db['host'],
        $db['port'],
        $db['name'],
        $db['charset']
    );

    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    initialize_schema($pdo);

    return $pdo;
}

function initialize_schema(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            api_key_plain VARCHAR(255) DEFAULT NULL,
            api_key_hash VARCHAR(255) DEFAULT NULL,
            api_key_last4 VARCHAR(4) DEFAULT NULL,
            api_key_created_at DATETIME DEFAULT NULL,
            sample_seeded_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $apiKeyPlainCheck = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name'
    );
    $apiKeyPlainCheck->execute([
        ':table_name' => 'users',
        ':column_name' => 'api_key_plain',
    ]);

    if ((int) $apiKeyPlainCheck->fetchColumn() === 0) {
        $pdo->exec('ALTER TABLE users ADD COLUMN api_key_plain VARCHAR(255) DEFAULT NULL AFTER password_hash');
    }

    $columnCheck = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND COLUMN_NAME = :column_name'
    );
    $columnCheck->execute([
        ':table_name' => 'products',
        ':column_name' => 'user_id',
    ]);

    if ((int) $columnCheck->fetchColumn() === 0) {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS products (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(180) NOT NULL,
                description TEXT DEFAULT NULL,
                price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                stock INT NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    } else {
        try {
            $pdo->exec('ALTER TABLE products DROP FOREIGN KEY fk_products_users');
        } catch (Throwable $exception) {
        }

        try {
            $pdo->exec('ALTER TABLE products DROP COLUMN user_id');
        } catch (Throwable $exception) {
        }
    }

    seed_default_products($pdo);
}

function hash_api_key(string $key): string
{
    return hash('sha256', $key);
}

function generate_api_key(): string
{
    return 'merch_' . bin2hex(random_bytes(24));
}

function make_api_key_bundle(): array
{
    $plain = generate_api_key();

    return [
        'plain' => $plain,
        'hash' => hash_api_key($plain),
        'last4' => strtoupper(substr($plain, -4)),
    ];
}

function normalize_email(string $email): string
{
    return strtolower(trim($email));
}

function find_user_by_id(int $id): ?array
{
    $stmt = db()->prepare('SELECT id, name, email, api_key_plain, api_key_hash, api_key_last4, api_key_created_at, sample_seeded_at, created_at, updated_at FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function find_user_by_email(string $email): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => normalize_email($email)]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function find_user_by_api_key(string $apiKey): ?array
{
    $stmt = db()->prepare('SELECT id, name, email, api_key_plain, api_key_last4, api_key_created_at, created_at, updated_at FROM users WHERE api_key_hash = :hash LIMIT 1');
    $stmt->execute([':hash' => hash_api_key($apiKey)]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function create_user(string $name, string $email, string $password): array
{
    if (find_user_by_email($email)) {
        throw new RuntimeException('Email sudah terdaftar.');
    }

    $bundle = make_api_key_bundle();
    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, api_key_plain, api_key_hash, api_key_last4, api_key_created_at, created_at, updated_at) VALUES (:name, :email, :password_hash, :api_key_plain, :api_key_hash, :api_key_last4, :api_key_created_at, :created_at, :updated_at)');
    $stmt->execute([
        ':name' => trim($name),
        ':email' => normalize_email($email),
        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ':api_key_plain' => $bundle['plain'],
        ':api_key_hash' => $bundle['hash'],
        ':api_key_last4' => $bundle['last4'],
        ':api_key_created_at' => now(),
        ':created_at' => now(),
        ':updated_at' => now(),
    ]);

    $userId = (int) $pdo->lastInsertId();
    $user = find_user_by_id($userId);

    return [
        'user' => $user,
        'api_key' => $bundle['plain'],
    ];
}

function authenticate_user(string $email, string $password): ?array
{
    $user = find_user_by_email($email);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return null;
    }

    return $user;
}

function login_user(array $user): void
{
    start_session();
    $_SESSION['user_id'] = (int) $user['id'];
}

function current_user(): ?array
{
    start_session();

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    return find_user_by_id((int) $_SESSION['user_id']);
}

function logout_user(): void
{
    start_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function sample_product_rows(): array
{
    return [
        [
            'name' => 'Gojo Satoru Figure',
            'description' => 'Figure Gojo Satoru dari Jujutsu Kaisen ukuran 20cm',
            'price' => 450000,
            'stock' => 10,
        ],
        [
            'name' => 'Anya Forger Acrylic Stand',
            'description' => 'Acrylic stand karakter Anya Forger dari Spy x Family',
            'price' => 85000,
            'stock' => 25,
        ],
        [
            'name' => 'Naruto Keychain Konoha',
            'description' => 'Gantungan kunci Naruto logo desa Konoha',
            'price' => 35000,
            'stock' => 30,
        ],
        [
            'name' => 'Levi Ackerman Figure',
            'description' => 'Figure Levi Ackerman dari Attack on Titan limited edition',
            'price' => 520000,
            'stock' => 8,
        ],
        [
            'name' => 'Miku Hatsune Acrylic Stand',
            'description' => 'Standee anime Hatsune Miku aesthetic edition',
            'price' => 95000,
            'stock' => 15,
        ],
        [
            'name' => 'Luffy Gear 5 Keychain',
            'description' => 'Keychain Monkey D. Luffy Gear 5 berbahan acrylic',
            'price' => 40000,
            'stock' => 40,
        ],
        [
            'name' => 'Zero Two Figure',
            'description' => 'Figure Zero Two dari Darling in the Franxx',
            'price' => 475000,
            'stock' => 12,
        ],
        [
            'name' => 'Rem Re:Zero Pillow Plush',
            'description' => 'Boneka bantal karakter Rem dari Re:Zero',
            'price' => 150000,
            'stock' => 20,
        ],
        [
            'name' => 'Kakashi Sharingan Poster',
            'description' => 'Poster anime Kakashi Hatake ukuran A3',
            'price' => 50000,
            'stock' => 18,
        ],
        [
            'name' => 'Tanjiro Kamado Keychain',
            'description' => 'Keychain karakter Tanjiro dari Demon Slayer',
            'price' => 30000,
            'stock' => 28,
        ],
        [
            'name' => 'Nezuko Figure Demon Form',
            'description' => 'Figure Nezuko mode demon premium edition',
            'price' => 430000,
            'stock' => 9,
        ],
        [
            'name' => 'Bocchi The Rock Acrylic Stand',
            'description' => 'Acrylic stand karakter Bocchi aesthetic version',
            'price' => 90000,
            'stock' => 14,
        ],
        [
            'name' => 'Genshin Impact Hu Tao Keychain',
            'description' => 'Keychain Hu Tao chibi edition',
            'price' => 45000,
            'stock' => 35,
        ],
        [
            'name' => 'Itachi Uchiha Figure',
            'description' => 'Figure Itachi Uchiha Akatsuki version',
            'price' => 510000,
            'stock' => 7,
        ],
        [
            'name' => 'Mikasa Ackerman Poster',
            'description' => 'Poster Mikasa Ackerman Attack on Titan',
            'price' => 55000,
            'stock' => 16,
        ],
    ];
}

function seed_default_products(PDO $pdo): void
{
    $count = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();

    if ($count > 0) {
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO products (name, description, price, stock, created_at, updated_at) VALUES (:name, :description, :price, :stock, :created_at, :updated_at)');

    foreach (sample_product_rows() as $row) {
        $stmt->execute([
            ':name' => $row['name'],
            ':description' => $row['description'],
            ':price' => $row['price'],
            ':stock' => $row['stock'],
            ':created_at' => now(),
            ':updated_at' => now(),
        ]);
    }
}

function first_six_products_label(): string
{
    return 'Data produk global tersedia untuk semua user yang login dengan API KEY valid.';
}

function normalize_product_payload(array $payload): array
{
    $errors = [];

    $name = trim((string) ($payload['name'] ?? ''));
    if ($name === '') {
        $errors[] = 'Nama produk wajib diisi.';
    }

    $description = trim((string) ($payload['description'] ?? ''));
    $priceRaw = $payload['price'] ?? null;
    $stockRaw = $payload['stock'] ?? null;

    if ($priceRaw === null || $priceRaw === '') {
        $errors[] = 'Harga wajib diisi.';
        $price = 0.0;
    } elseif (!is_numeric($priceRaw) || (float) $priceRaw < 0) {
        $errors[] = 'Harga harus berupa angka positif.';
        $price = 0.0;
    } else {
        $price = round((float) $priceRaw, 2);
    }

    if ($stockRaw === null || $stockRaw === '') {
        $errors[] = 'Stok wajib diisi.';
        $stock = 0;
    } elseif (filter_var($stockRaw, FILTER_VALIDATE_INT) === false || (int) $stockRaw < 0) {
        $errors[] = 'Stok harus berupa bilangan bulat positif.';
        $stock = 0;
    } else {
        $stock = (int) $stockRaw;
    }

    return [
        'errors' => $errors,
        'data' => [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
        ],
    ];
}

function list_products_for_user(int $userId): array
{
    $stmt = db()->query('SELECT id, name, description, price, stock, created_at, updated_at FROM products ORDER BY id DESC');

    return $stmt->fetchAll() ?: [];
}

function get_product_for_user(int $userId, int $productId): ?array
{
    $stmt = db()->prepare('SELECT id, name, description, price, stock, created_at, updated_at FROM products WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch();

    return $product ?: null;
}

function create_product_record(array $data, int $userId): array
{
    $stmt = db()->prepare('INSERT INTO products (name, description, price, stock, created_at, updated_at) VALUES (:name, :description, :price, :stock, :created_at, :updated_at)');
    $stmt->execute([
        ':name' => $data['name'],
        ':description' => $data['description'] !== '' ? $data['description'] : null,
        ':price' => $data['price'],
        ':stock' => $data['stock'],
        ':created_at' => now(),
        ':updated_at' => now(),
    ]);

    return get_product_for_user($userId, (int) db()->lastInsertId()) ?? [];
}

function update_product_record(int $userId, int $productId, array $data): ?array
{
    $stmt = db()->prepare('UPDATE products SET name = :name, description = :description, price = :price, stock = :stock, updated_at = :updated_at WHERE id = :id');
    $stmt->execute([
        ':name' => $data['name'],
        ':description' => $data['description'] !== '' ? $data['description'] : null,
        ':price' => $data['price'],
        ':stock' => $data['stock'],
        ':updated_at' => now(),
        ':id' => $productId,
    ]);

    return get_product_for_user($userId, $productId);
}

function delete_product_record(int $userId, int $productId): bool
{
    $stmt = db()->prepare('DELETE FROM products WHERE id = :id');

    $stmt->execute([
        ':id' => $productId,
    ]);

    return $stmt->rowCount() > 0;
}

function api_key_from_request(): ?string
{
    $headers = request_headers();

    if (!empty($headers['x-api-key'])) {
        return trim((string) $headers['x-api-key']);
    }

    $authorization = trim((string) ($headers['authorization'] ?? ''));

    if ($authorization !== '' && preg_match('/Bearer\s+(.*)$/i', $authorization, $matches)) {
        return trim($matches[1]);
    }

    return null;
}

function require_login(): array
{
    $user = current_user();

    if (!$user) {
        flash_set('error', 'Silakan login terlebih dahulu.');
        redirect_to('/login');
    }

    return $user;
}

function require_api_user(): array
{
    $apiKey = api_key_from_request();

    if (!$apiKey) {
        json_response([
            'success' => false,
            'message' => 'API KEY tidak ditemukan. Gunakan header X-API-KEY atau Authorization Bearer token.',
        ], 401);
    }

    $user = find_user_by_api_key($apiKey);

    if (!$user) {
        json_response([
            'success' => false,
            'message' => 'API KEY tidak valid.',
        ], 401);
    }

    return $user;
}

function render(string $template, array $data = []): never
{
    $templateFile = base_path('views/' . $template . '.php');

    if (!is_file($templateFile)) {
        throw new RuntimeException('Template tidak ditemukan: ' . $template);
    }

    start_session();
    $flash = flash_pull();
    $app = app_config();
    extract($data, EXTR_SKIP);

    ob_start();
    include $templateFile;
    $content = ob_get_clean();

    include base_path('views/layout.php');
    exit;
}
