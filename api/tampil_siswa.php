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

$sql = "SELECT * FROM siswa";
if ($kelas && $jurusan) {
    $sql .= " WHERE kelas='$kelas' AND jurusan='$jurusan'";
}
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
