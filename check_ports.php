<?php
// コマンドライン引数からIPアドレスまたはドメイン名を取得
if ($argc != 2) {
    echo "Usage: php check_ports.php <IP Address or Domain>\n";
    exit(1);
}

$input = $argv[1];

// ドメインが入力された場合、IPアドレスに変換
if (filter_var($input, FILTER_VALIDATE_IP)) {
    $ip = $input;
} else {
    $ip = gethostbyname($input);
    if ($ip === $input) {
        echo "Error: Unable to resolve domain to IP address.\n";
        exit(1);
    }
}

// IPアドレスを表示
echo "Scanning IP: $ip\n";

// 出力バッファリングを開始
ob_start();

function checkPorts($ip, $startPort, $endPort) {
    for ($port = $startPort; $port <= $endPort; $port++) {
        // 現在のポート番号をステータスとして表示
        echo "Checking port: $port\r";
        ob_flush();
        flush();

        $connection = @fsockopen($ip, $port, $errno, $errstr, 0.5);
        if (is_resource($connection)) {
            // 開いているポートのみ表示
            echo "Port $port is open\n";
            fclose($connection);
        }

        // ステータスの行を再表示
        echo "Checking port: $port\r";
        ob_flush();
        flush();
    }
}

$startPort = 1;    // スキャンする開始ポート
$endPort = 65535;  // スキャンする終了ポート

checkPorts($ip, $startPort, $endPort);

// 最後のステータス行をクリア
echo str_repeat(' ', 30) . "\r";
ob_end_flush();
?>
