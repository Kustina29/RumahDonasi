<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare("SELECT id_user, username, password, level_user FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashedPasswordFromDb = $user['password'];

        if (password_verify($password, $hashedPasswordFromDb)) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['level_user'] = $user['level_user'];

            $stmt->close();
            $mysqli->close();

            header("Location: ../index.php");
            exit;
        } else {
            echo "<script>alert('Username atau password salah!'); window.location.href='../login.html';</script>";
        }
    } else {
        echo "<script>alert('Username atau password salah!'); window.location.href='../login.html';</script>";
    }

    $stmt->close();
    $mysqli->close();
} else {
    header("Location: ../login.html");
    exit;
}
?>