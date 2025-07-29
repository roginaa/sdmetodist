<?php
ob_start(); // Tangkap semua output (hindari warning HTML merusak JSON)
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS, GET");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'database_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menyimpan data baru
    if (
        isset($_POST['judul'], $_POST['deskripsi'], $_POST['tanggal'], $_POST['lokasi'], $_POST['kategori'])
    ) {
        $judul = $_POST['judul'];
        $deskripsi = $_POST['deskripsi'];
        $tanggal = $_POST['tanggal'];
        $lokasi = $_POST['lokasi'];
        $kategori = $_POST['kategori'];

        $gambarPath = '';

        // Proses upload gambar
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $gambarName = uniqid() . '_' . basename($_FILES['gambar']['name']);
            $gambarPath = $uploadDir . $gambarName;

            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $gambarPath)) {
                echo json_encode(['status' => 'error', 'message' => 'Gagal upload gambar']);
                exit();
            }
        }

        // Simpan ke database
        $stmt = $koneksi->prepare("INSERT INTO info_sekolah (judul, deskripsi, tanggal, lokasi, kategori, gambar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $judul, $deskripsi, $tanggal, $lokasi, $kategori, $gambarPath);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal insert ke database']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    }

    exit();
}

// Jika GET, tampilkan semua data info sekolah
$sql = "SELECT * FROM info_sekolah ORDER BY tanggal DESC";
$result = $koneksi->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['gambar'] = !empty($row['gambar']) ? 'http://localhost/api/' . $row['gambar'] : null;

    $data[] = [
        'id' => $row['id'],
        'judul' => $row['judul'],
        'deskripsi' => $row['deskripsi'],
        'tanggal' => $row['tanggal'],
        'lokasi' => $row['lokasi'],
        'kategori' => $row['kategori'],
        'gambar' => $row['gambar']
    ];
}
ob_clean(); // Hapus semua output lain sebelum kirim JSON


echo json_encode($data);
