<?php
// db.php
$host = 'db.pxxl.pro'; // The universal internal address
$port = '44318';      // The standard internal MySQL port
$db = 'pxxldb_moa3zm57f51898a';
$user = 'pxxluser_moa3zm5607ac023';
$pass = '67a111991c4a241117646a565accbe84d2c08a85c8f79abb3944bb1a0ef6d753';
try {
    // We explicitly include the port 3306 for the internal network

    $conn = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "error",
        "message" => "Connection Failed: " . $e->getMessage(),
        "debug_info" => "Connecting to $host on port $port"
    ]);
    exit;
}


// Global Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

function sendError($msg, $code = 400)
{
    http_response_code($code);
    echo json_encode(["status" => "error", "message" => $msg]);
    exit;
}