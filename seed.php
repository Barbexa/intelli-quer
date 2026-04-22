<?php
// seed.php
require 'db.php';
require 'utils.php';

// --- NEW PART: CREATE TABLE IF IT DOESN'T EXIST ---
try {
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
    echo "Table 'profiles' is ready.<br>";
} catch (PDOException $e) {
    die("Table creation failed: " . $e->getMessage());
}
// --------------------------------------------------

$my_file = 'seed_profiles.json';

if (!file_exists($my_file)) {
    die("Error: I cannot find <b>$my_file</b>.");
}

$jsonData = file_get_contents($my_file);
$decodedData = json_decode($jsonData, true);
$profiles = $decodedData['profiles'] ?? [];

if (empty($profiles)) {
    die("Error: The file was found, but the 'profiles' list is empty.");
}

echo "Found " . count($profiles) . " profiles. Starting seed...<br>";

// We use IGNORE here to skip duplicates based on the UNIQUE 'name' field
$sql = "INSERT IGNORE INTO profiles (
            id, name, gender, gender_probability, age, 
            age_group, country_id, country_name, country_probability
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$count = 0;

foreach ($profiles as $profile) {
    try {
        $stmt->execute([
            generateUUIDv7(),
            $profile['name'],
            $profile['gender'],
            $profile['gender_probability'],
            $profile['age'],
            $profile['age_group'],
            $profile['country_id'],
            $profile['country_name'],
            $profile['country_probability']
        ]);
        $count++;
    } catch (Exception $e) {
        continue;
    }
}

echo "<b>Success!</b> Seeded $count records into the database.";