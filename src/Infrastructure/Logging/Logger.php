<?php

namespace Infrastructure\Logging;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;

class Logger extends MonologLogger
{
    private static ?Logger $instance = null;

    public function __construct()
    {
        parent::__construct('app');
        
        // ログディレクトリのパス
        $logDir = __DIR__ . '/../../../logs';
        
        // ディレクトリが存在しない場合は作成
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        // フォーマット設定
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
        $formatter = new LineFormatter($output, $dateFormat);
        
        // 標準出力ハンドラー（開発環境用）
        $stdoutHandler = new StreamHandler('php://stdout', MonologLogger::DEBUG);
        $stdoutHandler->setFormatter($formatter);
        
        // ファイルハンドラー
        $fileHandler = new RotatingFileHandler(
            $logDir . '/application.log',
            10,
            MonologLogger::INFO
        );
        $fileHandler->setFormatter($formatter);
        
        // エラーファイルハンドラー
        $errorFileHandler = new RotatingFileHandler(
            $logDir . '/error.log',
            10,
            MonologLogger::ERROR
        );
        $errorFileHandler->setFormatter($formatter);
        
        // プロセッサの追加
        $this->pushProcessor(new IntrospectionProcessor());
        $this->pushProcessor(new WebProcessor());
        
        // ハンドラーの追加
        $this->pushHandler($stdoutHandler);
        $this->pushHandler($fileHandler);
        $this->pushHandler($errorFileHandler);
    }
    
    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
} 