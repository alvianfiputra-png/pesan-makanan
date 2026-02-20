<?php
require_once 'koneksi.php';

// Ambil data dari form
$nama = $_POST['nama'] ?? '';
$telepon = $_POST['telepon'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$catatan = $_POST['catatan'] ?? '';
$produk = $_POST['produk'] ?? []; // array [id_produk => jumlah]

// Validasi dasar
if (empty($nama) || empty($telepon) || empty($alamat)) {
    die("Error: Data diri harus diisi lengkap!");
}

// Filter produk yang jumlahnya > 0
$items = [];
foreach ($produk as $id => $jumlah) {
    if ($jumlah > 0) {
        $items[$id] = (int)$jumlah;
    }
}

if (empty($items)) {
    die("Error: Pilih minimal 1 produk!");
}

// Mulai transaksi database
$conn->begin_transaction();

try {
    // Hitung total harga
    $total = 0;
    $placeholders = implode(',', array_fill(0, count($items), '?'));
    $ids = array_keys($items);
    
    $stmt = $conn->prepare("SELECT id, harga FROM produk WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $harga_produk = [];
    while ($row = $result->fetch_assoc()) {
        $harga_produk[$row['id']] = $row['harga'];
        $total += $row['harga'] * $items[$row['id']];
    }
    
    // Simpan ke tabel pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (nama_pelanggan, telepon, alamat, catatan, total_harga) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nama, $telepon, $alamat, $catatan, $total);
    $stmt->execute();
    $pesanan_id = $conn->insert_id;
    
    // Simpan detail pesanan
    $stmt = $conn->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
    foreach ($items as $produk_id => $jumlah) {
        $harga = $harga_produk[$produk_id];
        $stmt->bind_param("iiii", $pesanan_id, $produk_id, $jumlah, $harga);
        $stmt->execute();
    }
    
    // Commit transaksi
    $conn->commit();
    
    // Redirect ke halaman sukses
    header("Location: sukses.php?id=" . $pesanan_id);
    exit;
    
} catch (Exception $e) {
    $conn->rollback();
    die("Error: Gagal memproses pesanan. " . $e->getMessage());
}
?>