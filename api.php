<?php
require_once 'inclode/totp.php';

header('Content-Type: application/json');

$seed = $_GET['seed'] ?? '';

$totp = new Totp();

if (!empty($seed)) {
    $totp->setSeed16($seed);
}

$totpCode = $totp->getTOTPKeyNow();

echo json_encode(['totp' => $totpCode]);
