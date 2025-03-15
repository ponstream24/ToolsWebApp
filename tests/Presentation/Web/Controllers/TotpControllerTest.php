<?php

namespace Tests\Presentation\Web\Controllers;

use PHPUnit\Framework\TestCase;
use Presentation\Web\Controllers\TotpController;
use Application\Services\TotpApplicationService;
use Infrastructure\Logging\Logger;
use Mockery;
use Application\Exceptions\InvalidTotpSecretException;

class TotpControllerTest extends TestCase
{
    private TotpController $controller;
    private TotpApplicationService $totpApplicationService;
    private string $logDir;

    protected function setUp(): void
    {
        $this->totpApplicationService = Mockery::mock(TotpApplicationService::class);
        $this->controller = new TotpController($this->totpApplicationService);

        // Loggerの設定
        $this->logDir = __DIR__ . '/../../../../logs/tests';
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
        Logger::setLogDir($this->logDir);

        // ヘッダー送信のエラーを防ぐ
        if (!headers_sent()) {
            @header_remove();
        }
    }

    protected function tearDown(): void
    {
        Mockery::close();
        
        // ログディレクトリの削除は環境に依存するので実行しない
        // array_map('unlink', glob($this->logDir . '/*'));
        // rmdir($this->logDir);
        Logger::clearInstance();
    }

    public function testGenerateSuccess(): void
    {
        // POSTリクエストをシミュレート
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['secret'] = 'JBSWY3DPEHPK3PXP';
        $_POST['issuer'] = 'WebTools';
        $_POST['account'] = 'test@example.com';

        $this->totpApplicationService->shouldReceive('generateTotpUri')
            ->once()
            ->with($_POST['secret'], $_POST['issuer'], $_POST['account'])
            ->andReturn([
                'success' => true,
                'uri' => 'otpauth://totp/WebTools:test@example.com?secret=JBSWY3DPEHPK3PXP&issuer=WebTools',
                'secret' => 'JBSWY3DPEHPK3PXP'
            ]);

        // 出力をキャプチャ
        ob_start();
        $this->controller->generate();
        $output = ob_get_clean();

        // JSONデータを検証
        $data = json_decode($output, true);
        $this->assertTrue($data['success']);
        $this->assertEquals('otpauth://totp/WebTools:test@example.com?secret=JBSWY3DPEHPK3PXP&issuer=WebTools', $data['uri']);
        $this->assertEquals('JBSWY3DPEHPK3PXP', $data['secret']);

        // ログを確認
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP generation request received');
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP generation successful');
    }

    public function testGenerateError(): void
    {
        // POSTリクエストをシミュレート
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['secret'] = 'invalid';
        $_POST['issuer'] = 'WebTools';
        $_POST['account'] = 'test@example.com';

        $this->totpApplicationService->shouldReceive('generateTotpUri')
            ->once()
            ->with($_POST['secret'], $_POST['issuer'], $_POST['account'])
            ->andReturn([
                'success' => false,
                'error' => 'Invalid TOTP secret format'
            ]);

        // 出力をキャプチャ
        ob_start();
        $this->controller->generate();
        $output = ob_get_clean();

        // JSONデータを検証
        $data = json_decode($output, true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Invalid TOTP secret format', $data['error']);

        // ログを確認
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP generation request received');
        $this->assertLogContainsIfExists($this->logDir . '/error.log', 'TOTP generation failed');
    }

    public function testGenerateWithInvalidMethod(): void
    {
        // GETリクエストをシミュレート
        $_SERVER['REQUEST_METHOD'] = 'GET';

        // 出力をキャプチャ
        ob_start();
        $this->controller->generate();
        $output = ob_get_clean();

        // JSONデータを検証
        $data = json_decode($output, true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Invalid request method', $data['error']);

        // ログを確認
        $this->assertLogContainsIfExists($this->logDir . '/warning.log', 'Invalid request method for TOTP generation');
    }

    public function testGenerateWithDefaultValues(): void
    {
        // POSTリクエストをシミュレート
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = []; // パラメータなし

        $this->totpApplicationService->shouldReceive('generateTotpUri')
            ->once()
            ->with(null, 'WebTools', 'user@example.com')
            ->andReturn([
                'success' => true,
                'uri' => 'otpauth://totp/WebTools:user@example.com?secret=NEWGENERATEDSECRET&issuer=WebTools',
                'secret' => 'NEWGENERATEDSECRET'
            ]);

        // 出力をキャプチャ
        ob_start();
        $this->controller->generate();
        $output = ob_get_clean();

        // JSONデータを検証
        $data = json_decode($output, true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('uri', $data);
        $this->assertArrayHasKey('secret', $data);

        // ログを確認
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP generation request received');
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP generation successful');
    }

    // ログファイルの確認をスキップするヘルパーメソッド
    private function assertLogContainsIfExists(string $logPath, string $expected): void
    {
        // ログファイルがなければ空のファイルを作成
        if (!file_exists($logPath)) {
            touch($logPath);
        }
        
        // ログが書き込まれるまで少し待機
        usleep(100000); // 0.1秒待機
        
        $logContent = file_get_contents($logPath);
        $this->assertStringContainsString($expected, $logContent);
    }
} 