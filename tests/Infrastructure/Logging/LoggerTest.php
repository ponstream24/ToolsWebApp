<?php

namespace Tests\Infrastructure\Logging;

use PHPUnit\Framework\TestCase;
use Infrastructure\Logging\Logger;
use Monolog\Logger as MonologLogger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;

class LoggerTest extends TestCase
{
    private string $logDir;

    protected function setUp(): void
    {
        $this->logDir = __DIR__ . '/../../../logs/tests';
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0777, true);
        }
        Logger::setLogDir($this->logDir);
    }

    protected function tearDown(): void
    {
        // ログディレクトリの削除は環境に依存するので実行しない
        // array_map('unlink', glob($this->logDir . '/*'));
        // rmdir($this->logDir);
        Logger::clearInstance();
    }

    public function testGetInstance(): void
    {
        $logger = Logger::getInstance();
        $this->assertInstanceOf(MonologLogger::class, $logger);
    }

    public function testLoggerHandlers(): void
    {
        $logger = Logger::getInstance();
        $handlers = $logger->getHandlers();

        $this->assertCount(4, $handlers);
        foreach ($handlers as $handler) {
            $this->assertInstanceOf(StreamHandler::class, $handler);
        }
    }

    public function testErrorLogging(): void
    {
        $logger = Logger::getInstance();
        $logger->error('Test error message');
        
        $this->assertLogContainsIfExists($this->logDir . '/error.log', 'Test error message');
    }

    public function testAccessLogging(): void
    {
        $logger = Logger::getInstance();
        $logger->info('Test access message');
        
        $this->assertLogContainsIfExists($this->logDir . '/access.log', 'Test access message');
    }

    public function testAuditLogging(): void
    {
        $logger = Logger::getInstance();
        $logger->notice('Test audit message');
        
        $this->assertLogContainsIfExists($this->logDir . '/audit.log', 'Test audit message');
    }

    // ログファイルの確認をスキップするヘルパーメソッド
    private function assertLogContainsIfExists(string $logPath, string $expected): void
    {
        // ログファイルがなければ空のファイルを作成
        if (!file_exists($logPath)) {
            touch($logPath);
        }
        
        // ログを書き込んだ後、少し待機してファイルの書き込みが完了するのを待つ
        usleep(100000); // 0.1秒待機
        
        $logContent = file_get_contents($logPath);
        $this->assertStringContainsString($expected, $logContent);
    }
} 