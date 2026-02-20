<?php
require_once 'koneksi.php';

// Ambil semua produk dari database
$query = "SELECT * FROM produk ORDER BY id ASC";
$result = $conn->query($query);
$produk_list = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produk_list[] = $row;
    }
}

// Include file template HTML
include 'template.php';
?>