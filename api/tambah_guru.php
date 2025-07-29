<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");

include 'database_config.php';

$targetDir = "uploads/";
$fotoName = NULL;

// Buat folder uploads jika belum ada
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

// Proses upload foto jika ada
if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == 0) {
    $fotoName = basename($_FILES["foto"]["name"]);
    $targetFile = $targetDir . $fotoName;

    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
        echo json_encode(["status" => "error", "message" => "Upload foto gagal."]);
        exit;
    }
}

// Ambil data input dari form
$nip = $_POST['nip'] ?? '';
$nama = $_POST['nama'] ?? '';
$mapel = $_POST['mapel'] ?? '';
$nohp = $_POST['nohp'] ?? '';
$jenjang = $_POST['jenjang_pendidikan'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$tanggal_lahir = $_POST['tanggal_lahir'] ?? '';

// Validasi sederhana
if (empty($nip) || empty($nama)) {
    echo json_encode(["status" => "error", "message" => "NIP dan Nama wajib diisi."]);
    exit;
}

// Cek apakah NIP sudah terdaftar
$stmt_check = $conn->prepare("SELECT nip FROM guru WHERE nip = ?");
$stmt_check->bind_param("s", $nip);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "NIP sudah terdaftar."]);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

// Query insert data guru
$stmt = $conn->prepare("INSERT INTO guru (nip, nama, mapel, nohp, jenjang_pendidikan, alamat, tanggal_lahir, foto) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $nip, $nama, $mapel, $nohp, $jenjang, $alamat, $tanggal_lahir, $fotoName);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Data guru berhasil ditambahkan."]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan data: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
