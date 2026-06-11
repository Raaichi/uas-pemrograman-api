CREATE DATABASE IF NOT EXISTS uas_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE uas_api;

CREATE TABLE IF NOT EXISTS users (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO products (name, description, price, stock, created_at, updated_at) VALUES
('Gojo Satoru Figure', 'Figure Gojo Satoru dari Jujutsu Kaisen ukuran 20cm', 450000, 10, NOW(), NOW()),
('Anya Forger Acrylic Stand', 'Acrylic stand karakter Anya Forger dari Spy x Family', 85000, 25, NOW(), NOW()),
('Naruto Keychain Konoha', 'Gantungan kunci Naruto logo desa Konoha', 35000, 30, NOW(), NOW()),
('Levi Ackerman Figure', 'Figure Levi Ackerman dari Attack on Titan limited edition', 520000, 8, NOW(), NOW()),
('Miku Hatsune Acrylic Stand', 'Standee anime Hatsune Miku aesthetic edition', 95000, 15, NOW(), NOW()),
('Luffy Gear 5 Keychain', 'Keychain Monkey D. Luffy Gear 5 berbahan acrylic', 40000, 40, NOW(), NOW()),
('Zero Two Figure', 'Figure Zero Two dari Darling in the Franxx', 475000, 12, NOW(), NOW()),
('Rem Re:Zero Pillow Plush', 'Boneka bantal karakter Rem dari Re:Zero', 150000, 20, NOW(), NOW()),
('Kakashi Sharingan Poster', 'Poster anime Kakashi Hatake ukuran A3', 50000, 18, NOW(), NOW()),
('Tanjiro Kamado Keychain', 'Keychain karakter Tanjiro dari Demon Slayer', 30000, 28, NOW(), NOW()),
('Nezuko Figure Demon Form', 'Figure Nezuko mode demon premium edition', 430000, 9, NOW(), NOW()),
('Bocchi The Rock Acrylic Stand', 'Acrylic stand karakter Bocchi aesthetic version', 90000, 14, NOW(), NOW()),
('Genshin Impact Hu Tao Keychain', 'Keychain Hu Tao chibi edition', 45000, 35, NOW(), NOW()),
('Itachi Uchiha Figure', 'Figure Itachi Uchiha Akatsuki version', 510000, 7, NOW(), NOW()),
('Mikasa Ackerman Poster', 'Poster Mikasa Ackerman Attack on Titan', 55000, 16, NOW(), NOW());