<?php

define('BASE32_ALPHABET', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567');

class Totp
{
    /**
     * @var string シード値
     */
    private string $seed;

    /**
     * @var int 時間間隔
     */
    private int $time_step = 30;

    public function __construct()
    {
        $this->generateSeed();
    }

    public function generateSeed(): string{
        $this->setSeedBinary(random_bytes(20));
        return $this->seed;
    }

    public function setSeedBinary($seed){
        $this->seed = $seed;
    }

    public function setSeed16($seed){
        $this->seed = hex2bin($seed);
    }
    
    public function getSeedBytes(): string{
        return $this->seed;
    }
    
    public function getSeedString(): string{
        return $this->base32_encode($this->getSeedBytes());
    }

    private function get_current_steps(): int {
        return intdiv(time(), $this->time_step);
    }

    public function getTOTPKey(int $steps): string {

        $steps_string = $this->int64BytesBigEndian($steps);
    
        $otp_string = $this->getHOTPKey($this->getSeedBytes(), $steps_string);
    
        return $otp_string;
    }
    
    public function getTOTPKeyNow(): int {
        return $this->getTOTPKey($this->get_current_steps());
    }

    /**
     * $stringの値が正しいか判別
     * @param string $string 確認文字
     * @param int $tolerance 許容範囲
     */
    public function checkKey(string $string, int $tolerance): bool{

        if( $tolerance < 0 ){
            $tolerance *= -1;
        }

        for ($i= -1 * $tolerance ; $i <= $tolerance; $i++) { 

            $key = $this->getTOTPKey($this->get_current_steps() + $i);
            
            if( $key == $string ) return true;
        }
        return false;
    }

    /**
     * 長さ 64 ビットの符号付き整数をビッグエンディアン形式でバイト列に変換
     * @param int $number 符号付き整数
     */
    private function int64BytesBigEndian(int $number): string
    {

        $bytes = '';

        $bytes[0] = chr($number >> 56 & 0xff);
        $bytes[1] = chr($number >> 48 & 0xff);
        $bytes[2] = chr($number >> 40 & 0xff);
        $bytes[3] = chr($number >> 32 & 0xff);
        $bytes[4] = chr($number >> 24 & 0xff);
        $bytes[5] = chr($number >> 16 & 0xff);
        $bytes[6] = chr($number >>  8 & 0xff);
        $bytes[7] = chr($number       & 0xff);

        return $bytes;
    }

    /**
     * Base32をエンコード
     * @param string $string Base32
     */
    private function base32_encode(string $string): string
    {

        $byte_length = strlen($string);

        $data_buffer = 0;
        $data_buffer_bit_length = 0;

        $byte_offset = 0;

        $result = '';

        $base32_alpthabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

        while ($data_buffer_bit_length > 0 || $byte_offset < $byte_length) {

            if ($data_buffer_bit_length < 5) {
                if ($byte_offset < $byte_length) {

                    $data_buffer <<= 8;
                    $data_buffer |= ord($string[$byte_offset++]);
                    $data_buffer_bit_length += 8;
                } else {
                    
                    $data_buffer <<= 5 - $data_buffer_bit_length;
                    $data_buffer_bit_length = 5;
                }
            }

            $data_buffer_bit_length -= 5;
            $value = $data_buffer >> $data_buffer_bit_length & 0x1f;

            $result .= $base32_alpthabet[$value];
        }

        $target_length = ceil(strlen($result) / 8) * 8;
        $result_padded = str_pad($result, $target_length, '=');

        return $result_padded;
    }

    /**
     * HOTP を計算する
     * @param string $seed シード値
     * @param string $counter カウンター
     * カウンタはビッグエンディアンで 8 バイト
     */
    public function getHOTPKey(string $seed, string $counter): string
    {

        $digest = hash_hmac('sha1', $counter, $seed, true);

        $otp = $this->dynamicTruncate($digest) % 1000000;

        $otp_string = str_pad($otp, 6, '0', STR_PAD_LEFT);

        return $otp_string;
    }

    /**
     * 動的切り捨てする
     * @param string $digest 文字列retu
     * @return
     */
    function dynamicTruncate(string $digest): int
    {

        $offset = ord($digest[19]) & 0xf;

        $binary = (
            ord($digest[$offset]) << 24 |
            ord($digest[$offset + 1]) << 16 |
            ord($digest[$offset + 2]) <<  8 |
            ord($digest[$offset + 3])
        );

        $binary_masked = $binary & 0x7fffffff;

        return $binary_masked;
    }
}