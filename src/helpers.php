<?php

if (!function_exists('config')) {
    /**
     * 設定値を取得する
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        return Infrastructure\Config\Config::get($key, $default);
    }
}

if (!function_exists('view')) {
    /**
     * ビューをレンダリングする
     *
     * @param string $view
     * @param array $data
     * @return void
     */
    function view(string $view, array $data = [])
    {
        extract($data);
        require __DIR__ . '/Presentation/Web/Views/' . $view . '.php';
    }
} 