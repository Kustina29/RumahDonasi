<?php
session_start();

$host = 'localhost';
$dbname = 'rumah_donasi';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $donasi_id = $_POST['donasi_id'];
    $action = $_POST['action'];
    $catatan_admin = $_POST['catatan_admin'] ?? '';
    
    if ($action == 'terima') {
        $status = 'diterima';
    } elseif ($action == 'tolak') {
        $status = 'ditolak';
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE donasi SET status = ?, catatan_admin = ?, tanggal_verifikasi = NOW() WHERE id = ?");
        $stmt->execute([$status, $catatan_admin, $donasi_id]);
        
        $message = "Donasi berhasil " . ($status == 'diterima' ? 'diterima' : 'ditolak');
        $message_type = 'success';
    } catch(PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

$filter = $_GET['filter'] ?? 'pending';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM donasi WHERE 1=1";
$params = [];

if ($filter != 'semua') {
    $sql .= " AND status = ?";
    $params[] = $filter;
}

if (!empty($search)) {
    $sql .= " AND (nama_barang LIKE ? OR kategori_barang LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY tanggal_donasi DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $donasi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $donasi_list = [];
    $message = "Error mengambil data: " . $e->getMessage();
    $message_type = 'error';
}

try {
    $stats_stmt = $pdo->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'diterima' THEN 1 ELSE 0 END) as diterima,
        SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
        FROM donasi");
    $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $stats = ['total' => 0, 'pending' => 0, 'diterima' => 0, 'ditolak' => 0];
}
?>
