<?php

namespace Presentation\Web\Routing;

use Presentation\Web\Controllers\{
    ToolController,
    QrController,
    PasswordController,
    TotpController
};

/**
 * アプリケーションのルーティングを処理するクラス
 */
class Router
{
    private ToolController $toolController;
    private QrController $qrController;
    private PasswordController $passwordController;
    private TotpController $totpController;

    /**
     * コントローラーをDIで注入
     */
    public function __construct(
        ToolController $toolController,
        QrController $qrController,
        PasswordController $passwordController,
        TotpController $totpController
    ) {
        $this->toolController = $toolController;
        $this->qrController = $qrController;
        $this->passwordController = $passwordController;
        $this->totpController = $totpController;
    }

    /**
     * リクエストURIに基づいて適切なコントローラーとアクションを呼び出す
     *
     * @param string|null $uri リクエストURI（nullの場合はグローバル変数から取得）
     * @return bool ルーティングが成功したかどうか
     */
    public function dispatch(?string $uri = null): bool
    {
        // URIが指定されていない場合は$_SERVER['REQUEST_URI']から取得
        $path = $uri ?? ($_SERVER['REQUEST_URI'] ?? '/');
        
        // パスのみを抽出（クエリパラメータを除外）
        $path = parse_url($path, PHP_URL_PATH);
        
        // ベースパスを取り除く
        $basePath = '/ToolsWebApp/public';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        // 空のパスを '/' に変換
        if (empty($path)) {
            $path = '/';
        }

        // デバッグ出力
        error_log("処理されたパス: " . $path);

        // 適切なコントローラとアクションにルーティング
        switch ($path) {
            case '/':
                $this->toolController->index();
                break;
            case '/tool/qr':
                $this->qrController->index();
                break;
            case '/tool/qr/generate':
                $this->qrController->generate();
                break;
            case '/tool/password':
                $this->passwordController->index();
                break;
            case '/tool/password/generate':
                $this->passwordController->generate();
                break;
            case '/tool/totp':
                $this->totpController->index();
                break;
            case '/tool/totp/generate':
                $this->totpController->generate();
                break;
            case (preg_match('/^\/tool\/(\d+)$/', $path, $matches) ? true : false):
                $this->toolController->show((int)$matches[1]);
                break;
            default:
                $this->handleNotFound();
                return false;
        }
        
        return true;
    }

    /**
     * 404 Not Foundエラーを処理
     */
    private function handleNotFound(): void
    {
        // テスト環境ではヘッダー送信をスキップ
        if (php_sapi_name() !== 'cli') {
            header('HTTP/1.0 404 Not Found');
        }
        echo '404 Not Found - パス: ' . $_SERVER['REQUEST_URI'] ?? 'unknown';
    }
} 