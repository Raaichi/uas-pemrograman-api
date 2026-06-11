# UAS API Store

Website ini menyediakan:

- login dan daftar akun sederhana
- pembuatan API key setelah login
- API CRUD data produk berbasis MySQL lokal
- dokumentasi penggunaan API
- collection Postman
- client website yang memakai API secara langsung

## Cara menjalankan

1. Pastikan PHP dan MySQL lokal aktif.
2. Buka folder project ini di Laragon atau terminal.
3. Jalankan server PHP bawaan:

```bash
php -S localhost:8000 router.php
```

4. Buka `http://localhost:8000`.

Jika memakai Laragon Apache, pastikan folder project dijadikan document root atau akses lewat virtual host seperti biasa.

## Database

Project ini memakai MySQL lokal dan akan mencoba membuat database `uas_api` otomatis.

Default koneksi diatur di `config.php`:

- host: `127.0.0.1`
- user: `root`
- password: kosong
- database: `uas_api`

Jika konfigurasi MySQL kamu berbeda, ubah file tersebut.

Jika ingin impor manual, gunakan `database/schema.sql`.

## Alur penggunaan

1. Buka halaman register dan buat akun.
2. Login ke dashboard.
3. API key pertama dibuat otomatis, atau regenerate dari dashboard.
4. Tambahkan data produk dari dashboard atau client website.
5. Gunakan API key di Postman atau client website.

## Endpoint API

- `GET /api/me` untuk cek API key dan data user
- `GET /api/products` untuk melihat semua data
- `GET /api/products/{id}` untuk detail data
- `POST /api/products` untuk create data
- `PUT /api/products/{id}` untuk update data
- `DELETE /api/products/{id}` untuk delete data

Header otorisasi yang didukung:

- `X-API-KEY: your_api_key`
- `Authorization: Bearer your_api_key`

Contoh yang benar:

```bash
curl -H "Authorization: Bearer merch_xxxxxxxxxxxxxxxxxx" http://localhost:8000/api/products
```

Jangan menulis `bearer_x-api-key`. Yang benar adalah nama header `Authorization` dan nilai diawali `Bearer ` lalu diikuti API key utuh.

## Postman

Import file berikut:

- `postman/uas-api.postman_collection.json`
- `postman/uas-api.postman_environment.json`

Lalu isi `api_key` dengan key yang dibuat dari dashboard.
