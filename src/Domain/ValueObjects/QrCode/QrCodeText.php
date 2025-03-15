<?php

namespace Domain\ValueObjects\QrCode;

use Domain\Exceptions\InvalidQrCodeTextException;

class QrCodeText
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidQrCodeTextException('QRコードのテキストは空にできません。');
        }
        if (mb_strlen($value) > 1000) {
            throw new InvalidQrCodeTextException('QRコードのテキストは1000文字以内である必要があります。');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }
} 