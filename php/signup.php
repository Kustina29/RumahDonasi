<?php

require_once 'config.php';

$nama = $_POST['nama'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$level = "Donatur"; 

$stmt = $mysqli->prepare("INSERT INTO users (nama, email, username, password, level_user) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $nama, $email, $username, $hashedPassword, $level);

if ($stmt->execute()) {
    header("Location: ../login.html?status=success&message=" . urlencode("Registrasi berhasil! Silakan login."));
} else {
    if ($mysqli->errno == 1062) {
        $error_message = "Username atau email sudah terdaftar.";
    } else {
        $error_message = "Gagal mendaftar: " . $stmt->error;
    }
    header("Location: ../signup.html?status=error&message=" . urlencode($error_message));
}

$stmt->close();
$mysqli->close();
exit();
?>