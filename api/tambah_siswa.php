<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

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

if ($nama && $nis && $kelas && $jurusan) {
    $stmt = $conn->prepare("INSERT INTO siswa (nama, nis, kelas, jurusan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $nis, $kelas, $jurusan);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Siswa berhasil ditambahkan"]);
    } else {
        echo json_encode(["success" => false, "message" => "Gagal menambahkan siswa"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap"]);
}

$conn->close();
?>
