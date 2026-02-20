<?php
session_start();
require_once '../koneksi.php';

// Login check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;

if (!$id) {
    die("ID pesanan tidak valid!");
}

// Ambil data pesanan
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_assoc();

if (!$pesanan) {
    die("Pesanan tidak ditemukan");
}

// Update status jika ada request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $update_stmt = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $id);
    $update_stmt->execute();
    $pesanan['status'] = $new_status;
}

// Ambil detail pesanan
$stmt = $conn->prepare("
    SELECT d.*, p.nama as produk_nama 
    FROM detail_pesanan d
    JOIN produk p ON d.produk_id = p.id
    WHERE d.pesanan_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$details = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= $id ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            flex: 1;
        }

        .back-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .back-link:active {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(1px);
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            padding: 30px;
            margin-bottom: 25px;
        }

        h3 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 20px;
            font-weight: 600;
            border-left: 4px solid #00b894;
            padding-left: 12px;
        }

        .info-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            margin: 14px 0;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: 700;
            color: #2c3e50;
            font-size: 14px;
        }

        .info-row > div:last-child {
            color: #555;
            line-height: 1.6;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table thead {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
        }

        table th {
            color: white;
            padding: 14px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e8eef5;
            color: #2c3e50;
            font-size: 14px;
        }

        table tbody tr {
            background: white;
        }

        table tbody tr:nth-child(even) {
            background: #f8f9ff;
        }

        .total-row {
            font-size: 16px;
            font-weight: 700;
            border-top: 2px solid #e0e0e0;
            padding-top: 14px;
            margin-top: 16px;
            text-align: right;
            color: #2c3e50;
        }

        /* BADGE */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .badge.pending {
            background: #fef3cd;
            color: #856404;
        }

        .badge.confirmed {
            background: #d4edda;
            color: #155724;
        }

        .badge.completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* FORM */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }

        .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            color: #2c3e50;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .form-group select:focus {
            border-color: #00b894;
            outline: none;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-group button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
        }

        .form-group button:active {
            transform: translateY(2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .status-info {
            padding: 14px;
            background: #f8f9ff;
            border-left: 4px solid #00b894;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 14px;
            color: #2c3e50;
        }

        .status-info strong {
            color: #2c3e50;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            h1 {
                width: 100%;
            }

            .card {
                padding: 20px;
            }

            .info-row {
                grid-template-columns: 100px 1fr;
            }

            table th,
            table td {
                padding: 10px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Detail Pesanan #<?= $id ?></h1>
            <a href="index.php" class="back-link">← Kembali</a>
        </div>

        <div class="card">
            <h3>👤 Informasi Pelanggan</h3>
            <div class="info-row">
                <div class="label">Nama:</div>
                <div><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></div>
            </div>
            <div class="info-row">
                <div class="label">Telepon:</div>
                <div><?= htmlspecialchars($pesanan['telepon']) ?></div>
            </div>
            <div class="info-row">
                <div class="label">Alamat:</div>
                <div><?= nl2br(htmlspecialchars($pesanan['alamat'])) ?></div>
            </div>
            <?php if (!empty($pesanan['catatan'])): ?>
            <div class="info-row">
                <div class="label">Catatan:</div>
                <div><?= nl2br(htmlspecialchars($pesanan['catatan'])) ?></div>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <div class="label">Waktu Pesan:</div>
                <div><?= date('d/m/Y H:i:s', strtotime($pesanan['created_at'])) ?></div>
            </div>
        </div>

        <div class="card">
            <h3>Detail Pesanan</h3>
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($detail = $details->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($detail['produk_nama']) ?></td>
                        <td><?= $detail['jumlah'] ?></td>
                        <td>Rp <?= number_format($detail['harga_satuan'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($detail['harga_satuan'] * $detail['jumlah'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="total-row">
                Total: Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?>
            </div>
        </div>

        <div class="card">
            <h3>📊 Status Pesanan</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="status">Ubah Status:</label>
                    <select name="status" id="status">
                        <option value="pending" <?= $pesanan['status'] == 'pending' ? 'selected' : '' ?>>⏳ Pending</option>
                        <option value="confirmed" <?= $pesanan['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="completed" <?= $pesanan['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= $pesanan['status'] == 'cancelled' ? 'selected' : '' ?>>✗ Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit">Perbarui</button>
                </div>
            </form>
            <div class="status-info">
                <strong>Status saat ini:</strong> <span class="badge <?= $pesanan['status'] ?>"><?= ucfirst($pesanan['status']) ?></span>
            </div>
        </div>
    </div>
</body>
</html>

