<?php
$host = 'localhost';
$user = 'root';      // default XAMPP
$pass = '';          // default XAMPP kosong
$db   = 'preorder_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset agar bisa baca Indonesia
$conn->set_charset("utf8");
?>