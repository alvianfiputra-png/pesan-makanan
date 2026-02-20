🚀 PANDUAN SETUP SISTEM PRE-ORDER MAKANAN
================================================

LANGKAH 1: IMPORT DATABASE
==========================
1. Buka phpMyAdmin (akses via http://localhost/phpmyadmin)
2. Login dengan:
   - Username: root
   - Password: (kosong, tekan Enter)
3. Klik "Import" di menu atas
4. Pilih file "setup.sql" dari folder pre-order
5. Klik "Import" untuk menjalankan query

Atau menggunakan MySQL Command Line:
   mysql -u root < setup.sql

Tabel yang akan dibuat:
✓ produk - Daftar menu makanan
✓ pesanan - Data pesanan pelanggan
✓ detail_pesanan - Item detail dalam setiap pesanan

LANGKAH 2: VERIFIKASI KONEKSI DATABASE
========================================
Pastikan file koneksi.php sudah benar:
- Host: localhost
- Username: root
- Password: (kosong)
- Database: preorder_db

LANGKAH 3: JALANKAN SISTEM
===========================
Home Page (Customer):
   http://localhost/pre-order/index.php

Admin Panel:
   http://localhost/pre-order/admin/index.php
   Login dengan:
   - Username: admin
   - Password: rahasia123
   
   ⚠️  GANTI PASSWORD INI di file admin/login.php

FITUR YANG TERSEDIA
===================

CUSTOMER (index.php):
✓ Lihat daftar menu makanan dengan harga
✓ Pilih jumlah pesanan untuk setiap item
✓ Tambahkan catatan khusus (MSG, level pedas, dll)
✓ Input data diri (nama, telepon, alamat)
✓ Submit pesanan otomatis ke database

ADMIN:
✓ Login untuk akses panel admin
✓ Lihat semua pesanan yang masuk
✓ Lihat detail pesanan (pelanggan, item, total harga)
✓ Update status pesanan (pending → confirmed → completed)
✓ Logout

FILE-FILE PENTING
=================
index.php           - Halaman customer (tempat order)
proses.php          - Mencatat pesanan ke database
sukses.php          - Halaman konfirmasi setelah order
koneksi.php         - Konfigurasi database
setup.sql           - Database initialization script

admin/index.php     - Daftar semua pesanan
admin/detail.php    - Detail pesanan + update status
admin/login.php     - Form login admin
admin/logout.php    - Logout admin

KUSTOMISASI
===========

1. Edit daftar menu:
   - Buka phpmyadmin
   - Masuk tabel 'produk'
   - Tambah/edit menu sesuai kebutuhan

2. Ganti password admin:
   - Edit admin/login.php
   - Ubah nilai di baris: if ($username === 'admin' && $password === 'rahasia123')

3. Ubah styling:
   - Edit css/style.css untuk mengubah tampilan

TROUBLESHOOTING
===============

❌ Error: "Koneksi gagal"
   → Pastikan MySQL running
   → Cek username/password di koneksi.php

❌ Error: "Table doesn't exist"
   → Jalankan setup.sql melalui phpmyadmin

❌ Session tidak bekerja
   → Pastikan folder admin/sessions bisa write
   → Atau gunakan session handler default

❌ Form tidak submit
   → Pastikan JavaScript enabled
   → Buka console (F12) untuk melihat error

TIPS KEAMANAN
=============
⚠️  Ini adalah sistem dasar. Untuk production:
   - Gunakan password hashing (bcrypt/argon2)
   - Implementasi CSRF token
   - Validate & sanitize semua input
   - Gunakan HTTPS
   - Backup database secara berkala
   - Batasi akses admin dengan IP whitelist

SUPPORT
=======
Jika ada error, cek:
1. Error log PHP di XAMPP/logs
2. Browser console (F12)
3. Database dengan phpMyAdmin
4. File permissions (chmod 755 untuk folder)
