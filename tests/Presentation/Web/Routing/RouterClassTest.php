<?php

namespace Tests\Presentation\Web\Routing;

use PHPUnit\Framework\TestCase;
use Presentation\Web\Routing\Router;
use Presentation\Web\Controllers\{
    ToolController,
    QrController,
    PasswordController,
    TotpController
};

class RouterClassTest extends TestCase
{
    private $toolController;
    private $qrController;
    private $passwordController;
    private $totpController;
    private $router;

    protected function setUp(): void
    {
        parent::setUp();

        // コントローラーをモック化
        $this->toolController = $this->createMock(ToolController::class);
        $this->qrController = $this->createMock(QrController::class);
        $this->passwordController = $this->createMock(PasswordController::class);
        $this->totpController = $this->createMock(TotpController::class);

        // ルーターを作成
        $this->router = new Router(
            $this->toolController,
            $this->qrController,
            $this->passwordController,
            $this->totpController
        );
    }

    /**
     * ルーティングが正常に動作することをテスト
     *
     * @param string $uri リクエストURI
     * @param string $controller 呼び出されるべきコントローラー
     * @param string $method 呼び出されるべきメソッド
     * @param array $params メソッドに渡されるパラメータ
     *
     * @dataProvider routeProvider
     */
    public function testRouting(string $uri, string $controller, string $method, array $params = []): void
    {
        // テスト対象のコントローラーに期待を設定
        $this->{$controller}->expects($this->once())
            ->method($method)
            ->with(...$params);

        // その他のコントローラーは呼び出されないことを期待
        $controllers = ['toolController', 'qrController', 'passwordController', 'totpController'];
        foreach ($controllers as $mockController) {
            if ($mockController !== $controller) {
                $this->{$mockController}->expects($this->never())
                    ->method($this->anything());
            }
        }

        // ルーティングを実行
        $this->assertTrue($this->router->dispatch($uri));
    }

    /**
     * 存在しないルートが404エラーとして処理されることをテスト
     */
    public function test404Routing(): void
    {
        // 出力バッファを開始
        ob_start();

        // すべてのコントローラーは呼び出されないことを期待
        $this->toolController->expects($this->never())->method($this->anything());
        $this->qrController->expects($this->never())->method($this->anything());
        $this->passwordController->expects($this->never())->method($this->anything());
        $this->totpController->expects($this->never())->method($this->anything());

        // ルーティングを実行
        $this->assertFalse($this->router->dispatch('/non-existent-route'));

        // バッファをクリア
        ob_end_clean();
    }

    /**
     * テスト用のルートデータを提供
     */
    public function routeProvider(): array
    {
        return [
            'Home route' => ['/', 'toolController', 'index'],
            'QR index route' => ['/tool/qr', 'qrController', 'index'],
            'QR generate route' => ['/tool/qr/generate', 'qrController', 'generate'],
            'Password index route' => ['/tool/password', 'passwordController', 'index'],
            'Password generate route' => ['/tool/password/generate', 'passwordController', 'generate'],
            'TOTP index route' => ['/tool/totp', 'totpController', 'index'],
            'TOTP generate route' => ['/tool/totp/generate', 'totpController', 'generate'],
            'Tool show route' => ['/tool/123', 'toolController', 'show', [(int)123]],
        ];
    }
} 