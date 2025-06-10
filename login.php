<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $host = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "db_rumahdonasi";

    $conn = new mysqli($host, $dbuser, $dbpass, $dbname);

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $hashedPasswordFromDb = $user['password'];

        if (password_verify($password, $hashedPasswordFromDb)) {
            $_SESSION['username'] = $user['username'];

            $stmt->close();
            $conn->close();

            header("Location: home.html");
            exit;
        } else {
            echo "<script>alert('Username atau password salah!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('Username atau password salah!'); window.location.href='login.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: login.html");
    exit;
}
?>