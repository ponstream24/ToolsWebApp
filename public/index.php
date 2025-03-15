<?php
// エラー表示を本番環境では無効にし、ログに記録する
ini_set('display_errors', 0);
error_reporting(E_ALL);

// グローバルエラーハンドラー
set_error_handler(function ($severity, $message, $file, $line) {
    $logger = \Infrastructure\Logging\Logger::getInstance();
    $logger->error('PHPエラーが発生しました', [
        'severity' => $severity,
        'message' => $message,
        'file' => $file,
        'line' => $line
    ]);
    
    // 深刻なエラーの場合はJSONレスポンスを返す
    if (in_array($severity, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'サーバーエラーが発生しました。後ほど再試行してください。'
        ]);
        exit;
    }
    
    return true;
});

// 例外ハンドラー
set_exception_handler(function (\Throwable $e) {
    $logger = \Infrastructure\Logging\Logger::getInstance();
    $logger->error('未処理の例外が発生しました', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'サーバーエラーが発生しました。後ほど再試行してください。'
    ]);
    exit;
});

// autoloadを追加
require_once __DIR__ . '/../vendor/autoload.php';

use Infrastructure\Config\Config;
use Infrastructure\Cache\RedisCache;
use Infrastructure\Logging\Logger;
use Domain\Services\TotpService;
use Domain\Services\QrCodeService;
use Domain\Services\EncoderService;
use Application\Services\TotpApplicationService;
use Application\Services\EncoderApplicationService;
use Presentation\Web\Controllers\TotpController;
use Presentation\Web\Controllers\QrCodeController;
use Presentation\Web\Controllers\EncoderController;
use Presentation\Web\Router;
use Domain\Models\Tool;

// 設定の読み込み
Config::load('app');

// ベースパスの設定
$basePath = '/ToolsWebApp/public';

// Redisキャッシュのインスタンス作成
$cache = RedisCache::getInstance();

// ロガーのインスタンス作成
$logger = new Logger();

// サービスのインスタンス作成
$totpService = new TotpService($cache, $logger);
$totpAppService = new TotpApplicationService($totpService);
$qrCodeService = new QrCodeService();
$encoderService = new EncoderService();
$encoderAppService = new EncoderApplicationService($encoderService);

// コントローラのインスタンス作成
$totpController = new TotpController($totpAppService);
$qrCodeController = new QrCodeController($qrCodeService);
$encoderController = new EncoderController($encoderAppService);

// ツールの定義
$tools = [
    new Tool(
        'QRコードジェネレーター',
        'テキストやURLからQRコードを生成します。',
        $basePath . '/tool/qr',
        'generator'
    ),
    new Tool(
        'TOTPジェネレーター',
        '2段階認証用のTOTPを生成します。',
        $basePath . '/tool/totp',
        'security'
    ),
    new Tool(
        'テキストエンコーダー/デコーダー',
        'テキストを様々な形式でエンコード・デコードします。',
        $basePath . '/tool/encoder',
        'converter'
    )
];

try {
    // ルーターのインスタンス作成と設定
    $router = new Router($basePath);

    // 通常のルート（ビュー表示用）
    $router->addRoute('/', function() use ($tools, $basePath) {
        require __DIR__ . '/../src/Presentation/Web/Views/tools/index.php';
    });
    $router->addRoute('/tool/qr', [$qrCodeController, 'index']);
    $router->addRoute('/tool/totp', [$totpController, 'index']);
    $router->addRoute('/tool/encoder', [$encoderController, 'index']);

    // APIルート
    $router->addApiRoute('/api/tool/qr', [$qrCodeController, 'generate']);
    $router->addApiRoute('/api/tool/totp', [$totpController, 'generate']);
    $router->addApiRoute('/api/tool/totp/qr', [$totpController, 'generateQr']);
    $router->addApiRoute('/api/tool/totp/verify', [$totpController, 'verify']);
    $router->addApiRoute('/api/tool/encoder/encode', [new \Presentation\Web\Api\EncoderController(), 'encode']);
    $router->addApiRoute('/api/tool/encoder/decode', [new \Presentation\Web\Api\EncoderController(), 'decode']);

    // リクエストの処理
    $router->dispatch();
} catch (\Throwable $e) {
    $logger->error('リクエスト処理中にエラーが発生しました', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    
    if (!headers_sent()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'サーバーエラーが発生しました。後ほど再試行してください。'
        ]);
    }
    exit;
}
?> 