<?php

namespace Domain\ValueObjects\QrCode;

use Domain\Exceptions\InvalidQrCodeSizeException;

class QrCodeSize
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 100 || $value > 1000) {
            throw new InvalidQrCodeSizeException('QRコードのサイズは100から1000ピクセルの間である必要があります。');
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }
} 