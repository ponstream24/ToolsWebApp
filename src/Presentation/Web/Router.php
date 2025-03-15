<?php

namespace Presentation\Web;

use Presentation\Web\Controllers\QrCodeController;
use Infrastructure\Logging\Logger;

class Router
{
    /** @var array */
    private $routes = [];
    
    /** @var array */
    private $apiRoutes = [];
    
    /** @var string */
    private $basePath;
    
    /** @var Logger */
    private $logger;

    public function __construct(string $basePath = '/ToolsWebApp/public')
    {
        $this->basePath = $basePath;
        $this->logger = Logger::getInstance();
    }

    public function addRoute(string $path, callable $handler): void
    {
        $this->routes[$path] = $handler;
    }

    public function addApiRoute(string $path, callable $handler): void
    {
        $this->apiRoutes[$path] = $handler;
    }

    public function dispatch(): void
    {
        try {
            $requestUri = $_SERVER['REQUEST_URI'];
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            
            // ベースパスを除去
            $path = str_replace($this->basePath, '', $requestUri);
            
            // URLパラメータを除去
            $path = parse_url($path, PHP_URL_PATH);

            $this->logger->info('リクエストを処理します', [
                'path' => $path,
                'method' => $requestMethod
            ]);

            // APIルートのチェック
            if (isset($this->apiRoutes[$path])) {
                if ($requestMethod !== 'POST') {
                    $this->sendJsonResponse(false, 'Invalid request method');
                    return;
                }
                $handler = $this->apiRoutes[$path];
                if (is_array($handler)) {
                    [$controller, $method] = $handler;
                    echo $controller->$method();
                } else {
                    $handler();
                }
                return;
            }

            // 通常のルートのチェック
            if (isset($this->routes[$path])) {
                if ($requestMethod !== 'GET') {
                    $this->sendJsonResponse(false, 'Invalid request method');
                    return;
                }
                $handler = $this->routes[$path];
                if (is_array($handler)) {
                    [$controller, $method] = $handler;
                    $controller->$method();
                } else {
                    $handler();
                }
                return;
            }

            // ルートが見つからない場合
            $this->logger->warning('ルートが見つかりません', [
                'path' => $path,
                'method' => $requestMethod
            ]);
            $this->send404Response();

        } catch (\Throwable $e) {
            $this->logger->error('ルーティング処理中にエラーが発生しました', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            if (strpos($requestUri, '/api/') !== false) {
                $this->sendJsonResponse(false, 'サーバーエラーが発生しました。');
            } else {
                header('Content-Type: text/html; charset=UTF-8');
                echo 'エラーが発生しました: ' . $e->getMessage();
            }
        }
    }

    private function send404Response(): void
    {
        header('HTTP/1.0 404 Not Found');
        echo 'Page not found';
    }

    private function sendJsonResponse(bool $success, string $message): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'error' => $message
        ]);
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }
} 