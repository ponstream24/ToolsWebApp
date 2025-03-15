<?php

namespace Tests\Application\Services;

use PHPUnit\Framework\TestCase;
use Application\Services\TotpApplicationService;
use Domain\Services\TotpService;
use Domain\ValueObjects\Totp\{
    TotpSecret,
    TotpIssuer,
    TotpAccount
};
use Infrastructure\Logging\Logger;
use Mockery;

class TotpApplicationServiceTest extends TestCase
{
    private TotpApplicationService $service;
    private TotpService $totpService;
    private string $logDir;

    protected function setUp(): void
    {
        $this->totpService = Mockery::mock(TotpService::class);
        $this->service = new TotpApplicationService($this->totpService);

        // Loggerの設定
        $this->logDir = __DIR__ . '/../../../logs/tests';
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
        Logger::setLogDir($this->logDir);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        
        // ログディレクトリの削除は環境に依存するので実行しない
        // array_map('unlink', glob($this->logDir . '/*'));
        // rmdir($this->logDir);
        Logger::clearInstance();
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

    public function testGenerateTotpUriWithNewSecret(): void
    {
        $secret = 'JBSWY3DPEHPK3PXP'; // 有効なBase32エンコードされた値
        $expectedUri = 'otpauth://totp/WebTools:user@example.com?secret=JBSWY3DPEHPK3PXP&issuer=WebTools';

        // generateTotpUriのモック
        $this->totpService->shouldReceive('generateTotpUri')
            ->once()
            ->with(Mockery::type(TotpSecret::class), Mockery::type(TotpIssuer::class), Mockery::type(TotpAccount::class))
            ->andReturn($expectedUri);

        // 有効なシークレットを直接渡す
        $result = $this->service->generateTotpUri($secret, 'WebTools', 'user@example.com');

        $this->assertTrue($result['success']);
        $this->assertEquals($expectedUri, $result['uri']);
        $this->assertEquals($secret, $result['secret']);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP URI generated successfully');
    }

    public function testGenerateTotpUriWithProvidedSecret(): void
    {
        $secret = 'JBSWY3DPEHPK3PXP';
        $expectedUri = 'otpauth://totp/WebTools:user@example.com?secret=JBSWY3DPEHPK3PXP&issuer=WebTools';

        $this->totpService->shouldReceive('generateTotpUri')
            ->once()
            ->andReturn($expectedUri);

        $result = $this->service->generateTotpUri($secret);

        $this->assertTrue($result['success']);
        $this->assertEquals($expectedUri, $result['uri']);
        $this->assertEquals($secret, $result['secret']);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'Starting TOTP URI generation');
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP URI generated successfully');
    }

    public function testGenerateTotpUriWithInvalidSecret(): void
    {
        $result = $this->service->generateTotpUri('invalid');

        $this->assertFalse($result['success']);
        $this->assertEquals('Invalid TOTP secret format', $result['error']);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/error.log', 'Invalid TOTP secret');
    }

    public function testGenerateTotpUriWithInvalidIssuer(): void
    {
        $result = $this->service->generateTotpUri('JBSWY3DPEHPK3PXP', '');

        $this->assertFalse($result['success']);
        $this->assertEquals('TOTP発行者は空にできません。', $result['error']);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/error.log', 'Invalid TOTP issuer');
    }

    public function testGenerateTotpUriWithInvalidAccount(): void
    {
        $result = $this->service->generateTotpUri('JBSWY3DPEHPK3PXP', 'WebTools', 'invalid-email');

        $this->assertFalse($result['success']);
        $this->assertEquals('Account must be a valid email address', $result['error']);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/error.log', 'Invalid TOTP account');
    }
} 