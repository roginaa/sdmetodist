<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'database_config.php';

$nip = $_POST['nip'] ?? '';
$nama = $_POST['nama'] ?? '';
$mapel = $_POST['mapel'] ?? '';
$jam = $_POST['jam'] ?? '';
$hari = $_POST['hari'] ?? '';
$status = $_POST['status'] ?? '';

if (!$nip || !$nama) {
    echo json_encode(['status' => false, 'message' => 'NIP dan Nama wajib diisi']);
    exit;
}

// Cek apakah guru terdaftar
$stmt = $conn->prepare("SELECT nip, nama FROM guru WHERE nip = ? AND nama = ?");
$stmt->bind_param("ss", $nip, $nama);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => false, 'message' => 'Guru tidak terdaftar']);
    exit;
}

// Cek apakah sudah absen hari ini
$tanggal = date('Y-m-d');
$cek = $conn->prepare("SELECT id FROM absensi WHERE guru_nip = ? AND tanggal = ?");
$cek->bind_param("ss", $nip, $tanggal);
$cek->execute();
$hasilCek = $cek->get_result();

if ($hasilCek->num_rows > 0) {
    echo json_encode(['status' => false, 'message' => 'Anda sudah absen hari ini']);
    exit;
}

// Simpan absensi
$waktu = date('H:i:s');
$insert = $conn->prepare("INSERT INTO absensi (guru_nip, tanggal, waktu, mapel, jam, hari, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
$insert->bind_param("sssssss", $nip, $tanggal, $waktu, $mapel, $jam, $hari, $status);

if ($insert->execute()) {
    echo json_encode(['status' => true, 'message' => 'Absensi berhasil']);
} else {
    echo json_encode(['status' => false, 'message' => 'Gagal menyimpan absensi']);
}
?>

