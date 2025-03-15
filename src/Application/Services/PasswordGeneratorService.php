<?php

namespace Application\Services;

use Domain\ValueObjects\Password\PasswordLength;
use Domain\ValueObjects\Password\PasswordCharacterSet;

class PasswordGeneratorService
{
    /**
     * 指定された長さと文字セットでパスワードを生成する
     *
     * @param PasswordLength $length パスワードの長さ
     * @param PasswordCharacterSet $characterSet 使用する文字セット
     * @return string 生成されたパスワード
     */
    public function generate(PasswordLength $length, PasswordCharacterSet $characterSet): string
    {
        $chars = '';
        $password = '';
        
        // 文字セットの構築
        if ($characterSet->useUppercase()) {
            $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        
        if ($characterSet->useLowercase()) {
            $chars .= 'abcdefghijklmnopqrstuvwxyz';
        }
        
        if ($characterSet->useNumbers()) {
            $chars .= '0123456789';
        }
        
        if ($characterSet->useSymbols()) {
            $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
        }
        
        // 各文字セットから少なくとも1文字を含めることを保証
        $requiredChars = [];
        
        if ($characterSet->useUppercase()) {
            $requiredChars[] = $this->getRandomChar('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        }
        
        if ($characterSet->useLowercase()) {
            $requiredChars[] = $this->getRandomChar('abcdefghijklmnopqrstuvwxyz');
        }
        
        if ($characterSet->useNumbers()) {
            $requiredChars[] = $this->getRandomChar('0123456789');
        }
        
        if ($characterSet->useSymbols()) {
            $requiredChars[] = $this->getRandomChar('!@#$%^&*()_+-=[]{}|;:,.<>?');
        }
        
        // 必須文字をパスワードに追加
        shuffle($requiredChars);
        $password = implode('', $requiredChars);
        
        // 残りの文字をランダムに生成
        $remainingLength = $length->value() - count($requiredChars);
        
        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $this->getRandomChar($chars);
        }
        
        // パスワードの文字をシャッフル
        return $this->str_shuffle_unicode($password);
    }
    
    /**
     * 指定された文字セットからランダムな1文字を取得
     *
     * @param string $chars 文字セット
     * @return string ランダムな1文字
     */
    private function getRandomChar(string $chars): string
    {
        $index = random_int(0, strlen($chars) - 1);
        return $chars[$index];
    }
    
    /**
     * 文字列をランダムにシャッフル（マルチバイト文字対応）
     *
     * @param string $str シャッフルする文字列
     * @return string シャッフルされた文字列
     */
    private function str_shuffle_unicode(string $str): string
    {
        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        shuffle($chars);
        return implode('', $chars);
    }
} 