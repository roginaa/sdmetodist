<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


$data = json_decode(file_get_contents("php://input"));

$username = $data->username ?? '';
$password = $data->password ?? '';

if ($username === 'admin' && $password === 'admin123') {
    echo json_encode(["success" => true, "message" => "Login berhasil"]);
} else {
    echo json_encode(["success" => false, "message" => "Login gagal"]);
}
?>

