<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_barang = $_POST['nama_barang'] ?? '';
    $kategori_barang = $_POST['kategori_barang'] ?? '';
    $variasi = $_POST['variasi'] ?? null;
    $jumlah_barang = $_POST['jumlah_barang'] ?? 0;

    if (empty($nama_barang) || empty($kategori_barang) || $jumlah_barang <= 0) {
        $message = "Nama barang, kategori, dan jumlah harus diisi dengan benar.";
        header("Location: donasi_form.html?status=error&message=" . urlencode($message));
        exit;
    }

    $foto_path = null;
    if (isset($_FILES['foto_barang']) && $_FILES['foto_barang']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $file_name = uniqid() . "_" . basename($_FILES['foto_barang']['name']);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_extensions)) {
            $message = "Maaf, hanya file JPG, JPEG, PNG, & GIF yang diperbolehkan.";
            header("Location: donasi_form.html?status=error&message=" . urlencode($message));
            exit;
        }

        if ($_FILES['foto_barang']['size'] > 5000000) {
            $message = "Maaf, ukuran file terlalu besar (maks 5MB).";
            header("Location: donasi_form.html?status=error&message=" . urlencode($message));
            exit;
        }

        if (move_uploaded_file($_FILES['foto_barang']['tmp_name'], $target_file)) {
            $foto_path = $target_file;
        } else {
            $message = "Maaf, terjadi kesalahan saat mengunggah file Anda.";
            header("Location: donasi_form.html?status=error&message=" . urlencode($message));
            exit;
        }
    }

    $host = "localhost";
    $dbname = "db_rumahdonasi";
    $dbuser = "root";
    $dbpass = "";

    $conn = new mysqli($host, $dbuser, $dbpass, $dbname);

    if ($conn->connect_error) {
        $message = "Koneksi database gagal: " . $conn->connect_error;
        header("Location: donasi_form.html?status=error&message=" . urlencode($message));
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO donasi_barang (nama_barang, kategori_barang, variasi, jumlah_barang, foto_path) VALUES (?, ?, ?, ?, ?)");

    if ($stmt === false) {
        $message = "Gagal menyiapkan query: " . $conn->error;
        header("Location: donasi_form.html?status=error&message=" . urlencode($message));
        exit;
    }

    $stmt->bind_param("sssis", $nama_barang, $kategori_barang, $variasi, $jumlah_barang, $foto_path);

    if ($stmt->execute()) {
        $message = "Donasi barang berhasil ditambahkan!";
        header("Location: donasi_form.html?status=success&message=" . urlencode($message));
        exit;
    } else {
        $message = "Error saat menyimpan donasi: " . $stmt->error;
        header("Location: donasi_form.html?status=error&message=" . urlencode($message));
        exit;
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: donasi_form.html");
    exit;
}
?>