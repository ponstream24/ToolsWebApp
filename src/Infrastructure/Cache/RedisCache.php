<?php

namespace Infrastructure\Cache;

use Predis\Client;
use Monolog\Logger as MonologLogger;

class RedisCache
{
    private static ?RedisCache $instance = null;
    private ?Client $client = null;
    private MonologLogger $logger;
    private bool $available = false;

    private function __construct()
    {
        $this->logger = \Infrastructure\Logging\Logger::getInstance();
        try {
            $this->client = new Client([
                'scheme' => 'tcp',
                'host'   => 'localhost',
                'port'   => 6379,
                'timeout' => 1.0, // 短いタイムアウトを設定
            ]);
            // 接続テスト
            $this->client->ping();
            $this->available = true;
            $this->logger->info('Redis接続成功');
        } catch (\Exception $e) {
            $this->logger->warning('Redis接続失敗: ' . $e->getMessage());
            $this->available = false;
        }
    }

    public static function getInstance(): RedisCache
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setValue(string $key, string $value, int $ttl = 3600): void
    {
        if (!$this->available) {
            $this->logger->info('Redisは利用不可: setValue操作をスキップ', ['key' => $key]);
            return;
        }

        try {
            $this->client->set($key, $value);
            $this->client->expire($key, $ttl);
        } catch (\Exception $e) {
            $this->logger->error('Redisにデータを設定できませんでした', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getValue(string $key): ?string
    {
        if (!$this->available) {
            $this->logger->info('Redisは利用不可: getValue操作をスキップ', ['key' => $key]);
            return null;
        }

        try {
            $value = $this->client->get($key);
            return $value !== null ? (string)$value : null;
        } catch (\Exception $e) {
            $this->logger->error('Redisからデータを取得できませんでした', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function deleteValue(string $key): void
    {
        if (!$this->available) {
            $this->logger->info('Redisは利用不可: deleteValue操作をスキップ', ['key' => $key]);
            return;
        }

        try {
            $this->client->del([$key]);
        } catch (\Exception $e) {
            $this->logger->error('Redisからデータを削除できませんでした', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function exists(string $key): bool
    {
        if (!$this->available) {
            $this->logger->info('Redisは利用不可: exists操作をスキップ', ['key' => $key]);
            return false;
        }

        try {
            return (bool)$this->client->exists([$key]);
        } catch (\Exception $e) {
            $this->logger->error('Redis existsチェック失敗', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }
} 