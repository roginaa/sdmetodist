<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'database_config.php';

$data = json_decode(file_get_contents("php://input"));

$nama = $data->nama ?? '';
$nis = $data->nis ?? '';
$kelas = $data->kelas ?? '';
$jurusan = $data->jurusan ?? '';
$mapel = $data->mapel ?? '';

// Validasi data kosong
if (!$nama || !$nis || !$kelas || !$jurusan || !$mapel) {
    echo json_encode(["success" => false, "message" => "Semua data wajib diisi."]);
    exit();
}

// Validasi apakah siswa terdaftar
$stmt = $conn->prepare("SELECT * FROM siswa WHERE nama = ? AND nis = ? AND kelas = ? AND jurusan = ?");
$stmt->bind_param("ssss", $nama, $nis, $kelas, $jurusan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Siswa tidak terdaftar."]);
    $stmt->close();
    $conn->close();
    exit();
}

// Cek apakah sudah absen hari ini untuk mapel yang sama
$stmtCek = $conn->prepare("SELECT * FROM absensi_siswa WHERE nis = ? AND mapel = ? AND DATE(waktu) = CURDATE()");
$stmtCek->bind_param("ss", $nis, $mapel);
$stmtCek->execute();
$resCek = $stmtCek->get_result();

if ($resCek->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Siswa sudah absen untuk mapel ini hari ini."]);
    $stmtCek->close();
    $stmt->close();
    $conn->close();
    exit();
}

// Simpan data absensi
$stmt_insert = $conn->prepare("INSERT INTO absensi_siswa (nama, nis, kelas, jurusan, mapel, waktu) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt_insert->bind_param("sssss", $nama, $nis, $kelas, $jurusan, $mapel);

if ($stmt_insert->execute()) {
    echo json_encode(["success" => true, "message" => "Absensi berhasil disimpan."]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan absensi."]);
}

// Tutup koneksi
$stmt_insert->close();
$stmtCek->close();
$stmt->close();
$conn->close();
?>
