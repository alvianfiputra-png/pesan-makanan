<?php
session_start();
require_once '../koneksi.php';

// Login sederhana (opsional)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Ambil semua pesanan
$query = "
    SELECT p.*, 
    (SELECT COUNT(*) FROM detail_pesanan WHERE pesanan_id = p.id) as jumlah_item
    FROM pesanan p 
    ORDER BY p.created_at DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Daftar Pre-Order</title>
    <link rel="stylesheet" href="css/style.css?v=2.0">
</head>
<body>
    <h1>Daftar Pre-Order</h1>
    <p><a href="logout.php">Logout</a></p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Waktu Pemesanan</th>
                <th>Nama Pelanggan</th>
                <th>Telepon</th>
                <th>Total Harga</th>
                <th>Item</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $row['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                    <td><?= htmlspecialchars($row['telepon']) ?></td>
                    <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                    <td><?= $row['jumlah_item'] ?> item</td>
                    <td>
                        <span class="badge <?= $row['status'] ?>"><?= $row['status'] ?></span>
                    </td>
                    <td>
                        <a href="detail.php?id=<?= $row['id'] ?>">Detail</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8" style="text-align:center">Belum ada pesanan</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>