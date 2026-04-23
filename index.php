<?php
require 'db.php';
require 'parser.php';

// Essential for grading and frontend access
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$params = $_GET;

// Route 1: Natural Language Search
if (str_ends_with($uri, '/api/profiles/search')) {
    $q = $_GET['q'] ?? '';
    if (!$q) {
        sendError("Missing or empty parameter", 400);
    }

    $nlqFilters = parseNLQ($q);
    if (!$nlqFilters) {
        sendError("Unable to interpret query", 400);
    }

    // Merge the interpreted English filters into our parameters
    $params = array_merge($params, $nlqFilters);

    // Route 2: Standard Filtering
} elseif (str_ends_with($uri, '/api/profiles')) {
    // No extra parsing needed here, we just use the raw $_GET params

} else {
    // If they hit a URL that doesn't match our API paths
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Endpoint not found"]);
    exit;
}

// --- Dynamic Query Building ---
$sql = "SELECT * FROM profiles";
$where = [];
$values = [];

$filterDefinitions = [
    'gender' => 'gender = ?',
    'age_group' => 'age_group = ?',
    'country_id' => 'country_id = ?',
    'min_age' => 'age >= ?',
    'max_age' => 'age <= ?',
    'min_gender_probability' => 'gender_probability >= ?',
    'min_country_probability' => 'country_probability >= ?'
];

foreach ($filterDefinitions as $key => $clause) {
    if (isset($params[$key]) && $params[$key] !== '') {
        $where[] = $clause;
        $values[] = $params[$key];
    }
}

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Sorting logic
$sortable = ['age', 'created_at', 'gender_probability'];
$sort_by = in_array($params['sort_by'] ?? '', $sortable) ? $params['sort_by'] : 'created_at';
$order = (strtoupper($params['order'] ?? '') === 'DESC') ? 'DESC' : 'ASC';
$sql .= " ORDER BY $sort_by $order";

// Pagination logic
$limit = isset($params['limit']) ? min((int) $params['limit'], 50) : 10;
$page = isset($params['page']) ? max((int) $params['page'], 1) : 1;
$offset = ($page - 1) * $limit;

// Get Total for metadata (needed for the response structure)
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM (" . str_replace("SELECT *", "SELECT 1", $sql) . ") as sub");
$countStmt->execute($values);
$total = $countStmt->fetchColumn();

// Final Query with Limit and Offset
$sql .= " LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($values);
$results = $stmt->fetchAll();

// Mandatory Response Structure
echo json_encode([
    "status" => "success",
    "page" => (int) $page,
    "limit" => (int) $limit,
    "total" => (int) $total,
    "data" => $results
]);