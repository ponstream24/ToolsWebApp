<?php

namespace Domain\Services;

use Domain\ValueObjects\Password\{
    PasswordLength,
    PasswordCharacterSet
};

class PasswordService
{
    public function generate(
        PasswordLength $length,
        PasswordCharacterSet $characterSet
    ): string {
        $characters = $characterSet->value();
        $password = '';
        
        // 各文字セットから最低1文字を確保
        foreach ($characterSet->getRequiredCharacters() as $char) {
            $password .= $char;
        }
        
        // 残りの文字をランダムに生成
        $remainingLength = $length->value() - strlen($password);
        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        // 生成したパスワードをシャッフル
        return str_shuffle($password);
    }
} 