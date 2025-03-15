<?php

namespace Presentation\Web\Api;

use Application\Services\PasswordGeneratorService;
use Domain\ValueObjects\Password\PasswordLength;
use Domain\ValueObjects\Password\PasswordCharacterSet;
use Infrastructure\Http\JsonResponse;

class PasswordController
{
    private PasswordGeneratorService $passwordGeneratorService;

    public function __construct()
    {
        $this->passwordGeneratorService = new PasswordGeneratorService();
    }

    public function generate(): void
    {
        try {
            // リクエストパラメータの取得
            $data = array_merge($_GET, $_POST);
            
            // パスワード長の取得（デフォルト16）
            $length = isset($data['length']) ? (int)$data['length'] : 16;
            
            // 文字セットの設定
            $useUppercase = isset($data['uppercase']) ? filter_var($data['uppercase'], FILTER_VALIDATE_BOOLEAN) : true;
            $useLowercase = isset($data['lowercase']) ? filter_var($data['lowercase'], FILTER_VALIDATE_BOOLEAN) : true;
            $useNumbers = isset($data['numbers']) ? filter_var($data['numbers'], FILTER_VALIDATE_BOOLEAN) : true;
            $useSymbols = isset($data['symbols']) ? filter_var($data['symbols'], FILTER_VALIDATE_BOOLEAN) : true;
            
            // 少なくとも1つの文字セットが選択されていることを確認
            if (!$useUppercase && !$useLowercase && !$useNumbers && !$useSymbols) {
                throw new \InvalidArgumentException('少なくとも1つの文字セットを選択してください。');
            }
            
            // パスワード長のバリデーション
            $passwordLength = new PasswordLength($length);
            
            // 文字セットの作成
            $characterSet = new PasswordCharacterSet($useUppercase, $useLowercase, $useNumbers, $useSymbols);
            
            // パスワード生成
            $password = $this->passwordGeneratorService->generate($passwordLength, $characterSet);
            
            // 成功レスポンス
            JsonResponse::success($password);
        } catch (\Exception $e) {
            // エラーレスポンス
            JsonResponse::error($e->getMessage());
        }
    }
} 