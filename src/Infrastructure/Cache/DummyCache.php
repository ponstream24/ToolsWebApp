<?php

namespace Infrastructure\Cache;

use Infrastructure\Logging\Logger;

/**
 * キャッシュを使用しないダミークラス
 */
class DummyCache
{
    private static ?DummyCache $instance = null;
    private Logger $logger;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
    }

    /**
     * シングルトンインスタンスを取得
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * テスト用にインスタンスを設定
     */
    public static function setInstance(DummyCache $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * テスト用にインスタンスをクリア
     */
    public static function clearInstance(): void
    {
        self::$instance = null;
    }

    /**
     * キャッシュが利用可能かどうかを確認
     */
    public function isAvailable(): bool
    {
        return false;
    }

    /**
     * 値を設定（何もしない）
     */
    public function setValue(string $key, $value, int $ttl = 3600): bool
    {
        $this->logger->debug('DummyCache: setValue called (no-op)', [
            'key' => $key,
            'ttl' => $ttl
        ]);
        return true;
    }

    /**
     * 値を取得（常にnullを返す）
     */
    public function getValue(string $key)
    {
        $this->logger->debug('DummyCache: getValue called (no-op)', [
            'key' => $key
        ]);
        return null;
    }

    /**
     * キーが存在するか確認（常にfalseを返す）
     */
    public function exists(string $key): bool
    {
        $this->logger->debug('DummyCache: exists called (no-op)', [
            'key' => $key
        ]);
        return false;
    }

    /**
     * キーを削除（何もしない）
     */
    public function delete(string $key): bool
    {
        $this->logger->debug('DummyCache: delete called (no-op)', [
            'key' => $key
        ]);
        return true;
    }
} 