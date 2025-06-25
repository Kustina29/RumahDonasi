<?php
session_start();

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Akses ditolak. Anda harus login terlebih dahulu.']);
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        handleGetRequest($mysqli, $resource);
        break;
    case 'POST':
        handlePostRequest($mysqli, $resource, $input);
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Metode request tidak diizinkan.']);
        break;
}

function handleGetRequest($mysqli, $resource) {
    $loggedInUserId = $_SESSION['id_user'] ?? 0;
    $loggedInUserLevel = $_SESSION['level_user'] ?? '';

    switch ($resource) {
        case 'dashboard_counts':
            getDashboardCounts($mysqli);
            break;
        case 'donatur':
            getDonaturData($mysqli);
            break;
        case 'distributor':
            getDistributorData($mysqli);
            break;
        case 'barang_donasi':
            getBarangDonasiData($mysqli);
            break;
        case 'permintaan_distribusi_admin':
            if ($loggedInUserLevel === 'Admin') {
                getPermintaanDistribusiAdminData($mysqli);
            } else {
                http_response_code(403);
                echo json_encode(['message' => 'Akses ditolak.']);
            }
            break;
        case 'permintaan_distribusi_distributor':
            if ($loggedInUserLevel === 'Distributor') {
                getPermintaanDistribusiDistributorData($mysqli, $loggedInUserId);
            } else {
                http_response_code(403);
                echo json_encode(['message' => 'Akses ditolak.']);
            }
            break;
        case 'barang_tersedia':
            if ($loggedInUserLevel === 'Distributor') {
                getBarangTersediaData($mysqli);
            } else {
                http_response_code(403);
                echo json_encode(['message' => 'Akses ditolak.']);
            }
            break;
        case 'histori_distribusi_distributor':
            if ($loggedInUserLevel === 'Distributor') {
                getHistoriDistribusiDistributorData($mysqli, $loggedInUserId);
            } else {
                http_response_code(403);
                echo json_encode(['message' => 'Akses ditolak.']);
            }
            break;
        case 'histori_distribusi_donasi_admin':
            getHistoriDistribusiDonasiAdminData($mysqli);
            break;
        case 'permintaan_konfirmasi_selesai':
            if ($_SESSION['level_user'] === 'Admin') {
                getPermintaanKonfirmasiSelesaiData($mysqli);
            } else {
            }
            break;
        case 'histori_donasi_donatur':
            getHistoriDonasiDonaturData($mysqli, $loggedInUserId);
            break;
        case 'riwayat_distribusi_donatur':
            if ($loggedInUserLevel === 'Donatur') {
                getRiwayatDistribusiDonaturData($mysqli, $loggedInUserId);
            } else {
                http_response_code(403);
                echo json_encode(['message' => 'Akses ditolak.']);
            }
            break;
        case 'pesan':
            getPesanData($mysqli);
            break;
        default:
            http_response_code(404);
            echo json_encode(['message' => 'Resource tidak ditemukan.']);
            break;
    }
}

function getBarangTersediaData($mysqli) {
    $sql = "
        SELECT 
            b.barang_id,
            b.nama_barang,
            b.kategori,
            b.kondisi_barang,
            -- Rumus: Jumlah awal dikurangi total yang sudah dialokasikan (bukan 'Ditolak')
            (b.jumlah - IFNULL(SUM(d.jumlah_disalurkan), 0)) AS jumlah_tersedia 
        FROM 
            barang_donasi AS b
        LEFT JOIN 
            -- Gabungkan dengan distribusi yang statusnya BUKAN 'Ditolak'
            distribusi_donasi AS d ON b.barang_id = d.barang_id AND d.status_penyaluran != 'Ditolak'
        GROUP BY 
            -- Kelompokkan berdasarkan setiap barang unik
            b.barang_id, b.nama_barang, b.kategori, b.kondisi_barang, b.jumlah
        HAVING 
            -- Tampilkan HANYA jika jumlah tersisa lebih dari 0
            jumlah_tersedia > 0;";
    
    $result = $mysqli->query($sql);
    if (!$result) {
        http_response_code(500);
        echo json_encode(['message' => 'Query error: ' . $mysqli->error]);
        return;
    }
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

function handlePostRequest($mysqli, $resource, $input) {
    $allowed_non_admin_resources = [
        'barang_donasi',
        'ajukan_permintaan_distribusi',
        'update_distribusi_status',
        'selesaikan_distribusi'
    ];

    if ($_SESSION['level_user'] !== 'Admin' && !in_array($resource, $allowed_non_admin_resources)) {
        http_response_code(403);
        echo json_encode(['message' => 'Akses ditolak. Anda tidak memiliki izin untuk aksi ini.']);
        return;
    }

    switch ($resource) {
        case 'donatur':
            addDonatur($mysqli, $input);
            break;
        case 'distributor':
            addDistributor($mysqli, $input);
            break;
        case 'barang_donasi':
            addBarangDonasi($mysqli);
            break;
        case 'update_distribusi_status':
            updateDistribusiStatus($mysqli, $input);
            break;
        case 'selesaikan_distribusi':
            selesaikanDistribusi($mysqli);
            break;
        case 'ajukan_permintaan_distribusi':
            addPermintaanDistribusi($mysqli, $input);
            break;
        default:
            http_response_code(404);
            echo json_encode(['message' => 'Resource tidak ditemukan atau metode POST tidak didukung.']);
            break;
    }
}

function getDashboardCounts($mysqli) {
    $counts = [];
    $counts['donatur'] = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE level_user = 'Donatur'")->fetch_assoc()['total'] ?? 0;
    $counts['distributor'] = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE level_user = 'Distributor'")->fetch_assoc()['total'] ?? 0;
    $counts['barang_donasi'] = $mysqli->query("SELECT COUNT(*) AS total FROM barang_donasi")->fetch_assoc()['total'] ?? 0;
    $counts['distribusi_donasi'] = $mysqli->query("SELECT COUNT(*) AS total FROM distribusi_donasi")->fetch_assoc()['total'] ?? 0;
    echo json_encode($counts);
}

function getDonaturData($mysqli) {
    $result = $mysqli->query("SELECT id_user, nama, email, telepon, tanggal_daftar FROM users WHERE level_user = 'Donatur'");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

function getBarangDonasiData($mysqli) {
    $result = $mysqli->query("SELECT * FROM barang_donasi");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

function getHistoriDonasiDonaturData($mysqli, $donatur_id) {
    $stmt = $mysqli->prepare("SELECT * FROM barang_donasi WHERE donatur_id = ? ORDER BY tanggal_donasi DESC");
    $stmt->bind_param("i", $donatur_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}

function getRiwayatDistribusiDonaturData($mysqli, $donatur_id) {
    $stmt = $mysqli->prepare(
        "SELECT 
            bd.nama_barang,
            dd.jumlah_disalurkan,
            dd.status_penyaluran,
            dd.tanggal_penyaluran,
            distributor.nama AS nama_distributor
         FROM barang_donasi AS bd
         JOIN distribusi_donasi AS dd ON bd.barang_id = dd.barang_id
         JOIN users AS distributor ON dd.distributor_id = distributor.id_user
         WHERE bd.donatur_id = ?
         ORDER BY dd.tanggal_pengajuan DESC"
    );
    $stmt->bind_param("i", $donatur_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}

function addBarangDonasi($mysqli) {
    $donatur_id = $_SESSION['id_user'] ?? null;
    $level_user = $_SESSION['level_user'] ?? '';
    
    if ($level_user === 'Admin' && !empty($_POST['donatur_id'])) {
        $donatur_id = $_POST['donatur_id'];
    }

    if (!$donatur_id) {
        http_response_code(400);
        echo json_encode(['message' => 'ID Donatur tidak valid atau tidak ditemukan.']);
        return;
    }

    $nama_barang = $_POST['nama_barang'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $deskripsi_barang = $_POST['deskripsi_barang'] ?? null;
    $satuan = $_POST['satuan'] ?? '';
    $jumlah = $_POST['jumlah'] ?? null;
    $kondisi_barang = $_POST['kondisi_barang'] ?? '';
    $status_barang = 'Tersedia';
    
    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/barang_donasi/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = uniqid() . '_' . basename($_FILES['foto']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto_path = 'uploads/barang_donasi/' . $file_name;
        }
    }
    
    $stmt = $mysqli->prepare("INSERT INTO barang_donasi (donatur_id, nama_barang, kategori, deskripsi_barang, satuan, jumlah, kondisi_barang, tanggal_donasi, status_barang, foto) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("issssisss", $donatur_id, $nama_barang, $kategori, $deskripsi_barang, $satuan, $jumlah, $kondisi_barang, $status_barang, $foto_path);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Barang donasi berhasil ditambahkan.', 'id' => $mysqli->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Gagal menambahkan barang donasi: ' . $stmt->error]);
    }
    $stmt->close();
}

function addDonatur($mysqli, $input) {
    $nama = $input['nama'] ?? '';
    $email = $input['email'] ?? null;
    $username = $input['username'] ?? '';
    $password = password_hash($input['password'] ?? '', PASSWORD_DEFAULT);
    $telepon = $input['telepon'] ?? null;
    $level_user = 'Donatur';

    $stmt = $mysqli->prepare("INSERT INTO users (nama, email, username, password, telepon, level_user) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $email, $username, $password, $telepon, $level_user);
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Donatur berhasil ditambahkan.', 'id' => $mysqli->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Gagal menambahkan donatur: ' . $stmt->error]);
    }
    $stmt->close();
}

function addDistributor($mysqli, $input) {
    $nama = $input['nama'] ?? '';
    $email = $input['email'] ?? null;
    $username = $input['username'] ?? '';
    $password = password_hash($input['password'] ?? '', PASSWORD_DEFAULT);
    $telepon = $input['telepon'] ?? '';
    $level_user = 'Distributor';

    $jenis_distributor = $input['jenis_distributor'] ?? '';
    $alamat = $input['alamat'] ?? null;
    $deskripsi = $input['deskripsi'] ?? null;

    $mysqli->begin_transaction();

    try {
        $stmt_user = $mysqli->prepare("INSERT INTO users (nama, email, username, password, telepon, level_user) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_user->bind_param("ssssss", $nama, $email, $username, $password, $telepon, $level_user);
        $stmt_user->execute();
        
        $new_user_id = $mysqli->insert_id;
        if ($new_user_id == 0) {
            throw new Exception("Gagal membuat entri user.");
        }
        $stmt_user->close();

        $stmt_dist = $mysqli->prepare("INSERT INTO distributor (distributor_id, jenis_distributor, alamat, deskripsi) VALUES (?, ?, ?, ?)");
        $stmt_dist->bind_param("isss", $new_user_id, $jenis_distributor, $alamat, $deskripsi);
        $stmt_dist->execute();
        $stmt_dist->close();
        
        $mysqli->commit();
        echo json_encode(['message' => 'Distributor berhasil ditambahkan.']);
    } catch (Exception $e) {
        $mysqli->rollback();
        http_response_code(500);
        echo json_encode(['message' => 'Gagal menambahkan distributor: ' . $e->getMessage() . ' | ' . $mysqli->error]);
    }
}

function getDistributorData($mysqli) {
    $sql = "SELECT u.id_user, u.nama, u.email, u.telepon, d.jenis_distributor, d.alamat, u.tanggal_daftar 
            FROM users u
            JOIN distributor d ON u.id_user = d.distributor_id
            WHERE u.level_user = 'Distributor'";
    $result = $mysqli->query($sql);
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

function getPermintaanDistribusiAdminData($mysqli) {
    $sql = "SELECT dd.distribusi_id, bd.nama_barang, u.nama as nama_distributor, dd.jumlah_disalurkan, dd.status_penyaluran
            FROM distribusi_donasi dd
            JOIN barang_donasi bd ON dd.barang_id = bd.barang_id
            JOIN users u ON dd.distributor_id = u.id_user
            WHERE dd.status_penyaluran = 'Menunggu'";
    $result = $mysqli->query($sql);
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

function addPermintaanDistribusi($mysqli, $input) {
    $distributor_id = $_SESSION['id_user'] ?? 0;
    $barang_id = $input['barang_id'] ?? 0;
    $jumlah_diminta = $input['jumlah_disalurkan'] ?? 0;

    if (empty($distributor_id) || empty($barang_id) || empty($jumlah_diminta) || $jumlah_diminta <= 0) {
        http_response_code(400);
        echo json_encode(['message' => 'Data tidak lengkap atau tidak valid.']);
        return;
    }

    $stmt_check = $mysqli->prepare(
        "INSERT INTO distribusi_donasi (barang_id, distributor_id, jumlah_disalurkan, status_penyaluran) 
         VALUES (?, ?, ?, 'Menunggu')"
    );
    $stmt_check->bind_param("iii", $barang_id, $distributor_id, $jumlah_diminta);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $barang = $result_check->fetch_assoc();

    if (!$barang || $jumlah_diminta > $barang['jumlah']) {
        http_response_code(400);
        echo json_encode(['message' => 'Permintaan gagal. Stok tidak mencukupi atau barang tidak lagi tersedia.']);
        return;
    }
    $stmt_check->close();

    $stmt = $mysqli->prepare("INSERT INTO distribusi_donasi (barang_id, distributor_id, jumlah_disalurkan, status_penyaluran) VALUES (?, ?, ?, 'Menunggu')");
    $stmt->bind_param("iii", $barang_id, $distributor_id, $jumlah_diminta);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Permintaan berhasil diajukan.']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Gagal mengajukan permintaan: ' . $stmt->error]);
    }
    $stmt->close();
}

function updateDistribusiStatus($mysqli, $input) {
    $distribusi_id = $input['distribusi_id'] ?? 0;
    $new_status = $input['new_status'] ?? '';
    
    $allowed_statuses = ['Diproses', 'Diproses Distributor', 'Selesai', 'Ditolak'];
    if (empty($distribusi_id) || !in_array($new_status, $allowed_statuses)) {
        http_response_code(400);
        echo json_encode(['message' => 'Aksi atau status tidak valid untuk fungsi ini.']);
        return;
    }

    $sql = "";
    if ($new_status === 'Diproses' || $new_status === 'Ditolak' || $new_status === 'Selesai') {
        if ($_SESSION['level_user'] !== 'Admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Hanya admin yang dapat melakukan aksi ini.']);
            return;
        }

        if ($new_status === 'Selesai') {
            $sql = "UPDATE distribusi_donasi SET status_penyaluran = ?, tanggal_penyaluran = NOW() WHERE distribusi_id = ?";
        } else {
            $sql = "UPDATE distribusi_donasi SET status_penyaluran = ? WHERE distribusi_id = ?";
        }
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $new_status, $distribusi_id);
    } else {
        if ($_SESSION['level_user'] !== 'Distributor') {
            http_response_code(403);
            echo json_encode(['message' => 'Hanya distributor yang dapat melakukan aksi ini.']);
            return;
        }
        $sql = "UPDATE distribusi_donasi SET status_penyaluran = ?, tanggal_penerimaan = NOW() WHERE distribusi_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $new_status, $distribusi_id);
    }

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['message' => 'Status distribusi berhasil diperbarui.']);
        } else {
            echo json_encode(['message' => 'Tidak ada data yang diubah.']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Gagal memperbarui status: ' . $stmt->error]);
    }
    $stmt->close();
}

function selesaikanDistribusi($mysqli) {
    if ($_SESSION['level_user'] !== 'Distributor') {
        http_response_code(403);
        echo json_encode(['message' => 'Hanya distributor yang dapat menyelesaikan distribusi.']);
        return;
    }

    $distribusi_id = $_POST['distribusi_id'] ?? 0;
    $catatan = $_POST['catatan_distribusi'] ?? null;
    $foto_path = null;

    if (empty($distribusi_id)) {
        http_response_code(400);
        echo json_encode(['message' => 'ID Distribusi tidak valid.']);
        return;
    }

    if (isset($_FILES['bukti_foto_penyaluran']) && $_FILES['bukti_foto_penyaluran']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/bukti_penyaluran_selesai/'; 
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = $distribusi_id . '_' . uniqid() . '_' . basename($_FILES['bukti_foto_penyaluran']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['bukti_foto_penyaluran']['tmp_name'], $target_file)) {
            $foto_path = 'uploads/bukti_penyaluran_selesai/' . $file_name;
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Gagal mengunggah file.']);
            return;
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Foto bukti penyaluran wajib diunggah.']);
        return;
    }

    $stmt = $mysqli->prepare(
        "UPDATE distribusi_donasi 
         SET status_penyaluran = 'Menunggu Konfirmasi Selesai', 
             catatan_distribusi = ?, 
             bukti_foto_penyaluran = ? 
         WHERE distribusi_id = ?"
    );
    $stmt->bind_param("ssi", $catatan, $foto_path, $distribusi_id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Bukti berhasil diunggah dan sedang menunggu konfirmasi dari Admin.']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Gagal menyelesaikan distribusi: ' . $stmt->error]);
    }
    $stmt->close();
}

function getPermintaanDistribusiDistributorData($mysqli, $distributor_id) {
    $stmt = $mysqli->prepare(
        "SELECT dd.distribusi_id, bd.nama_barang, dd.jumlah_disalurkan, dd.tanggal_pengajuan, dd.status_penyaluran
         FROM distribusi_donasi dd
         JOIN barang_donasi bd ON dd.barang_id = bd.barang_id
         WHERE dd.distributor_id = ?
         ORDER BY dd.tanggal_penyaluran DESC"
    );
    $stmt->bind_param("i", $distributor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}

function getHistoriDistribusiDonasiAdminData($mysqli) {
    $sql = "SELECT dd.distribusi_id, dd.tanggal_penyaluran, dd.jumlah_disalurkan, dd.status_penyaluran,
                   bd.nama_barang,
                   u.nama AS nama_distributor
            FROM distribusi_donasi dd
            JOIN barang_donasi bd ON dd.barang_id = bd.barang_id
            JOIN users u ON dd.distributor_id = u.id_user
            ORDER BY dd.tanggal_penyaluran DESC";

    $result = $mysqli->query($sql); 
    if (!$result) {
        http_response_code(500);
        echo json_encode(['message' => 'Query error: ' . $mysqli->error]);
        return;
    }
    echo json_encode($result->fetch_all(MYSQLI_ASSOC)); 
}

function getPesanData($mysqli) { 
    $sql = "SELECT * FROM contact_us ORDER BY id_pesan DESC"; 
    $result = $mysqli->query($sql); 
    $data = []; 
    if ($result && $result->num_rows > 0) { 
        while ($row = $result->fetch_assoc()) { 
            $data[] = $row; 
        }
    }
    echo json_encode($data); 
}

function prosesDistribusi($mysqli) {
    if ($_SESSION['level_user'] !== 'Admin') {
        http_response_code(403);
        echo json_encode(['message' => 'Akses ditolak.']);
        return;
    }

    $distribusi_id = $_POST['distribusi_id'] ?? 0;
    $catatan = $_POST['catatan_distribusi'] ?? null;
    $foto_path = null;

    if (empty($distribusi_id)) {
        http_response_code(400);
        echo json_encode(['message' => 'ID Distribusi tidak valid.']);
        return;
    }

    if (isset($_FILES['bukti_foto_penyaluran']) && $_FILES['bukti_foto_penyaluran']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/bukti_penyaluran/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = $distribusi_id . '_' . uniqid() . '_' . basename($_FILES['bukti_foto_penyaluran']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['bukti_foto_penyaluran']['tmp_name'], $target_file)) {
            $foto_path = 'uploads/bukti_penyaluran/' . $file_name;
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Gagal mengunggah file.']);
            return;
        }
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Foto bukti penyaluran wajib diunggah.']);
        return;
    }

    $stmt = $mysqli->prepare(
        "UPDATE distribusi_donasi 
         SET status_penyaluran = 'Diproses', 
             tanggal_penyaluran = NOW(), 
             catatan_distribusi = ?, 
             bukti_foto_penyaluran = ? 
         WHERE distribusi_id = ?"
    );
    $stmt->bind_param("ssi", $catatan, $foto_path, $distribusi_id);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Permintaan berhasil diproses dan disalurkan.']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Gagal memproses permintaan: ' . $stmt->error]);
    }
    $stmt->close();
}

function getPermintaanKonfirmasiSelesaiData($mysqli) {
    $sql = "SELECT dd.*, u.nama as nama_distributor, bd.nama_barang
            FROM distribusi_donasi dd
            JOIN users u ON dd.distributor_id = u.id_user
            JOIN barang_donasi bd ON dd.barang_id = bd.barang_id
            WHERE dd.status_penyaluran = 'Menunggu Konfirmasi Selesai'
            ORDER BY dd.tanggal_pengajuan DESC";
    
    $result = $mysqli->query($sql);
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

function getHistoriDistribusiDistributorData($mysqli, $distributor_id) {
    $stmt = $mysqli->prepare(
        "SELECT 
            dd.distribusi_id, 
            bd.nama_barang, 
            dd.jumlah_disalurkan, 
            dd.tanggal_pengajuan,
            dd.tanggal_penyaluran,
            dd.tanggal_penerimaan,
            dd.status_penyaluran
         FROM distribusi_donasi dd
         JOIN barang_donasi bd ON dd.barang_id = bd.barang_id
         WHERE dd.distributor_id = ?
         ORDER BY dd.tanggal_pengajuan DESC"
    );
    $stmt->bind_param("i", $distributor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}

$mysqli->close();
?>