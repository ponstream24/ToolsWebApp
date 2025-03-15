<?php

namespace Application\Services;

use Domain\Services\EncoderService;
use Domain\Exceptions\EncoderException;

class EncoderApplicationService
{
    private EncoderService $encoderService;

    public function __construct()
    {
        $this->encoderService = new EncoderService();
    }

    /**
     * テキストをエンコードする
     *
     * @param string $text エンコードするテキスト
     * @param string $type エンコードタイプ（base64, url, html, json, hex, binary, md5, sha1, sha256）
     * @return string エンコードされたテキスト
     * @throws EncoderException エンコード処理中にエラーが発生した場合
     */
    public function encode(string $text, string $type): string
    {
        try {
            return $this->encoderService->encode($text, $type);
        } catch (EncoderException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EncoderException('エンコード処理中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    /**
     * テキストをデコードする
     *
     * @param string $text デコードするテキスト
     * @param string $type デコードタイプ（base64, url, html, json, hex, binary）
     * @return string デコードされたテキスト
     * @throws EncoderException デコード処理中にエラーが発生した場合
     */
    public function decode(string $text, string $type): string
    {
        try {
            return $this->encoderService->decode($text, $type);
        } catch (EncoderException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new EncoderException('デコード処理中にエラーが発生しました: ' . $e->getMessage());
        }
    }
} 