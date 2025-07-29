<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
include 'database_config.php';

$kelas = $_GET['kelas'] ?? '';
$jurusan = $_GET['jurusan'] ?? '';
$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

// Siapkan query dasar dengan placeholder
$sql = "SELECT a.id, s.nama, s.kelas, s.jurusan, DATE(a.waktu) as tanggal, a.keterangan 
        FROM absensi_siswa a 
        JOIN siswa s ON a.siswa_id = s.id
        WHERE DATE(a.waktu) = ?";

$params = [$tanggal];
$types = "s";

if ($kelas && $jurusan) {
    $sql .= " AND s.kelas = ? AND s.jurusan = ?";
    $params[] = $kelas;
    $params[] = $jurusan;
    $types .= "ss";
}

// Prepare statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare SQL statement']);
    exit;
}

// Bind parameters dynamically
$stmt->bind_param($types, ...$params);

$stmt->execute();

$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>
