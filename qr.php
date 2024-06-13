<?php
// 
$time_step = 30;

// 
$seed_string = '3UPCMJNI3J72HODLQCPNSIGINCHVHA2G';

// otpauth URI を生成する
$issuer = rawurlencode('Test Issuer');
$accountname = rawurlencode('Test Account Name');

$type = 'totp';
$label = "{$issuer}:{$accountname}";
$parameters = "secret={$seed_string}&issuer={$issuer}&algorithm=SHA1&digits=6&period={$time_step}";

$otpauth_uri = "otpauth://{$type}/{$label}?{$parameters}";

echo $otpauth_uri, PHP_EOL;