<?php

namespace Domain\Services;

use Domain\Exceptions\EncoderException;

class EncoderService
{
    /**
     * テキストをエンコードする
     *
     * @param string $text エンコードするテキスト
     * @param string $type エンコードタイプ
     * @return string エンコードされたテキスト
     * @throws EncoderException エンコード処理中にエラーが発生した場合
     */
    public function encode(string $text, string $type): string
    {
        switch ($type) {
            case 'base64':
                return base64_encode($text);
                
            case 'url':
                return urlencode($text);
                
            case 'html':
                return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                
            case 'json':
                return json_encode($text, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                
            case 'hex':
                return $this->textToHex($text);
                
            case 'binary':
                return $this->textToBinary($text);
                
            case 'md5':
                return md5($text);
                
            case 'sha1':
                return sha1($text);
                
            case 'sha256':
                return hash('sha256', $text);
                
            default:
                throw new EncoderException('サポートされていないエンコードタイプです: ' . $type);
        }
    }
    
    /**
     * テキストをデコードする
     *
     * @param string $text デコードするテキスト
     * @param string $type デコードタイプ
     * @return string デコードされたテキスト
     * @throws EncoderException デコード処理中にエラーが発生した場合
     */
    public function decode(string $text, string $type): string
    {
        switch ($type) {
            case 'base64':
                $decoded = base64_decode($text, true);
                if ($decoded === false) {
                    throw new EncoderException('無効なBase64エンコードされた文字列です。');
                }
                return $decoded;
                
            case 'url':
                return urldecode($text);
                
            case 'html':
                return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                
            case 'json':
                // JSON文字列をデコード
                $decoded = json_decode('"' . $text . '"');
                if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                    throw new EncoderException('無効なJSONエンコードされた文字列です: ' . json_last_error_msg());
                }
                return $decoded;
                
            case 'hex':
                return $this->hexToText($text);
                
            case 'binary':
                return $this->binaryToText($text);
                
            case 'md5':
            case 'sha1':
            case 'sha256':
                throw new EncoderException('ハッシュ関数はデコードできません。');
                
            default:
                throw new EncoderException('サポートされていないデコードタイプです: ' . $type);
        }
    }
    
    /**
     * テキストを16進数に変換
     *
     * @param string $text 変換するテキスト
     * @return string 16進数文字列
     */
    private function textToHex(string $text): string
    {
        $hex = '';
        $length = strlen($text);
        
        for ($i = 0; $i < $length; $i++) {
            $hex .= bin2hex($text[$i]) . ' ';
        }
        
        return trim($hex);
    }
    
    /**
     * 16進数をテキストに変換
     *
     * @param string $hex 16進数文字列
     * @return string 変換されたテキスト
     * @throws EncoderException 無効な16進数文字列の場合
     */
    private function hexToText(string $hex): string
    {
        // スペースを削除
        $hex = str_replace(' ', '', $hex);
        
        // 16進数文字列の長さが偶数でない場合はエラー
        if (strlen($hex) % 2 !== 0) {
            throw new EncoderException('無効な16進数文字列です。');
        }
        
        $text = '';
        
        for ($i = 0; $i < strlen($hex); $i += 2) {
            $text .= chr(hexdec(substr($hex, $i, 2)));
        }
        
        return $text;
    }
    
    /**
     * テキストを2進数に変換
     *
     * @param string $text 変換するテキスト
     * @return string 2進数文字列
     */
    private function textToBinary(string $text): string
    {
        $binary = '';
        $length = strlen($text);
        
        for ($i = 0; $i < $length; $i++) {
            $binary .= str_pad(decbin(ord($text[$i])), 8, '0', STR_PAD_LEFT) . ' ';
        }
        
        return trim($binary);
    }
    
    /**
     * 2進数をテキストに変換
     *
     * @param string $binary 2進数文字列
     * @return string 変換されたテキスト
     * @throws EncoderException 無効な2進数文字列の場合
     */
    private function binaryToText(string $binary): string
    {
        // スペースで分割
        $binaryValues = explode(' ', trim($binary));
        $text = '';
        
        foreach ($binaryValues as $value) {
            // 空の値はスキップ
            if (empty($value)) {
                continue;
            }
            
            // 2進数文字列が8の倍数でない場合はエラー
            if (strlen($value) % 8 !== 0) {
                throw new EncoderException('無効な2進数文字列です。');
            }
            
            // 2進数を10進数に変換し、文字に変換
            $text .= chr(bindec($value));
        }
        
        return $text;
    }
} 