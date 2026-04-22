<?php
function generateUUIDv7()
{
    // Get current time in milliseconds (48 bits)
    $milli = (int) (microtime(true) * 1000);
    $milliHex = str_pad(dechex($milli), 12, '0', STR_PAD_LEFT);

    // Generate 12 bits for version (7) and more randomness
    // UUID v7 format: tttttttt-tttt-7xxx-yxxx-xxxxxxxxxxxx
    $randomPart = bin2hex(random_bytes(10));

    $uuid = sprintf(
        '%s-%s-%s-%s-%s',
        substr($milliHex, 0, 8),
        substr($milliHex, 8, 4),
        '7' . substr($randomPart, 0, 3),
        // Variant must be 8, 9, a, or b
        ['8', '9', 'a', 'b'][random_int(0, 3)] . substr($randomPart, 3, 3),
        substr($randomPart, 6, 12)
    );

    return $uuid;
}