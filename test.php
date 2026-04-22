<?php
require 'db.php';
require 'parser.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$params = $_GET;

// Route 1: Natural Language Search
if (str_ends_with($uri, '/api/profiles/search')) {
    $q = $_GET['q'] ?? '';
    if (!$q)
        sendError("Missing or empty parameter", 400);

    $nlqFilters = parseNLQ($q);
    if (!$nlqFilters)
        sendError("Unable to interpret query", 400);

    $params = array_merge($params, $nlqFilters);

    // Route 2: Standard Filtering
} elseif (str_ends_with($uri, '/api/profiles')) {
    // No special parsing needed, $params already has $_GET

} else {
    // If they hit the root or a wrong URL
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Endpoint not found"]);
    exit;
}

// ... Rest of your Dynamic Query Building code below ...