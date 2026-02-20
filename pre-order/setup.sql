-- Database untuk Pre-Order Makanan
-- Jalankan query ini di phpMyAdmin atau MySQL Command Line

-- Buat database
CREATE DATABASE IF NOT EXISTS preorder_db;
USE preorder_db;

-- Tabel produk
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel pesanan
CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL,
    telepon VARCHAR(15) NOT NULL,
    alamat TEXT NOT NULL,
    catatan TEXT,
    total_harga INT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel detail pesanan
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    produk_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga_satuan INT NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert data produk contoh
INSERT INTO produk (nama, harga, deskripsi) VALUES
('Nasi Goreng', 25000, 'Nasi goreng dengan telur, sayuran segar'),
('Mie Ayam', 18000, 'Mie kuning dengan ayam cincang dan wajan'),
('Soto Ayam', 15000, 'Sup tradisional dengan ayam dan rempah'),
('Lumpia Goreng', 12000, 'Lumpia renyah isi daging dan sayuran'),
('Es Teh Manis', 5000, 'Minuman segar untuk menemani pesanan'),
('Bakso Sapi', 22000, 'Bakso daging sapi dalam kuah gurih');
