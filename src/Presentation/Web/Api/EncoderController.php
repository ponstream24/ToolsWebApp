<?php

namespace Presentation\Web\Api;

use Application\Services\EncoderApplicationService;
use Infrastructure\Http\JsonResponse;

class EncoderController
{
    private EncoderApplicationService $encoderService;

    public function __construct()
    {
        $this->encoderService = new EncoderApplicationService();
    }

    public function encode(): void
    {
        try {
            // リクエストパラメータの取得
            $data = array_merge($_GET, $_POST);
            
            // 必須パラメータのチェック
            if (!isset($data['text']) || !isset($data['type'])) {
                throw new \InvalidArgumentException('テキストとエンコードタイプを指定してください。');
            }
            
            $text = $data['text'];
            $type = $data['type'];
            
            // エンコード処理
            $result = $this->encoderService->encode($text, $type);
            
            // 成功レスポンス
            JsonResponse::success($result);
        } catch (\Exception $e) {
            // エラーレスポンス
            JsonResponse::error($e->getMessage());
        }
    }

    public function decode(): void
    {
        try {
            // リクエストパラメータの取得
            $data = array_merge($_GET, $_POST);
            
            // 必須パラメータのチェック
            if (!isset($data['text']) || !isset($data['type'])) {
                throw new \InvalidArgumentException('テキストとデコードタイプを指定してください。');
            }
            
            $text = $data['text'];
            $type = $data['type'];
            
            // デコード処理
            $result = $this->encoderService->decode($text, $type);
            
            // 成功レスポンス
            JsonResponse::success($result);
        } catch (\Exception $e) {
            // エラーレスポンス
            JsonResponse::error($e->getMessage());
        }
    }
} 