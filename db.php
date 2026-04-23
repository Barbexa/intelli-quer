<?php
// db.php
$host = 'db.pxxl.pro';
$port = '44318';
$db = 'pxxldb_moa3zm57f51898a';
$user = 'pxxluser_moa3zm5607ac023';
$pass = '67a111991c4a241117646a565accbe84d2c08a85c8f79abb3944bb1a0ef6d753';

try {
    // We rename $conn to $pdo to match your index.php
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // 🚀 CRITICAL: This builds the table so index.php won't crash on line 77
    $createTableSql = "
    CREATE TABLE IF NOT EXISTS profiles (
        id CHAR(36) PRIMARY KEY,
        name VARCHAR(255) UNIQUE,
        gender VARCHAR(10),
        gender_probability FLOAT,
        age INT,
        age_group VARCHAR(20),
        country_id VARCHAR(2),
        country_name VARCHAR(100),
        country_probability FLOAT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;";

    $pdo->exec($createTableSql);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Connection Failed: " . $e->getMessage()
    ]);
    exit;
}

// Global Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Helper function for errors
if (!function_exists('sendError')) {
    function sendError($msg, $code = 400)
    {
        http_response_code($code);
        echo json_encode(["status" => "error", "message" => $msg]);
        exit;
    }
}