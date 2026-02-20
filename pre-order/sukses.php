<?php
require_once 'koneksi.php';

$id = $_GET['id'] ?? 0;

// Ambil data pesanan
$stmt = $conn->prepare("SELECT * FROM pesanan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_assoc();

if (!$pesanan) {
    die("Pesanan tidak ditemukan");
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
$detail = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            padding: 40px;
            max-width: 650px;
            width: 100%;
            text-align: center;
        }

        .sukses-icon {
            font-size: 64px;
            margin-bottom: 20px;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
            background: linear-gradient(135deg, #00b894, #00cec9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            color: #7f8c8d;
            font-size: 16px;
            margin-bottom: 30px;
        }

        .card {
            background: #f0fdf4;
            padding: 24px;
            border-radius: 12px;
            text-align: left;
            border-left: 4px solid #00b894;
            margin-bottom: 25px;
        }

        .card h3 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .info-row {
            display: grid;
            grid-template-columns: 120px 1fr;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .info-label {
            font-weight: 600;
            color: #2c3e50;
        }

        .info-value {
            color: #555;
            line-height: 1.5;
        }

        .items-list {
            background: white;
            border-radius: 8px;
            padding: 16px;
            margin: 16px 0;
        }

        .items-list h4 {
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .items-list ul {
            list-style: none;
            padding: 0;
        }

        .items-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e8eef5;
            font-size: 14px;
            color: #555;
            display: flex;
            justify-content: space-between;
        }

        .items-list li:last-child {
            border-bottom: none;
        }

        .total-box {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
            color: white;
            padding: 16px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: #00b894;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .back-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 184, 148, 0.3);
        }

        .back-link:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(0, 184, 148, 0.2);
        }

        .order-id {
            color: #00b894;
            font-weight: 700;
            font-size: 18px;
        }

        .payment-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 28px;
            border-radius: 12px;
            text-align: center;
            margin-top: 24px;
            color: white;
        }

        .payment-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .qris-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            margin: 16px 0;
        }

        .qris-code {
            width: 200px;
            height: 200px;
            background: #f5f5f5;
            border: 3px dashed #ddd;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 12px;
            text-align: center;
            padding: 10px;
        }

        .payment-amount {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .payment-instructions {
            background: #f0f0f0;
            color: #2c3e50;
            padding: 16px;
            border-radius: 8px;
            text-align: left;
            font-size: 14px;
            line-height: 1.6;
            margin-top: 16px;
        }

        .payment-instructions strong {
            display: block;
            margin-bottom: 8px;
            color: #667eea;
        }

        .payment-instructions ol {
            margin-left: 20px;
            padding: 0;
        }

        .payment-instructions li {
            margin-bottom: 8px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
            }

            h1 {
                font-size: 26px;
            }

            .sukses-icon {
                font-size: 48px;
            }

            .info-row {
                grid-template-columns: 100px 1fr;
            }

            .total-box {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sukses-icon"></div>
        <h1>Pesanan Berhasil!</h1>
        <p class="subtitle">Terima kasih, pesanan Anda telah kami terima.</p>
        
        <div class="card">
            <h3>Detail Pesanan <span class="order-id">#<?= $id ?></span></h3>
            
            <div class="info-row">
                <div class="info-label">Nama:</div>
                <div class="info-value"><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Telepon:</div>
                <div class="info-value"><?= htmlspecialchars($pesanan['telepon']) ?></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Alamat:</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($pesanan['alamat'])) ?></div>
            </div>
            
            <?php if (!empty($pesanan['catatan'])): ?>
            <div class="info-row">
                <div class="info-label">Catatan:</div>
                <div class="info-value"><?= nl2br(htmlspecialchars($pesanan['catatan'])) ?></div>
            </div>
            <?php endif; ?>
            
            <div class="items-list">
                <h4>Item Pesanan:</h4>
                <ul>
                    <?php while ($item = $detail->fetch_assoc()): ?>
                        <li>
                            <span><?= $item['produk_nama'] ?> <strong>x<?= $item['jumlah'] ?></strong></span>
                            <span>Rp <?= number_format($item['jumlah'] * $item['harga_satuan'], 0, ',', '.') ?></span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            
            <div class="total-box">
                <span>Total Harga:</span>
                <span>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- METODE PEMBAYARAN QRIS -->
        <div class="payment-card">
            <div class="payment-title">Metode Pembayaran</div>
            
            <div class="qris-container">
                <div class="qris-code">QRIS CODE AKAN DITAMPILKAN DI SINI</div>
                <div class="payment-amount">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></div>
            </div>

            <div class="payment-instructions">
                <strong>Cara Pembayaran:</strong>
                <ol>
                    <li>Buka aplikasi e-wallet atau banking Anda</li>
                    <li>Pilih menu "Scan QRIS" atau "Bayar dengan QRIS"</li>
                    <li>Arahkan kamera ke QR Code di atas</li>
                    <li>Konfirmasi nominal pembayaran</li>
                    <li>Selesaikan transaksi!</li>
                </ol>
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap;">
            <a href="index.php" class="back-link">← Pesan Lagi</a>
        </div>

        <div style="background: #fff3cd; padding: 16px; border-radius: 8px; margin-top: 24px; border-left: 4px solid #ffc107; color: #856404;">
            <strong>CATATAN:</strong> SCREENSHOT BUKTI PEMBAYARAN ANDA DAN KIRIMKAN KE NOMOR WA KAMI UNTUK KONFIRMASI PESANAN. TERIMA KASIH!
        </div>

        <div style="text-align: center; margin-top: 20px; display: flex; align-items: center; justify-content: center; gap: 5px;">
            <a href="https://wa.me/628970744900" target="_blank" style="font-size: 48px; text-decoration: none; display: inline-block; transition: transform 0.2s ease;">
                ✆
            </a>
            <a href="https://wa.me/628970744900" target="_blank" style="color: #25d366; text-decoration: none; font-weight: 700; font-size: 18px;">
                +62 897-0744-900
            </a>
        </div>
    </div>
</body>
</html>