<?php

namespace Tests\Domain\Services;

use PHPUnit\Framework\TestCase;
use Domain\Services\TotpService;
use Domain\ValueObjects\Totp\{
    TotpSecret,
    TotpIssuer,
    TotpAccount
};
use Domain\Exceptions\{
    InvalidTotpSecretException,
    InvalidTotpIssuerException,
    InvalidTotpAccountException
};
use Infrastructure\Cache\RedisCache;
use Infrastructure\Logging\Logger;
use Monolog\Logger as MonologLogger;
use Predis\Client;
use Mockery;

class TotpServiceTest extends TestCase
{
    private \Predis\Client $redisMock;
    private RedisCache $redisCache;
    private TotpService $service;
    private Client $redis;
    private MonologLogger $logger;
    private string $logDir;
    private \Predis\Client $redisClient;
    private TotpService $totpService;

    protected function setUp(): void
    {
        parent::setUp();

        // Redisモックの設定
        $this->redis = Mockery::mock(Client::class);
        $this->redis->shouldReceive('exists')->with(Mockery::type('array'))->andReturn(false);
        $this->redis->shouldReceive('get')->with(Mockery::any())->andReturn(null);
        $this->redis->shouldReceive('set')->with(Mockery::any(), Mockery::any())->andReturn(true);
        $this->redis->shouldReceive('expire')->with(Mockery::any(), Mockery::any())->andReturn(true);
        RedisCache::setInstance($this->redis);

        // Loggerの設定
        $this->logDir = __DIR__ . '/../../../logs/tests';
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
        Logger::setLogDir($this->logDir);
        $this->logger = Logger::getInstance();

        // 個別のテスト用のモック
        $this->redisMock = Mockery::mock(\Predis\Client::class);
        $this->redisMock->shouldReceive('exists')->with(Mockery::type('array'))->andReturn(false);
        $this->redisMock->shouldReceive('get')->with(Mockery::any())->andReturn(null);
        $this->redisMock->shouldReceive('set')->with(Mockery::any(), Mockery::any())->andReturn(true);
        $this->redisMock->shouldReceive('expire')->with(Mockery::any(), Mockery::any())->andReturn(true);
        $this->redisCache = new RedisCache($this->redisMock);
        $this->service = new TotpService($this->redisCache);

        $this->redisClient = Mockery::mock(\Predis\Client::class);
        $this->redisClient->shouldReceive('exists')->with(Mockery::type('array'))->andReturn(false);
        $this->redisClient->shouldReceive('get')->with(Mockery::any())->andReturn(null);
        $this->redisClient->shouldReceive('set')->with(Mockery::any(), Mockery::any())->andReturn(true);
        $this->redisClient->shouldReceive('expire')->with(Mockery::any(), Mockery::any())->andReturn(true);
        $this->totpService = new TotpService($this->redisCache);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
        RedisCache::clearInstance();
        
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

    public function testGenerateTotpUri(): void
    {
        $secret = new TotpSecret('JBSWY3DPEHPK3PXP');
        $issuer = new TotpIssuer('WebTools');
        $account = new TotpAccount('test@example.com');
        $expectedUri = 'otpauth://totp/WebTools:test%40example.com?secret=JBSWY3DPEHPK3PXP&issuer=WebTools';

        $this->redisMock->shouldReceive('set')
            ->with(Mockery::any(), Mockery::any(), Mockery::any())
            ->andReturn(true);

        $uri = $this->service->generateTotpUri($secret, $issuer, $account);

        $this->assertEquals($expectedUri, $uri);
    }

    public function testInvalidSecret(): void
    {
        $this->expectException(InvalidTotpSecretException::class);
        new TotpSecret('invalid');
    }

    public function testInvalidIssuer(): void
    {
        $this->expectException(InvalidTotpIssuerException::class);
        new TotpIssuer('');
    }

    public function testInvalidAccount(): void
    {
        $this->expectException(InvalidTotpAccountException::class);
        new TotpAccount('invalid-email');
    }

    public function testGenerateTotpUriFromCache(): void
    {
        $secret = new TotpSecret('JBSWY3DPEHPK3PXP');
        $issuer = new TotpIssuer('WebTools');
        $account = new TotpAccount('test@example.com');
        $expectedUri = 'otpauth://totp/WebTools:test%40example.com?secret=JBSWY3DPEHPK3PXP&issuer=WebTools';
        $cacheKey = 'totp_uri:' . $secret->value();

        // 個別のテスト用に新しいモックを作成
        $redisMock = Mockery::mock(\Predis\Client::class);
        $redisMock->shouldReceive('exists')->with([$cacheKey])->andReturn(true);
        $redisMock->shouldReceive('get')->with($cacheKey)->andReturn($expectedUri);
        
        $redisCache = new RedisCache($redisMock);
        $service = new TotpService($redisCache);

        $uri = $service->generateTotpUri($secret, $issuer, $account);

        $this->assertEquals($expectedUri, $uri);
    }

    public function testGenerateTotpUriWithoutCache(): void
    {
        $secret = new TotpSecret('JBSWY3DPEHPK3PXP');
        $issuer = new TotpIssuer('WebTools');
        $account = new TotpAccount('test@example.com');
        $expectedUri = 'otpauth://totp/WebTools:test%40example.com?secret=JBSWY3DPEHPK3PXP&issuer=WebTools';
        $cacheKey = 'totp_uri:' . $secret->value();

        // 個別のテスト用に新しいモックを作成
        $redisMock = Mockery::mock(\Predis\Client::class);
        $redisMock->shouldReceive('exists')->with([$cacheKey])->andReturn(false);
        $redisMock->shouldReceive('set')->with($cacheKey, $expectedUri)->andReturn(true);
        $redisMock->shouldReceive('expire')->with($cacheKey, 3600)->andReturn(true);
        
        $redisCache = new RedisCache($redisMock);
        $service = new TotpService($redisCache);

        $uri = $service->generateTotpUri($secret, $issuer, $account);

        $this->assertEquals($expectedUri, $uri);
    }

    public function testValidateSecretSuccess(): void
    {
        $secretValue = 'JBSWY3DPEHPK3PXP';
        $result = $this->service->validateSecret($secretValue);
        $this->assertTrue($result);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP secret validated successfully');
    }

    public function testValidateSecretFailure(): void
    {
        $secretValue = 'invalid';
        $result = $this->service->validateSecret($secretValue);
        $this->assertFalse($result);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/error.log', 'Invalid TOTP secret');
    }

    public function testValidateIssuerSuccess(): void
    {
        $issuerValue = 'WebTools';
        $result = $this->service->validateIssuer($issuerValue);
        $this->assertTrue($result);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP issuer validated successfully');
    }

    public function testValidateIssuerFailure(): void
    {
        $result = $this->service->validateIssuer('');
        $this->assertFalse($result);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/error.log', 'Invalid TOTP issuer');
    }

    public function testValidateAccountSuccess(): void
    {
        $result = $this->service->validateAccount('test@example.com');
        $this->assertTrue($result);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'TOTP account validated successfully');
    }

    public function testValidateAccountFailure(): void
    {
        $result = $this->service->validateAccount('invalid-email');
        $this->assertFalse($result);

        // ログの確認
        $this->assertLogContainsIfExists($this->logDir . '/error.log', 'Invalid TOTP account');
    }
} 