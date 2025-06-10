<?php

$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$host = "localhost";
$dbname = "db_rumahdonasi";
$dbuser = "root";
$dbpass = "";
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
die("Koneksi gagal: " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $username, $hashedPassword);

if ($stmt->execute()) {
    echo "<script>alert('Signup berhasil!'); window.history.back();</script>";
} else {
    echo "<script>alert('Error signup: " . $stmt->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>