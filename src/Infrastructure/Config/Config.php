<?php

namespace Infrastructure\Config;

class Config
{
    private static array $config = [];

    public static function load(string $file): void
    {
        $path = __DIR__ . '/../../config/' . $file . '.php';
        if (file_exists($path)) {
            self::$config[$file] = require $path;
        }
    }

    public static function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $config = self::$config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                return $default;
            }
            $config = $config[$k];
        }

        return $config;
    }
} 