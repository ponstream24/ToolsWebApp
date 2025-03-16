<?php

namespace Domain\Services;

use Domain\ValueObjects\Totp\{
    TotpSecret,
    TotpIssuer,
    TotpAccount
};
use Infrastructure\Cache\DummyCache;
use Infrastructure\Logging\Logger;
use Monolog\Logger as MonologLogger;

class TotpService
{
    /** @var DummyCache */
    private $cache;
    /** @var MonologLogger */
    private $logger;
    private const TOTP_CACHE_PREFIX = 'totp_secret_';

    public function __construct(DummyCache $cache, MonologLogger $logger)
    {
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function generateTotpUri(?string $secret = null, string $issuer = 'WebTools', string $account = 'user@example.com'): array
    {
        try {
            // シークレットの処理
            if ($secret === null) {
                // 新しいシークレットを生成
                $secret = $this->generateRandomSecret();
                $this->logger->info('新しいTOTPシークレットを生成しました', [
                    'secret' => $secret
                ]);
            }

            // 発行者とアカウントの検証
            if (empty($issuer) || strlen($issuer) > 64) {
                $this->logger->error('無効な発行者名です', [
                    'issuer' => $issuer
                ]);
                return [
                    'success' => false,
                    'error' => '発行者名は1〜64文字である必要があります。'
                ];
            }

            if (empty($account) || strlen($account) > 64) {
                $this->logger->error('無効なアカウント名です', [
                    'account' => $account
                ]);
                return [
                    'success' => false,
                    'error' => 'アカウント名は1〜64文字である必要があります。'
                ];
            }

            // TOTPのURIを生成
            $escapedIssuer = rawurlencode($issuer);
            $escapedAccount = rawurlencode($account);
            $uri = sprintf(
                'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
                $escapedIssuer,
                $escapedAccount,
                $secret,
                $escapedIssuer
            );

            return [
                'success' => true,
                'uri' => $uri,
                'secret' => $secret
            ];
        } catch (\Exception $e) {
            $this->logger->error('TOTP URI生成中にエラーが発生しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'TOTP生成中にエラーが発生しました: ' . $e->getMessage()
            ];
        }
    }

    private function generateRandomSecret(int $length = 16): string
    {
        $bytes = random_bytes($length);
        return $this->base32Encode($bytes);
    }

    private function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        foreach (str_split($data) as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        
        $result = '';
        $fiveBitBinary = str_split($binary, 5);
        foreach ($fiveBitBinary as $five) {
            if (strlen($five) == 5) {
                $result .= $alphabet[bindec($five)];
            }
        }
        
        return $result;
    }

    private function isValidBase32(string $string): bool
    {
        return preg_match('/^[A-Z2-7]+$/', $string) === 1;
    }

    public function validateSecret(string $secret): bool
    {
        try {
            new TotpSecret($secret);
            Logger::getInstance()->info('TOTP secret validated successfully', [
                'secret' => $secret
            ]);
            return true;
        } catch (\Exception $e) {
            Logger::getInstance()->error('Invalid TOTP secret', [
                'secret' => $secret,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function validateIssuer(string $issuer): bool
    {
        try {
            new TotpIssuer($issuer);
            Logger::getInstance()->info('TOTP issuer validated successfully', [
                'issuer' => $issuer
            ]);
            return true;
        } catch (\Exception $e) {
            Logger::getInstance()->error('Invalid TOTP issuer', [
                'issuer' => $issuer,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function validateAccount(string $account): bool
    {
        try {
            new TotpAccount($account);
            Logger::getInstance()->info('TOTP account validated successfully', [
                'account' => $account
            ]);
            return true;
        } catch (\Exception $e) {
            Logger::getInstance()->error('Invalid TOTP account', [
                'account' => $account,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function generateSecret(): TotpSecret
    {
        $bytes = random_bytes(20);
        $secret = base32_encode($bytes);
        return new TotpSecret($secret);
    }

    private function base32_encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';
        $dataLength = strlen($data);
        
        // バイナリ文字列に変換
        for ($i = 0; $i < $dataLength; $i++) {
            $binary .= str_pad(decbin(ord($data[$i])), 8, '0', STR_PAD_LEFT);
        }
        
        // 5ビットずつ処理
        $binaryLength = strlen($binary);
        $encoded = '';
        for ($i = 0; $i + 5 <= $binaryLength; $i += 5) {
            $chunk = substr($binary, $i, 5);
            $encoded .= $alphabet[bindec($chunk)];
        }
        
        // パディングを追加
        $padding = strlen($encoded) % 8;
        if ($padding > 0) {
            $encoded .= str_repeat('=', 8 - $padding);
        }
        
        return $encoded;
    }

    public function generateCode(string $secret): string
    {
        try {
            $this->logger->info('TOTPコード生成を開始します', [
                'secret' => $secret
            ]);

            // Base32デコード
            $decodedSecret = $this->base32Decode($secret);
            $this->logger->debug('Base32デコードが完了しました', [
                'decodedLength' => strlen($decodedSecret)
            ]);

            // 現在の30秒間隔のカウンターを計算
            $counter = floor(time() / 30);
            $this->logger->debug('カウンターを計算しました', [
                'counter' => $counter
            ]);

            // カウンターを8バイトのバイナリ文字列に変換
            $counterBin = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $counter);
            $this->logger->debug('カウンターをバイナリに変換しました', [
                'binaryLength' => strlen($counterBin)
            ]);

            // HMAC-SHA1を計算
            $hash = hash_hmac('sha1', $counterBin, $decodedSecret, true);
            if ($hash === false) {
                throw new \RuntimeException('HMAC-SHA1の計算に失敗しました');
            }
            $this->logger->debug('HMAC-SHA1を計算しました', [
                'hashLength' => strlen($hash)
            ]);

            // 最後のバイトのオフセットを取得
            $offset = ord($hash[strlen($hash) - 1]) & 0xf;
            $this->logger->debug('オフセットを計算しました', [
                'offset' => $offset
            ]);

            // 4バイトを取得し、最上位ビットをマスク
            $binary = (
                ((ord($hash[$offset]) & 0x7f) << 24) |
                ((ord($hash[$offset + 1]) & 0xff) << 16) |
                ((ord($hash[$offset + 2]) & 0xff) << 8) |
                (ord($hash[$offset + 3]) & 0xff)
            );
            $this->logger->debug('バイナリ値を計算しました', [
                'binary' => $binary
            ]);

            // 6桁のコードを生成
            $code = str_pad($binary % 1000000, 6, '0', STR_PAD_LEFT);
            $this->logger->info('TOTPコードを生成しました', [
                'code' => $code
            ]);

            return $code;
        } catch (\Exception $e) {
            $this->logger->error('TOTPコード生成中にエラーが発生しました', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function base32Decode(string $input): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper($input);
        $input = str_replace('=', '', $input);
        
        $binary = '';
        $inputLength = strlen($input);
        
        // 各文字を5ビットのバイナリに変換
        for ($i = 0; $i < $inputLength; $i++) {
            $char = $input[$i];
            $position = strpos($alphabet, $char);
            if ($position === false) {
                throw new \InvalidArgumentException('無効なBase32文字が含まれています: ' . $char);
            }
            $binary .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }
        
        // 8ビットずつ処理してバイト列に変換
        $result = '';
        $binaryLength = strlen($binary);
        for ($i = 0; $i + 8 <= $binaryLength; $i += 8) {
            $chunk = substr($binary, $i, 8);
            $result .= chr(bindec($chunk));
        }
        
        return $result;
    }
} 