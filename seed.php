<?php
// seed.php
require 'db.php';
require 'utils.php';

// 1. We define the name of the file FIRST.
$my_file = 'seed_profiles.json';

// 2. We check if it exists.
if (!file_exists($my_file)) {
    die("Error: I cannot find <b>$my_file</b>. Make sure it is in the same folder as this script!");
}

// 3. Now we read it.
$jsonData = file_get_contents($my_file);
$decodedData = json_decode($jsonData, true);

// 4. Get the profiles (going inside the "profiles" key we saw earlier)
$profiles = $decodedData['profiles'] ?? [];

if (empty($profiles)) {
    die("Error: The file was found, but the 'profiles' list is empty or formatted wrong.");
}

echo "Found " . count($profiles) . " profiles. Starting seed...<br>";

// 5. Prepare the SQL (ON DUPLICATE KEY UPDATE ensures we don't double-seed)
$sql = "INSERT INTO profiles (
            id, name, gender, gender_probability, age, 
            age_group, country_id, country_name, country_probability
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE name=name";

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
        // This quietly skips people who are already in the database
        continue;
    }
}

echo "<b>Success!</b> Seeded $count records into the database.";