<?php

namespace Domain\ValueObjects\QrCode;

class QrCodeMargin
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('マージンは0以上の値である必要があります。');
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }
} 