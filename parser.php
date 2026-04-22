<?php
// parser.php
function parseNLQ($q)
{
    $q = strtolower(trim($q));
    if (empty($q))
        return null;

    $filters = [];

    // Gender Logic
    if (preg_match('/\b(males?|men)\b/', $q))
        $filters['gender'] = 'male';
    if (preg_match('/\b(females?|women)\b/', $q))
        $filters['gender'] = 'female';

    // Age Group Logic
    foreach (['child', 'teenager', 'adult', 'senior'] as $group) {
        if (str_contains($q, $group))
            $filters['age_group'] = $group;
    }

    // The "Young" Rule (Requirement 4)
    if (str_contains($q, 'young')) {
        $filters['min_age'] = 16;
        $filters['max_age'] = 24;
    }

    // Comparison Logic (e.g., "above 30")
    if (preg_match('/above (\d+)/', $q, $matches))
        $filters['min_age'] = $matches[1];
    if (preg_match('/under (\d+)/', $q, $matches))
        $filters['max_age'] = $matches[1];

    // Country Detection (Simplified Example - Expand as needed)
    $countryMap = ['nigeria' => 'NG', 'kenya' => 'KE', 'angola' => 'AO', 'benin' => 'BJ'];
    foreach ($countryMap as $name => $code) {
        if (str_contains($q, $name))
            $filters['country_id'] = $code;
    }

    return empty($filters) ? null : $filters;
}