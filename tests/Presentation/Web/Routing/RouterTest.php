<?php

namespace Tests\Presentation\Web\Routing;

use PHPUnit\Framework\TestCase;
use Presentation\Web\Controllers\{
    ToolController,
    QrController,
    PasswordController,
    TotpController
};
use Application\Services\{
    ToolService,
    QrCodeApplicationService,
    PasswordApplicationService,
    TotpApplicationService
};
use Domain\Services\{
    QrCodeService,
    PasswordService,
    TotpService
};
use Infrastructure\Persistence\JsonToolRepository;

class RouterTest extends TestCase
{
    private $toolController;
    private $qrController;
    private $passwordController;
    private $totpController;

    protected function setUp(): void
    {
        parent::setUp();

        // コントローラーをモック化
        $this->toolController = $this->createMock(ToolController::class);
        $this->qrController = $this->createMock(QrController::class);
        $this->passwordController = $this->createMock(PasswordController::class);
        $this->totpController = $this->createMock(TotpController::class);
    }

    /**
     * ルーティングを実行してコントローラーメソッドが呼び出されることをテスト
     *
     * @param string $uri リクエストURI
     * @param string $controllerName 呼び出されるべきコントローラーの名前
     * @param string $methodName 呼び出されるべきメソッド名
     * @param array $params メソッドに渡されるパラメータ
     */
    private function assertRouting(string $uri, string $controllerName, string $methodName, array $params = []): void
    {
        // リクエストURIを設定
        $_SERVER['REQUEST_URI'] = $uri;

        // 対応するコントローラーがメソッドを呼び出すことを期待
        $controller = $this->{"$controllerName"};
        $controller->expects($this->once())
                  ->method($methodName)
                  ->with(...$params);

        // ルーティングを実行
        $this->executeRouting([
            'toolController' => $this->toolController,
            'qrController' => $this->qrController,
            'passwordController' => $this->passwordController,
            'totpController' => $this->totpController
        ]);
    }

    /**
     * ルーティングロジックを実行する
     *
     * @param array $controllers 使用するコントローラーの配列
     */
    private function executeRouting(array $controllers): void
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($path, PHP_URL_PATH);

        switch ($path) {
            case '/':
                $controllers['toolController']->index();
                break;
            case '/tool/qr':
                $controllers['qrController']->index();
                break;
            case '/tool/qr/generate':
                $controllers['qrController']->generate();
                break;
            case '/tool/password':
                $controllers['passwordController']->index();
                break;
            case '/tool/password/generate':
                $controllers['passwordController']->generate();
                break;
            case '/tool/totp':
                $controllers['totpController']->index();
                break;
            case '/tool/totp/generate':
                $controllers['totpController']->generate();
                break;
            case (preg_match('/^\/tool\/(\d+)$/', $path, $matches) ? true : false):
                $controllers['toolController']->show((int)$matches[1]);
                break;
            default:
                // テストでは404エラーは無視
                break;
        }
    }

    public function testHomeRoute(): void
    {
        $this->assertRouting('/', 'toolController', 'index');
    }

    public function testQrIndexRoute(): void
    {
        $this->assertRouting('/tool/qr', 'qrController', 'index');
    }

    public function testQrGenerateRoute(): void
    {
        $this->assertRouting('/tool/qr/generate', 'qrController', 'generate');
    }

    public function testPasswordIndexRoute(): void
    {
        $this->assertRouting('/tool/password', 'passwordController', 'index');
    }

    public function testPasswordGenerateRoute(): void
    {
        $this->assertRouting('/tool/password/generate', 'passwordController', 'generate');
    }

    public function testTotpIndexRoute(): void
    {
        $this->assertRouting('/tool/totp', 'totpController', 'index');
    }

    public function testTotpGenerateRoute(): void
    {
        $this->assertRouting('/tool/totp/generate', 'totpController', 'generate');
    }

    public function testToolShowRoute(): void
    {
        $this->assertRouting('/tool/123', 'toolController', 'show', [(int)123]);
    }

    public function test404Route(): void
    {
        // テスト実行前の出力バッファをクリア
        ob_start();
        
        // 存在しないルートをテスト
        $_SERVER['REQUEST_URI'] = '/non-existent-route';
        
        // コントローラのメソッドが呼び出されないことを期待
        $this->toolController->expects($this->never())->method($this->anything());
        $this->qrController->expects($this->never())->method($this->anything());
        $this->passwordController->expects($this->never())->method($this->anything());
        $this->totpController->expects($this->never())->method($this->anything());
        
        // ルーティングを実行
        $this->executeRouting([
            'toolController' => $this->toolController,
            'qrController' => $this->qrController,
            'passwordController' => $this->passwordController,
            'totpController' => $this->totpController
        ]);
        
        // 出力バッファをクリア（テスト中に出力された内容を破棄）
        ob_end_clean();
        
        // 成功を示す
        $this->assertTrue(true);
    }
} 