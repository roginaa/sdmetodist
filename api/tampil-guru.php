<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'database_config.php'; // pastikan file ini berisi koneksi ke $conn

// Ambil aksi dari parameter GET
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'list_guru') {
    // Periksa koneksi database
    if ($conn->connect_error) {
        echo json_encode(["status" => "error", "message" => "Koneksi database gagal: " . $conn->connect_error]);
        exit;
    }

    $res = $conn->query("SELECT * FROM guru");

    if (!$res) {
        echo json_encode(["status" => "error", "message" => "Query gagal: " . $conn->error]);
        exit;
    }

    $data = [];

    while ($row = $res->fetch_assoc()) {
        $row['foto'] = isset($row['foto']) && $row['foto'] != '' 
            ? "http://" . $_SERVER['HTTP_HOST'] . "/api/uploads/" . $row['foto'] 
            : null;
        $data[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid action: " . $action]);
}
?>
