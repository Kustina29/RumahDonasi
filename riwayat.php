<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$dbname = "db_rumahdonasi";
$dbuser = "root";
$dbpass = "";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$sql = "SELECT id, nama_barang, kategori_barang, variasi, jumlah_barang, foto_path, tanggal_donasi FROM donasi_barang ORDER BY tanggal_donasi DESC";
$result = $conn->query($sql);

$donasi_items = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $donasi_items[] = $row;
    }
}

$conn->close();
?>