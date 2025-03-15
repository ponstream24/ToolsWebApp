<?php

namespace Infrastructure\Http;

class JsonResponse
{
    /**
     * 成功レスポンスを返す
     *
     * @param mixed $data レスポンスデータ
     * @return void
     */
    public static function success($data): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
        exit;
    }

    /**
     * エラーレスポンスを返す
     *
     * @param string $message エラーメッセージ
     * @param int $code エラーコード
     * @return void
     */
    public static function error(string $message, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $message
        ]);
        exit;
    }
} 